.PHONY: help install migrate seed setup restart status monitor backup clean test deploy

help:
	@echo "================================"
	@echo "InterfazFree Nativa - Makefile"
	@echo "================================"
	@echo ""
	@echo "Comandos disponibles:"
	@echo "  make install    - Instalar dependencias de Laravel"
	@echo "  make migrate    - Ejecutar migraciones"
	@echo "  make seed       - Ejecutar seeders"
	@echo "  make setup      - Configuración completa del sistema (requiere sudo)"
	@echo "  make restart    - Reiniciar servicios (requiere sudo)"
	@echo "  make status     - Ver estado del sistema"
	@echo "  make monitor    - Monitorear sistema y base de datos"
	@echo "  make backup     - Realizar backup completo"
	@echo "  make clean      - Limpiar caché y archivos temporales"
	@echo "  make test       - Ejecutar tests"
	@echo "  make deploy     - Optimizar para producción"
	@echo ""

install:
	@echo "Instalando dependencias de Laravel..."
	composer install --no-interaction --optimize-autoloader
	@echo "✓ Dependencias instaladas"

migrate:
	@echo "Ejecutando migraciones..."
	php artisan migrate --force
	@echo "✓ Migraciones completadas"

seed:
	@echo "Ejecutando seeders..."
	php artisan db:seed --force
	@echo "✓ Seeders completados"

setup:
	@echo "Ejecutando setup completo..."
	sudo bash scripts/setup.sh

restart:
	@echo "Reiniciando servicios..."
	sudo bash scripts/restart.sh

status:
	@echo "Verificando estado del sistema..."
	bash scripts/status.sh

monitor:
	@echo "Monitoreando sistema..."
	bash scripts/monitor.sh

backup:
	@echo "Realizando backup..."
	sudo bash scripts/backup.sh

clean:
	@echo "Limpiando caché y archivos temporales..."
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	rm -rf storage/logs/*.log
	@echo "✓ Limpieza completada"

test:
	@echo "Ejecutando tests..."
	php artisan test

deploy:
	@echo "Optimizando para producción..."
	composer install --no-dev --optimize-autoloader
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	php artisan migrate --force
	@echo "✓ Aplicación optimizada para producción"
