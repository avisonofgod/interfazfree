#!/bin/bash

if [ -d "/var/www/interfazfree/interfazfree-nativa" ]; then
    PROJECT_DIR="/var/www/interfazfree/interfazfree-nativa"
else
    SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
    PROJECT_DIR="$( cd "$SCRIPT_DIR/.." && pwd )"
fi

echo "================================"
echo "InterfazFree Nativa - Status"
echo "================================"
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

check_service() {
    service=$1
    if systemctl is-active --quiet $service; then
        echo -e "${GREEN}✓${NC} $service está activo"
        return 0
    else
        echo -e "${RED}✗${NC} $service está inactivo"
        return 1
    fi
}

echo "Estado de servicios:"
check_service "mariadb"
mariadb_status=$?
check_service "freeradius"
freeradius_status=$?
check_service "nginx"
nginx_status=$?

echo ""
echo "Conectividad de base de datos:"
if mysql -u interfazfree -pinterfazfree_password -e "USE interfazfree_db;" 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Conexión a base de datos exitosa"
    db_status=0
else
    echo -e "${RED}✗${NC} No se pudo conectar a la base de datos"
    db_status=1
fi

echo ""
echo "Información del sistema:"
echo "Hostname: $(hostname)"
echo "IP: $(hostname -I | awk '{print $1}')"
echo "Uptime: $(uptime -p)"
echo "Carga: $(uptime | awk -F'load average:' '{print $2}')"

echo ""
echo "Uso de recursos:"
free -h | grep "Mem:" | awk '{print "Memoria: " $3 " usado de " $2 " (" int($3/$2*100) "%)"}'
df -h $PROJECT_DIR | tail -1 | awk '{print "Disco: " $3 " usado de " $2 " (" $5 ")"}'

echo ""
echo "Versiones instaladas:"
php -v | head -1
mysql --version
echo "Laravel: $(cd $PROJECT_DIR && php artisan --version)"

echo ""
echo "Estadísticas de fichas:"
mysql -u interfazfree -pinterfazfree_password interfazfree_db -N -e "
    SELECT 
        CONCAT('Total: ', COUNT(*)) FROM fichas
    UNION ALL
    SELECT CONCAT('Sin usar: ', COUNT(*)) FROM fichas WHERE estado = 'sin_usar'
    UNION ALL
    SELECT CONCAT('Activas: ', COUNT(*)) FROM fichas WHERE estado = 'activa'
    UNION ALL
    SELECT CONCAT('Caducadas: ', COUNT(*)) FROM fichas WHERE estado = 'caducada';
" 2>/dev/null | while read line; do
    echo "  $line"
done

echo ""
echo "================================"

if [ $mariadb_status -eq 0 ] && [ $freeradius_status -eq 0 ] && [ $nginx_status -eq 0 ] && [ $db_status -eq 0 ]; then
    echo -e "${GREEN}Sistema operando correctamente${NC}"
    exit 0
else
    echo -e "${YELLOW}Algunos servicios presentan problemas${NC}"
    exit 1
fi
