# 🚀 Instrucciones de Configuración - Forma Real

## 📋 Problema Actual
Cuando visitas `http://forma-real.test`, ves el blog de WordPress con "Hello World!" en lugar de tu página de inicio personalizada.

## 🔧 Solución (3 minutos)

### Paso 1: Copiar el archivo seeder.php
Copia `seeder.php` a la raíz de tu instalación WordPress:
```
C:\laragon\www\forma-real\seeder.php
```
> **IMPORTANTE:** Debe estar en la misma carpeta donde está `wp-config.php`

### Paso 2: Ejecutar el seeder
1. Abre tu navegador
2. Ve a: `http://forma-real.test/seeder.php`
3. Espera a que aparezca el mensaje de éxito ✅

### Paso 3: Verificar que funciona
Visita: `http://forma-real.test`

Deberías ver tu página de inicio con:
- Título grande: "Fitness Real, Resultados Reales"
- Sección de actividad reciente
- Diseño moderno y limpio

## 🎯 Enlaces importantes

| Página | URL |
|--------|-----|
| Inicio | http://forma-real.test |
| Foro principal | http://forma-real.test/foro |
| Categoría Rutinas | http://forma-real.test/foro/rutinas |
| Categoría Nutrición | http://forma-real.test/foro/nutricion |

## 🔒 Seguridad
Una vez que el seeder funcione, elimina el archivo:
```bash
del C:\laragon\www\forma-real\seeder.php
```

## ❓ Solución de problemas

### El seeder muestra "Error: Debes estar logueado como administrador"
1. Ve a: `http://forma-real.test/wp-admin`
2. Inicia sesión con tu usuario administrador
3. Vuelve a ejecutar: `http://forma-real.test/seeder.php`

### Sigo viendo "Hello World!" después del seeder
1. WordPress Admin → Ajustes → Lectura
2. Seleccionar "Una página estática"
3. Página de inicio: "Inicio"
4. Guardar cambios

### Las URLs del foro dan error 404
1. WordPress Admin → Ajustes → Enlaces permanentes
2. Clic en "Guardar cambios" (sin cambiar nada)
