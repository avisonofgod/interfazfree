# InterfazFree Nativa

Panel administrativo para FreeRADIUS desarrollado con Laravel 10 + Filament 3, diseñado para instalación nativa sobre Debian 12.

## 🎯 Características

- **Stack Tecnológico**: Laravel 10 + Filament 3 (TALL stack)
- **Base de Datos**: MariaDB 11
- **RADIUS**: FreeRADIUS nativo con soporte para Mikrotik y OPNsense
- **Gestión de Fichas**: Control completo de estados (Sin Usar, Activa, Caducada)
- **Perfiles Configurables**: Corrido, Pausado y Recurrente
- **Generación de Lotes**: Creación masiva de fichas con passwords personalizables
- **Integración RADIUS**: Acceso directo a tablas radcheck, radreply, radacct
- **Automatización**: Scripts de setup, monitoreo, backup y gestión

## 📋 Requisitos del Sistema

- Debian 12 (Bookworm)
- PHP 8.2+
- MariaDB 11
- FreeRADIUS
- Composer
- Nginx/Apache

## 🚀 Instalación

### ⚡ Instalación con Un Solo Comando (Recomendado)

**Para producción en VPS/Servidor:**

```bash
# 1. Clonar el repositorio en /var/www
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/avisonofgod/interfazfree.git

# 2. Ejecutar instalador (UN SOLO COMANDO)
cd interfazfree/interfazfree-nativa
sudo bash install.sh
```

El instalador hará automáticamente:
- ✅ Verificar dependencias del sistema
- ✅ Configurar base de datos con credenciales seguras
- ✅ Instalar dependencias de Composer
- ✅ Generar clave de aplicación
- ✅ Ejecutar migraciones y seeders
- ✅ Crear usuario administrador
- ✅ Configurar Nginx con PHP-FPM
- ✅ Establecer permisos correctos
- ✅ Optimizar la aplicación
- ✅ Verificar que todo funcione

**Al finalizar verás:**
```
✓ Instalación Completada

Acceso al panel de administración:
  URL: http://TU_IP/admin
  Usuario: admin@interfazfree.local
  Contraseña: [generada automáticamente]

⚠️ IMPORTANTE: Guarde estas credenciales en un lugar seguro
```

### 🔧 Instalación para Desarrollo (localhost)

```bash
# 1. Clonar y entrar al proyecto
git clone https://github.com/avisonofgod/interfazfree.git
cd interfazfree/interfazfree-nativa

# 2. Copiar .env y configurar
cp .env.example .env
# Editar .env con tus credenciales de base de datos

# 3. Instalar dependencias y configurar
composer install
php artisan key:generate
php artisan migrate:fresh --seed

# 4. Crear usuario admin
php artisan tinker
>>> \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('admin123')])

# 5. Iniciar servidor de desarrollo
php artisan serve

# 6. Acceder al panel
# http://localhost:8000/admin
# Usuario: admin@test.com
# Contraseña: admin123
```

Este comando ejecutará:
- Instalación de dependencias del sistema
- Configuración de MariaDB
- Instalación de FreeRADIUS
- Configuración de Laravel
- Ejecución de migraciones y seeders
- Configuración de Nginx (si se selecciona producción)

### Instalación Manual

1. **Clonar el repositorio**
```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/avisonofgod/interfazfree.git
cd interfazfree/interfazfree-nativa
```

2. **Instalar dependencias**
```bash
make install
```

