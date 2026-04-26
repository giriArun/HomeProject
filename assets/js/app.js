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

    // Modal: Add/Edit User - populate fields
    const addEditUserModal = document.getElementById('addEditUserModal');
    if (addEditUserModal) {
        addEditUserModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const userEmail = button.getAttribute('data-user-email');
            const userIsAdmin = button.getAttribute('data-user-is-admin');
            const userIsActive = button.getAttribute('data-user-is-active');

            const form = addEditUserModal.querySelector('form');
            const hiddenInput = addEditUserModal.querySelector('input[name="user_id"]');
            const nameInput = addEditUserModal.querySelector('input[name="user_name"]');
            const emailInput = addEditUserModal.querySelector('input[name="user_email"]');
            const userRoleSelect = addEditUserModal.querySelector('[name="user_role"]');
            const userStatusSelect = addEditUserModal.querySelector('[name="user_status"]');

            if (hiddenInput) hiddenInput.value = userId || 0;
            if (nameInput) nameInput.value = userName || '';
            if (emailInput) emailInput.value = userEmail || '';
            if (userRoleSelect) userRoleSelect.value = userIsAdmin || 0;
            if (userStatusSelect) userStatusSelect.value = userIsActive || 1;
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
