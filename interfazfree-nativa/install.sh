#!/bin/bash

set -e

REQUIRED_INSTALL_PATH="/var/www/interfazfree/interfazfree-nativa"
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
CURRENT_PATH="$(pwd)"

echo "========================================"
echo "   InterfazFree Nativa - Instalador   "
echo "========================================"
echo ""

if [ "$EUID" -ne 0 ]; then 
    echo "❌ ERROR: Este script debe ejecutarse como root"
    echo "   Uso: sudo bash install.sh"
    exit 1
fi

if [[ "$SCRIPT_DIR" == /root/* ]] || [[ "$CURRENT_PATH" == /root/* ]]; then
    echo "❌ ERROR: No se puede instalar desde /root"
    echo ""
    echo "Nginx (www-data) no puede acceder a archivos en /root por seguridad."
    echo ""
    echo "Solución:"
    echo "  1. Clonar el repositorio en /var/www:"
    echo "     sudo mkdir -p /var/www"
    echo "     cd /var/www"
    echo "     sudo git clone https://github.com/avisonofgod/interfazfree.git"
    echo "     cd interfazfree/interfazfree-nativa"
    echo "     sudo bash install.sh"
    echo ""
    exit 1
fi

if [ "$SCRIPT_DIR" != "$REQUIRED_INSTALL_PATH" ]; then
    echo "⚠️  ADVERTENCIA: Ruta de instalación no estándar"
    echo "   Ubicación actual: $SCRIPT_DIR"
    echo "   Ubicación recomendada: $REQUIRED_INSTALL_PATH"
    echo ""
    read -p "¿Continuar de todos modos? (s/N): " confirm
    if [[ ! "$confirm" =~ ^[sS]$ ]]; then
        echo "Instalación cancelada."
        exit 1
    fi
fi

PROJECT_DIR="$SCRIPT_DIR"
echo "✓ Instalando en: $PROJECT_DIR"
echo ""

check_command() {
    if ! command -v $1 &> /dev/null; then
        echo "❌ ERROR: $1 no está instalado"
        exit 1
    fi
}

echo "Verificando dependencias del sistema..."
check_command git
check_command php
check_command composer
check_command mysql
check_command nginx

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
if [[ ! "$PHP_VERSION" =~ ^8\.[1-9] ]]; then
    echo "❌ ERROR: Se requiere PHP 8.1 o superior (instalado: $PHP_VERSION)"
    exit 1
fi
echo "✓ PHP $PHP_VERSION"

if ! php -m | grep -q pdo_mysql; then
    echo "❌ ERROR: Extensión PHP pdo_mysql no está instalada"
    exit 1
fi
echo "✓ Todas las dependencias del sistema están instaladas"
echo ""

read -p "Ingrese su IP pública o dominio (ej: 192.168.1.100 o example.com): " PUBLIC_HOST
if [ -z "$PUBLIC_HOST" ]; then
    echo "❌ ERROR: Debe proporcionar una IP pública o dominio"
    exit 1
fi
echo ""

echo "Configurando base de datos..."
DB_NAME="interfazfree_db"
DB_USER="interfazfree"
DB_PASS=$(openssl rand -base64 16 | tr -d "=+/" | cut -c1-16)

mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME};" 2>/dev/null || true
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';" 2>/dev/null || true
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';" 2>/dev/null || true
mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true

echo "✓ Base de datos configurada"
echo "  - Base de datos: $DB_NAME"
echo "  - Usuario: $DB_USER"
echo ""

echo "Configurando archivo .env..."
cd "$PROJECT_DIR"

if [ ! -f .env ]; then
    cp .env.example .env
fi

sed -i "s|APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|APP_URL=.*|APP_URL=http://${PUBLIC_HOST}|" .env
sed -i "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" .env
sed -i "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" .env
sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env

echo "✓ Archivo .env configurado"
echo ""

echo "Instalando dependencias de Composer..."
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev 2>&1 | grep -v "Warning"

echo "✓ Dependencias de Composer instaladas"
echo ""

echo "Generando clave de aplicación..."
php artisan key:generate --force

echo "✓ Clave de aplicación generada"
echo ""

echo "Ejecutando migraciones de base de datos..."
php artisan migrate:fresh --force --seed

echo "✓ Migraciones ejecutadas y datos iniciales creados"
echo ""

echo "Creando usuario administrador..."
ADMIN_EMAIL="admin@interfazfree.local"
ADMIN_PASSWORD=$(openssl rand -base64 12)

php artisan tinker --execute="
\$user = \App\Models\User::firstOrCreate(
    ['email' => '${ADMIN_EMAIL}'],
    [
        'name' => 'Administrador',
        'password' => bcrypt('${ADMIN_PASSWORD}')
    ]
);
if (\$user->wasRecentlyCreated) {
    echo 'Usuario creado exitosamente';
} else {
    \$user->password = bcrypt('${ADMIN_PASSWORD}');
    \$user->save();
    echo 'Contraseña actualizada';
}
"

echo "✓ Usuario administrador configurado"
echo ""

echo "Configurando permisos..."
chown -R www-data:www-data "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
chmod -R 755 "$PROJECT_DIR/public"

if [[ "$PROJECT_DIR" == /var/www/* ]]; then
    chmod 755 /var/www
    if [[ "$PROJECT_DIR" == /var/www/interfazfree/* ]]; then
        chmod 755 /var/www/interfazfree
    fi
fi

echo "✓ Permisos configurados"
echo ""

echo "Configurando Nginx..."
NGINX_CONF="/etc/nginx/sites-available/interfazfree"

cat > "$NGINX_CONF" <<EOF
server {
    listen 80;
    listen [::]:80;
    server_name ${PUBLIC_HOST};
    
    root ${PROJECT_DIR}/public;
    index index.php index.html;
    
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
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/interfazfree 2>/dev/null || true
rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true

if nginx -t 2>&1 | grep -q "test is successful"; then
    echo "✓ Configuración de Nginx válida"
else
    echo "❌ ERROR: Configuración de Nginx inválida"
    nginx -t
    exit 1
fi

systemctl restart nginx
systemctl restart php8.2-fpm

echo "✓ Nginx configurado y reiniciado"
echo ""

echo "Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:cache-components

echo "✓ Aplicación optimizada"
echo ""

echo "Verificando instalación..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/admin 2>/dev/null || echo "000")

if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
    echo "✓ Panel de administración accesible"
else
    echo "⚠️  Advertencia: No se pudo verificar el acceso al panel (código HTTP: $HTTP_CODE)"
fi
echo ""

echo "========================================"
echo "   ✓ Instalación Completada"
echo "========================================"
echo ""
echo "Acceso al panel de administración:"
echo "  URL: http://${PUBLIC_HOST}/admin"
echo "  Usuario: ${ADMIN_EMAIL}"
echo "  Contraseña: ${ADMIN_PASSWORD}"
echo ""
echo "⚠️  IMPORTANTE: Guarde estas credenciales en un lugar seguro"
echo ""
echo "Base de datos:"
echo "  Nombre: ${DB_NAME}"
echo "  Usuario: ${DB_USER}"
echo "  Contraseña: ${DB_PASS}"
echo ""
echo "Comandos útiles:"
echo "  - Ver logs: tail -f ${PROJECT_DIR}/storage/logs/laravel.log"
echo "  - Reiniciar servicios: systemctl restart nginx php8.2-fpm"
echo "  - Estado: systemctl status nginx php8.2-fpm"
echo ""

CREDENTIALS_FILE="${PROJECT_DIR}/CREDENCIALES.txt"
cat > "$CREDENTIALS_FILE" <<EOF
InterfazFree Nativa - Credenciales de Instalación
=================================================

Fecha de instalación: $(date)

Panel de Administración:
  URL: http://${PUBLIC_HOST}/admin
  Usuario: ${ADMIN_EMAIL}
  Contraseña: ${ADMIN_PASSWORD}

Base de Datos:
  Nombre: ${DB_NAME}
  Usuario: ${DB_USER}
  Contraseña: ${DB_PASS}
  Host: localhost
  Puerto: 3306

Ubicación del proyecto: ${PROJECT_DIR}

IMPORTANTE: Elimine este archivo después de guardar las credenciales.
EOF

chmod 600 "$CREDENTIALS_FILE"
echo "Las credenciales también se guardaron en: $CREDENTIALS_FILE"
echo ""
