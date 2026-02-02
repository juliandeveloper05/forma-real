<?php
/**
 * Partial: Notifications Dropdown
 * Dropdown de notificaciones para el header
 */

if (!is_user_logged_in()) return;

$notification = new FR_Notification();
$unread_count = $notification->get_unread_count(get_current_user_id());
$notifications = $notification->get_unread(get_current_user_id(), 5);
?>

<div class="relative" id="notifications-wrapper">
    
    <!-- Bell Button -->
    <button 
        type="button"
        onclick="toggleNotifications()"
        class="relative"
        style="width: 36px; height: 36px; border-radius: var(--r-md); display: flex; align-items: center; justify-content: center; background: transparent; border: 1.5px solid var(--color-border); color: var(--color-text-muted); transition: all var(--ease); cursor: pointer;"
        aria-label="Notificaciones"
        onmouseover="this.style.borderColor='var(--color-primary)'; this.style.color='var(--color-primary)'; this.style.background='var(--color-primary-subtle)';"
        onmouseout="this.style.borderColor='var(--color-border)'; this.style.color='var(--color-text-muted)'; this.style.background='transparent';"
    >
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        
        <?php if ($unread_count > 0) : ?>
            <span id="notification-count" style="position: absolute; top: -4px; right: -4px; min-width: 18px; height: 18px; background: var(--color-danger); color: #fff; font-size: 0.65rem; font-weight: 700; border-radius: var(--r-full); display: flex; align-items: center; justify-content: center; padding: 0 4px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4); animation: pulse-badge 2s infinite;">
                <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
            </span>
        <?php endif; ?>
    </button>

    <!-- Dropdown -->
    <div 
        id="notifications-dropdown"
        class="hidden"
        style="position: absolute; right: 0; top: calc(100% + 8px); width: 380px; background: var(--color-card); border-radius: var(--r-xl); box-shadow: var(--shadow-lg); border: 1.5px solid var(--color-border); z-index: 1000; overflow: hidden; animation: slideDown 0.2s ease;"
    >
        <!-- Header -->
        <div style="padding: 1rem 1.25rem; border-bottom: 1.5px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; background: var(--color-border-light);">
            <h3 style="font-size: 0.85rem; font-weight: 700; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem;">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Notificaciones
            </h3>
            <?php if ($unread_count > 0) : ?>
                <button 
                    onclick="markAllAsRead()"
                    style="font-size: 0.72rem; color: var(--color-primary); font-weight: 600; background: transparent; border: none; cursor: pointer; padding: 0.3rem 0.6rem; border-radius: var(--r-sm); transition: all var(--ease);"
                    onmouseover="this.style.background='var(--color-primary-subtle)';"
                    onmouseout="this.style.background='transparent';"
                >
                    Marcar todo leído
                </button>
            <?php endif; ?>
        </div>

        <!-- Notifications List -->
        <div style="max-height: 420px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--color-border) transparent;" id="notifications-list">
            <?php if (empty($notifications)) : ?>
                <div style="padding: 3rem 1.5rem; text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5;">🔕</div>
                    <p style="color: var(--color-text-muted); font-size: 0.85rem;">No tienes notificaciones</p>
                </div>
            <?php else : ?>
                <?php foreach ($notifications as $notif) : ?>
                    <a 
                        href="<?php echo esc_url($notif->link); ?>"
                        onclick="markAsRead(<?php echo $notif->id; ?>)"
                        style="display: block; padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-border-light); transition: all var(--ease); text-decoration: none; background: var(--color-card);"
                        onmouseover="this.style.background='var(--color-bg)';"
                        onmouseout="this.style.background='var(--color-card)';"
                    >
                        <div style="display: flex; gap: 0.85rem; align-items: flex-start;">
                            
                            <!-- Icon -->
                            <span style="flex-shrink: 0; width: 34px; height: 34px; border-radius: var(--r-md); background: var(--color-primary-subtle); display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <?php echo FR_Notification::get_icon($notif->type); ?>
                            </span>
                            
                            <!-- Content -->
                            <div style="flex-grow: 1; min-width: 0;">
                                <p style="font-size: 0.82rem; color: var(--color-text); line-height: 1.5; margin: 0 0 0.35rem 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo esc_html($notif->content); ?>
                                </p>
                                <div style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span style="font-size: 0.7rem; color: var(--color-text-muted);">
                                        <?php echo FR_Helpers::time_ago($notif->created_at); ?>
                                    </span>
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--color-primary); flex-shrink: 0;"></span>
                                </div>
                            </div>
                            
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div style="padding: 0.75rem 1.25rem; border-top: 1.5px solid var(--color-border); background: var(--color-border-light); text-align: center;">
            <a href="<?php echo home_url('/perfil/notificaciones/'); ?>" style="font-size: 0.8rem; color: var(--color-primary); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; transition: gap var(--ease);">
                Ver todas las notificaciones
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse-badge {
    0%, 100% { 
        transform: scale(1);
    }
    50% { 
        transform: scale(1.1);
    }
}

/* Custom scrollbar for notifications */
#notifications-list::-webkit-scrollbar {
    width: 6px;
}

#notifications-list::-webkit-scrollbar-track {
    background: transparent;
}

#notifications-list::-webkit-scrollbar-thumb {
    background: var(--color-border);
    border-radius: 3px;
}

#notifications-list::-webkit-scrollbar-thumb:hover {
    background: var(--color-text-muted);
}
</style>

<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    const isHidden = dropdown.classList.contains('hidden');
    
    if (isHidden) {
        dropdown.classList.remove('hidden');
    } else {
        dropdown.classList.add('hidden');
    }
}

function markAsRead(id) {
    fetch(fr_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'fr_mark_notification_read',
            nonce: fr_ajax.nonce,
            notification_id: id
        })
    });
}

function markAllAsRead() {
    fetch(fr_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'fr_mark_all_notifications_read',
            nonce: fr_ajax.nonce
        })
    }).then(() => {
        // Remove badge
        const badge = document.getElementById('notification-count');
        if (badge) {
            badge.style.transform = 'scale(0)';
            badge.style.transition = 'transform 0.2s ease';
            setTimeout(() => badge.remove(), 200);
        }
        
        // Update list
        document.getElementById('notifications-list').innerHTML = `
            <div style="padding: 3rem 1.5rem; text-align: center;">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5;">✅</div>
                <p style="color: var(--color-text-muted); font-size: 0.85rem;">Todo al día</p>
            </div>
        `;
    });
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notifications-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        const dropdown = document.getElementById('notifications-dropdown');
        if (dropdown) {
            dropdown.classList.add('hidden');
        }
    }
});
</script>
