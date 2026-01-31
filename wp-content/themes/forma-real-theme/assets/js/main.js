/**
 * Forma Real — Main JS v2.0
 */
(function() {
    'use strict';

    // ── Header scroll shadow ──
    const header = document.getElementById('site-header');
    if (header) {
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    header.classList.toggle('scrolled', window.scrollY > 24);
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    // ── AJAX config ──
    const cfg = window.fr_ajax_obj || {};
    const ajaxUrl = cfg.ajax_url;
    const nonce   = cfg.nonce;

    // ── Helper: submit form via fetch ──
    function submitForm(form, onSuccess, onError) {
        const btn = form.querySelector('button[type="submit"]');
        const origText = btn ? btn.textContent : '';

        if (btn) { btn.disabled = true; btn.textContent = 'Publicando…'; }

        const fd = new FormData(form);
        fd.append('nonce', nonce);

        fetch(ajaxUrl, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    onSuccess(data.data);
                } else {
                    if (btn) { btn.disabled = false; btn.textContent = origText; }
                    if (onError) onError(data.data);
                }
            })
            .catch(err => {
                console.error('Forma Real AJAX error:', err);
                if (btn) { btn.disabled = false; btn.textContent = origText; }
                if (onError) onError({ message: 'Error de conexión. Intenta de nuevo.' });
            });
    }

    // ── Create Topic ──
    const topicForm = document.getElementById('create-topic-form');
    if (topicForm) {
        topicForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this,
                function(data) { window.location.href = data.redirect_url; },
                function(data) { alert(data.message); }
            );
        });
    }

    // ── Create Reply ──
    const replyForm = document.getElementById('create-reply-form');
    if (replyForm) {
        replyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this,
                function() { window.location.reload(); },
                function(data) { alert(data.message); }
            );
        });
    }

})();
