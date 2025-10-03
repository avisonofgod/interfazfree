#!/bin/bash

echo "================================"
echo "InterfazFree Nativa - Restart"
echo "================================"
echo ""

if [ "$EUID" -ne 0 ]; then 
    echo "Por favor ejecute como root o con sudo"
    exit 1
fi

echo "Reiniciando servicios..."

echo "Reiniciando MariaDB..."
systemctl restart mariadb
if [ $? -eq 0 ]; then
    echo "✓ MariaDB reiniciado"
else
    echo "✗ Error al reiniciar MariaDB"
fi

echo "Reiniciando FreeRADIUS..."
systemctl restart freeradius
if [ $? -eq 0 ]; then
    echo "✓ FreeRADIUS reiniciado"
else
    echo "✗ Error al reiniciar FreeRADIUS"
fi

echo "Reiniciando Nginx..."
systemctl restart nginx
if [ $? -eq 0 ]; then
    echo "✓ Nginx reiniciado"
else
    echo "✗ Error al reiniciar Nginx"
fi

echo ""
echo "Limpiando caché de Laravel..."
cd /root/interfazfree-nativa
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo "✓ Caché de Laravel limpiado"

echo ""
echo "Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Aplicación optimizada"

echo ""
echo "Estado de los servicios:"
systemctl status mariadb --no-pager | grep "Active:"
systemctl status freeradius --no-pager | grep "Active:"
systemctl status nginx --no-pager | grep "Active:"

echo ""
echo "================================"
echo "Reinicio completado!"
echo "================================"
