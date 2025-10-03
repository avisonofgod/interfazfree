#!/bin/bash

set -e

echo "================================"
echo "InterfazFree Nativa - Setup"
echo "================================"
echo ""

if [ "$EUID" -ne 0 ]; then 
    echo "Por favor ejecute como root o con sudo"
    exit 1
fi

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$( cd "$SCRIPT_DIR/.." && pwd )"

echo "Directorio del proyecto detectado: $PROJECT_DIR"
echo ""

echo "Configuración de usuario del sistema:"
echo "1) Usar usuario existente www-data (por defecto)"
echo "2) Crear usuario dedicado 'interfazfree'"
read -p "Opción [1-2]: " USER_OPTION

if [ "$USER_OPTION" == "2" ]; then
    APP_USER="interfazfree"
    APP_GROUP="interfazfree"
    
    if ! id "$APP_USER" &>/dev/null; then
        echo "Creando usuario $APP_USER..."
        useradd -r -m -s /bin/bash -d /home/$APP_USER -c "InterfazFree Application User" $APP_USER
        
        usermod -a -G www-data $APP_USER
        
        echo "Usuario $APP_USER creado exitosamente"
    else
        echo "Usuario $APP_USER ya existe"
    fi
else
    APP_USER="www-data"
    APP_GROUP="www-data"
fi

echo "Usuario de la aplicación: $APP_USER"
echo ""

echo "Seleccione el tipo de instalación:"
echo "1) Desarrollo (localhost)"
echo "2) Producción (IP pública o dominio)"
read -p "Opción [1-2]: " INSTALL_TYPE

if [ "$INSTALL_TYPE" == "2" ]; then
    read -p "Ingrese la IP pública o dominio (ej: 192.168.1.100 o ejemplo.com): " PUBLIC_HOST
    read -p "¿Desea configurar HTTPS con certificado SSL? [s/N]: " USE_SSL
    APP_URL="http://${PUBLIC_HOST}"
    if [ "$USE_SSL" == "s" ] || [ "$USE_SSL" == "S" ]; then
        APP_URL="https://${PUBLIC_HOST}"
    fi
    APP_ENV="production"
    APP_DEBUG="false"
    echo ""
    echo "Configurando para producción:"
    echo "  - URL: $APP_URL"
    echo "  - Entorno: $APP_ENV"
    echo ""
else
    PUBLIC_HOST="localhost"
    APP_URL="http://localhost"
    APP_ENV="local"
    APP_DEBUG="true"
    echo ""
    echo "Configurando para desarrollo (localhost)"
    echo ""
fi

echo "Instalando dependencias del sistema..."
apt update
apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-xml \
    php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-gd \
    php8.2-intl php8.2-tokenizer php8.2-fpm nginx mariadb-server mariadb-client \
    freeradius freeradius-mysql freeradius-utils unzip curl git

echo "Verificando instalación de Composer..."
if ! command -v composer &> /dev/null; then
    echo "Instalando Composer..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
fi

echo "Configurando base de datos..."
mysql -e "CREATE DATABASE IF NOT EXISTS interfazfree_db;"
mysql -e "CREATE USER IF NOT EXISTS 'interfazfree'@'localhost' IDENTIFIED BY 'interfazfree_password';"
mysql -e "GRANT ALL PRIVILEGES ON interfazfree_db.* TO 'interfazfree'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "Configurando permisos iniciales..."
if [ "$APP_USER" != "www-data" ]; then
    echo "Asignando propiedad del proyecto a $APP_USER..."
    chown -R $APP_USER:$APP_GROUP "$PROJECT_DIR"
    chmod -R 755 "$PROJECT_DIR"
fi

echo "Instalando dependencias de Laravel..."
cd "$PROJECT_DIR"

if [ "$APP_USER" != "www-data" ]; then
    echo "Ejecutando composer como usuario $APP_USER..."
    sudo -u $APP_USER composer install --no-interaction --optimize-autoloader
