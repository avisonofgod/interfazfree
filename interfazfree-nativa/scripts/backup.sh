#!/bin/bash

if [ -d "/var/www/interfazfree/interfazfree-nativa" ]; then
    PROJECT_DIR="/var/www/interfazfree/interfazfree-nativa"
else
    SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
    PROJECT_DIR="$( cd "$SCRIPT_DIR/.." && pwd )"
fi

BACKUP_DIR="${HOME}/backups/interfazfree"
DATE=$(date +%Y%m%d_%H%M%S)

echo "================================"
echo "InterfazFree Nativa - Backup"
echo "================================"
echo ""

mkdir -p $BACKUP_DIR

echo "Realizando backup de la base de datos..."
mysqldump -u interfazfree -pinterfazfree_password interfazfree_db > "$BACKUP_DIR/db_backup_$DATE.sql"
if [ $? -eq 0 ]; then
    echo "✓ Backup de base de datos completado: db_backup_$DATE.sql"
else
    echo "✗ Error al realizar backup de base de datos"
    exit 1
fi

echo "Comprimiendo backup de base de datos..."
gzip "$BACKUP_DIR/db_backup_$DATE.sql"
echo "✓ Backup comprimido: db_backup_$DATE.sql.gz"

echo "Realizando backup de archivos de la aplicación..."
PROJECT_PARENT="$(dirname "$PROJECT_DIR")"
PROJECT_NAME="$(basename "$PROJECT_DIR")"
tar -czf "$BACKUP_DIR/app_backup_$DATE.tar.gz" \
    -C "$PROJECT_PARENT" \
    --exclude="$PROJECT_NAME/vendor" \
    --exclude="$PROJECT_NAME/node_modules" \
    --exclude="$PROJECT_NAME/storage/logs/*.log" \
    --exclude="$PROJECT_NAME/.git" \
    "$PROJECT_NAME"

if [ $? -eq 0 ]; then
    echo "✓ Backup de aplicación completado: app_backup_$DATE.tar.gz"
else
    echo "✗ Error al realizar backup de aplicación"
    exit 1
fi

echo ""
echo "Limpiando backups antiguos (manteniendo últimos 7 días)..."
find $BACKUP_DIR -name "*.gz" -type f -mtime +7 -delete
echo "✓ Backups antiguos eliminados"

echo ""
echo "Backups disponibles en: $BACKUP_DIR"
ls -lh $BACKUP_DIR | tail -5

echo ""
echo "================================"
echo "Backup completado exitosamente!"
echo "================================"
