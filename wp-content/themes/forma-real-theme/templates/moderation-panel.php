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
$pending_count = $moderation->count_pending();
?>

<div class="container" style="padding-top: 2.5rem; padding-bottom: 4rem;">

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; gap: 1.5rem; flex-wrap: wrap;">
        <div>
            <h1 style="font-size: clamp(1.8rem, 4vw, 2.4rem); margin-bottom: 0.5rem;">
                🛡️ Panel de Moderación
            </h1>
            <p style="color: var(--color-text-2); font-size: 0.95rem;">
                Gestiona los reportes de la comunidad y mantén un ambiente saludable
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <?php if ($pending_count > 0) : ?>
                <div style="padding: 0.6rem 1.2rem; background: var(--color-warning-bg); border: 1.5px solid var(--color-warning); border-radius: var(--r-lg); display: flex; align-items: center; gap: 0.6rem;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--color-warning); animation: pulse-dot 2s infinite;"></span>
                    <span style="font-weight: 700; color: var(--color-warning-text); font-size: 0.9rem;">
                        <?php echo $pending_count; ?> pendiente<?php echo $pending_count > 1 ? 's' : ''; ?>
                    </span>
                </div>
            <?php endif; ?>
            <a href="<?php echo home_url('/foro/'); ?>" class="btn btn-outline btn-sm">
                ← Volver al Foro
            </a>
        </div>
    </div>

    <?php if (empty($pending_reports)) : ?>
        
        <!-- No Reports - Success State -->
        <div style="background: linear-gradient(135deg, var(--color-success-bg) 0%, transparent 100%); border-radius: var(--r-2xl); border: 2px solid var(--color-success); padding: 4rem 2rem; text-align: center;">
            <div style="font-size: 4rem; margin-bottom: 1.25rem; animation: fadeUp 0.5s ease;">✅</div>
            <h3 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--color-success-text);">
                ¡Todo limpio!
            </h3>
            <p style="color: var(--color-text-2); max-width: 420px; margin: 0 auto 1.5rem;">
                No hay reportes pendientes por revisar. La comunidad está en buen estado.
            </p>
            <a href="<?php echo home_url('/foro/'); ?>" class="btn btn-primary">
                Explorar el Foro
            </a>
        </div>
        
    <?php else : ?>
        
        <!-- Reports List -->
        <div class="space-y-4">
            <?php foreach ($pending_reports as $idx => $report) : ?>
                <div class="card anim-up" id="report-<?php echo $report->id; ?>" style="padding: 0; border-radius: var(--r-xl); overflow: hidden; animation-delay: <?php echo $idx * 0.08; ?>s;">
                    
                    <div style="display: grid; grid-template-columns: 1fr auto; gap: 1.5rem; padding: 1.75rem;">
                        
                        <!-- Report Info -->
                        <div>
                            
                            <!-- Header with Badges -->
                            <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                                <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.3rem 0.75rem; background: var(--color-danger-bg); color: var(--color-danger-text); border-radius: var(--r-full); font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?php echo $report->content_type === 'topic' ? '📄 Tema' : '💬 Respuesta'; ?>
                                </span>
                                <span style="padding: 0.3rem 0.75rem; background: var(--color-warning-bg); color: var(--color-warning-text); border-radius: var(--r-full); font-size: 0.7rem; font-weight: 600;">
                                    <?php echo esc_html($reasons[$report->reason] ?? $report->reason); ?>
                                </span>
                                <span style="padding: 0.3rem 0.75rem; background: var(--color-border-light); color: var(--color-text-muted); border-radius: var(--r-full); font-size: 0.7rem;">
                                    <?php echo FR_Helpers::time_ago($report->created_at); ?>
                                </span>
                            </div>

                            <!-- Content Preview -->
                            <div style="background: var(--color-bg); border-radius: var(--r-lg); border: 1.5px solid var(--color-border); padding: 1.25rem; margin-bottom: 1.25rem;">
                                <?php if ($report->content_type === 'topic') : ?>
                                    <h4 style="font-weight: 600; font-size: 0.95rem; margin-bottom: 0.6rem; color: var(--color-text);">
                                        <?php echo esc_html($report->content->title ?? 'Contenido eliminado'); ?>
                                    </h4>
                                <?php endif; ?>
                                <p style="color: var(--color-text-2); font-size: 0.85rem; line-height: 1.7; margin: 0;">
                                    <?php echo wp_trim_words(strip_tags($report->content->content ?? ''), 50); ?>
                                </p>
                            </div>

                            <!-- Report Details Grid -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <span style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 0.25rem;">
                                        Reportado por
                                    </span>
                                    <span style="font-size: 0.88rem; font-weight: 600; color: var(--color-text);">
                                        <?php echo esc_html($report->reporter_name); ?>
                                    </span>
                                </div>
                                <div>
                                    <span style="font-size: 0.72rem; color: var(--color-text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 0.25rem;">
                                        Usuario reportado
                                    </span>
                                    <span style="font-size: 0.88rem; font-weight: 600; color: var(--color-danger-text);">
                                        <?php echo esc_html($report->reported_name); ?>
                                    </span>
                                </div>
                            </div>

                            <?php if ($report->description) : ?>
                                <div style="padding: 0.85rem 1.1rem; background: var(--color-primary-subtle); border-left: 3px solid var(--color-primary); border-radius: var(--r-md); font-size: 0.82rem; color: var(--color-text-2); font-style: italic;">
                                    "<?php echo esc_html($report->description); ?>"
                                </div>
                            <?php endif; ?>
                            
                        </div>

                        <!-- Actions Column -->
                        <div style="flex-shrink: 0; display: flex; flex-direction: column; gap: 0.6rem; min-width: 140px;">
                            <button 
                                onclick="reviewReport(<?php echo $report->id; ?>, 'dismiss')"
                                class="btn btn-outline btn-sm"
                                style="justify-content: center; font-size: 0.8rem;"
                                title="Ignorar este reporte"
                            >
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Ignorar
                            </button>
                            
                            <button 
                                onclick="reviewReport(<?php echo $report->id; ?>, 'warn')"
                                style="padding: 0.45rem 0.85rem; background: var(--color-warning-bg); color: var(--color-warning-text); border: 1.5px solid var(--color-warning); border-radius: var(--r-md); font-size: 0.8rem; font-weight: 600; transition: all var(--ease); cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                title="Advertir al usuario"
                            >
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 0.35rem;"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                                Advertir
                            </button>
                            
                            <button 
                                onclick="reviewReport(<?php echo $report->id; ?>, 'delete')"
                                style="padding: 0.45rem 0.85rem; background: var(--color-danger-bg); color: var(--color-danger-text); border: 1.5px solid var(--color-danger); border-radius: var(--r-md); font-size: 0.8rem; font-weight: 600; transition: all var(--ease); cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                title="Eliminar el contenido"
                            >
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Eliminar
                            </button>
                            
                            <button 
                                onclick="reviewReport(<?php echo $report->id; ?>, 'ban')"
                                style="padding: 0.45rem 0.85rem; background: var(--color-danger); color: #fff; border: 1.5px solid var(--color-danger); border-radius: var(--r-md); font-size: 0.8rem; font-weight: 700; transition: all var(--ease); cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                title="Banear al usuario permanentemente"
                            >
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 0.35rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
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
    const actionMessages = {
        'dismiss': '¿Ignorar este reporte?',
        'warn': '¿Enviar una advertencia al usuario?',
        'delete': '¿Eliminar este contenido?',
        'ban': '⚠️ ¿BANEAR a este usuario permanentemente? Esta acción es seria.'
    };
    
    if (!confirm(actionMessages[actionType])) {
        return;
    }

    const reportEl = document.getElementById('report-' + reportId);
    if (reportEl) {
        reportEl.style.opacity = '0.5';
        reportEl.style.pointerEvents = 'none';
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
            // Animate out
            if (reportEl) {
                reportEl.style.transform = 'translateX(100%)';
                reportEl.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    reportEl.remove();
                    
                    // Check if no more reports
                    const remainingReports = document.querySelectorAll('[id^="report-"]');
                    if (remainingReports.length === 0) {
                        window.location.reload();
                    }
                }, 300);
            }
            
            // Update counter
            const counter = document.querySelector('.bg-yellow-100');
            if (counter) {
                const current = parseInt(counter.textContent);
                if (current > 1) {
                    counter.textContent = (current - 1) + ' pendiente' + (current - 1 > 1 ? 's' : '');
                } else {
                    window.location.reload();
                }
            }
        } else {
            alert(data.data.message || 'Error al procesar el reporte');
            if (reportEl) {
                reportEl.style.opacity = '1';
                reportEl.style.pointerEvents = 'auto';
            }
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error de conexión. Intenta de nuevo.');
        if (reportEl) {
            reportEl.style.opacity = '1';
            reportEl.style.pointerEvents = 'auto';
        }
    });
}
</script>

<?php get_footer(); ?>
