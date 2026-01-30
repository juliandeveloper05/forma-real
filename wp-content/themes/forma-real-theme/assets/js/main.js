/**
 * Forma Real - Main JS
 * Manejo de formularios AJAX y utilidades
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Configuración AJAX desde wp_localize_script
    const ajaxUrl = fr_ajax_obj.ajax_url;
    const nonce = fr_ajax_obj.nonce;

    /**
     * Handler: Crear Topic
     */
    const createTopicForm = document.getElementById('create-topic-form');
    if (createTopicForm) {
        createTopicForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerText;
            button.disabled = true;
            button.innerText = 'Publicando...';

            const formData = new FormData(this);
            formData.append('nonce', nonce);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir al nuevo tema
                    window.location.href = data.data.redirect_url;
                } else {
                    alert('Error: ' + data.data.message);
                    button.disabled = false;
                    button.innerText = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error inesperado.');
                button.disabled = false;
                button.innerText = originalText;
            });
        });
    }

    /**
     * Handler: Crear Reply
     */
    const createReplyForm = document.getElementById('create-reply-form');
    if (createReplyForm) {
        createReplyForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerText;
            button.disabled = true;
            button.innerText = 'Publicando...';

            const formData = new FormData(this);
            formData.append('nonce', nonce);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recargar la página para ver la nueva respuesta (MVP solution)
                    // Idealmente insertaríamos el HTML devuelto sin recargar
                    window.location.reload();
                } else {
                    alert('Error: ' + data.data.message);
                    button.disabled = false;
                    button.innerText = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error inesperado.');
                button.disabled = false;
                button.innerText = originalText;
            });
        });
    }

});
