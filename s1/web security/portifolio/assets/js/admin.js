/**
 * Admin Console Interactivity
 */

document.addEventListener('DOMContentLoaded', () => {
    // Confirm Deletion prompts
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const msg = btn.getAttribute('data-confirm') || 'Are you certain you want to purge this record? This action cannot be reversed.';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // Auto-dismiss alert notifications after 6 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 6000);
    });

    // Image Upload Previews
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    fileInputs.forEach((input) => {
        input.addEventListener('change', () => {
            const previewId = input.getAttribute('data-preview');
            const previewEl = document.getElementById(previewId);
            if (!previewEl || !input.files || !input.files[0]) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                previewEl.src = e.target.result;
                previewEl.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        });
    });
});
