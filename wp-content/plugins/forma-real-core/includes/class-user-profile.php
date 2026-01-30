<?php
/**
 * User Profile Handler
 * Maneja los perfiles extendidos de usuarios
 */
class FR_User_Profile {
    private $db;
    
    public function __construct() {
        $this->db = FR_Database::get_instance();
    }
    
    /**
     * Obtener perfil completo de usuario
     */
    public function get_profile($user_id) {
        if (!$user_id) return false;
        
        // Intentar obtener perfil extendido
        $profile = $this->db->get_row('profiles', ['user_id' => $user_id]);
        
        // Si no existe, crearlo por defecto
        if (!$profile) {
            $this->create_default_profile($user_id);
            $profile = $this->db->get_row('profiles', ['user_id' => $user_id]);
        }
        
        // Combinar con datos de WP_User
        $wp_user = get_userdata($user_id);
        
        if ($wp_user) {
            $profile->display_name = $wp_user->display_name;
            $profile->email = $wp_user->user_email;
            $profile->avatar_url = get_avatar_url($user_id);
            $profile->registered_at = $wp_user->user_registered;
        }
        
        return $profile;
    }
    
    /**
     * Actualizar perfil
     */
    public function update_profile($user_id, $data) {
        $clean_data = [];
        
        // Campos permitidos
        $allowed_fields = ['bio', 'fitness_level', 'fitness_goals'];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $clean_data[$field] = sanitize_textarea_field($data[$field]);
                
                if ($field === 'fitness_level' && !$this->is_valid_level($data[$field])) {
                    continue; 
                }
            }
        }
        
        if (!empty($clean_data)) {
            return $this->db->update('profiles', $clean_data, ['user_id' => $user_id]);
        }
        
        return false;
    }
    
    /**
     * Crear perfil por defecto
     */
    public function create_default_profile($user_id) {
        return $this->db->insert('profiles', [
            'user_id' => $user_id,
            'fitness_level' => 'beginner'
        ]);
    }
    
    private function is_valid_level($level) {
        return in_array($level, ['beginner', 'intermediate', 'advanced']);
    }
}
