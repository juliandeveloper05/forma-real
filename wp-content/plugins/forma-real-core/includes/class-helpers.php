<?php
/**
 * Helpers Class
 * Utilidades generales para el proyecto
 */
class FR_Helpers {
    
    /**
     * Formatear fecha para mostrar "hace X tiempo"
     */
    public static function time_ago($datetime) {
        $time = strtotime($datetime);
        return human_time_diff($time, current_time('timestamp')) . ' atrás';
    }
    
    /**
     * Obtener clase CSS para etiqueta de nivel
     */
    public static function get_level_badge_class($level) {
        switch ($level) {
            case 'advanced': return 'bg-red-100 text-red-800';
            case 'intermediate': return 'bg-yellow-100 text-yellow-800';
            default: return 'bg-green-100 text-green-800';
        }
    }
    
    /**
     * Truncar texto para previews
     */
    public static function excerpt($text, $limit = 100) {
        $text = strip_tags($text);
        if (strlen($text) > $limit) {
            $text = substr($text, 0, $limit) . '...';
        }
        return $text;
    }

    /**
     * Renderizar template parcial
     */
    public static function load_template_part($slug, $name = null, $args = []) {
        // Permitir pasar argumentos al template via query var global temporal
        if (!empty($args)) {
            set_query_var('fr_args', $args);
        }
        
        get_template_part('partials/' . $slug, $name);
        
        // Limpiar
        set_query_var('fr_args', null);
    }
}
