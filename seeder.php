<?php
/**
 * FORMA REAL - Database Seeder
 * 
 * Este script crea datos de prueba para el foro y configura WordPress
 * para usar la página de inicio personalizada.
 * 
 * INSTRUCCIONES:
 * 1. Coloca este archivo en la raíz de tu instalación WordPress (junto a wp-config.php)
 * 2. Accede a: http://forma-real.test/seeder.php
 * 3. ¡Listo! Tu sitio estará configurado
 */

// Cargar WordPress
require_once __DIR__ . '/wp-load.php';

// Verificar que estamos logueados como admin
if (!current_user_can('manage_options')) {
    die('❌ Error: Debes estar logueado como administrador.');
}

echo "<h1>🚀 Forma Real - Seeder Database</h1>";
echo "<p>Iniciando configuración del sitio...</p>";

global $wpdb;

/**
 * PASO 1: Crear Foros/Categorías
 */
echo "<h2>📂 Paso 1: Creando categorías del foro...</h2>";

$forums = [
    [
        'name' => 'Rutinas de Entrenamiento',
        'slug' => 'rutinas',
        'description' => 'Comparte y discute rutinas de entrenamiento, técnicas y consejos de ejercicio.',
        'icon' => '💪',
        'color' => '#3b82f6',
        'display_order' => 1
    ],
    [
        'name' => 'Nutrición y Dieta',
        'slug' => 'nutricion',
        'description' => 'Todo sobre alimentación saludable, dietas y planes nutricionales.',
        'icon' => '🥗',
        'color' => '#10b981',
        'display_order' => 2
    ],
    [
        'name' => 'Suplementación',
        'slug' => 'suplementos',
        'description' => 'Debate sobre proteínas, vitaminas y otros suplementos deportivos.',
        'icon' => '💊',
        'color' => '#f59e0b',
        'display_order' => 3
    ],
    [
        'name' => 'Motivación y Progreso',
        'slug' => 'motivacion',
        'description' => 'Comparte tu progreso, logros y mantente motivado con la comunidad.',
        'icon' => '🎯',
        'color' => '#ef4444',
        'display_order' => 4
    ]
];

$forums_table = $wpdb->prefix . 'fr_forums';
$created_forums = 0;

foreach ($forums as $forum_data) {
    // Verificar si ya existe
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$forums_table} WHERE slug = %s",
        $forum_data['slug']
    ));
    
    if (!$exists) {
        $wpdb->insert($forums_table, $forum_data);
        echo "✅ Creado: {$forum_data['name']}<br>";
        $created_forums++;
    } else {
        echo "ℹ️ Ya existe: {$forum_data['name']}<br>";
    }
}

echo "<p><strong>Resultado:</strong> {$created_forums} categorías nuevas creadas.</p>";

/**
 * PASO 2: Crear un tema de ejemplo
 */
echo "<h2>📝 Paso 2: Creando tema de ejemplo...</h2>";

// Obtener el primer foro (Rutinas)
$forum_rutinas = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$forums_table} WHERE slug = %s",
    'rutinas'
));

if ($forum_rutinas) {
    $topics_table = $wpdb->prefix . 'fr_topics';
    
    // Verificar si ya existe un tema
    $topic_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$topics_table}");
    
    if ($topic_exists == 0) {
        // Obtener el ID del usuario admin (normalmente es 1)
        $admin_id = 1;
        
        $topic_data = [
            'forum_id' => $forum_rutinas->id,
            'user_id' => $admin_id,
            'title' => '¿Cuál es tu rutina favorita para hipertrofia?',
            'slug' => 'rutina-favorita-hipertrofia',
            'content' => "Hola comunidad,\n\nLlevo 6 meses entrenando y quiero optimizar mi rutina para ganar masa muscular. Actualmente hago:\n\n- Lunes: Pecho y tríceps\n- Martes: Espalda y bíceps\n- Miércoles: Descanso\n- Jueves: Piernas\n- Viernes: Hombros\n- Fin de semana: Descanso\n\n¿Qué rutinas les han funcionado mejor a ustedes? ¿Recomiendan cambiar a una rutina torso-pierna?\n\n¡Gracias!",
            'status' => 'approved',
            'is_sticky' => 0,
            'view_count' => 42,
            'reply_count' => 0,
            'last_active_time' => current_time('mysql')
        ];
        
        $wpdb->insert($topics_table, $topic_data);
        echo "✅ Tema creado: '{$topic_data['title']}'<br>";
        
        // Actualizar contador del foro
        $wpdb->query($wpdb->prepare(
            "UPDATE {$forums_table} SET topic_count = topic_count + 1 WHERE id = %d",
            $forum_rutinas->id
        ));
    } else {
        echo "ℹ️ Ya existen temas en la base de datos.<br>";
    }
}

