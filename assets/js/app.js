/**
 * SPK Pemilihan Jasa Pengiriman MOORA - FAM Parfum
 * Main Application Interactive Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Lucide Icons if available
    if (window.lucide) {
        lucide.createIcons();
    }

    // 2. Mobile Sidebar Toggle
    const mobileToggleBtn = document.getElementById('mobileToggleBtn');
    const sidebar = document.getElementById('appSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (mobileToggleBtn && sidebar) {
        mobileToggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('show');
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            if (sidebar) sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('show');
        });
    }

    // 3. Client-side Table Search Filter
    const searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(input => {
        const targetTableId = input.getAttribute('data-table-search');
        const table = document.getElementById(targetTableId);
        if (!table) return;

        input.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // 4. Modal Helpers
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            if (window.lucide) lucide.createIcons();
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    };

    // Close modal on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });

    // 5. MOORA Step Switching
    window.switchMooraStep = function(stepIndex) {
        // Update nav items
        document.querySelectorAll('.step-item').forEach(item => {
            if (item.getAttribute('data-step') === String(stepIndex)) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        // Update content panes
        document.querySelectorAll('.moora-step-content').forEach(pane => {
            if (pane.getAttribute('data-step') === String(stepIndex)) {
                pane.style.display = 'block';
            } else {
                pane.style.display = 'none';
            }
        });

        if (window.lucide) lucide.createIcons();
    };

    // 6. Auto-dismiss alerts after 4 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        });
    }, 4000);
});