else
    composer install --no-interaction --optimize-autoloader
fi

echo "Configurando archivo .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    if [ "$APP_USER" != "www-data" ]; then
        sudo -u $APP_USER php artisan key:generate
    else
        php artisan key:generate
    fi
fi

sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|" .env
sed -i "s/APP_ENV=.*/APP_ENV=${APP_ENV}/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=${APP_DEBUG}/" .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=interfazfree_db/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=interfazfree/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=interfazfree_password/' .env

echo "Ejecutando migraciones..."
if [ "$APP_USER" != "www-data" ]; then
    sudo -u $APP_USER php artisan migrate --force
else
    php artisan migrate --force
fi

echo "Ejecutando seeders..."
if [ "$APP_USER" != "www-data" ]; then
    sudo -u $APP_USER php artisan db:seed --force
else
    php artisan db:seed --force
fi

echo "Configurando permisos finales..."
chown -R $APP_USER:$APP_GROUP storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ "$INSTALL_TYPE" == "2" ]; then
    echo "Configurando Nginx para producción..."
    
    if [ "$APP_USER" != "www-data" ]; then
        echo "Configurando pool PHP-FPM para $APP_USER..."
        
        cat > /etc/php/8.2/fpm/pool.d/$APP_USER.conf << EOF
[$APP_USER]
user = $APP_USER
group = $APP_GROUP
listen = /var/run/php/php8.2-fpm-$APP_USER.sock
listen.owner = $APP_USER
listen.group = $APP_GROUP
listen.mode = 0660
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
EOF
        
        PHP_FPM_SOCK="/var/run/php/php8.2-fpm-$APP_USER.sock"
    else
        PHP_FPM_SOCK="/var/run/php/php8.2-fpm.sock"
    fi
    
    cat > /etc/nginx/sites-available/interfazfree << EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${PUBLIC_HOST};
    root ${PROJECT_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:${PHP_FPM_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

    ln -sf /etc/nginx/sites-available/interfazfree /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    
    echo "Actualizando configuración de Laravel..."
    if [ "$APP_USER" != "www-data" ]; then
        sudo -u $APP_USER php artisan config:clear
        sudo -u $APP_USER php artisan config:cache
    else
        php artisan config:clear
        php artisan config:cache
    fi
    
    echo "Verificando configuración de Nginx..."
    nginx -t
    
    echo "Reiniciando servicios..."
    systemctl restart php8.2-fpm
    systemctl enable php8.2-fpm
    systemctl restart nginx
    systemctl enable nginx
    
    if [ "$USE_SSL" == "s" ] || [ "$USE_SSL" == "S" ]; then
        echo ""
        echo "Para configurar SSL/HTTPS, ejecute:"
        echo "  apt install certbot python3-certbot-nginx"
        echo "  certbot --nginx -d ${PUBLIC_HOST}"
        echo ""
    fi
fi

echo "Optimizando aplicación..."
if [ "$APP_USER" != "www-data" ]; then
    sudo -u $APP_USER php artisan config:cache
    sudo -u $APP_USER php artisan route:cache
    sudo -u $APP_USER php artisan view:cache
else
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "================================"
echo "Setup completado exitosamente!"
echo "================================"
echo ""
if [ "$INSTALL_TYPE" == "2" ]; then
    echo "Accede al panel en: ${APP_URL}/admin"
    echo ""
    echo "Configuración de Nginx completada"
    echo "Servicios activos: Nginx, PHP-FPM, MariaDB, FreeRADIUS"
else
    echo "Para desarrollo, inicia el servidor con:"
    echo "  cd $PROJECT_DIR"
    echo "  php artisan serve"
    echo ""
    echo "Accede al panel en: http://localhost:8000/admin"
fi
echo ""
echo "Usuario: admin@interfazfree.local"
echo "Contraseña: admin123"
echo ""
echo "⚠️  IMPORTANTE: Cambia las credenciales después de la instalación"
