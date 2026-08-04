// -------------------------------------------------------------
// MODULE: NAVIGATION & ROUTING MANAGER
// -------------------------------------------------------------

function renderRoleBasedNavigation() {
    const desktopContainer = document.getElementById('desktop-nav-container');
    const mobileContainer = document.getElementById('mobile-nav-container');
    if (!desktopContainer && !mobileContainer) return;

    let desktopHtml = '';
    let mobileHtml = '';

    const routes = window.APP_NAV_ROUTES || [];

    routes.forEach(groupConfig => {
        const allowedItems = groupConfig.items.filter(item => hasPermission(item.permission));
        if (allowedItems.length === 0) return;

        desktopHtml += `
            <div>
                <span class="sidebar-section-title px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">${escapeHtml(groupConfig.group)}</span>
                <nav class="space-y-1">
        `;

        mobileHtml += `
            <div>
                <span class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">${escapeHtml(groupConfig.group)}</span>
                <nav class="space-y-1">
        `;

        allowedItems.forEach(item => {
            desktopHtml += `
                <a href="#" onclick="onNavRouteClick(event, '${item.id}')" data-route="${item.id}" class="nav-link-item group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:bg-slate-900 transition">
                    <i class="fa-solid ${item.icon} text-slate-400 group-hover:text-blue-400 w-5 text-center transition"></i>
                    <span class="sidebar-label">${escapeHtml(item.label)}</span>
                    <div class="sidebar-tooltip">${escapeHtml(item.label)}</div>
                </a>
            `;
            mobileHtml += `
                <a href="#" onclick="onNavRouteClick(event, '${item.id}')" data-route="${item.id}" class="nav-link-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 hover:bg-slate-900 transition">
                    <i class="fa-solid ${item.icon} text-slate-400 w-5 text-center"></i>
                    <span>${escapeHtml(item.label)}</span>
                </a>
            `;
        });

        desktopHtml += `</nav></div>`;
        mobileHtml += `</nav></div>`;
    });

    if (desktopContainer) desktopContainer.innerHTML = desktopHtml;
    if (mobileContainer) mobileContainer.innerHTML = mobileHtml;

    applyPermissionGuardsOnUI();
}
window.renderRoleBasedNavigation = renderRoleBasedNavigation;

function applyPermissionGuardsOnUI() {
    document.querySelectorAll('[data-permission]').forEach(el => {
        const requiredPerm = el.getAttribute('data-permission');
        if (!hasPermission(requiredPerm)) {
            el.classList.add('hidden');
            el.disabled = true;
        } else {
            el.classList.remove('hidden');
            el.disabled = false;
        }
    });
}
window.applyPermissionGuardsOnUI = applyPermissionGuardsOnUI;

function toggleDesktopSidebar() {
    const sidebar = document.getElementById('app-sidebar');
    const toggleIcon = document.getElementById('sidebar-toggle-icon');
    if (!sidebar) return;

    const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
    if (toggleIcon) {
        toggleIcon.className = isCollapsed ? 'fa-solid fa-angles-right text-sm' : 'fa-solid fa-angles-left text-sm';
    }
    localStorage.setItem('sgpm_sidebar_collapsed', isCollapsed ? 'true' : 'false');
}
window.toggleDesktopSidebar = toggleDesktopSidebar;

function initDesktopSidebarState() {
    const sidebar = document.getElementById('app-sidebar');
    const toggleIcon = document.getElementById('sidebar-toggle-icon');
    if (!sidebar) return;

    const isCollapsed = localStorage.getItem('sgpm_sidebar_collapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('sidebar-collapsed');
        if (toggleIcon) toggleIcon.className = 'fa-solid fa-angles-right text-sm';
    }
}
window.initDesktopSidebarState = initDesktopSidebarState;

function openMobileDrawer() {
    const drawer = document.getElementById('mobile-nav-drawer');
    const overlay = document.getElementById('mobile-drawer-overlay');
    const btn = document.getElementById('mobile-hamburger-btn');
    if (!drawer) return;

    drawer.classList.remove('-translate-x-full');
    drawer.classList.add('translate-x-0');

    if (overlay) {
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100', 'pointer-events-auto');
    }
    if (btn) btn.setAttribute('aria-expanded', 'true');
}
window.openMobileDrawer = openMobileDrawer;

function closeMobileDrawer() {
    const drawer = document.getElementById('mobile-nav-drawer');
    const overlay = document.getElementById('mobile-drawer-overlay');
    const btn = document.getElementById('mobile-hamburger-btn');
    if (!drawer) return;

    drawer.classList.add('-translate-x-full');
    drawer.classList.remove('translate-x-0');

    if (overlay) {
        overlay.classList.add('opacity-0', 'pointer-events-none');
        overlay.classList.remove('opacity-100', 'pointer-events-auto');
    }
    if (btn) btn.setAttribute('aria-expanded', 'false');
}
window.closeMobileDrawer = closeMobileDrawer;

