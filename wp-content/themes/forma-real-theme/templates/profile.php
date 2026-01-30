<?php
/**
 * Template Name: User Profile
 * Perfil público del usuario
 */

get_header();

$username = get_query_var('username');
$user = get_user_by('slug', $username);

if (!$user) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
}

$profile_handler = new FR_User_Profile();
$profile = $profile_handler->get_profile($user->ID);

// Obtener estadísticas
$topic_count = count_user_posts($user->ID, 'fr_topics'); // Nota: esto requiere custom post type hook o query manual
// Usaremos query manual por simplicidad
global $wpdb;
$topic_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}fr_topics WHERE user_id = %d AND status = 'approved'", $user->ID));
$reply_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}fr_replies WHERE user_id = %d AND status = 'approved'", $user->ID));

$is_own_profile = is_user_logged_in() && get_current_user_id() == $user->ID;
?>

<div class="container py-8">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Sidebar Perfil -->
        <div class="md:col-span-1">
            <div class="card text-center">
                <div class="card-body">
                    <div class="mb-4 flex justify-center">
                        <?php echo get_avatar($user->ID, 150, '', '', ['class' => 'rounded-full border-4 border-gray-100 shadow-sm']); ?>
                    </div>
                    
                    <h1 class="text-2xl font-bold text-gray-900 mb-1"><?php echo esc_html($profile->display_name); ?></h1>
                    <p class="text-gray-500 text-sm mb-4">@<?php echo esc_html($user->user_login); ?></p>
                    
                    <div class="inline-block mb-6">
                        <span class="badge <?php echo FR_Helpers::get_level_badge_class($profile->fitness_level); ?> text-sm px-3 py-1">
                            <?php echo ucfirst($profile->fitness_level); ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4 text-sm">
                        <div>
                            <span class="block font-bold text-gray-900 text-lg"><?php echo number_format($topic_count); ?></span>
                            <span class="text-gray-500">Temas</span>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-900 text-lg"><?php echo number_format($reply_count); ?></span>
                            <span class="text-gray-500">Respuestas</span>
                        </div>
                    </div>
                    
                    <?php if ($is_own_profile) : ?>
                        <div class="mt-6">
                            <button onclick="document.getElementById('edit-profile-modal').showModal()" class="btn btn-outline w-full text-sm">
                                📝 Editar Perfil
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="font-bold text-gray-900">Bio</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($profile->bio)) : ?>
                        <p class="text-gray-600 text-sm leading-relaxed"><?php echo nl2br(esc_html($profile->bio)); ?></p>
                    <?php else : ?>
                        <p class="text-gray-400 italic text-sm">Sin descripción.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Metas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-gray-900">Objetivos Fitness</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($profile->fitness_goals)) : ?>
                        <p class="text-gray-700"><?php echo nl2br(esc_html($profile->fitness_goals)); ?></p>
                    <?php else : ?>
                        <p class="text-gray-400 italic">No ha definido objetivos aún.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Última Actividad (Placeholder) -->
            <div class="card">
                <div class="card-header">
                    <h3 class="font-bold text-gray-900">Actividad Reciente</h3>
                </div>
                <div class="card-body">
                    <p class="text-gray-500 text-sm">Próximamente: Historial de posts.</p>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Modal Editar (Solo HTML básico por ahora) -->
<?php if ($is_own_profile) : ?>
<dialog id="edit-profile-modal" class="p-0 rounded-lg shadow-xl backdrop:bg-gray-900/50 w-full max-w-lg">
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">Editar Perfil</h2>
        <form method="post" action="">
            <!-- Simulación de formulario, implementar handler PHP luego -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nivel</label>
                    <select name="fitness_level" class="w-full border rounded p-2">
                        <option value="beginner" <?php selected($profile->fitness_level, 'beginner'); ?>>Principiante</option>
                        <option value="intermediate" <?php selected($profile->fitness_level, 'intermediate'); ?>>Intermedio</option>
                        <option value="advanced" <?php selected($profile->fitness_level, 'advanced'); ?>>Avanzado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Bio</label>
                    <textarea name="bio" rows="3" class="w-full border rounded p-2"><?php echo esc_textarea($profile->bio); ?></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('edit-profile-modal').close()" class="btn btn-outline">Cancelar</button>
                    <button type="submit" class="btn btn-primary" disabled>Guardar (WIP)</button>
                </div>
            </div>
        </form>
    </div>
</dialog>
<?php endif; ?>

<?php get_footer(); ?>
