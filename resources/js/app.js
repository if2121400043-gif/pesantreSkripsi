// ============================================================
// App JS — PP Nurul Furqon
// All UI interactions are handled via addEventListener (no inline onclick)
// ============================================================

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!sidebar) return;

    const isOpen = !sidebar.classList.contains('-translate-x-full');

    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('block');
        }
        document.body.classList.remove('overflow-hidden');
    } else {
        sidebar.classList.remove('-translate-x-full');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('block');
        }
        document.body.classList.add('overflow-hidden');
    }
}

function toggleNotifications() {
    const dropdown = document.getElementById('notification-dropdown');
    const userDropdown = document.getElementById('user-dropdown');

    if (!dropdown) return;

    if (userDropdown) userDropdown.classList.add('hidden');

    dropdown.classList.toggle('hidden');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('user-dropdown');
    const notifDropdown = document.getElementById('notification-dropdown');

    if (!dropdown) return;

    if (notifDropdown) notifDropdown.classList.add('hidden');

    dropdown.classList.toggle('hidden');
}

function openSearch() {
    const modal = document.getElementById('global-search-modal');
    const input = document.getElementById('global-search-input');
    if (!modal) return;

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    if (input) {
        input.value = '';
        // Small delay to ensure the modal is visible before focusing
        setTimeout(() => input.focus(), 50);
    }
}

function closeSearch() {
    const modal = document.getElementById('global-search-modal');
    if (!modal) return;

    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// ── Close dropdowns when clicking outside ──
document.addEventListener('click', function(e) {
    const notifWrapper = document.getElementById('notification-wrapper');
    const userWrapper = document.getElementById('user-menu-wrapper');
    const notifDropdown = document.getElementById('notification-dropdown');
    const userDropdown = document.getElementById('user-dropdown');

    if (notifWrapper && !notifWrapper.contains(e.target) && notifDropdown) {
        notifDropdown.classList.add('hidden');
    }

    if (userWrapper && !userWrapper.contains(e.target) && userDropdown) {
        userDropdown.classList.add('hidden');
    }
});

// ── Keyboard shortcuts ──
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openSearch();
    }

    if (e.key === 'Escape') {
        const modal = document.getElementById('global-search-modal');
        const notifDropdown = document.getElementById('notification-dropdown');
        const userDropdown = document.getElementById('user-dropdown');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (modal && !modal.classList.contains('hidden')) {
            closeSearch();
        }
        if (notifDropdown) notifDropdown.classList.add('hidden');
        if (userDropdown) userDropdown.classList.add('hidden');

        if (sidebar && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 768) {
            sidebar.classList.add('-translate-x-full');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('block');
            }
            document.body.classList.remove('overflow-hidden');
        }
    }
});

// ── Responsive sidebar handling ──
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (window.innerWidth >= 768) {
        if (sidebar) sidebar.classList.remove('-translate-x-full');
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.classList.remove('block');
        }
        document.body.classList.remove('overflow-hidden');
    } else {
        if (sidebar) sidebar.classList.add('-translate-x-full');
    }
});

// ── Attach all event listeners on DOM ready ──
document.addEventListener('DOMContentLoaded', function() {
    // Re-create Lucide icons (for dynamically loaded content)
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Hamburger menu button
    const toggleSidebarBtn = document.getElementById('btn-toggle-sidebar');
    if (toggleSidebarBtn) {
        toggleSidebarBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Sidebar overlay (click to close sidebar)
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }

    // Global search button
    const searchBtn = document.getElementById('btn-global-search');
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openSearch();
        });
    }

    // Close search button
    const closeSearchBtn = document.getElementById('btn-close-search');
    if (closeSearchBtn) {
        closeSearchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeSearch();
        });
    }

    // Close search when clicking backdrop (outside the modal content)
    const searchModal = document.getElementById('global-search-modal');
    if (searchModal) {
        searchModal.addEventListener('click', function(e) {
            // Only close if clicking the backdrop itself, not the modal content
            if (e.target === searchModal) {
                closeSearch();
            }
        });
    }

    // Notification bell button
    const notificationBtn = document.getElementById('btn-notifications');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleNotifications();
        });
    }

    // User menu button
    const userMenuBtn = document.getElementById('btn-user-menu');
    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleUserMenu();
        });
    }

    console.log('🕌 Sistem Manajemen Pesantren Nurul Furqon — Ready');
});

// Expose functions globally for any remaining inline usage
window.toggleSidebar = toggleSidebar;
window.toggleNotifications = toggleNotifications;
window.toggleUserMenu = toggleUserMenu;
window.openSearch = openSearch;
window.closeSearch = closeSearch;
