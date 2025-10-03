#!/bin/bash

set -e

echo "================================"
echo "Configurar Nginx para Producción"
echo "================================"
echo ""

if [ "$EUID" -ne 0 ]; then 
    echo "Por favor ejecute como root o con sudo"
    exit 1
fi

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
DEFAULT_PROJECT_DIR="$( cd "$SCRIPT_DIR/.." && pwd )"

read -p "Ingrese la IP pública o dominio: " PUBLIC_HOST
read -p "Ingrese la ruta del proyecto [$DEFAULT_PROJECT_DIR]: " PROJECT_PATH
PROJECT_PATH=${PROJECT_PATH:-$DEFAULT_PROJECT_DIR}

echo ""
echo "¿Qué usuario ejecuta la aplicación?"
echo "1) www-data (por defecto)"
echo "2) interfazfree (usuario dedicado)"
read -p "Opción [1-2]: " USER_OPTION

if [ "$USER_OPTION" == "2" ]; then
    APP_USER="interfazfree"
    PHP_FPM_SOCK="/var/run/php/php8.2-fpm-interfazfree.sock"
    
    if ! id "$APP_USER" &>/dev/null; then
        echo "Error: El usuario $APP_USER no existe. Ejecute setup.sh primero."
        exit 1
    fi
    
    if [ ! -f /etc/php/8.2/fpm/pool.d/$APP_USER.conf ]; then
        echo "Creando pool PHP-FPM para $APP_USER..."
        cat > /etc/php/8.2/fpm/pool.d/$APP_USER.conf << EOF
[$APP_USER]
user = $APP_USER
group = $APP_USER
listen = $PHP_FPM_SOCK
listen.owner = $APP_USER
listen.group = $APP_USER
listen.mode = 0660
pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
EOF
    fi
else
    APP_USER="www-data"
    PHP_FPM_SOCK="/var/run/php/php8.2-fpm.sock"
fi

echo ""
echo "Creando configuración de Nginx..."

cat > /etc/nginx/sites-available/interfazfree << EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${PUBLIC_HOST};
    root ${PROJECT_PATH}/public;

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
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 100M;
}
EOF

echo "Activando sitio en Nginx..."
ln -sf /etc/nginx/sites-available/interfazfree /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

echo "Verificando configuración de Nginx..."
nginx -t

echo "Reiniciando servicios..."
systemctl restart nginx
systemctl enable nginx
systemctl restart php8.2-fpm
systemctl enable php8.2-fpm

echo "Actualizando APP_URL en .env..."
cd ${PROJECT_PATH}
sed -i "s|APP_URL=.*|APP_URL=http://${PUBLIC_HOST}|" .env
php artisan config:clear
php artisan config:cache

echo ""
echo "================================"
echo "Nginx configurado exitosamente!"
echo "================================"
echo ""
echo "Accede al panel en: http://${PUBLIC_HOST}/admin"
echo ""
echo "Para configurar SSL/HTTPS con Let's Encrypt:"
echo "  apt install certbot python3-certbot-nginx"
echo "  certbot --nginx -d ${PUBLIC_HOST}"
echo ""
