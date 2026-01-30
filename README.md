# Forma Real — Proyecto Portfolio PHP/WordPress

Bienvenido al repositorio de **Forma Real**, un proyecto de portfolio diseñado para demostrar habilidades en desarrollo web utilizando tecnologías clave del ecosistema PHP, orientado a la creación de una comunidad de fitness.

## 🎯 Objetivo
Crear un sistema de foros y comunidad de fitness desde cero, demostrando:
- Dominio de **PHP puro y OOP** (Programación Orientada a Objetos).
- Integración profunda con **WordPress** como framework.
- Arquitectura sólida de **Base de Datos MySQL**.
- Código limpio, documentado y listo para portfolio.

## 🛠️ Stack Tecnológico
- **Entorno:** Laragon (recomendado para desarrollo local en Windows).
- **Backend:** PHP 8.1+ (Uso estricto de Clases y Objetos).
- **Base de Datos:** MySQL 8.0 (Gestionado con HeidiSQL).
- **CMS:** WordPress 6.4+ (Utilizado como motor base y sistema de usuarios).
- **Frontend:** HTML5, CSS3, JavaScript Vanilla.

## 🗂 Estructura del Proyecto
El proyecto se encuentra en la carpeta `forma-real/`.
```
forma-real/
├── database/            # Scripts SQL (Schema)
├── docs/                # Documentación
├── wp-content/
│   ├── plugins/
│   │   └── forma-real-core/  # Lógica principal del negocio (OOP)
│   └── themes/
│       └── forma-real-theme/ # Diseño y presentación
```

## 🚀 Instalación
1. Copia el contenido de `forma-real/wp-content` a tu instalación local de WordPress.
2. Activa el plugin **Forma Real Core**.
3. (Opcional) Importa `database/schema.sql` usando HeidiSQL si deseas visualizar la estructura, aunque el plugin crea las tablas automáticamente al activarse.

## 📈 Roadmap
Consulta `task.md` para ver el progreso del desarrollo paso a paso.