3. **Configurar archivo .env**
```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` con las credenciales de base de datos:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=interfazfree_db
DB_USERNAME=interfazfree
DB_PASSWORD=interfazfree_password
```

4. **Ejecutar migraciones y seeders**
```bash
make migrate
make seed
```

5. **Crear usuario administrador**
```bash
php artisan make:filament-user
```

## 🏗️ Estructura del Proyecto

```
interfazfree-nativa/
├── app/
│   ├── Filament/
│   │   └── Resources/          # Recursos de Filament
│   │       ├── PerfilResource.php
│   │       ├── NasResource.php
│   │       ├── LoteResource.php
│   │       └── FichaResource.php
│   └── Models/                  # Modelos Eloquent
│       ├── Perfil.php
│       ├── Nas.php
│       ├── Lote.php
│       ├── Ficha.php
│       ├── Atributo.php
│       ├── Radcheck.php
│       ├── Radreply.php
│       └── Radacct.php
├── database/
│   ├── migrations/             # Migraciones de base de datos
│   └── seeders/                # Seeders iniciales
│       ├── PerfilSeeder.php
│       └── AtributoSeeder.php
├── scripts/                    # Scripts de automatización
│   ├── setup.sh               # Instalación completa
│   ├── monitor.sh             # Monitoreo del sistema
│   ├── backup.sh              # Backup de BD y archivos
│   ├── restart.sh             # Reinicio de servicios
│   └── status.sh              # Estado del sistema
├── Makefile                    # Comandos estandarizados
└── README.md                   # Documentación
```

## 📊 Modelos y Relaciones

### Perfil
- **Tipos**: Corrido, Pausado, Recurrente
- **Relaciones**: hasMany Ficha, hasMany Atributo, hasMany Lote
- **Atributos**: nombre, tipo, descripción, velocidad_subida, velocidad_bajada, tiempo_vigencia, precio

### Ficha
- **Estados**: Sin Usar (nunca iniciada), Activa (sesión iniciada), Caducada (expirada)
- **Relaciones**: belongsTo Perfil, belongsTo Lote, hasMany Radcheck, hasMany Radreply, hasMany Radacct
- **Atributos**: username, password, estado, fecha_inicio, fecha_expiracion

### Nas
- **Tipos**: Mikrotik, OPNsense
- **Relaciones**: hasMany Lote
- **Atributos**: nombre, shortname, tipo, ip, puerto, secreto

### Lote
- **Relaciones**: belongsTo Perfil, belongsTo Nas, hasMany Ficha
- **Atributos**: nombre, cantidad, longitud_password, tipo_password

### Atributo
- **Relaciones**: belongsTo Perfil
- **Atributos**: nombre, operador, valor, tipo (check/reply)

## 🔧 Comandos Make Disponibles

```bash
make help       # Mostrar ayuda de comandos
make install    # Instalar dependencias
make migrate    # Ejecutar migraciones
make seed       # Ejecutar seeders
make setup      # Configuración completa (requiere sudo)
make restart    # Reiniciar servicios (requiere sudo)
make status     # Ver estado del sistema
make monitor    # Monitorear sistema
make backup     # Realizar backup
make clean      # Limpiar caché
make test       # Ejecutar tests
make deploy     # Optimizar para producción
```

## 🔐 Acceso al Panel

### Desarrollo (localhost)
- **URL**: http://localhost:8000/admin
- **Comando**: `php artisan serve`

### Producción (VPS con IP pública)
- **URL**: http://TU_IP_PUBLICA/admin
- **Servicios**: Nginx + PHP-FPM

### Credenciales por defecto
- **Usuario**: admin@interfazfree.local
- **Contraseña**: admin123

⚠️ **Importante**: Cambiar las credenciales por defecto después de la primera instalación.

### Configurar SSL/HTTPS (Producción)

Si tienes un dominio válido, puedes configurar SSL con Let's Encrypt:

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d tu-dominio.com
```

Certbot configurará automáticamente Nginx para HTTPS y renovará el certificado automáticamente.

## 🔧 Solución de Problemas

### Error "File not found" en Nginx

Si al acceder a `http://TU_IP/admin` aparece el error "File not found", el problema es que www-data no puede acceder a los archivos del proyecto.

**Causa más común: Instalación en /root**

Si instalaste en `/root/interfazfree/interfazfree-nativa`, www-data no puede acceder porque `/root` tiene permisos 700 por defecto.

