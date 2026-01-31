<?php
/**
 * Template: User Profile
 */
get_header();

$username = get_query_var('username');
$user     = get_user_by('slug', $username);

if (!$user) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part('404');
    exit();
}

$profile_handler = new FR_User_Profile();
$profile         = $profile_handler->get_profile($user->ID);

global $wpdb;
$topic_count = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}fr_topics WHERE user_id = %d AND status = 'approved'", $user->ID
));
$reply_count = (int) $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}fr_replies WHERE user_id = %d AND status = 'approved'", $user->ID
));

$is_own = is_user_logged_in() && get_current_user_id() === $user->ID;
?>

<div class="container" style="padding-top: 2.5rem; padding-bottom: 4rem;">
    <div style="display:grid; grid-template-columns: 1fr; gap: 1.5rem; max-width: 920px; margin: 0 auto;">

        <!-- ─── Left: Profile Card ─── -->
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">

            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-avatar-wrap">
                    <div class="av-xl"><?php echo get_avatar($user->ID, 100, '', '', ['echo'=>false]); ?></div>
                </div>
                <div class="profile-info-block">
                    <div class="p-name"><?php echo esc_html($profile->display_name); ?></div>
                    <div class="p-user">@<?php echo esc_html($user->user_login); ?></div>
                    <span class="badge <?php echo FR_Helpers::get_level_badge_class($profile->fitness_level); ?>">
                        <?php echo ucfirst($profile->fitness_level); ?>
                    </span>
                </div>
                <div class="profile-stats-row">
                    <div class="p-stat">
                        <div class="ps-val"><?php echo number_format($topic_count); ?></div>
                        <div class="ps-lbl">Temas</div>
                    </div>
                    <div class="p-stat">
                        <div class="ps-val"><?php echo number_format($reply_count); ?></div>
                        <div class="ps-lbl">Respuestas</div>
                    </div>
                </div>
                <?php if ($is_own) : ?>
                <div class="profile-actions">
                    <button onclick="document.getElementById('edit-profile-modal').showDialog()" class="btn btn-outline btn-sm w-full">📝 Editar Perfil</button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Bio Card -->
            <div>
                <div class="card" style="border-radius: var(--r-lg); border: 1px solid var(--color-border); background: var(--color-card); overflow: hidden;">
                    <div style="padding: 0.8rem 1rem; background: var(--color-border-light); border-bottom: 1px solid var(--color-border);">
                        <h4 style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--color-text-2);">Bio</h4>
                    </div>
                    <div style="padding: 1rem;">
                        <?php if (!empty($profile->bio)) : ?>
                            <p style="font-size: 0.82rem; color: var(--color-text-2); line-height: 1.7;"><?php echo nl2br(esc_html($profile->bio)); ?></p>
                        <?php else : ?>
                            <p style="font-size: 0.8rem; color: var(--color-text-muted); font-style: italic;">Sin descripción.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Goals Card -->
                <div class="card" style="border-radius: var(--r-lg); border: 1px solid var(--color-border); background: var(--color-card); overflow: hidden; margin-top: 1rem;">
                    <div style="padding: 0.8rem 1rem; background: var(--color-border-light); border-bottom: 1px solid var(--color-border);">
                        <h4 style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--color-text-2);">Objetivos Fitness</h4>
                    </div>
                    <div style="padding: 1rem;">
                        <?php if (!empty($profile->fitness_goals)) : ?>
                            <p style="font-size: 0.82rem; color: var(--color-text-2); line-height: 1.7;"><?php echo nl2br(esc_html($profile->fitness_goals)); ?></p>
                        <?php else : ?>
                            <p style="font-size: 0.8rem; color: var(--color-text-muted); font-style: italic;">No ha definido objetivos aún.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Bottom: Activity ─── -->
        <div class="card" style="border-radius: var(--r-lg); border: 1px solid var(--color-border); background: var(--color-card); overflow: hidden;">
            <div style="padding: 0.8rem 1rem; background: var(--color-border-light); border-bottom: 1px solid var(--color-border);">
                <h4 style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--color-text-2);">Actividad Reciente</h4>
            </div>
            <div style="padding: 1.25rem 1rem;">
                <p style="font-size: 0.82rem; color: var(--color-text-muted); text-align: center; padding: 1rem 0;">Próximamente: historial de posts y participaciones.</p>
            </div>
        </div>

    </div>
</div>

<!-- ═══ Modal: Editar Perfil ═══ -->
<?php if ($is_own) : ?>
<dialog id="edit-profile-modal">
    <div class="modal-head">
        <h2>Editar Perfil</h2>
        <button class="modal-close" onclick="document.getElementById('edit-profile-modal').close()">&times;</button>
    </div>
    <div class="modal-body">
        <form method="post">
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Nivel</label>
                <select name="fitness_level" class="form-select">
                    <option value="beginner"     <?php selected($profile->fitness_level, 'beginner'); ?>>Principiante</option>
                    <option value="intermediate" <?php selected($profile->fitness_level, 'intermediate'); ?>>Intermedio</option>
                    <option value="advanced"     <?php selected($profile->fitness_level, 'advanced'); ?>>Avanzado</option>
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label">Bio</label>
                <textarea name="bio" rows="3" class="form-textarea"><?php echo esc_textarea($profile->bio); ?></textarea>
            </div>
            <div style="margin-bottom: 0.5rem;">
                <label class="form-label">Objetivos</label>
                <textarea name="fitness_goals" rows="2" class="form-textarea"><?php echo esc_textarea($profile->fitness_goals); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="button" onclick="document.getElementById('edit-profile-modal').close()" class="btn btn-outline btn-sm">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm" disabled>Guardar (WIP)</button>
            </div>
        </form>
    </div>
</dialog>
<?php endif; ?>

<?php get_footer(); ?>
