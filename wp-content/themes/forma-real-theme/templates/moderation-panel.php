<?php
/**
 * Template Name: Moderation Panel
 * Panel de moderación (solo administradores)
 */

get_header();

// Verificar permisos
if (!current_user_can('moderate_comments')) {
    wp_redirect(home_url('/'));
    exit;
}

$moderation = new FR_Moderation();
$pending_reports = $moderation->get_pending_reports();
$reasons = FR_Moderation::get_report_reasons();
?>

<div class="container py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Panel de Moderación</h1>
            <p class="text-gray-600">Gestiona los reportes de la comunidad</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                <?php echo $moderation->count_pending(); ?> pendientes
            </span>
        </div>
    </div>

    <?php if (empty($pending_reports)) : ?>
        <!-- No Reports -->
        <div class="card p-12 text-center">
            <div class="text-6xl mb-4">✅</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">¡Todo limpio!</h3>
            <p class="text-gray-500">No hay reportes pendientes por revisar.</p>
        </div>
    <?php else : ?>
        <!-- Reports List -->
        <div class="space-y-4">
            <?php foreach ($pending_reports as $report) : ?>
                <div class="card p-6" id="report-<?php echo $report->id; ?>">
                    <div class="flex gap-6">
                        <!-- Report Info -->
                        <div class="flex-grow">
                            <!-- Header -->
                            <div class="flex items-center gap-3 mb-4">
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                                    <?php echo $report->content_type === 'topic' ? 'Tema' : 'Respuesta'; ?>
                                </span>
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                    <?php echo esc_html($reasons[$report->reason] ?? $report->reason); ?>
                                </span>
                                <span class="text-sm text-gray-500">
                                    <?php echo FR_Helpers::time_ago($report->created_at); ?>
                                </span>
                            </div>

                            <!-- Content Preview -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <?php if ($report->content_type === 'topic') : ?>
                                    <h4 class="font-semibold mb-2"><?php echo esc_html($report->content->title ?? 'Contenido eliminado'); ?></h4>
                                <?php endif; ?>
                                <p class="text-gray-700 text-sm">
                                    <?php echo wp_trim_words(strip_tags($report->content->content ?? ''), 50); ?>
                                </p>
                            </div>

                            <!-- Report Details -->
                            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                <div>
                                    <span class="text-gray-500">Reportado por:</span>
                                    <span class="font-medium"><?php echo esc_html($report->reporter_name); ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Usuario reportado:</span>
                                    <span class="font-medium"><?php echo esc_html($report->reported_name); ?></span>
                                </div>
                            </div>

                            <?php if ($report->description) : ?>
                                <div class="text-sm text-gray-600 italic">
                                    "<?php echo esc_html($report->description); ?>"
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Actions -->
                        <div class="shrink-0 flex flex-col gap-2">
                            <button 
                                onclick="reviewReport(<?php echo $report->id; ?>, 'dismiss')"
                                class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                            >
                                Ignorar
                            </button>
                            <button 
                                onclick="reviewReport(<?php echo $report->id; ?>, 'warn')"
                                class="px-4 py-2 text-sm bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200"
                            >
                                Advertir
                            </button>
                            <button 
                                onclick="reviewReport(<?php echo $report->id; ?>, 'delete')"
                                class="px-4 py-2 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                            >
                                Eliminar
                            </button>
                            <button 
                                onclick="reviewReport(<?php echo $report->id; ?>, 'ban')"
                                class="px-4 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                            >
                                Banear
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function reviewReport(reportId, actionType) {
    if (actionType === 'ban' && !confirm('¿Estás seguro de banear a este usuario?')) {
        return;
    }

    fetch(fr_ajax.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'fr_review_report',
            nonce: fr_ajax.nonce,
            report_id: reportId,
            action_type: actionType
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('report-' + reportId).remove();
            // Actualizar contador si hay
            const counter = document.querySelector('.bg-yellow-100');
            if (counter) {
                const current = parseInt(counter.textContent);
                counter.textContent = (current - 1) + ' pendientes';
            }
        } else {
            alert(data.data.message);
        }
    });
}
</script>

<?php get_footer(); ?>