**Solución:**
```bash
# Dar permisos de traversal a www-data (sin exponer el contenido de /root)
sudo chmod 755 /root /root/interfazfree /root/interfazfree/interfazfree-nativa
sudo systemctl restart nginx
```

**Verificar logs de Nginx:**
```bash
sudo tail -f /var/log/nginx/error.log
```

**Verificar permisos:**
```bash
ls -la /root/interfazfree/interfazfree-nativa/public
# Debe mostrar que www-data puede leer los archivos
```

**Verificar que Nginx apunta al directorio correcto:**
```bash
sudo cat /etc/nginx/sites-available/interfazfree | grep root
# Debe mostrar: root /root/interfazfree/interfazfree-nativa/public;
```

**Solución alternativa (Recomendada para producción):**

Reinstalar en `/var/www` para evitar problemas de permisos:

```bash
cd /var/www
sudo git clone https://github.com/avisonofgod/interfazfree.git
cd interfazfree/interfazfree-nativa
sudo bash install.sh  # o scripts/setup.sh si usas la versión antigua
```

### Panel de Administración Vacío o Sin Recursos

Si el panel carga pero no muestra recursos o formularios:

```bash
cd /var/www/interfazfree/interfazfree-nativa
php artisan config:clear
php artisan cache:clear
php artisan filament:cache-components
sudo systemctl restart php8.2-fpm
```

### Problemas de Base de Datos

**Verificar conexión:**
```bash
cd /var/www/interfazfree/interfazfree-nativa
php artisan tinker
>>> DB::connection()->getPdo();
# Debe retornar objeto PDO sin errores
```

**Re-ejecutar migraciones:**
```bash
php artisan migrate:fresh --seed
```

### Verificar Estado de Servicios

```bash
# Estado de servicios (important-comment)
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mariadb

# Reiniciar servicios si es necesario (important-comment)
sudo systemctl restart nginx php8.2-fpm mariadb
```

## 📡 Integración con FreeRADIUS

El sistema se integra directamente con las tablas de FreeRADIUS:

- **radcheck**: Atributos de autenticación
- **radreply**: Atributos de respuesta
- **radacct**: Registro de sesiones
- **radpostauth**: Log de autenticaciones

### Perfiles y Atributos RADIUS

#### Perfil Corrido
- Fall-Through: Yes
- Simultaneous-Use: 1
- Access-Period: 86400 (tiempo corrido)

#### Perfil Pausado
- Fall-Through: Yes
- Simultaneous-Use: 1
- Max-All-Session: 86400 (tiempo pausado)

#### Perfil Recurrente
- Fall-Through: Yes
- Simultaneous-Use: 1

## 🛠️ Scripts de Automatización

### setup.sh
Instalación y configuración completa del sistema.
```bash
sudo bash scripts/setup.sh
```

### monitor.sh
Monitoreo en tiempo real del sistema y base de datos.
```bash
bash scripts/monitor.sh
```

### backup.sh
Backup completo de base de datos y archivos.
```bash
sudo bash scripts/backup.sh
```

### restart.sh
Reinicio de todos los servicios.
```bash
sudo bash scripts/restart.sh
```

### status.sh
Estado detallado del sistema.
```bash
bash scripts/status.sh
```

## 🔄 Workflow de Uso

1. **Crear Perfil**: Definir tipo (Corrido/Pausado/Recurrente), velocidades y vigencia
2. **Configurar NAS**: Registrar servidor RADIUS (Mikrotik/OPNsense)
3. **Generar Lote**: Crear múltiples fichas asociadas a perfil y NAS
4. **Gestionar Fichas**: Monitorear estados y uso
5. **Revisar Sesiones**: Consultar tabla radacct para sesiones activas

## 📈 Monitoreo

El sistema incluye monitoreo automático de:
- Estado de servicios (MariaDB, FreeRADIUS, Nginx)
- Conteo de fichas por estado
- Sesiones RADIUS activas
- Autenticaciones recientes
- Uso de recursos del sistema