/**
 * PASO 3: Crear y configurar la página de inicio
 */
echo "<h2>🏠 Paso 3: Configurando página de inicio...</h2>";

// Crear página de inicio si no existe
$home_page = get_page_by_path('inicio');

if (!$home_page) {
    $home_page_id = wp_insert_post([
        'post_title' => 'Inicio',
        'post_name' => 'inicio',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => '<!-- Esta página usa el template home.php del tema -->'
    ]);
    
    if ($home_page_id) {
        // Asignar el template personalizado
        update_post_meta($home_page_id, '_wp_page_template', 'templates/home.php');
        echo "✅ Página de inicio creada (ID: {$home_page_id})<br>";
    }
} else {
    $home_page_id = $home_page->ID;
    update_post_meta($home_page_id, '_wp_page_template', 'templates/home.php');
    echo "ℹ️ Página de inicio ya existe (ID: {$home_page_id})<br>";
}

// Configurar WordPress para usar esta página como portada
update_option('show_on_front', 'page');
update_option('page_on_front', $home_page_id);

echo "✅ WordPress configurado para mostrar la página de inicio<br>";

/**
 * PASO 4: Crear página del Foro
 */
echo "<h2>💬 Paso 4: Creando página del foro...</h2>";

$forum_page = get_page_by_path('foro');

if (!$forum_page) {
    $forum_page_id = wp_insert_post([
        'post_title' => 'Foro',
        'post_name' => 'foro',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => '<!-- Esta página usa el template forum-index.php -->'
    ]);
    
    if ($forum_page_id) {
        update_post_meta($forum_page_id, '_wp_page_template', 'templates/forum-index.php');
        echo "✅ Página de foro creada (ID: {$forum_page_id})<br>";
    }
} else {
    echo "ℹ️ Página de foro ya existe<br>";
}

/**
 * PASO 5: Flush rewrite rules
 */
echo "<h2>🔄 Paso 5: Actualizando permalinks...</h2>";
flush_rewrite_rules();
echo "✅ Permalinks actualizados<br>";

/**
 * RESUMEN FINAL
 */
echo "<hr>";
echo "<h2>✨ ¡Configuración completada!</h2>";
echo "<div style='background: #d1fae5; padding: 20px; border-radius: 8px; border-left: 4px solid #10b981;'>";
echo "<h3>🎉 Tu sitio está listo</h3>";
echo "<ul>";
echo "<li><strong>Página de inicio:</strong> <a href='" . home_url('/') . "' target='_blank'>" . home_url('/') . "</a></li>";
echo "<li><strong>Foro principal:</strong> <a href='" . home_url('/foro/') . "' target='_blank'>" . home_url('/foro/') . "</a></li>";
echo "<li><strong>Categorías creadas:</strong> {$created_forums}</li>";
echo "</ul>";
echo "<p><strong>Próximos pasos:</strong></p>";
echo "<ol>";
echo "<li>Visita la página de inicio para ver el diseño</li>";
echo "<li>Explora el foro y sus categorías</li>";
echo "<li>Prueba crear un tema nuevo</li>";
echo "<li>Personaliza los colores y diseño según tus preferencias</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p style='text-align: center; color: #6b7280; margin-top: 40px;'>";
echo "⚠️ <strong>Importante:</strong> Por seguridad, elimina este archivo (seeder.php) después de usarlo.<br>";
echo "<code>rm " . __FILE__ . "</code>";
echo "</p>";
?>
