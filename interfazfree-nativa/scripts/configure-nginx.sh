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
DETECTED_DIR="$( cd "$SCRIPT_DIR/.." && pwd )"

# Usar /var/www como directorio recomendado (important-comment)
if [ -d "/var/www/interfazfree/interfazfree-nativa" ]; then
    DEFAULT_PROJECT_DIR="/var/www/interfazfree/interfazfree-nativa"
else
    DEFAULT_PROJECT_DIR="$DETECTED_DIR"
fi

read -p "Ingrese la IP pública o dominio: " PUBLIC_HOST
read -p "Ingrese la ruta del proyecto [$DEFAULT_PROJECT_DIR]: " PROJECT_PATH
PROJECT_PATH=${PROJECT_PATH:-$DEFAULT_PROJECT_DIR}

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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
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

if [[ "$PROJECT_PATH" == /root/* ]]; then
    echo "Detectada instalación en /root, ajustando permisos de traversal..."
    chmod 755 /root
    if [[ "$PROJECT_PATH" == /root/interfazfree/* ]]; then
        chmod 755 /root/interfazfree
    fi
    chmod 755 "$PROJECT_PATH"
fi

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