## 🔒 Seguridad

- Autenticación mediante Filament
- Passwords encriptados
- Validación de sesiones RADIUS
- Control de Simultaneous-Use
- Logs de autenticación

### Configuración de Firewall (Producción)

Para despliegues en VPS, asegúrate de abrir los puertos necesarios:

```bash
# Permitir HTTP y HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Permitir RADIUS (si aplica)
sudo ufw allow 1812/udp  # RADIUS Authentication
sudo ufw allow 1813/udp  # RADIUS Accounting

# Habilitar firewall
sudo ufw enable
```

### Recomendaciones de Seguridad

1. **Cambiar credenciales por defecto** inmediatamente después de la instalación
2. **Configurar SSL/HTTPS** para producción
3. **Usar contraseñas fuertes** para la base de datos
4. **Restringir acceso SSH** solo a IPs conocidas
5. **Mantener el sistema actualizado**: `sudo apt update && sudo apt upgrade`
6. **Configurar backups automáticos** con cron
7. **Revisar logs regularmente**: `sudo tail -f /var/log/nginx/error.log`

## 🐛 Troubleshooting

### Problema: Error "File not found" al acceder a http://IP/admin

Este error indica que Nginx no puede encontrar los archivos del proyecto. Soluciones:

1. **Verificar la ruta en Nginx:**
```bash
sudo cat /etc/nginx/sites-available/interfazfree | grep root
```

2. **Si la ruta es incorrecta, reconfigurar Nginx:**
```bash
sudo bash scripts/configure-nginx.sh
```

3. **Verificar permisos del directorio:**
```bash
# Asegúrate que www-data pueda leer el directorio
sudo chmod -R 755 /ruta/a/tu/proyecto
sudo chown -R www-data:www-data /ruta/a/tu/proyecto/storage /ruta/a/tu/proyecto/bootstrap/cache
```

4. **Verificar que el archivo index.php existe:**
```bash
ls -la /ruta/a/tu/proyecto/public/index.php
```

5. **Revisar logs de Nginx para más detalles:**
```bash
sudo tail -f /var/log/nginx/error.log
```

### Problema: Servicios no inician
```bash
make status      # Verificar estado
make restart     # Reiniciar servicios
```

### Problema: Error de conexión a base de datos
1. Verificar credenciales en `.env`
2. Verificar que MariaDB esté activo: `systemctl status mariadb`
3. Verificar permisos del usuario en MySQL

### Problema: FreeRADIUS no autentica
1. Verificar configuración de NAS
2. Revisar logs: `/var/log/freeradius/radius.log`
3. Verificar atributos en radcheck/radreply

## 📝 Desarrollo

### Crear nueva migración
```bash
php artisan make:migration nombre_de_migracion
```

### Crear nuevo modelo
```bash
php artisan make:model NombreModelo -m
```

### Crear Filament Resource
```bash
php artisan make:filament-resource NombreModelo --generate
```

## 🤝 Contribuciones

Este proyecto fue desarrollado por:
- **Usuario**: Abdias Rivera Bautista
- **GitHub**: @avisonofgod
- **Desarrollo**: Devin AI
- **Sesión**: https://app.devin.ai/sessions/aaa56e7f3731478a827fe26d9a6d45a4

## 📄 Licencia

Este proyecto está bajo la licencia especificada por el propietario del repositorio.

## 📞 Soporte

Para soporte técnico y reportar issues:
- GitHub Issues: https://github.com/avisonofgod/interfazfree/issues
- Documentación: Este README.md

## 🔮 Roadmap

- [ ] Módulo de reportes avanzados
- [ ] Exportación de fichas a PDF
- [ ] API REST para integración externa
- [ ] Dashboard con métricas en tiempo real
- [ ] Notificaciones por email/SMS
- [ ] Integración con sistemas de pago
- [ ] Multi-tenancy

---

**Versión**: 1.0.0  
**Última actualización**: Octubre 2025
