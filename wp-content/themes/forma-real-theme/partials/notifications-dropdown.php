<?php
/**
 * Partial: Notifications Dropdown
 * Dropdown de notificaciones para el header
 */

if (!is_user_logged_in()) return;

// Check if FR_Notification class exists (plugin must be active)
$notifications = [];
$unread_count = 0;

if (class_exists('FR_Notification')) {
    try {
        $notification = new FR_Notification();
        $unread_count = $notification->get_unread_count(get_current_user_id());
        $notifications = $notification->get_unread(get_current_user_id(), 5);
    } catch (Exception $e) {
        // Silently fail
    }
}

// Helper function for time ago if class doesn't exist
if (!function_exists('fr_time_ago')) {
    function fr_time_ago($datetime) {
        if (class_exists('FR_Helpers') && method_exists('FR_Helpers', 'time_ago')) {
            return FR_Helpers::time_ago($datetime);
        }
        return human_time_diff(strtotime($datetime), current_time('timestamp')) . ' ago';
    }
}

// Helper function for notification icon
if (!function_exists('fr_get_notification_icon')) {
    function fr_get_notification_icon($type) {
        if (class_exists('FR_Notification') && method_exists('FR_Notification', 'get_icon')) {
            return FR_Notification::get_icon($type);
        }
        $icons = ['reply' => '💬', 'mention' => '@', 'moderation' => '⚠️', 'system' => 'ℹ️'];
        return $icons[$type] ?? '🔔';
    }
}
?>

<div class="notifications-wrapper" id="notifications-wrapper" style="position: relative;">
    
    <!-- Bell Button -->
    <button 
        type="button"
        onclick="toggleNotifications()"
        class="notifications-btn"
        aria-label="Notificaciones"
        style="
            width: 38px; 
            height: 38px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: transparent; 
            border: 1.5px solid #e2e8f0; 
            color: #94a3b8; 
            transition: all 200ms ease;
            cursor: pointer;
            position: relative;
        "
    >
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        
        <?php if ($unread_count > 0) : ?>
            <span id="notification-count" style="
                position: absolute; 
                top: -4px; 
                right: -4px; 
                min-width: 18px; 
                height: 18px; 
                background: #ef4444; 
                color: #fff; 
                font-size: 0.65rem; 
                font-weight: 700; 
                border-radius: 9999px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                padding: 0 4px; 
                box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
                animation: pulse-badge 2s infinite;
            ">
                <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
            </span>
        <?php endif; ?>
    </button>

    <!-- Dropdown -->
    <div 
        id="notifications-dropdown"
        class="hidden"
        style="
            position: absolute; 
            right: 0; 
            top: calc(100% + 8px); 
            width: 380px; 
            background: #ffffff; 
            border-radius: 18px; 
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.1); 
            border: 1.5px solid #e2e8f0; 
            z-index: 1000; 
            overflow: hidden;
        "
    >
        <!-- Header -->
        <div style="
            padding: 1rem 1.25rem; 
            border-bottom: 1.5px solid #e2e8f0; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: #f1f5f9;
        ">
            <h3 style="
                font-size: 0.85rem; 
                font-weight: 700; 
                color: #1e293b; 
                display: flex; 
                align-items: center; 
                gap: 0.5rem;
                margin: 0;
            ">
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Notificaciones
            </h3>
            <?php if ($unread_count > 0) : ?>
                <button 
                    onclick="markAllAsRead()"
                    style="
                        font-size: 0.72rem; 
                        color: #2563eb; 
                        font-weight: 600; 
                        background: transparent; 
                        border: none; 
                        cursor: pointer; 
                        padding: 0.3rem 0.6rem; 
                        border-radius: 6px; 
                        transition: all 200ms ease;
                    "
                >
                    Marcar todo leído
                </button>
            <?php endif; ?>
        </div>

        <!-- Notifications List -->
        <div style="max-height: 420px; overflow-y: auto;" id="notifications-list">
            <?php if (empty($notifications)) : ?>
                <div style="padding: 3rem 1.5rem; text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5;">🔕</div>
                    <p style="color: #94a3b8; font-size: 0.85rem; margin: 0;">No tienes notificaciones</p>
                </div>
            <?php else : ?>
                <?php foreach ($notifications as $notif) : ?>
                    <a 
                        href="<?php echo esc_url($notif->link); ?>"
                        onclick="markAsRead(<?php echo $notif->id; ?>)"
                        style="
                            display: block; 
                            padding: 1rem 1.25rem; 
                            border-bottom: 1px solid #f1f5f9; 
                            transition: all 200ms ease; 
                            text-decoration: none; 
                            background: #ffffff;
                        "
                    >
                        <div style="display: flex; gap: 0.85rem; align-items: flex-start;">
                            
                            <!-- Icon -->
                            <span style="
                                flex-shrink: 0; 
                                width: 34px; 
                                height: 34px; 
                                border-radius: 10px; 
                                background: rgba(37, 99, 235, 0.07); 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                font-size: 1rem;
                            ">
                                <?php echo fr_get_notification_icon($notif->type); ?>
                            </span>
                            
                            <!-- Content -->
                            <div style="flex-grow: 1; min-width: 0;">
                                <p style="
                                    font-size: 0.82rem; 
                                    color: #1e293b; 
                                    line-height: 1.5; 
                                    margin: 0 0 0.35rem 0;
                                ">
                                    <?php echo esc_html($notif->content); ?>
                                </p>
                                <div style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span style="font-size: 0.7rem; color: #94a3b8;">
                                        <?php echo fr_time_ago($notif->created_at); ?>
                                    </span>
                                    <span style="
                                        width: 6px; 
                                        height: 6px; 
                                        border-radius: 50%; 
                                        background: #2563eb; 
                                        flex-shrink: 0;
                                    "></span>
                                </div>
                            </div>
                            
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div style="
            padding: 0.75rem 1.25rem; 
            border-top: 1.5px solid #e2e8f0; 
            background: #f1f5f9; 
            text-align: center;
        ">
            <a href="<?php echo home_url('/perfil/notificaciones/'); ?>" style="
                font-size: 0.8rem; 
                color: #2563eb; 
                font-weight: 600; 
                text-decoration: none; 
                display: inline-flex; 
                align-items: center; 
                gap: 0.35rem;
            ">
                Ver todas las notificaciones
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<style>
@keyframes pulse-badge {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.notifications-btn:hover {
    border-color: #2563eb !important;
    color: #2563eb !important;
    background: rgba(37, 99, 235, 0.07) !important;
}

#notifications-list a:hover {
    background: #f8fafc !important;
}

.notifications-wrapper button[onclick="markAllAsRead()"]:hover {
    background: rgba(37, 99, 235, 0.07) !important;
}
</style>

<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

function markAsRead(id) {
    if (typeof fr_ajax !== 'undefined') {
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
}

function markAllAsRead() {
    if (typeof fr_ajax !== 'undefined') {
        fetch(fr_ajax.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'fr_mark_all_notifications_read',
                nonce: fr_ajax.nonce
            })
        }).then(() => {
            const badge = document.getElementById('notification-count');
            if (badge) {
                badge.style.transform = 'scale(0)';
                badge.style.transition = 'transform 0.2s ease';
                setTimeout(() => badge.remove(), 200);
            }
            
            document.getElementById('notifications-list').innerHTML = `
                <div style="padding: 3rem 1.5rem; text-align: center;">
                    <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5;">✅</div>
                    <p style="color: #94a3b8; font-size: 0.85rem; margin: 0;">Todo al día</p>
                </div>
            `;
        });
    }
}

// Close dropdown when clicking outside
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