function toggleMobileDrawer() {
    const drawer = document.getElementById('mobile-nav-drawer');
    if (!drawer) return;
    if (drawer.classList.contains('-translate-x-full')) {
        openMobileDrawer();
    } else {
        closeMobileDrawer();
    }
}
window.toggleMobileDrawer = toggleMobileDrawer;

function toggleTopProfileDropdown(e) {
    if (e) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
    }
    const dropdown = document.getElementById('top-profile-dropdown');
    if (!dropdown) return;

    dropdown.classList.toggle('hidden');

    if (AppStore.user) {
        const uNameEl = document.getElementById('dropdown-user-name');
        const uEmailEl = document.getElementById('dropdown-user-email');
        if (uNameEl) uNameEl.innerText = AppStore.user.display_name || AppStore.user.username || 'Santomo Group';
        if (uEmailEl) uEmailEl.innerText = `${(AppStore.user.username || 'user').toLowerCase()}@sgpm.id`;
    }
}
window.toggleTopProfileDropdown = toggleTopProfileDropdown;

function toggleProfileDropdown(e) {
    if (e) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
    }
    const dropdown = document.getElementById('top-profile-dropdown') || document.getElementById('profile-dropdown');
    if (!dropdown) return;

    const isHidden = dropdown.classList.contains('hidden');
    ['top-profile-dropdown', 'profile-dropdown', 'mobile-profile-dropdown'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });

    if (isHidden) {
        dropdown.classList.remove('hidden');
    }

    if (AppStore.user) {
        const uNameEl = document.getElementById('dropdown-user-name');
        const uEmailEl = document.getElementById('dropdown-user-email');
        if (uNameEl) uNameEl.innerText = AppStore.user.display_name || AppStore.user.username || 'Santomo Group';
        if (uEmailEl) uEmailEl.innerText = `${(AppStore.user.username || 'user').toLowerCase()}@sgpm.id`;
    }
}
window.toggleProfileDropdown = toggleProfileDropdown;

function toggleMobileProfileDropdown(e) {
    if (e) {
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
    }
    const menu = document.getElementById('mobile-profile-dropdown');
    if (menu) menu.classList.toggle('hidden');
}
window.toggleMobileProfileDropdown = toggleMobileProfileDropdown;

function closeMobileProfileDropdown() {
    const menu = document.getElementById('mobile-profile-dropdown');
    if (menu) menu.classList.add('hidden');
}
window.closeMobileProfileDropdown = closeMobileProfileDropdown;

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    const topMenu = document.getElementById('top-profile-dropdown');
    const topBtn = document.getElementById('btn-top-profile-trigger');
    if (topMenu && !topMenu.classList.contains('hidden')) {
        if (!topMenu.contains(e.target) && topBtn && !topBtn.contains(e.target)) {
            topMenu.classList.add('hidden');
        }
    }

    const menu = document.getElementById('profile-dropdown');
    if (menu && !menu.classList.contains('hidden')) {
        if (!menu.contains(e.target)) {
            menu.classList.add('hidden');
        }
    }

    const mobileMenu = document.getElementById('mobile-profile-dropdown');
    const mobileBtn = document.getElementById('mobile-profile-btn');
    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
        if (!mobileMenu.contains(e.target) && mobileBtn && !mobileBtn.contains(e.target)) {
            mobileMenu.classList.add('hidden');
        }
    }
});

function setActiveNavRoute(routeId) {
    if (!routeId) return;

    document.querySelectorAll('.nav-link-item').forEach(el => {
        el.classList.remove('active');
    });

    document.querySelectorAll(`.nav-link-item[data-route="${routeId}"]`).forEach(el => {
        el.classList.add('active');
    });
}
window.setActiveNavRoute = setActiveNavRoute;

