# InterfazFree - Repositorio Principal

Este repositorio contiene el proyecto **InterfazFree Nativa**, un panel administrativo para FreeRADIUS desarrollado con Laravel 10 + Filament 3.

## 📁 Estructura del Repositorio

```
interfazfree/
└── interfazfree-nativa/    # Proyecto Laravel completo
    ├── app/
    ├── database/
    ├── scripts/
    ├── README.md           # Documentación completa del proyecto
    └── ...
```

## 🚀 Inicio Rápido

### Instalación en VPS/Servidor (Producción)

La ruta de instalación recomendada es `/root/interfazfree/`:

```bash
# 1. Clonar el repositorio
cd /root
git clone https://github.com/avisonofgod/interfazfree.git
cd interfazfree/interfazfree-nativa

# 2. Ejecutar instalación completa
sudo bash scripts/setup.sh
# Seleccionar opción 2 para producción
# Ingresar IP pública o dominio

# 3. Acceder al panel
# http://TU_IP_PUBLICA/admin
```

### Instalación para Desarrollo (localhost)

```bash
# 1. Clonar el repositorio
git clone https://github.com/avisonofgod/interfazfree.git
cd interfazfree/interfazfree-nativa

# 2. Ejecutar instalación
sudo bash scripts/setup.sh
# Seleccionar opción 1 para desarrollo

# 3. Iniciar servidor de desarrollo
php artisan serve

# 4. Acceder al panel
# http://localhost:8000/admin
```

## 📖 Documentación Completa

Para documentación detallada, instalación manual, configuración avanzada y troubleshooting, consulta:

**[interfazfree-nativa/README.md](./interfazfree-nativa/README.md)**

## ✨ Características Principales

- **Backend**: Laravel 10
- **Frontend**: Filament 3 (TALL stack)
- **Base de Datos**: MariaDB 11
- **RADIUS**: FreeRADIUS nativo
- **Despliegue**: Soporte para desarrollo (localhost) y producción (VPS con IP pública/dominio)
- **Automatización**: Scripts de setup, monitoreo, backup y gestión
- **Compatibilidad**: Mikrotik y OPNsense

## 🔐 Credenciales por Defecto

- **Usuario**: admin@interfazfree.local
- **Contraseña**: admin123

⚠️ **Importante**: Cambiar credenciales después de la instalación.

## 🛠️ Scripts Disponibles

Todos los scripts están en `interfazfree-nativa/scripts/`:

- `setup.sh` - Instalación completa del sistema
- `configure-nginx.sh` - Configuración manual de Nginx
- `monitor.sh` - Monitoreo del sistema
- `backup.sh` - Backup de base de datos
- `restart.sh` - Reinicio de servicios
- `status.sh` - Estado del sistema

## 🤝 Contribuciones

- **Usuario**: Abdias Rivera Bautista (@avisonofgod)
- **Desarrollo**: Devin AI
- **Sesión**: https://app.devin.ai/sessions/aaa56e7f3731478a827fe26d9a6d45a4

## 📞 Soporte

- GitHub Issues: https://github.com/avisonofgod/interfazfree/issues
- Documentación completa: [interfazfree-nativa/README.md](./interfazfree-nativa/README.md)

---

**Versión**: 1.0.0  
**Última actualización**: Octubre 2025
