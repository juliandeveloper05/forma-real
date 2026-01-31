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
        class="relative p-2 text-gray-600 hover:text-primary transition-colors"
        aria-label="Notificaciones"
    >
        🔔
        <?php if ($unread_count > 0) : ?>
            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center" id="notification-count">
                <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
            </span>
        <?php endif; ?>
    </button>

    <!-- Dropdown -->
    <div 
        id="notifications-dropdown"
        class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900">Notificaciones</h3>
            <?php if ($unread_count > 0) : ?>
                <button 
                    onclick="markAllAsRead()"
                    class="text-xs text-primary hover:underline"
                >
                    Marcar todo como leído
                </button>
            <?php endif; ?>
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 overflow-y-auto" id="notifications-list">
            <?php if (empty($notifications)) : ?>
                <div class="px-4 py-8 text-center text-gray-500">
                    <div class="text-3xl mb-2">🔕</div>
                    No tienes notificaciones
                </div>
            <?php else : ?>
                <?php foreach ($notifications as $notif) : ?>
                    <a 
                        href="<?php echo esc_url($notif->link); ?>"
                        class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0"
                        onclick="markAsRead(<?php echo $notif->id; ?>)"
                    >
                        <div class="flex gap-3">
                            <span class="text-xl shrink-0"><?php echo FR_Notification::get_icon($notif->type); ?></span>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-900 line-clamp-2"><?php echo esc_html($notif->content); ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?php echo FR_Helpers::time_ago($notif->created_at); ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="px-4 py-2 border-t border-gray-100">
            <a href="<?php echo home_url('/perfil/notificaciones/'); ?>" class="text-sm text-primary hover:underline">
                Ver todas →
            </a>
        </div>
    </div>
</div>

<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    dropdown.classList.toggle('hidden');
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
        document.getElementById('notification-count')?.remove();
        document.getElementById('notifications-list').innerHTML = `
            <div class="px-4 py-8 text-center text-gray-500">
                <div class="text-3xl mb-2">🔕</div>
                No tienes notificaciones
            </div>
        `;
    });
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notifications-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('notifications-dropdown')?.classList.add('hidden');
    }
});
</script>
