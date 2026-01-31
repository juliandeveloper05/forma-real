# 🏋️ Forma Real — Foro de Fitness con PHP/WordPress

[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.4+-21759B?style=flat-square&logo=wordpress&logoColor=white)](https://wordpress.org)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)

> Comunidad de fitness donde la experiencia supera a la teoría. Comparte rutinas, resuelve dudas y documenta tu progreso.

![Forma Real Preview](https://via.placeholder.com/800x400/0f172a/3b82f6?text=Forma+Real+-+Fitness+Forum)

## ✨ Características

### Core Features
- 🏠 **Landing Page** moderna con diseño responsive
- 💬 **Sistema de Foros** con categorías, temas y respuestas
- 👤 **Perfiles de Usuario** con estadísticas y niveles
- ⚡ **AJAX** para interacciones sin recargar la página
- 📱 **Mobile-First** - Funciona perfecto en cualquier dispositivo
- 🔐 **Seguridad** con nonces de WordPress y sanitización

### Semana 5: Features Avanzadas ✨
- 🔍 **Búsqueda FULLTEXT** - Busca en títulos y contenido de temas/respuestas
- 🛡️ **Panel de Moderación** - Gestión de reportes (Ignorar, Advertir, Eliminar, Banear)
- 🔔 **Notificaciones en Tiempo Real** - Alertas cuando responden a tus temas
- 📊 **Sistema de Reportes** - Los usuarios pueden reportar contenido inapropiado

## 🛠️ Stack Tecnológico

| Categoría | Tecnología |
|-----------|------------|
| **Backend** | PHP 8.1+ (OOP) |
| **CMS** | WordPress 6.4+ |
| **Base de Datos** | MySQL 8.0 |
| **Frontend** | HTML5, CSS3, JavaScript |
| **Entorno Local** | Laragon |

## 📁 Estructura del Proyecto

```
forma-real/
├── 📂 database/
│   └── schema.sql           # Esquema de base de datos
├── 📂 wp-content/
│   ├── 📂 plugins/
│   │   └── forma-real-core/ # Plugin con lógica OOP
│   │       ├── includes/
│   │       │   ├── class-database.php
│   │       │   ├── class-forum.php
│   │       │   ├── class-topic.php
│   │       │   ├── class-reply.php
│   │       │   ├── class-user-profile.php
│   │       │   ├── class-helpers.php
│   │       │   ├── class-ajax-handler.php
│   │       │   ├── class-search.php        # 🔍 Búsqueda FULLTEXT
│   │       │   ├── class-moderation.php    # 🛡️ Sistema de moderación
│   │       │   └── class-notification.php  # 🔔 Notificaciones
│   │       └── forma-real-core.php
│   └── 📂 themes/
│       └── forma-real-theme/ # Tema responsive
│           ├── templates/
│           │   ├── home.php
│           │   ├── forum-index.php
│           │   ├── forum-category.php
│           │   ├── topic-single.php
│           │   ├── profile.php
│           │   ├── search-results.php      # 🔍 Resultados de búsqueda
│           │   └── moderation-panel.php    # 🛡️ Panel de moderación
│           ├── partials/
│           │   └── notifications-dropdown.php  # 🔔 Dropdown de notificaciones
│           ├── assets/
│           │   ├── css/responsive.css
│           │   └── js/main.js
│           ├── header.php
│           ├── footer.php
│           └── functions.php
├── seeder.php               # Script de configuración automática
├── INSTRUCCIONES.md         # Guía de instalación
└── README.md
```

## 🚀 Instalación Rápida

### Requisitos
- PHP 8.1+
- MySQL 8.0+
- WordPress 6.4+
- Laragon (recomendado para Windows)

### Pasos

1. **Clona el repositorio**
   ```bash
   git clone https://github.com/juliandeveloper05/forma-real.git
   ```

2. **Copia los archivos a WordPress**
   ```
   wp-content/plugins/forma-real-core → tu-wordpress/wp-content/plugins/
   wp-content/themes/forma-real-theme → tu-wordpress/wp-content/themes/
   ```

3. **Activa en WordPress Admin**
   - Plugins → Activar "Forma Real Core"
   - Apariencia → Temas → Activar "Forma Real Theme"

4. **Ejecuta el Seeder**
   - Copia `seeder.php` a la raíz de WordPress
   - Visita: `http://tu-sitio/seeder.php`
   - ¡Listo! 🎉

> 📖 Para instrucciones detalladas, consulta [INSTRUCCIONES.md](INSTRUCCIONES.md)

## 🎯 Demo

| Página | URL Local |
|--------|-----------|
| Inicio | `http://forma-real.test/` |
| Foro | `http://forma-real.test/foro/` |
| Categoría | `http://forma-real.test/foro/rutinas/` |
| Tema | `http://forma-real.test/foro/rutinas/rutina-favorita-hipertrofia` |
| 🔍 Búsqueda | `http://forma-real.test/buscar/?q=rutina` |
| 🛡️ Moderación | `http://forma-real.test/moderacion/` |

## 📊 Progreso del Proyecto

- [x] **Semana 1:** Setup y Base de Datos
- [x] **Semana 2:** Clases PHP (OOP)
- [x] **Semana 3:** Theme y Frontend
- [x] **Semana 4:** CSS y JavaScript
- [x] **Semana 5:** Búsqueda, Moderación, Notificaciones ✅
- [ ] **Semana 6:** Documentación y Deploy

## 🔌 API AJAX Endpoints

| Endpoint | Descripción |
|----------|-------------|
| `fr_create_topic` | Crear nuevo tema |
| `fr_create_reply` | Publicar respuesta |
| `fr_search` | Búsqueda FULLTEXT (pública) |
| `fr_report_content` | Reportar tema/respuesta |
| `fr_get_notifications` | Obtener notificaciones |
| `fr_mark_notification_read` | Marcar como leída |
| `fr_mark_all_notifications_read` | Marcar todas como leídas |
| `fr_review_report` | Acción de moderador |

## 🧪 Tecnologías Demostradas

Este proyecto demuestra competencia en:

- **PHP OOP:** Clases, Singleton, encapsulación
- **WordPress:** Hooks, AJAX, custom routing, templates
- **MySQL:** Diseño de esquemas, relaciones, índices FULLTEXT
- **Frontend:** CSS responsive, JavaScript moderno, fetch API
- **Moderación:** Sistema de reportes y gestión de usuarios
- **Notificaciones:** Sistema real-time con AJAX polling
- **Git:** Control de versiones, commits semánticos

## 📝 Licencia

MIT License - Siéntete libre de usar este código para aprender o como base para tus proyectos.

---

<p align="center">
  Desarrollado con 💪 por <a href="https://github.com/juliandeveloper05">Julian</a>
</p>