function onNavRouteClick(e, routeId) {
    if (e && e.preventDefault) e.preventDefault();

    if (!canUserAccessRoute(routeId)) {
        showToast('Access Denied: You do not have permission to access this module.', 'error');
        const fallbackRoute = getDefaultUserRoute();
        setActiveNavRoute(fallbackRoute);
        return;
    }

    // Prevent navigation while mechanic is fixing
    if (AppStore.mechanicInProgress) {
        showToast('Repair in progress. Please submit before navigating.', 'warning');
        return;
    }

    localStorage.setItem('sgpm_last_route', routeId);
    setActiveNavRoute(routeId);
    closeMobileDrawer();

    if (AppStore.user?.role === 'shop_admin') {
        showSection('shop-admin-workspace');
        if (typeof setShopAdminTab === 'function') {
            setShopAdminTab(routeId === 'shop-history' ? 'history' : 'inventory');
        }
        return;
    }

    if (AppStore.user?.role === 'mechanic') {
        let mechSubTab = 'form';
        if (routeId === 'mechanic-review') mechSubTab = 'review';
        if (routeId === 'mechanic-history') mechSubTab = 'my-history';

        showSection('mechanic-workspace');
        if (typeof loadMechanicDashboard === 'function') loadMechanicDashboard();
        if (typeof setMechanicTab === 'function') setMechanicTab(mechSubTab);
        return;
    }

    if (AppStore.user?.role === 'super_admin') {
        showSection('super-admin-workspace');
        if (typeof setSuperAdminTab === 'function') {
            setSuperAdminTab(routeId);
        }
        return;
    }
}
window.onNavRouteClick = onNavRouteClick;

function showSection(sectionId) {
    ['auth-gate', 'mechanic-workspace', 'shop-admin-workspace', 'super-admin-workspace'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });
    const target = document.getElementById(sectionId);
    if (target) target.classList.remove('hidden');

    const main = document.getElementById('app-main-content') || document.querySelector('main');
    const sidebar = document.getElementById('app-sidebar');
    const mobileHeader = document.getElementById('mobile-nav-header');
    const topAppBar = document.getElementById('top-app-bar');

    if (sectionId === 'auth-gate') {
        if (main) main.className = "flex-1 w-full min-h-screen flex items-center justify-center p-6";
        if (sidebar) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('md:flex', 'lg:flex');
        }
        if (mobileHeader) mobileHeader.classList.add('hidden');
        if (topAppBar) {
            topAppBar.classList.add('hidden');
            topAppBar.classList.remove('md:flex');
        }
    } else {
        if (main) {
            if (['super-admin-workspace', 'shop-admin-workspace', 'mechanic-workspace'].includes(sectionId)) {
                main.className = "flex-1 w-full min-w-0 min-h-screen px-4 sm:px-8 pt-0 pb-6 overflow-y-auto block";
            } else {
                main.className = "flex-1 w-full min-w-0 min-h-screen px-4 sm:px-8 py-6 overflow-y-auto block";
            }
        }
        if (sidebar) {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('md:flex');
        }
        if (mobileHeader) {
            mobileHeader.classList.remove('hidden');
            mobileHeader.classList.add('flex', 'md:hidden');
        }
        if (topAppBar) {
            topAppBar.classList.add('hidden', 'md:flex');
            topAppBar.classList.remove('flex');
        }
    }
}
window.showSection = showSection;

function renderDashboardLayout() {
    const sidebar = document.getElementById('app-sidebar');
    if (sidebar) sidebar.classList.remove('hidden');
    const mobileHeader = document.getElementById('mobile-nav-header');
    if (mobileHeader) mobileHeader.classList.remove('hidden');

    if (typeof updateProfilePictureUI === 'function') updateProfilePictureUI();

    const uNameDisplay = document.getElementById('user-display-name');
    const dRole = document.getElementById('desktop-user-role');

    const name = AppStore.user.display_name || AppStore.user.username;
    if (uNameDisplay) uNameDisplay.innerText = name;

    const roleName = AppStore.user.role === 'super_admin' ? 'Super Admin' : (AppStore.user.role === 'shop_admin' ? 'Shop Admin' : 'Mechanic');

    if (dRole) {
        dRole.innerText = roleName;
        dRole.className = "text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full inline-block mt-0.5 ";
        if (AppStore.user.role === 'super_admin') {
            dRole.className += "bg-violet-950 text-violet-400 border border-violet-800/40";
        } else if (AppStore.user.role === 'shop_admin') {
            dRole.className += "bg-emerald-950 text-emerald-400 border border-emerald-800/40";
        } else {
            dRole.className += "bg-blue-950 text-blue-400 border border-blue-800/40";
        }
    }

    const mName = document.getElementById('mobile-user-name');
    if (mName) mName.innerText = name;
    const mRole = document.getElementById('mobile-user-role');
    if (mRole) mRole.innerText = roleName;

    renderRoleBasedNavigation();

    const savedRoute = localStorage.getItem('sgpm_last_route');
    let targetRoute = getDefaultUserRoute();
    if (savedRoute && canUserAccessRoute(savedRoute)) {
        targetRoute = savedRoute;
    }
    onNavRouteClick(null, targetRoute);
}
window.renderDashboardLayout = renderDashboardLayout;

// ESC Key Event Listener for Keyboard Accessibility
window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeMobileDrawer();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    initDesktopSidebarState();
});
