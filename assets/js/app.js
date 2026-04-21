document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.search-box input');
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.querySelector('[data-sidebar-toggle]');
    const closeButtons = document.querySelectorAll('[data-sidebar-close]');

    // Modal: Edit Tags - populate fields
    const editTagsModal = document.getElementById('editTagsModal');
    if (editTagsModal) {
        editTagsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const projectId = button.getAttribute('data-project-id');
            const projectTags = button.getAttribute('data-project-tags');
            const textarea = editTagsModal.querySelector('textarea');
            const hiddenInput = editTagsModal.querySelector('input[name="project_id"]');
            if (textarea) textarea.value = projectTags || '';
            if (hiddenInput) hiddenInput.value = projectId || 0;
        });
    }

    const setSidebarState = (isOpen) => {
        if (!sidebar || !toggleButton) {
            return;
        }

        body.classList.toggle('sidebar-open', isOpen);
        toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };

    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            setSidebarState(!body.classList.contains('sidebar-open'));
        });
    }

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setSidebarState(false);
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            setSidebarState(false);
        }
    });

    if (searchInput) {
        searchInput.addEventListener('focus', () => {
            searchInput.parentElement.classList.add('is-focused');
        });

        searchInput.addEventListener('blur', () => {
            searchInput.parentElement.classList.remove('is-focused');
        });
    }
});
