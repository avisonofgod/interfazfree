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
DETECTED_DIR="$( cd "$SCRIPT_DIR/.." && pwd )"

# Usar /var/www como directorio recomendado para producción
if [ ! -d "/var/www/interfazfree" ]; then
    PROJECT_DIR="$DETECTED_DIR"
else
    PROJECT_DIR="/var/www/interfazfree/interfazfree-nativa"
fi

echo "Directorio del proyecto detectado: $PROJECT_DIR"
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

echo "Instalando dependencias de Laravel..."
cd "$PROJECT_DIR"
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-interaction --optimize-autoloader

echo "Configurando archivo .env..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

sed -i "s|APP_URL=.*|APP_URL=${APP_URL}|" .env
sed -i "s/APP_ENV=.*/APP_ENV=${APP_ENV}/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=${APP_DEBUG}/" .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=interfazfree_db/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=interfazfree/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=interfazfree_password/' .env

echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Ejecutando seeders..."
php artisan db:seed --force

echo "Configurando permisos..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ "$INSTALL_TYPE" == "2" ]; then
    echo "Configurando Nginx para producción..."
    
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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
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
    
    nginx -t
    systemctl restart nginx
    systemctl enable nginx
    systemctl restart php8.2-fpm
    systemctl enable php8.2-fpm
    
    if [ "$USE_SSL" == "s" ] || [ "$USE_SSL" == "S" ]; then
        echo ""
        echo "Para configurar SSL/HTTPS, ejecute:"
        echo "  apt install certbot python3-certbot-nginx"
        echo "  certbot --nginx -d ${PUBLIC_HOST}"
        echo ""
    fi
fi

echo "Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

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
