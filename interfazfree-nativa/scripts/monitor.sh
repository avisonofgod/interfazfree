#!/bin/bash

if [ -d "/var/www/interfazfree/interfazfree-nativa" ]; then
    PROJECT_DIR="/var/www/interfazfree/interfazfree-nativa"
else
    SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
    PROJECT_DIR="$( cd "$SCRIPT_DIR/.." && pwd )"
fi

echo "================================"
echo "InterfazFree Nativa - Monitor"
echo "================================"
echo ""

check_service() {
    service=$1
    if systemctl is-active --quiet $service; then
        echo "✓ $service está activo"
    else
        echo "✗ $service está inactivo"
    fi
}

check_service "mariadb"
check_service "freeradius"
check_service "nginx"

echo ""
echo "Estado de la base de datos:"
mysql -u interfazfree -pinterfazfree_password interfazfree_db -e "
    SELECT 
        'Perfiles' as Tabla, COUNT(*) as Total FROM perfils
    UNION ALL
    SELECT 'NAS', COUNT(*) FROM nas
    UNION ALL
    SELECT 'Lotes', COUNT(*) FROM lotes
    UNION ALL
    SELECT 'Fichas', COUNT(*) FROM fichas
    UNION ALL
    SELECT 'Sin Usar', COUNT(*) FROM fichas WHERE estado = 'sin_usar'
    UNION ALL
    SELECT 'Activas', COUNT(*) FROM fichas WHERE estado = 'activa'
    UNION ALL
    SELECT 'Caducadas', COUNT(*) FROM fichas WHERE estado = 'caducada'
    UNION ALL
    SELECT 'Sesiones RADIUS', COUNT(*) FROM radacct;
" 2>/dev/null || echo "No se pudo conectar a la base de datos"

echo ""
echo "Uso de disco:"
df -h $PROJECT_DIR | tail -1 | awk '{print "Usado: " $3 " de " $2 " (" $5 ")"}'

echo ""
echo "Últimas 5 autenticaciones RADIUS:"
mysql -u interfazfree -pinterfazfree_password interfazfree_db -e "
    SELECT username, reply, DATE_FORMAT(authdate, '%Y-%m-%d %H:%i:%s') as fecha 
    FROM radpostauth 
    ORDER BY authdate DESC 
    LIMIT 5;
" 2>/dev/null || echo "No hay datos de autenticación"

echo ""
echo "================================"
