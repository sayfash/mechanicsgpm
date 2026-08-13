// Core store, auth, and navigation are imported from modules/store.js, modules/auth.js, and modules/navigation.js

function getActiveEl(id) {
    const activePanel = document.querySelector('#shop-panel-active-job:not(.hidden)') || document.querySelector('#shop-panel-intake:not(.hidden)');
    if (activePanel) {
        const el = activePanel.querySelector(`#${id}`);
        if (el) return el;
    }
    return document.getElementById(id);
}
window.getActiveEl = getActiveEl;

function getPageSize(key) {
    if (!AppStore.pagination[key]) {
        AppStore.pagination[key] = { page: 1, limit: 25, custom: '' };
    }
    const state = AppStore.pagination[key];
    if (state.limit === 'custom') {
        const parsed = parseInt(state.custom);
        return (!isNaN(parsed) && parsed > 0) ? parsed : 25;
    }
    return parseInt(state.limit) || 25;
}

function renderPaginationUI(key, totalItems, topTargetId, bottomTargetId, onPageChangeCallback) {
    if (!AppStore.pagination[key]) {
        AppStore.pagination[key] = { page: 1, limit: 25, custom: '' };
    }
    const state = AppStore.pagination[key];
    const pageSize = getPageSize(key);
    const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
    if (state.page > totalPages) state.page = totalPages;
    if (state.page < 1) state.page = 1;

    const startIdx = totalItems === 0 ? 0 : (state.page - 1) * pageSize + 1;
    const endIdx = Math.min(totalItems, state.page * pageSize);

    const isCustom = state.limit === 'custom';

    const html = `
                <div class="pagination-container flex flex-wrap items-center justify-between gap-3 bg-slate-900/60 border border-slate-800/80 p-3 rounded-xl text-xs text-slate-300">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5 font-medium">
                            <span class="text-slate-400">Items per page:</span>
                            <select onchange="window.handlePageSizeChange('${key}', this.value)" class="bg-slate-950 border border-slate-800 rounded px-2 py-1 text-slate-200 font-bold focus:outline-none focus:border-blue-500">
                                <option value="25" ${state.limit == 25 ? 'selected' : ''}>25</option>
                                <option value="50" ${state.limit == 50 ? 'selected' : ''}>50</option>
                                <option value="100" ${state.limit == 100 ? 'selected' : ''}>100</option>
                                <option value="custom" ${isCustom ? 'selected' : ''}>Custom</option>
                            </select>
                            ${isCustom ? `
                                <input type="number" min="1" max="1000" placeholder="Qty" value="${state.custom}" onchange="window.handleCustomPageSizeChange('${key}', this.value)" class="w-16 bg-slate-950 border border-slate-800 rounded px-2 py-1 text-slate-200 font-mono text-xs focus:outline-none focus:border-blue-500">
                            ` : ''}
                        </div>
                        <span class="text-slate-400 font-mono">Showing <strong class="text-slate-200">${startIdx}</strong> - <strong class="text-slate-200">${endIdx}</strong> of <strong class="text-blue-400">${totalItems}</strong></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="window.handlePageNav('${key}', 1)" ${state.page <= 1 ? 'disabled class="px-2 py-1 bg-slate-950 text-slate-600 rounded cursor-not-allowed"' : 'class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded font-bold transition"'}>
                            <i class="fa-solid fa-angles-left text-[10px]"></i>
                        </button>
                        <button onclick="window.handlePageNav('${key}', ${state.page - 1})" ${state.page <= 1 ? 'disabled class="px-2.5 py-1 bg-slate-950 text-slate-600 rounded cursor-not-allowed"' : 'class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded font-bold transition"'}>
                            <i class="fa-solid fa-angle-left text-[10px]"></i> Prev
                        </button>
                        <span class="px-3 py-1 bg-blue-600/20 border border-blue-500/30 text-blue-300 font-bold rounded font-mono">Page ${state.page} / ${totalPages}</span>
                        <button onclick="window.handlePageNav('${key}', ${state.page + 1})" ${state.page >= totalPages ? 'disabled class="px-2.5 py-1 bg-slate-950 text-slate-600 rounded cursor-not-allowed"' : 'class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded font-bold transition"'}>
                            Next <i class="fa-solid fa-angle-right text-[10px]"></i>
                        </button>
                        <button onclick="window.handlePageNav('${key}', ${totalPages})" ${state.page >= totalPages ? 'disabled class="px-2 py-1 bg-slate-950 text-slate-600 rounded cursor-not-allowed"' : 'class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded font-bold transition"'}>
                            <i class="fa-solid fa-angles-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
            `;

    const topEl = document.getElementById(topTargetId);
    const bottomEl = document.getElementById(bottomTargetId);

    if (topEl) topEl.innerHTML = html;
    if (bottomEl) bottomEl.innerHTML = html;
}

window.handlePageSizeChange = function (key, val) {
    if (!AppStore.pagination[key]) AppStore.pagination[key] = { page: 1, limit: 25, custom: '' };
    AppStore.pagination[key].limit = val;
    AppStore.pagination[key].page = 1;
    triggerTableRefresh(key);
};

window.handleCustomPageSizeChange = function (key, val) {
    if (!AppStore.pagination[key]) AppStore.pagination[key] = { page: 1, limit: 25, custom: '' };
    AppStore.pagination[key].custom = val;
    AppStore.pagination[key].page = 1;
    triggerTableRefresh(key);
};

window.handlePageNav = function (key, pageNum) {
    if (!AppStore.pagination[key]) AppStore.pagination[key] = { page: 1, limit: 25, custom: '' };
    AppStore.pagination[key].page = pageNum;
    triggerTableRefresh(key);
};

function triggerTableRefresh(key) {
    if (key === 'shopInventory') filterShopAdminInventory();
    else if (key === 'globalInventory') filterGlobalInventory();
    else if (key === 'maintenanceRecords') filterMaintenanceRecords();
    else if (key === 'auditLogs') fetchAuditLogs();
    else if (key === 'vehicles') filterVehiclesAndCustomers();
    else if (key === 'customers_table') filterCustomersTable();
}

// Initialize App on DOM Content Loaded
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    if (typeof initLanguage === 'function') initLanguage();
    initializeApp();
});

async function initializeApp() {
    try {
        await fetchMe();

        if (AppStore.user) {
            await fetchBranches();
            await fetchSparepartCategories();
            renderDashboardLayout();
        } else {
            showSection('auth-gate');
        }
    } catch (err) {
        console.error("Initialization error:", err);
        showSection('auth-gate');
    }
}

async function fetchSparepartCategories() {
    try {
        const data = await request('get_sparepart_categories', {}, 'GET');
        AppStore.sparepartCategories = data || [];
    } catch (err) {
        console.error("Failed to fetch sparepart categories:", err);
    }
}

function getCategoryOptionsHtml(selectedCategory = 'General') {
    const cats = AppStore.sparepartCategories || [];
    let found = false;
    let html = cats.map(c => {
        const sel = (c.name.toLowerCase() === (selectedCategory || 'General').toLowerCase()) ? 'selected' : '';
        if (sel) found = true;
        return `<option value="${escapeHtml(c.name)}" ${sel}>${escapeHtml(c.name)}</option>`;
    }).join('');

    if (!found && selectedCategory) {
        html = `<option value="${escapeHtml(selectedCategory)}" selected>${escapeHtml(selectedCategory)}</option>` + html;
    }
    return html;
}

// -------------------------------------------------------------
// Core Fetch Integration Methods
// -------------------------------------------------------------

async function request(action, params = {}, method = 'POST') {
    const legacyRoutes = {
        'add_customer': { url: '/api/customers', method: 'POST' },
        'edit_customer': { url: '/api/customers', method: 'PUT' },
        'delete_customer': { url: '/api/customers', method: 'DELETE' },
        'get_all_customers': { url: '/api/customers', method: 'GET' },
        'add_vehicle': { url: '/api/vehicles', method: 'POST' },
        'edit_vehicle': { url: '/api/vehicles', method: 'PUT' },
        'delete_vehicle': { url: '/api/vehicles', method: 'DELETE' },
        'get_all_vehicles': { url: '/api/vehicles', method: 'GET' },
        'add_customer_and_vehicle': { url: '/api/customers/register-with-vehicle', method: 'POST' },
        'submit_mechanic_job': { url: '/api/jobs/submit', method: 'POST' },
        'start_job': { url: '/api/jobs/start', method: 'POST' },
        'cancel_active_job': { url: '/api/jobs/cancel', method: 'POST' },
        'add_inventory': { url: '/api/inventory', method: 'POST' },
        'edit_inventory': { url: '/api/inventory', method: 'PUT' },
        'delete_inventory': { url: '/api/inventory', method: 'DELETE' },
        'delete_inventory_item': { url: '/api/inventory', method: 'DELETE' },
        'get_inventory': { url: '/api/inventory', method: 'GET' },
        'get_branches': { url: '/api/branches', method: 'GET' },
        'login': { url: '/api/login', method: 'POST' },
        'logout': { url: '/api/logout', method: 'POST' },
        'me': { url: '/api/me', method: 'GET' },
        'forgot_password': { url: '/api/forgot-password', method: 'POST' },
        'update_profile': { url: '/api/update-profile', method: 'POST' },
        'get_dashboard_stats': { url: '/api/dashboard-stats', method: 'GET' },
        'get_management_counts': { url: '/api/management-counts', method: 'GET' },
        'get_audit_logs': { url: '/api/audit-logs', method: 'GET' },
        'get_all_users': { url: '/api/users', method: 'GET' },
        'register': { url: '/api/users', method: 'POST' },
        'update_user': { url: '/api/users', method: 'PUT' },
        'delete_user': { url: '/api/users', method: 'DELETE' },
        'mechanic_check_in': { url: '/api/mechanic/check-in', method: 'POST' },
        'get_mechanic_jobs': { url: '/api/mechanic/jobs', method: 'GET' },
        'get_next_job_id': { url: '/api/mechanic/next-job-id', method: 'GET' },
        'lookup_customer_by_id_card': { url: '/api/mechanic/lookup-customer', method: 'GET' },
        'get_customer_maintenance_records': { url: '/api/maintenance-records', method: 'GET' },
        'get_all_maintenance_records': { url: '/api/maintenance-records', method: 'GET' },
        'export_shop_maintenance_records': { url: '/api/maintenance-records/export', method: 'GET' },
        'edit_maintenance_record': { url: '/api/maintenance-records', method: 'PUT' },
        'delete_maintenance_record': { url: '/api/maintenance-records', method: 'DELETE' },
        'get_sparepart_categories': { url: '/api/sparepart-categories', method: 'GET' },
        'get_spare_parts_history': { url: '/api/spare-parts-history', method: 'GET' },
        'get_common_issues': { url: '/api/common-issues', method: 'GET' },
        'add_common_issue': { url: '/api/common-issues', method: 'POST' },
        'edit_common_issue': { url: '/api/common-issues', method: 'PUT' },
        'delete_common_issue': { url: '/api/common-issues', method: 'DELETE' },
        'get_mechanic_form_items': { url: '/api/mechanic-form-items', method: 'GET' },
        'add_mechanic_form_item': { url: '/api/mechanic-form-items', method: 'POST' },
        'edit_mechanic_form_item': { url: '/api/mechanic-form-items', method: 'PUT' },
        'delete_mechanic_form_item': { url: '/api/mechanic-form-items', method: 'DELETE' },
        'get_other_services': { url: '/api/other-services', method: 'GET' },
        'add_other_service': { url: '/api/other-services', method: 'POST' },
        'edit_other_service': { url: '/api/other-services', method: 'PUT' },
        'delete_other_service': { url: '/api/other-services', method: 'DELETE' },
        'get_service_options': { url: '/api/service-options', method: 'GET' },
        'add_service_option': { url: '/api/service-options', method: 'POST' },
        'edit_service_option': { url: '/api/service-options', method: 'PUT' },
        'delete_service_option': { url: '/api/service-options', method: 'DELETE' },
        'import_inventory_batch': { url: '/api/inventory/batch', method: 'POST' },
        'bulk_delete_inventory': { url: '/api/inventory/bulk-delete', method: 'POST' },
        'import_customers_batch': { url: '/api/customers/batch', method: 'POST' },
        'import_vehicles_batch': { url: '/api/vehicles/batch', method: 'POST' },
        'add_branch': { url: '/api/branches', method: 'POST' },
        'edit_branch': { url: '/api/branches', method: 'PUT' },
        'delete_branch': { url: '/api/branches', method: 'DELETE' },
        'add_inventory_item': { url: '/api/inventory', method: 'POST' },
        'edit_inventory_item': { url: '/api/inventory', method: 'PUT' },
        'update_inventory': { url: '/api/inventory', method: 'PUT' },
        'edit_user': { url: '/api/users', method: 'PUT' },
        'add_sparepart_category': { url: '/api/sparepart-categories', method: 'POST' },
        'edit_sparepart_category': { url: '/api/sparepart-categories', method: 'PUT' },
        'delete_sparepart_category': { url: '/api/sparepart-categories', method: 'DELETE' },
        'import_excel_maintenance_records': { url: '/api/maintenance-records/batch', method: 'POST' },
        'bind_vehicle_customer': { url: '/api/vehicles/bind-customer', method: 'POST' },
        'edit_audit_log': { url: '/api/audit-logs', method: 'PUT' },
        'get_reports_summary': { url: '/api/reports/summary', method: 'GET' },
        'reports/summary': { url: '/api/reports/summary', method: 'GET' }
    };

    const route = legacyRoutes[action];
    const finalUrl = route ? route.url : '/api/legacy';
    const finalMethod = route ? route.method : method;

    const options = {
        method: finalMethod,
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };

    const payload = route ? { ...params } : { action, ...params };

    if (finalMethod === 'GET') {
        const queryParams = new URLSearchParams(payload).toString();
        return fetch(`${finalUrl}?${queryParams}`, options).then(r => handleFetchResponse(r));
    } else {
        options.body = JSON.stringify(payload);
        return fetch(finalUrl, options).then(r => handleFetchResponse(r));
    }
}

async function requestFormData(formData) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const options = {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        ...(csrfToken ? { headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } } : { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
    };
    return fetch('/api/update-profile', options).then(r => handleFetchResponse(r));
}

async function handleFetchResponse(response) {
    let data;
    try {
        data = await response.json();
    } catch (e) {
        // Fallback: try to get raw text for debugging
        const txt = await response.text();
        throw new Error('Server returned unreadable response: ' + txt);
    }

    if (!response.ok) {
        let msg = data.error || data.message;
        if (!msg && data.errors) {
            msg = Object.values(data.errors).flat().join(', ');
        }
        throw new Error(msg || 'Server error occurred.');
    }
    return data;
}

// -------------------------------------------------------------
// Authentication Actions
// -------------------------------------------------------------

async function fetchMe() {
    try {
        const data = await request('me', {}, 'GET');
        if (data.logged_in) {
            AppStore.user = data.user;
        } else {
            AppStore.user = null;
        }
    } catch (err) {
        AppStore.user = null;
    }
}

async function fetchBranches() {
    try {
        const raw = await request('get_branches', {}, 'GET');
        const data = Array.isArray(raw) ? raw : (raw && Array.isArray(raw.data) ? raw.data : []);
        AppStore.branches = data;

        // Populate branch dropdowns
        const mSelect = document.getElementById('mechanic-branch-select');
        const rSelect = document.getElementById('reg-branch');
        const fSelect = document.getElementById('super-admin-branch-filter');

        if (mSelect) {
            mSelect.innerHTML = AppStore.branches.map(b => `<option value="${b.id}">${escapeHtml(b.name)}</option>`).join('');
        }
        if (rSelect) {
            rSelect.innerHTML = '<option value="">No Branch Association (Super Admin Only)</option>' +
                AppStore.branches.map(b => `<option value="${b.id}">${escapeHtml(b.name)}</option>`).join('');
        }
        if (fSelect) {
            fSelect.innerHTML = '<option value="">All Branches</option>' +
                AppStore.branches.map(b => `<option value="${b.id}">${escapeHtml(b.name)}</option>`).join('');
        }
    } catch (err) {
        if (AppStore.user) {
            showToast(err.message, 'error');
        }
    }
}

async function handleLogin(e) {
    if (e && e.preventDefault) e.preventDefault();
    const u = document.getElementById('login-username').value;
    const p = document.getElementById('login-password').value;

    try {
        const data = await request('login', { username: u, password: p });
        AppStore.user = data.user;
        showToast(data.message, 'success');

        localStorage.removeItem('sgpm_last_route');
        await initializeApp();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function fillLogin(username, password, autoSubmit = false) {
    const uEl = document.getElementById('login-username');
    const pEl = document.getElementById('login-password');
    if (uEl) uEl.value = username;
    if (pEl) pEl.value = password;
    if (autoSubmit) {
        handleLogin();
    }
}

function toggleAuthView(view) {
    const loginForm = document.getElementById('login-form-wrapper');
    const forgotForm = document.getElementById('forgot-form-wrapper');

    if (loginForm) loginForm.classList.add('hidden');
    if (forgotForm) forgotForm.classList.add('hidden');

    if (view === 'login' && loginForm) {
        loginForm.classList.remove('hidden');
    } else if (view === 'forgot' && forgotForm) {
        forgotForm.classList.remove('hidden');
    }
}

async function handleForgotPasswordSubmit(e) {
    if (e && e.preventDefault) e.preventDefault();
    const u = document.getElementById('forgot-username').value.trim();
    try {
        const data = await request('forgot_password', { username: u });
        showToast(data.message, 'warning', 8000);
        toggleAuthView('login');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// --- Profile Dropdown and Settings ---
function toggleProfileDropdown(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const menu = document.getElementById('profile-dropdown');
    if (menu) menu.classList.toggle('hidden');
}

function toggleMobileProfileDropdown(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const menu = document.getElementById('mobile-profile-dropdown');
    if (menu) menu.classList.toggle('hidden');
}

function closeMobileProfileDropdown() {
    const menu = document.getElementById('mobile-profile-dropdown');
    if (menu) menu.classList.add('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    const menu = document.getElementById('profile-dropdown');
    if (menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
    }

    const mobileMenu = document.getElementById('mobile-profile-dropdown');
    const mobileBtn = document.getElementById('mobile-profile-btn');
    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
        if (!mobileMenu.contains(e.target) && (!mobileBtn || !mobileBtn.contains(e.target))) {
            mobileMenu.classList.add('hidden');
        }
    }
});

function openProfileModal() {
    const modal = document.getElementById('profile-modal');
    if (!modal) return;
    const usernameDisplay = document.getElementById('modal-profile-username-display');
    const usernameHandle = document.getElementById('modal-profile-username-handle');
    const displayNameInput = document.getElementById('profile-new-display-name');
    const oldPasswordInput = document.getElementById('profile-old-password');
    const newPasswordInput = document.getElementById('profile-new-password');
    const pictureInput = document.getElementById('profile-new-picture');
    const imgPreview = document.getElementById('modal-profile-picture-img');
    const iconPreview = document.getElementById('modal-profile-picture-icon');

    if (AppStore.user) {
        if (usernameDisplay) usernameDisplay.innerText = AppStore.user.display_name || AppStore.user.username;
        if (usernameHandle) usernameHandle.innerText = '@' + AppStore.user.username;
        if (displayNameInput) displayNameInput.value = AppStore.user.display_name || '';

        if (AppStore.user.profile_picture) {
            if (iconPreview) iconPreview.classList.add('hidden');
            if (imgPreview) {
                imgPreview.src = AppStore.user.profile_picture + '?t=' + new Date().getTime();
                imgPreview.classList.remove('hidden');
            }
        } else {
            if (iconPreview) iconPreview.classList.remove('hidden');
            if (imgPreview) {
                imgPreview.classList.add('hidden');
                imgPreview.src = '';
            }
        }
    }

    if (oldPasswordInput) oldPasswordInput.value = '';
    if (newPasswordInput) newPasswordInput.value = '';
    if (pictureInput) pictureInput.value = '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Hide dropdown
    const menu = document.getElementById('profile-dropdown');
    if (menu) menu.classList.add('hidden');
}

function previewSelectedProfilePicture(e) {
    const file = e.target.files[0];
    if (!file) return;

    if (file.size > 1048576) {
        showToast('Image size exceeds 1MB limit.', 'error');
        e.target.value = '';
        return;
    }

    const imgPreview = document.getElementById('modal-profile-picture-img');
    const iconPreview = document.getElementById('modal-profile-picture-icon');
    if (imgPreview && iconPreview) {
        imgPreview.src = URL.createObjectURL(file);
        imgPreview.classList.remove('hidden');
        iconPreview.classList.add('hidden');
    }
}

function closeProfileModal() {
    const modal = document.getElementById('profile-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

async function handleProfileUpdate(e) {
    if (e && e.preventDefault) e.preventDefault();
    const newDisplayName = document.getElementById('profile-new-display-name')?.value?.trim();
    const oldPassword = document.getElementById('profile-old-password')?.value;
    const newPassword = document.getElementById('profile-new-password')?.value;
    const pictureFile = document.getElementById('profile-new-picture')?.files[0];

    const formData = new FormData();
    formData.append('action', 'update_profile');
    if (newDisplayName) {
        formData.append('new_display_name', newDisplayName);
        formData.append('display_name', newDisplayName);
    }
    if (oldPassword) formData.append('old_password', oldPassword);
    if (newPassword) formData.append('new_password', newPassword);
    if (pictureFile) formData.append('profile_picture', pictureFile);

    try {
        const res = await requestFormData(formData);
        showToast(res.message || 'Profile updated successfully.', 'success');

        const updatedDisplayName = res.display_name || newDisplayName;
        if (AppStore.user) {
            AppStore.user.display_name = updatedDisplayName;
            AppStore.user.profile_picture = res.profile_picture || AppStore.user.profile_picture || null;
        }

        const nameEl = document.getElementById('user-display-name');
        if (nameEl) nameEl.innerText = updatedDisplayName;
        const dropdownNameEl = document.getElementById('dropdown-user-name');
        if (dropdownNameEl) dropdownNameEl.innerText = updatedDisplayName;
        const mobileDropdownNameEl = document.getElementById('mobile-dropdown-user-name');
        if (mobileDropdownNameEl) mobileDropdownNameEl.innerText = updatedDisplayName;

        updateProfilePictureUI();

        closeProfileModal();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function logout() {
    try {
        const data = await request('logout');
        localStorage.removeItem('auth_token');
        localStorage.removeItem('sgpm_last_route');
        showToast(data.message, 'success');
        AppStore.user = null;

        // Clean up timers
        if (AppStore.activeTimersInterval) {
            clearInterval(AppStore.activeTimersInterval);
        }

        document.getElementById('nav-header')?.classList.add('hidden');
        showSection('auth-gate');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// Export auth & profile handlers to global window object
window.handleLogin = handleLogin;
window.fillLogin = fillLogin;
window.toggleAuthView = toggleAuthView;
window.handleForgotPasswordSubmit = handleForgotPasswordSubmit;
window.toggleProfileDropdown = toggleProfileDropdown;
window.toggleMobileProfileDropdown = toggleMobileProfileDropdown;
window.closeMobileProfileDropdown = closeMobileProfileDropdown;
window.openProfileModal = openProfileModal;
window.closeProfileModal = closeProfileModal;
window.previewSelectedProfilePicture = previewSelectedProfilePicture;
window.handleProfileUpdate = handleProfileUpdate;
window.logout = logout;
window.toggleDesktopSidebar = toggleDesktopSidebar;
window.openMobileDrawer = openMobileDrawer;
window.closeMobileDrawer = closeMobileDrawer;
window.toggleMobileDrawer = toggleMobileDrawer;

// Mechanic Check-In (no changes needed)
window.handleMechanicCheckin = async function () {
    const select = document.getElementById('mechanic-branch-select');
    const branchId = select ? select.value : null;
    if (!branchId) {
        showToast('Please select a branch to check in.', 'error');
        return;
    }
    try {
        const data = await request('mechanic_check_in', { branch_id: branchId });
        // Update UI
        const banner = document.getElementById('mechanic-checkin-banner');
        if (banner) banner.classList.add('hidden');
        const activeBanner = document.getElementById('mechanic-active-banner');
        if (activeBanner) {
            activeBanner.classList.remove('hidden');
            const nameEl = document.getElementById('mechanic-active-branch-name');
            if (nameEl) nameEl.innerText = data.branch_name || (select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '');
        }
        // Store branch in AppStore for later use
        if (AppStore.user) AppStore.user.branch_id = parseInt(branchId);
        showToast('Checked in to branch successfully.', 'success');
    } catch (err) {
        showToast(err.message || 'Check-in failed.', 'error');
    }
};

// Submit Mechanic Job Record (handled in module below)
window.setActiveNavRoute = setActiveNavRoute;
window.onNavRouteClick = onNavRouteClick;

// Helper to warn on tab/window close
function handleBeforeUnload(e) {
    e.preventDefault();
    e.returnValue = '';
}

// Draft persistence for mechanic form
function saveMechanicDraft() {
    if (!AppStore.mechanicInProgress) return;
    const draft = {};
    const fields = document.querySelectorAll('#mechanic-workspace input, #mechanic-workspace select, #mechanic-workspace textarea');
    fields.forEach(el => {
        if (el.id) draft[el.id] = el.value;
    });
    localStorage.setItem('mechanicDraft', JSON.stringify(draft));
}

function loadMechanicDraft() {
    const draftStr = localStorage.getItem('mechanicDraft');
    if (!draftStr) return;
    try {
        const draft = JSON.parse(draftStr);
        Object.entries(draft).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.value = value;
        });
    } catch (e) {
        console.error('Failed to load mechanic draft', e);
    }
}

// Attach input listeners after DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const workspace = document.getElementById('mechanic-workspace');
    if (workspace) {
        workspace.addEventListener('input', saveMechanicDraft);
    }
});

window.hasPermission = hasPermission;
window.renderRoleBasedNavigation = renderRoleBasedNavigation;
window.canUserAccessRoute = canUserAccessRoute;
window.applyPermissionGuardsOnUI = applyPermissionGuardsOnUI;

// Security and RBAC Navigation Routes are imported from store.js and navigation.js

function hasPermission(permission) {
    if (!AppStore.user) return false;
    const userRole = AppStore.user.role || 'guest';

    // If explicit permissions array is sent from DB / JWT claims
    if (Array.isArray(AppStore.user.permissions) && AppStore.user.permissions.length > 0) {
        return AppStore.user.permissions.includes(permission) || AppStore.user.permissions.includes('*');
    }

    // Role-to-Permissions Mapping Matrix
    const ROLE_PERMISSIONS = {
        'super_admin': [
            'view_stats', 'view_reports', 'view_global_inventory', 'edit_inventory', 'delete_inventory',
            'view_maintenance', 'edit_maintenance', 'delete_maintenance',
            'view_vehicles', 'edit_vehicles', 'delete_vehicles',
            'view_customers', 'edit_customers', 'delete_customers',
            'view_management', 'edit_management', 'view_logs', 'export_data'
        ],
        'manager': [
            'view_stats', 'view_reports', 'view_global_inventory', 'view_maintenance',
            'view_vehicles', 'view_customers', 'export_data'
        ],
        'shop_admin': [
            'view_shop_inventory', 'view_parts_history', 'view_shop_records', 'edit_inventory', 'view_shop_intake', 'create_job', 'edit_job'
        ],
        'inventory_admin': [
            'view_shop_inventory', 'view_parts_history', 'edit_inventory'
        ],
        'mechanic': [
            'view_mechanic_workstation', 'create_job', 'edit_job'
        ]
    };

    const perms = ROLE_PERMISSIONS[userRole] || [];
    return perms.includes('*') || perms.includes(permission);
}

function renderRoleBasedNavigation() {
    const desktopContainer = document.getElementById('desktop-nav-container');
    const mobileContainer = document.getElementById('mobile-nav-container');
    if (!desktopContainer && !mobileContainer) return;

    let desktopHtml = '';
    let mobileHtml = '';

    const routes = window.APP_NAV_ROUTES || [];

    routes.forEach(groupConfig => {
        // Filter group items by user permissions
        const allowedItems = groupConfig.items.filter(item => hasPermission(item.permission));
        if (allowedItems.length === 0) return; // Skip empty groups for restricted roles

        // Build Desktop Group HTML
        desktopHtml += `
            <div>
                <span class="sidebar-section-title px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-2">${escapeHtml(groupConfig.group)}</span>
                <nav class="space-y-1">
        `;

        // Build Mobile Group HTML
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

    // Apply permission guards on action buttons (Create/Edit/Delete/Export)
    applyPermissionGuardsOnUI();
}

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

function canUserAccessRoute(routeId) {
    if (!routeId) return false;
    for (const group of APP_NAV_ROUTES) {
        for (const item of group.items) {
            if (item.id === routeId) {
                return hasPermission(item.permission);
            }
        }
    }
    return false;
}

function getDefaultUserRoute() {
    if (!AppStore.user) return 'auth-gate';
    if (AppStore.user.role === 'inventory_admin') return 'shop-inventory';
    if (AppStore.user.role === 'shop_admin') return 'shop-intake';
    if (AppStore.user.role === 'mechanic') return 'mechanic-review';
    if (hasPermission('view_stats')) return 'stats';
    return 'stats';
}

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

function openMobileDrawer() {
    const drawer = document.getElementById('mobile-nav-drawer');
    const overlay = document.getElementById('mobile-drawer-overlay');
    if (drawer && overlay) {
        drawer.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }
}

function closeMobileDrawer() {
    const drawer = document.getElementById('mobile-nav-drawer');
    const overlay = document.getElementById('mobile-drawer-overlay');
    if (drawer && overlay) {
        drawer.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

function toggleMobileDrawer() {
    const drawer = document.getElementById('mobile-nav-drawer');
    if (!drawer) return;
    if (drawer.classList.contains('-translate-x-full')) {
        openMobileDrawer();
    } else {
        closeMobileDrawer();
    }
}

function toggleProfileDropdown(e) {
    if (typeof window.toggleTopProfileDropdown === 'function') {
        window.toggleTopProfileDropdown(e);
    }
}
window.toggleProfileDropdown = toggleProfileDropdown;

function setActiveNavRoute(routeId) {
    document.querySelectorAll('.nav-link-item').forEach(el => {
        const isTarget = el.getAttribute('data-route') === routeId;
        if (isTarget) {
            el.classList.add('active-nav-link', 'bg-blue-600/10', 'text-blue-400', 'font-bold');
            el.classList.remove('text-slate-300');
        } else {
            el.classList.remove('active-nav-link', 'bg-blue-600/10', 'text-blue-400', 'font-bold');
            el.classList.add('text-slate-300');
        }
    });

    updateBreadcrumbHeader(routeId);
}

function updateBreadcrumbHeader(routeId) {
    const mobileContext = document.getElementById('mobile-page-context');
    const bSection = document.getElementById('breadcrumb-section-label');
    const bTab = document.getElementById('breadcrumb-tab-label');

    const roleSectionMap = {
        'super_admin': { id: 'Super Admin Portal', en: 'Super Admin Portal' },
        'manager': { id: 'Portal Manajerial', en: 'Managerial Portal' },
        'shop_admin': { id: 'Administrasi Cabang', en: 'Branch Administration' },
        'inventory_admin': { id: 'Manajemen Suku Cadang', en: 'Inventory Management' },
        'mechanic': { id: 'Stasiun Mekanik', en: 'Mechanic Workstation' }
    };

    const routeTitleMap = {
        'stats': { id: 'Metrik Kinerja Eksekutif', en: 'Executive Performance Metrics' },
        'reports': { id: 'Laporan & Analisis', en: 'Reports & Analytics' },
        'mechanic': { id: 'Lembar Kerja Mekanik', en: 'Mechanic Workstation' },
        'mechanic-review': { id: 'Verifikasi Daftar Periksa', en: 'Checklist Verification' },
        'mechanic-history': { id: 'Riwayat Perbaikan Saya', en: 'My Repair History' },
        'inventory': { id: 'Master Inventaris Global', en: 'Global Inventory Master' },
        'maintenance': { id: 'Catatan Perbaikan Rinci', en: 'Detailed Maintenance Records' },
        'vehicles': { id: 'Direktori Kendaraan', en: 'Vehicles Directory' },
        'customers': { id: 'Pelanggan Terdaftar', en: 'Registered Customers' },
        'management': { id: 'Kelola Master Data', en: 'Data Management Console' },
        'logs': { id: 'Log Audit Kepatuhan', en: 'Compliance Audit Logs' },
        'shop-inventory': { id: 'Stok Inventaris Cabang', en: 'Live Branch Stock' },
        'shop-intake': { id: 'Penerimaan & Servis', en: 'Intake & Repair Sheet' },
        'shop-active-job': { id: 'Detail Pekerjaan Aktif', en: 'Active Job Details' },
        'shop-maintenance': { id: 'Catatan Servis Cabang', en: 'Branch Maintenance Records' },
        'shop-review': { id: 'Riwayat Servis Pelanggan', en: 'Customer History Logs' },
        'shop-history': { id: 'Riwayat Pemakaian Part', en: 'Spare Part Usage History' }
    };

    const userRole = (window.AppStore && window.AppStore.user && window.AppStore.user.role) ? window.AppStore.user.role : '';
    const currentLang = (typeof getAppLanguage === 'function') ? getAppLanguage() : 'en';

    let sectionLabelText = 'Compliance Desk';
    if (roleSectionMap[userRole]) {
        sectionLabelText = roleSectionMap[userRole][currentLang] || roleSectionMap[userRole]['en'];
    }

    let tabLabelText = 'Overview';
    if (routeTitleMap[routeId]) {
        if (typeof routeTitleMap[routeId] === 'object') {
            tabLabelText = routeTitleMap[routeId][currentLang] || routeTitleMap[routeId]['en'];
        } else {
            tabLabelText = routeTitleMap[routeId];
        }
    }

    if (mobileContext) mobileContext.innerText = tabLabelText;
    if (bTab) bTab.innerText = tabLabelText;
    if (bSection) bSection.innerText = sectionLabelText;
}
window.updateBreadcrumbHeader = updateBreadcrumbHeader;

function onNavRouteClick(event, routeId) {
    if (event) event.preventDefault();

    // Guard route access
    if (!canUserAccessRoute(routeId)) {
        showToast('Access denied for your assigned role.', 'error');
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

    if (AppStore.user?.role === 'shop_admin' || AppStore.user?.role === 'inventory_admin') {
        showSection('shop-admin-workspace');

        if (routeId === 'shop-history') {
            setShopAdminTab('history');
        } else if (routeId === 'shop-intake') {
            setShopAdminTab('intake');
        } else if (routeId === 'shop-active-job') {
            setShopAdminTab('active-job');
        } else if (routeId === 'shop-maintenance') {
            setShopAdminTab('maintenance');
        } else if (routeId === 'shop-review') {
            setShopAdminTab('review');
        } else {
            setShopAdminTab('inventory');
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

    if (AppStore.user?.role === 'super_admin' || AppStore.user?.role === 'manager') {
        showSection('super-admin-workspace');
        if (typeof setSuperAdminTab === 'function') {
            setSuperAdminTab(routeId);
        }
        return;
    }
}

// ESC Key Event Listener for Keyboard Accessibility
window.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeMobileDrawer();
    }
});

// Initialize Sidebar State on Page Load
document.addEventListener('DOMContentLoaded', function () {
    initDesktopSidebarState();
});

// -------------------------------------------------------------
// Layout and Routing Manager
// -------------------------------------------------------------

function showSection(sectionId) {
    ['auth-gate', 'mechanic-workspace', 'shop-admin-workspace', 'super-admin-workspace'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });
    const target = document.getElementById(sectionId);
    if (target) target.classList.remove('hidden');

    // Adjust main alignment container classes
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
            topAppBar.classList.remove('flex', 'sm:flex', 'md:flex');
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
            topAppBar.classList.remove('hidden');
            topAppBar.classList.add('hidden', 'md:flex');
        }
    }
}

function renderDashboardLayout() {
    // Show Navigation Shell
    const sidebar = document.getElementById('app-sidebar');
    if (sidebar) sidebar.classList.remove('hidden');
    const mobileHeader = document.getElementById('mobile-nav-header');
    if (mobileHeader) mobileHeader.classList.remove('hidden');

    if (typeof updateProfilePictureUI === 'function') updateProfilePictureUI();
    initDesktopSidebarState();

    const name = (AppStore.user && (AppStore.user.display_name || AppStore.user.username)) || 'User';
    const roleName = (AppStore.user && AppStore.user.role)
        ? (AppStore.user.role === 'inventory_admin' ? 'INVENTORY ADMIN' : AppStore.user.role.toUpperCase().replace('_', ' '))
        : 'Role';

    const dName = document.getElementById('user-display-name');
    if (dName) dName.innerText = name;
    const dRole = document.getElementById('user-display-role');
    if (dRole) {
        dRole.innerText = roleName;
        dRole.className = "text-xs font-semibold px-2 py-0.5 rounded-full inline-block mt-0.5 ";
        if (AppStore.user.role === 'super_admin') {
            dRole.className += "bg-violet-950 text-violet-400 border border-violet-800/40";
        } else if (AppStore.user.role === 'manager') {
            dRole.className += "bg-cyan-950 text-cyan-400 border border-cyan-800/40";
        } else if (AppStore.user.role === 'shop_admin') {
            dRole.className += "bg-emerald-950 text-emerald-400 border border-emerald-800/40";
        } else if (AppStore.user.role === 'inventory_admin') {
            dRole.className += "bg-amber-950 text-amber-400 border border-amber-800/40";
        } else {
            dRole.className += "bg-blue-950 text-blue-400 border border-blue-800/40";
        }
    }

    const mName = document.getElementById('mobile-user-name');
    if (mName) mName.innerText = name;
    const mRole = document.getElementById('mobile-user-role');
    if (mRole) mRole.innerText = roleName;

    if (typeof updateDropdownUserRoleUI === 'function') {
        updateDropdownUserRoleUI('dropdown-user-name', 'dropdown-user-role');
        updateDropdownUserRoleUI('mobile-dropdown-user-name', 'mobile-dropdown-user-role');
    }

    // Dynamically render RBAC navigation links based on user permissions
    renderRoleBasedNavigation();

    // Restore saved active route from localStorage or default for user role
    const savedRoute = localStorage.getItem('sgpm_last_route');
    let targetRoute = getDefaultUserRoute();
    if (savedRoute && canUserAccessRoute(savedRoute)) {
        targetRoute = savedRoute;
    }
    onNavRouteClick(null, targetRoute);
}

function updateProfilePictureUI() {
    const icon = document.getElementById('profile-picture-icon');
    const img = document.getElementById('profile-picture-img');
    const mobileImg = document.getElementById('mobile-profile-img');
    const mobileIcon = document.getElementById('mobile-profile-icon');
    const mobileDropdownImg = document.getElementById('mobile-dropdown-profile-img');
    const mobileDropdownIcon = document.getElementById('mobile-dropdown-profile-icon');

    if (AppStore.user && AppStore.user.profile_picture) {
        const src = AppStore.user.profile_picture + '?t=' + new Date().getTime();
        if (icon) icon.classList.add('hidden');
        if (img) {
            img.src = src;
            img.classList.remove('hidden');
        }
        if (mobileIcon) mobileIcon.classList.add('hidden');
        if (mobileImg) {
            mobileImg.src = src;
            mobileImg.classList.remove('hidden');
        }
        if (mobileDropdownIcon) mobileDropdownIcon.classList.add('hidden');
        if (mobileDropdownImg) {
            mobileDropdownImg.src = src;
            mobileDropdownImg.classList.remove('hidden');
        }
    } else {
        if (icon) icon.classList.remove('hidden');
        if (img) {
            img.classList.add('hidden');
            img.src = '';
        }
        if (mobileIcon) mobileIcon.classList.remove('hidden');
        if (mobileImg) {
            mobileImg.classList.add('hidden');
            mobileImg.src = '';
        }
        if (mobileDropdownIcon) mobileDropdownIcon.classList.remove('hidden');
        if (mobileDropdownImg) {
            mobileDropdownImg.classList.add('hidden');
            mobileDropdownImg.src = '';
        }
    }
}

// -------------------------------------------------------------
// Mechanic Workflows
// -------------------------------------------------------------

async function loadMechanicDashboard() {
    const navBar = document.getElementById('mechanic-nav-tabs-bar');
    if (navBar) navBar.classList.remove('hidden');

    if (!AppStore.user.branch_id) {
        // If not checked in, force branch checkin block
        document.getElementById('mechanic-checkin-banner').classList.remove('hidden');
        document.getElementById('mechanic-active-banner').classList.add('hidden');
    } else {
        document.getElementById('mechanic-checkin-banner').classList.add('hidden');
        document.getElementById('mechanic-active-banner').classList.remove('hidden');

        const activeBranch = Array.isArray(AppStore.branches) ? AppStore.branches.find(b => b.id == AppStore.user.branch_id) : null;
        document.getElementById('mechanic-active-branch-name').innerText = activeBranch ? activeBranch.name : 'Unknown Station';

        // Fetch branch parts list to populate the spare part selector
        await fetchMechanicBranchParts();

        // Fetch dynamic checklists
        await fetchDynamicChecklists();
    }

    // Load any persisted draft data
    loadMechanicDraft();

    // Initialize active tab
    setMechanicTab(AppStore.mechanicActiveTab || 'form');
}

async function handleMechanicCheckin() {
    const branchId = document.getElementById('mechanic-branch-select').value;
    if (!branchId) {
        showToast('Please select a branch.', 'warning');
        return;
    }
    try {
        const data = await request('mechanic_check_in', { branch_id: branchId });
        AppStore.user.branch_id = branchId;
        showToast(data.message, 'success');
        loadMechanicDashboard();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function changeBranch() {
    if (AppStore.mechanicInProgress) {
        const proceed = confirm('Changing branch will delete unsaved data. Continue?');
        if (!proceed) return;
        // Reset mechanic state
        AppStore.mechanicInProgress = false;
        window.removeEventListener('beforeunload', handleBeforeUnload);
        localStorage.removeItem('mechanicDraft');
    }
    AppStore.user.branch_id = null;
    loadMechanicDashboard();
}

async function fetchMechanicBranchParts() {
    try {
        const userBranchId = AppStore.user ? AppStore.user.branch_id : null;
        const reqParams = userBranchId ? { branch_id: userBranchId } : {};
        const raw = await request('get_inventory', reqParams, 'GET');
        AppStore.mechanicBranchParts = Array.isArray(raw) ? raw : [];
        filterMechanicPartOptions();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function determineVehicleWarrantyCategory(activateDate, kmReached) {
    if (!activateDate) return 'Unclaimable / No Warranty';
    const d = new Date(activateDate);
    if (isNaN(d.getTime())) return 'Unclaimable / No Warranty';
    const now = new Date();
    const diffMonths = (now.getFullYear() - d.getFullYear()) * 12 + (now.getMonth() - d.getMonth());
    const km = parseInt(kmReached || 0);

    if (diffMonths <= 6 || km <= 10000) return 'Warranty A';
    if (diffMonths <= 12 || km <= 10000) return 'Warranty B';
    if (diffMonths <= 24 || km <= 20000) return 'Warranty C';
    return 'Unclaimable / No Warranty';
}
window.determineVehicleWarrantyCategory = determineVehicleWarrantyCategory;

function isPartClaimableForVehicle(vehicleCategory, partWarrantyCategory) {
    const order = {
        'Warranty A': 1,
        'Warranty B': 2,
        'Warranty C': 3,
        'Unclaimable / No Warranty': 4
    };
    const vLevel = order[vehicleCategory] || 4;
    const pLevel = order[partWarrantyCategory] || 4;
    if (pLevel === 4) return false;
    return vLevel <= pLevel;
}
window.isPartClaimableForVehicle = isPartClaimableForVehicle;

function getActiveVehicleWarrantyState() {
    const activePanel = (AppStore.shopAdminActiveTab === 'active-job')
        ? document.getElementById('shop-panel-active-job')
        : document.getElementById('shop-panel-intake');

    let activateDate = null;
    if (activePanel) {
        activateDate = activePanel.querySelector('#mech-display-activate-date')?.value || AppStore.activeVehicleActivateDate || null;
    } else {
        activateDate = getActiveEl('mech-display-activate-date')?.value || AppStore.activeVehicleActivateDate || null;
    }

    const kmVal = (activePanel ? activePanel.querySelector('#mech-km-reached') : getActiveEl('mech-km-reached'))?.value || 0;
    const vehicleCat = determineVehicleWarrantyCategory(activateDate, kmVal);
    return { activateDate, kmVal, vehicleCat };
}
window.getActiveVehicleWarrantyState = getActiveVehicleWarrantyState;

function onMechPartSelectChange(selectEl) {
    const activePanel = (AppStore.shopAdminActiveTab === 'active-job')
        ? document.getElementById('shop-panel-active-job')
        : document.getElementById('shop-panel-intake');
    
    if (!selectEl || !selectEl.value) {
        const claimedInput = (activePanel ? activePanel.querySelector('#mech-part-claimed-input') : getActiveEl('mech-part-claimed-input'));
        if (claimedInput) claimedInput.checked = false;
        return;
    }

    const partId = parseInt(selectEl.value);
    const partsCatalog = [...(AppStore.inventory || []), ...(AppStore.mechanicBranchParts || [])];
    const invItem = partsCatalog.find(i => i.id == partId);
    
    const { vehicleCat } = getActiveVehicleWarrantyState();
    const partWarrantyCat = invItem ? (invItem.warranty_category || 'Unclaimable / No Warranty') : 'Unclaimable / No Warranty';
    const isClaimable = isPartClaimableForVehicle(vehicleCat, partWarrantyCat);

    const claimedInput = (activePanel ? activePanel.querySelector('#mech-part-claimed-input') : getActiveEl('mech-part-claimed-input'));
    if (claimedInput) {
        claimedInput.checked = isClaimable;
    }
}
window.onMechPartSelectChange = onMechPartSelectChange;

function filterMechanicPartOptions() {
    const searchInput = getActiveEl('mech-part-search-input') || document.getElementById('mech-part-search-input');
    const query = (searchInput?.value || '').toLowerCase().trim();
    const data = AppStore.mechanicBranchParts || [];

    const { vehicleCat } = getActiveVehicleWarrantyState();

    const filtered = data.filter(p => {
        if (!query) return true;
        const sku = (p.sku || '').toLowerCase();
        const name = (p.part_name || '').toLowerCase();
        const desc = (p.description || '').toLowerCase();
        return sku.includes(query) || name.includes(query) || desc.includes(query);
    });

    const html = (filtered.length === 0)
        ? '<option value="">No matching spareparts found</option>'
        : '<option value="">-- Choose Sparepart --</option>' +
        filtered.map(p => {
            const descHint = p.description ? ` - ${escapeHtml(p.description)}` : '';
            const priceVal = (p.price !== undefined && p.price !== null) ? p.price : 0;
            const hasStock = (parseInt(p.available_qty || 0) > 0);
            
            const partWarranty = p.warranty_category || 'Unclaimable / No Warranty';
            const isClaimable = isPartClaimableForVehicle(vehicleCat, partWarranty);

            let styleAttr = '';
            let labelSuffix = ` [${partWarranty}]`;

            if (isClaimable) {
                // Purple bold text for claimable sparepart matching warranty conditions
                styleAttr = 'style="font-weight: bold; color: #a855f7;" class="font-bold text-purple-400"';
                labelSuffix += ' (CLAIMABLE)';
            } else if (hasStock) {
                styleAttr = 'style="font-weight: bold; color: #60a5fa;" class="font-bold text-blue-400"';
            } else {
                styleAttr = 'style="color: #94a3b8;" class="text-slate-400"';
            }

            return `<option value="${p.id}" ${styleAttr} data-warranty="${escapeHtml(partWarranty)}" data-sku="${escapeHtml(p.sku)}" data-name="${escapeHtml(p.part_name)}" data-price="${priceVal}" data-qty="${p.available_qty}" data-connected-service="${escapeHtml(p.connected_service || '')}">[Stock: ${p.available_qty}] ${escapeHtml(p.part_name)} (${escapeHtml(p.sku)})${labelSuffix}${descHint}</option>`;
        }).join('');

    document.querySelectorAll('#mech-part-select').forEach(select => {
        select.innerHTML = html;
    });
}

async function fetchDynamicChecklists() {
    try {
        const [issues, items, serviceOpts, otherOpts] = await Promise.all([
            request('get_common_issues', {}, 'GET'),
            request('get_mechanic_form_items', {}, 'GET'),
            request('get_service_options', {}, 'GET'),
            request('get_other_services', {}, 'GET')
        ]);

        const safeIssues = Array.isArray(issues) ? issues : [];
        const issuesHtml = safeIssues.map(issue => `
            <label class="flex items-center gap-2 cursor-pointer p-2 bg-slate-900/40 rounded border border-slate-800/60 hover:bg-slate-800/40 transition">
                <input type="checkbox" name="common_issue" value="${issue.name}"> ${issue.name}
            </label>
        `).join('');
        document.querySelectorAll('#mech-common-issues-container').forEach(c => c.innerHTML = issuesHtml);

        const safeItems = Array.isArray(items) ? items : [];
        const itemsHtml = safeItems.map(item => `
            <label class="flex items-center gap-2 cursor-pointer p-2 bg-slate-900/40 rounded border border-slate-800/60 hover:bg-slate-800/40 transition">
                <input type="checkbox" name="mechanic_form_item" value="${item.label}"> ${item.label}
            </label>
        `).join('');
        document.querySelectorAll('#mech-form-items-container').forEach(c => c.innerHTML = itemsHtml);

        // Populate dynamic Service Options dropdowns
        const safeServiceOpts = Array.isArray(serviceOpts) ? serviceOpts : [];
        const serviceHtml = '<option value="">-- Choose Service Option --</option>' +
            safeServiceOpts.map(s => `<option value="${escapeHtml(s.name)}" data-sku="${escapeHtml(s.sku || 'JASA-000')}" data-fee="${s.fee}">${escapeHtml(s.name)} (${formatIDR(s.fee)})</option>`).join('');
        document.querySelectorAll('#mech-service-select').forEach(sSelect => sSelect.innerHTML = serviceHtml);

        // Populate dynamic Other Services dropdowns
        const safeOtherOpts = Array.isArray(otherOpts) ? otherOpts : [];
        AppStore.allOtherServices = safeOtherOpts;
        const otherHtml = '<option value="">-- Choose Other Service --</option>' +
            safeOtherOpts.map(o => {
                const skuAttr = o.sku ? escapeHtml(o.sku) : 'SRV-OTH';
                const skuLabel = o.sku ? `[${escapeHtml(o.sku)}] ` : '';
                return `<option value="${escapeHtml(o.name)}" data-sku="${skuAttr}" data-fee="${o.fee}">${skuLabel}${escapeHtml(o.name)} (${formatIDR(o.fee)})</option>`;
            }).join('');
        document.querySelectorAll('#mech-other-service-select').forEach(oSelect => oSelect.innerHTML = otherHtml);
    } catch (err) {
        console.error("Error loading checklists/services: ", err);
    }
}

async function fetchMyRepairHistory() {
    try {
        const raw = await request('get_all_maintenance_records', {}, 'GET');
        const data = Array.isArray(raw) ? raw : [];
        const tbody = document.getElementById('mechanic-my-history-tbody');
        if (tbody) {
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-slate-500 font-semibold">No records found. Submit jobs to see them here!</td></tr>`;
                return;
            }
            tbody.innerHTML = data.map(r => {
                const dateStr = new Date(r.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                const issuesStr = [r.common_issues, r.other_issues].filter(Boolean).join(' | ') || 'N/A';

                let durationStr = 'N/A';
                if (r.start_time && r.end_time) {
                    const start = new Date(r.start_time);
                    const end = new Date(r.end_time);
                    const diffMs = end - start;
                    if (diffMs > 0) {
                        const diffMins = Math.round(diffMs / (1000 * 60));
                        durationStr = `${diffMins} mins`;
                    } else {
                        durationStr = '0 mins';
                    }
                }

                let partsCost = formatIDR(0);
                let partsDetails = 'None';
                if (r.parts && r.parts.length > 0) {
                    const totalCost = r.parts.reduce((acc, p) => acc + (parseFloat(p.price_at_use) * parseInt(p.quantity_used)), 0);
                    partsCost = formatIDR(totalCost);
                    partsDetails = r.parts.map(p => `${p.part_name} (x${p.quantity_used})`).join(', ');
                }

                return `
                            <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300">
                                <td class="p-4">${dateStr}</td>
                                <td class="p-4 font-bold text-slate-200">${r.customer_name}</td>
                                <td class="p-4 font-mono font-semibold text-slate-400">${r.license_plate}</td>
                                <td class="p-4 text-slate-300 font-semibold">${durationStr}</td>
                                <td class="p-4 max-w-xs truncate text-slate-300" title="${issuesStr}">${issuesStr}</td>
                                <td class="p-4 text-right">
                                    <span class="font-bold text-slate-200 block">${partsCost}</span>
                                    <span class="text-[10px] text-slate-400 block max-w-[150px] truncate mx-auto" title="${partsDetails}">${partsDetails}</span>
                                </td>
                            </tr>
                        `;
            }).join('');
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function setMechanicTab(tabName) {
    AppStore.mechanicActiveTab = tabName;
    let routeId = 'mechanic-form';
    if (tabName === 'review') routeId = 'mechanic-review';
    if (tabName === 'my-history') routeId = 'mechanic-history';
    localStorage.setItem('sgpm_last_route', routeId);
    showSection('mechanic-workspace');
    setActiveNavRoute(routeId);

    // Toggle active tabs
    const tabs = ['form', 'review', 'my-history'];
    tabs.forEach(name => {
        const el = document.getElementById(`mechanic-tab-${name}`);
        const btn = document.getElementById(`btn-mech-tab-${name}`);
        if (el) el.classList.add('hidden');
        if (btn) {
            btn.className = "flex-1 py-3 px-4 text-center font-bold text-xs rounded-lg transition duration-200 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60 flex items-center justify-center gap-2";
        }
    });

    const targetEl = document.getElementById(`mechanic-tab-${tabName}`);
    const targetBtn = document.getElementById(`btn-mech-tab-${tabName}`);
    if (targetEl) targetEl.classList.remove('hidden');
    if (targetBtn) {
        targetBtn.className = "flex-1 py-3 px-4 text-center font-bold text-xs rounded-lg transition duration-200 bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-lg shadow-blue-500/10 flex items-center justify-center gap-2";
    }

    if (tabName === 'form') {
        resetMechanicRepairForm();
    } else if (tabName === 'my-history') {
        fetchMyRepairHistory();
    }
}

async function resetMechanicRepairForm() {
    AppStore.activeIntakeRecordId = null;
    // Get next JOB ID
    try {
        const data = await request('get_next_job_id', {}, 'GET');
        document.getElementById('mech-job-id').innerText = data.formatted;
    } catch (err) {
        document.getElementById('mech-job-id').innerText = 'JOB-ERROR';
    }

    // Clear inputs
    document.getElementById('mech-search-query').value = '';
    document.getElementById('mech-selected-vehicle-id').value = '';
    document.getElementById('mech-display-name').value = '';
    if (document.getElementById('mech-display-phone')) document.getElementById('mech-display-phone').value = '';
    document.getElementById('mech-display-idcard').value = '';
    document.getElementById('mech-display-address').value = '';
    document.getElementById('mech-display-plate').value = '';
    document.getElementById('mech-display-type').value = '';
    document.getElementById('mech-display-frame').value = '';
    document.getElementById('mech-display-controller').value = '';
    document.getElementById('mech-km-reached').value = '';
    document.getElementById('mech-other-issues').value = '';
    if (document.getElementById('mech-assign-mechanic')) document.getElementById('mech-assign-mechanic').value = '';

    // Uncheck checkboxes
    document.querySelectorAll('input[name="common_issue"]').forEach(chk => chk.checked = false);
    document.querySelectorAll('input[name="mechanic_form_item"]').forEach(chk => chk.checked = false);

    // Reset accumulators and customer type
    AppStore.mechAccumulatedParts = [];
    AppStore.mechAccumulatedServices = [];
    AppStore.mechAccumulatedOtherServices = [];
    setMechanicCustomerType('internal');

    // Reset timer clock
    resetMechanicTimer();

    // Reset status tombol "Start Fixing & Save Data"
    const btnStartSave = document.getElementById('btn-mech-start-and-save');
    if (btnStartSave) {
        btnStartSave.disabled = false;
        btnStartSave.classList.remove('opacity-50', 'cursor-not-allowed');
        btnStartSave.innerHTML = '<i class="fa-solid fa-play-circle"></i> Start Fixing & Save Data';
    }
}

async function performMechanicCustomerLookup() {
    const queryEl = document.getElementById('mech-search-query');
    if (!queryEl) return;
    const query = queryEl.value.trim();
    if (!query) {
        showToast('Please enter Customer ID or ID Card Number.', 'warning');
        return;
    }

    try {
        const rawData = await request('lookup_customer_by_id_card', { query: query }, 'GET');

        // Normalize response if wrapped in an array or returned as collection
        const data = Array.isArray(rawData) ? rawData[0] : rawData;
        if (!data) {
            showToast('Customer data not found.', 'warning');
            return;
        }

        // Populate read-only inputs safely
        const nameEl = document.getElementById('mech-display-name');
        if (nameEl) nameEl.value = data.name || '';
        const phoneEl = document.getElementById('mech-display-phone');
        if (phoneEl) phoneEl.value = data.phone || 'N/A';
        const idcardEl = document.getElementById('mech-display-idcard');
        if (idcardEl) idcardEl.value = data.id_card_number || 'N/A';
        const addressEl = document.getElementById('mech-display-address');
        if (addressEl) addressEl.value = data.address || 'N/A';

        const statusSelect = document.getElementById('mech-customer-status');
        if (statusSelect) {
            const isInternal = (AppStore.mechCustomerType === 'internal');
            let targetStatus = 'Gomolis';
            if (data.customer_status && ['Gomolis', 'Operasional', 'Operational'].includes(data.customer_status)) {
                targetStatus = (data.customer_status === 'Operational') ? 'Operasional' : data.customer_status;
            } else if (isInternal) {
                targetStatus = (statusSelect.value && ['Gomolis', 'Operasional', 'Operational'].includes(statusSelect.value)) ? statusSelect.value : 'Gomolis';
            } else {
                targetStatus = data.customer_status || 'Retail';
            }
            statusSelect.value = targetStatus;
            if (window.onMechanicCustomerStatusChange) window.onMechanicCustomerStatusChange();
        }

        if (data.vehicles && Array.isArray(data.vehicles) && data.vehicles.length > 0) {
            const primaryVeh = data.vehicles[0];
            const vehIdEl = document.getElementById('mech-selected-vehicle-id');
            if (vehIdEl) vehIdEl.value = primaryVeh.vehicle_id || '';
            const plateEl = document.getElementById('mech-display-plate');
            if (plateEl) plateEl.value = primaryVeh.license_plate || '';
            const typeEl = document.getElementById('mech-display-type');
            if (typeEl) typeEl.value = primaryVeh.vehicle_type || 'N/A';
            const frameEl = document.getElementById('mech-display-frame');
            if (frameEl) frameEl.value = primaryVeh.frame_number || 'N/A';
            const ctrlEl = document.getElementById('mech-display-controller');
            if (ctrlEl) ctrlEl.value = primaryVeh.controller_number || 'N/A';
            const actDateEl = document.getElementById('mech-display-activate-date');
            if (actDateEl) actDateEl.value = primaryVeh.activate_date || primaryVeh.created_at || '';
            AppStore.activeVehicleActivateDate = primaryVeh.activate_date || primaryVeh.created_at || null;
            if (typeof filterMechanicPartOptions === 'function') filterMechanicPartOptions();
            showToast('Customer file loaded successfully.', 'success');

            showVehicleLastRepairPopup(primaryVeh.license_plate, primaryVeh.last_repair_time, primaryVeh.category_repairs);
        } else {
            const vehIdEl = document.getElementById('mech-selected-vehicle-id');
            if (vehIdEl) vehIdEl.value = '';
            const plateEl = document.getElementById('mech-display-plate');
            if (plateEl) plateEl.value = '';
            const typeEl = document.getElementById('mech-display-type');
            if (typeEl) typeEl.value = '';
            const frameEl = document.getElementById('mech-display-frame');
            if (frameEl) frameEl.value = '';
            const ctrlEl = document.getElementById('mech-display-controller');
            if (ctrlEl) ctrlEl.value = '';
            showToast('Customer found, but they have no registered vehicle.', 'warning');
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function formatDaysSince(lastRepairTime) {
    if (!lastRepairTime) return 'Never Repaired';
    const start = new Date(lastRepairTime);
    if (isNaN(start.getTime())) return 'Never Repaired';

    const end = new Date();
    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();
    let days = end.getDate() - start.getDate();

    if (days < 0) {
        months--;
        const prevMonth = new Date(end.getFullYear(), end.getMonth(), 0);
        days += prevMonth.getDate();
    }
    if (months < 0) {
        years--;
        months += 12;
    }

    if (years === 0 && months === 0 && days === 0) {
        return 'Today';
    }

    if (years === 0 && months === 0) {
        return `${days} Days`;
    }

    const parts = [];
    if (years > 0) parts.push(`${years} Years`);
    if (months > 0) parts.push(`${months} Months`);
    parts.push(`${days} Days`);
    return parts.join(' ');
}

function formatCategoryLastRepair(lastTime) {
    if (!lastTime) return 'Never Maintained';
    const d = new Date(lastTime);
    if (isNaN(d.getTime())) return 'Never Maintained';

    const now = new Date();
    const repairDate = new Date(d.getFullYear(), d.getMonth(), d.getDate());
    const currentDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());

    const diffMs = Math.max(0, currentDate - repairDate);
    const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return '1 day ago';
    return `${diffDays} days ago`;
}

function showVehicleLastRepairPopup(plate, lastRepairTime, categoryRepairs = null) {
    const elapsed = formatDaysSince(lastRepairTime);
    const catData = categoryRepairs || {};

    const tireText = formatCategoryLastRepair(catData.tire);
    const shockText = formatCategoryLastRepair(catData.shock);
    const bearingText = formatCategoryLastRepair(catData.bearing);
    const brakeText = formatCategoryLastRepair(catData.brake);

    const tireDateSub = catData.tire ? new Date(catData.tire).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '';
    const shockDateSub = catData.shock ? new Date(catData.shock).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '';
    const bearingDateSub = catData.bearing ? new Date(catData.bearing).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '';
    const brakeDateSub = catData.brake ? new Date(catData.brake).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '';

    const modal = document.createElement('div');
    modal.className = "fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4";
    modal.innerHTML = `
                <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative text-center border border-slate-800">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-blue-600"></div>
                    <div class="w-12 h-12 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center mx-auto mb-3 text-xl">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-100 mb-1">Vehicle Last Repaired</h4>
                    <p class="text-xs text-slate-400 mb-4">License Plate: <span class="font-mono text-blue-400 font-bold text-sm">${plate}</span></p>
                    
                    <!-- Overall Last Repair Banner -->
                    <div class="bg-slate-950/80 border border-slate-800/80 rounded-xl p-3.5 mb-4">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block mb-1">Overall Time Since Last Repair</span>
                        <span class="text-lg font-mono font-bold text-slate-200">${elapsed}</span>
                        ${lastRepairTime ? `<span class="text-[10px] text-slate-400 block mt-1">Last Date: ${new Date(lastRepairTime).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</span>` : ''}
                    </div>

                    <!-- 2x2 Category Last Repair Grid -->
                    <div class="mb-5 text-left">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2.5 text-center">Category Parts Maintenance History</span>
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Category 1: Tire -->
                            <div class="bg-slate-900/90 border border-slate-800/90 rounded-xl p-3 flex items-center gap-3 shadow-md">
                                <div class="w-9 h-9 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-dharmachakra"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tire</span>
                                    <span class="text-xs font-mono font-bold ${catData.tire ? 'text-cyan-400' : 'text-slate-500'} block truncate">${tireText}</span>
                                    ${tireDateSub ? `<span class="text-[9px] text-slate-500 block font-mono">${tireDateSub}</span>` : ''}
                                </div>
                            </div>

                            <!-- Category 2: Shock Breaker -->
                            <div class="bg-slate-900/90 border border-slate-800/90 rounded-xl p-3 flex items-center gap-3 shadow-md">
                                <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Shock Breaker</span>
                                    <span class="text-xs font-mono font-bold ${catData.shock ? 'text-amber-400' : 'text-slate-500'} block truncate">${shockText}</span>
                                    ${shockDateSub ? `<span class="text-[9px] text-slate-500 block font-mono">${shockDateSub}</span>` : ''}
                                </div>
                            </div>

                            <!-- Category 3: Bearing -->
                            <div class="bg-slate-900/90 border border-slate-800/90 rounded-xl p-3 flex items-center gap-3 shadow-md">
                                <div class="w-9 h-9 rounded-lg bg-violet-500/10 text-violet-400 flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-gear"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bearing</span>
                                    <span class="text-xs font-mono font-bold ${catData.bearing ? 'text-violet-400' : 'text-slate-500'} block truncate">${bearingText}</span>
                                    ${bearingDateSub ? `<span class="text-[9px] text-slate-500 block font-mono">${bearingDateSub}</span>` : ''}
                                </div>
                            </div>

                            <!-- Category 4: Brake Pad -->
                            <div class="bg-slate-900/90 border border-slate-800/90 rounded-xl p-3 flex items-center gap-3 shadow-md">
                                <div class="w-9 h-9 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Brake Pad</span>
                                    <span class="text-xs font-mono font-bold ${catData.brake ? 'text-rose-400' : 'text-slate-500'} block truncate">${brakeText}</span>
                                    ${brakeDateSub ? `<span class="text-[9px] text-slate-500 block font-mono">${brakeDateSub}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button onclick="this.parentElement.parentElement.remove()" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition shadow-lg shadow-blue-500/20">
                        Confirm & Continue
                    </button>
                </div>
            `;
    document.body.appendChild(modal);
}

function addPartToMechAccumulator() {
    const select = getActiveEl('mech-part-select') || document.getElementById('mech-part-select');
    const qtyInput = getActiveEl('mech-part-qty') || document.getElementById('mech-part-qty');

    if (!select || !select.value) {
        showToast('Please select a sparepart.', 'warning');
        return;
    }

    const partId = parseInt(select.value);
    const qty = parseInt(qtyInput ? qtyInput.value : 1);

    if (isNaN(qty) || qty <= 0) {
        showToast('Please enter a valid quantity.', 'warning');
        return;
    }

    if (!Array.isArray(AppStore.mechAccumulatedParts)) {
        AppStore.mechAccumulatedParts = [];
    }

    const selectedOpt = select.options[select.selectedIndex];
    const name = selectedOpt.getAttribute('data-name');
    const sku = selectedOpt.getAttribute('data-sku');
    const maxAvailable = parseInt(selectedOpt.getAttribute('data-qty'));
    const connectedService = selectedOpt.getAttribute('data-connected-service');

    if (AppStore.mechAccumulatedParts.length >= 25 && !AppStore.mechAccumulatedParts.find(p => p.inventory_id === partId)) {
        showToast('Maximum of 25 distinct parts checklist items exceeded.', 'warning');
        return;
    }

    const existing = AppStore.mechAccumulatedParts.find(p => p.inventory_id === partId);
    const currentAccumulatedQty = existing ? existing.qty : 0;

    if (currentAccumulatedQty + qty > maxAvailable) {
        showToast(`Quantity exceeds available stock. Available: ${maxAvailable}`, 'warning');
        return;
    }

    const status = getActiveCustomerStatus();
    const showChargedOpt = isChargedOptionStatus(status);

    // 1. Ambil status Charged & Claimed dari checkbox Sparepart
    const isChargedInput = showChargedOpt ? !!getActiveEl('mech-part-charged-input')?.checked : true;
    const isClaimedInput = !!getActiveEl('mech-part-claimed-input')?.checked;

    const partsCatalog = [...(AppStore.inventory || []), ...(AppStore.mechanicBranchParts || [])];
    const invItem = partsCatalog.find(i => i.id == partId);
    const attrPrice = selectedOpt.getAttribute('data-price');
    const itemPrice = (attrPrice !== null && !isNaN(parseFloat(attrPrice))) ? parseFloat(attrPrice) : (invItem ? parseFloat(invItem.price || 0) : 0);
    const warrantyCategory = invItem ? (invItem.warranty_category || 'Unclaimable / No Warranty') : 'Unclaimable / No Warranty';

    if (existing) {
        existing.qty += qty;
        existing.price = itemPrice;
        existing.is_charged = isChargedInput;
        existing.is_claimed = isClaimedInput;
        existing.warranty_category = warrantyCategory;
    } else {
        AppStore.mechAccumulatedParts.push({
            inventory_id: partId,
            part_name: name,
            sku: sku,
            qty: qty,
            price: itemPrice,
            is_charged: isChargedInput,
            is_claimed: isClaimedInput,
            warranty_category: warrantyCategory
        });
    }

    // 2. AUTO-ADD CONNECTED SERVICE TO ACCUMULATOR (Add 'qty' times per unit added)
    if (connectedService && connectedService.trim() !== '') {
        const sSelect = document.getElementById('mech-service-select');
        let matchedOption = null;

        if (sSelect) {
            for (let i = 0; i < sSelect.options.length; i++) {
                if (sSelect.options[i].value.toLowerCase().trim() === connectedService.toLowerCase().trim()) {
                    matchedOption = sSelect.options[i];
                    break;
                }
            }
        }

        if (!Array.isArray(AppStore.mechAccumulatedServices)) {
            AppStore.mechAccumulatedServices = [];
        }

        const serviceSku = matchedOption ? (matchedOption.getAttribute('data-sku') || 'JASA-AUTO') : 'JASA-AUTO';
        const serviceFee = matchedOption ? parseFloat(matchedOption.getAttribute('data-fee') || 0) : 0;
        const isServiceCharged = isChargedInput;

        const serviceCheckbox = document.getElementById('mech-service-charged-input');
        if (serviceCheckbox) {
            serviceCheckbox.checked = isServiceCharged;
        }

        for (let sIdx = 0; sIdx < qty; sIdx++) {
            AppStore.mechAccumulatedServices.push({
                id: Date.now() + Math.random() + sIdx,
                name: connectedService,
                sku: serviceSku,
                fee: serviceFee,
                is_charged: isServiceCharged
            });
        }

        renderMechServicesAccumulator();
        showToast(`Added ${qty} unit(s) of connected service "${connectedService}" (${isServiceCharged ? 'Charged' : 'Free'}).`, 'info');
    }

    qtyInput.value = 1;
    select.value = '';
    renderMechAccumulatedParts();
}

function removeMechAccumulatedPart(inventoryId) {
    if (!Array.isArray(AppStore.mechAccumulatedParts)) return;

    const partToRemove = AppStore.mechAccumulatedParts.find(p => p.inventory_id == inventoryId);
    AppStore.mechAccumulatedParts = AppStore.mechAccumulatedParts.filter(p => p.inventory_id != inventoryId);

    if (partToRemove) {
        const partsCatalog = [...(AppStore.inventory || []), ...(AppStore.mechanicBranchParts || [])];
        const invItem = partsCatalog.find(i => i.id == inventoryId);
        const connectedService = invItem ? invItem.connected_service : null;

        if (connectedService && connectedService.trim() !== '') {
            if (Array.isArray(AppStore.mechAccumulatedServices)) {
                let removedCount = 0;
                const qtyToRemove = partToRemove.qty || 1;
                AppStore.mechAccumulatedServices = AppStore.mechAccumulatedServices.filter(s => {
                    if (removedCount < qtyToRemove && (s.name || '').toLowerCase().trim() === connectedService.toLowerCase().trim()) {
                        removedCount++;
                        return false;
                    }
                    return true;
                });
                if (typeof renderMechServicesAccumulator === 'function') renderMechServicesAccumulator();
            }
        }
    }
}

function togglePartClaimed(inventoryId, isClaimed) {
    if (!Array.isArray(AppStore.mechAccumulatedParts)) return;
    const item = AppStore.mechAccumulatedParts.find(p => p.inventory_id == inventoryId);
    if (item) {
        item.is_claimed = !!isClaimed;
        renderMechAccumulatedParts();
    }
}
window.togglePartClaimed = togglePartClaimed;

// Service Options & Other Services Accumulators
AppStore.mechAccumulatedServices = [];
AppStore.mechAccumulatedOtherServices = [];
function getActiveEl(id) {
    const activePanel = document.querySelector('#shop-panel-active-job:not(.hidden)') || document.querySelector('#shop-panel-intake:not(.hidden)');
    if (activePanel) {
        const el = activePanel.querySelector('#' + id);
        if (el) return el;
    }
    return document.getElementById(id);
}
window.getActiveEl = getActiveEl;

function setMechanicCustomerType(type) {
    AppStore.mechCustomerType = type;

    // Simpan nilai tipe customer ke hidden input panel aktif
    const custTypeInput = getActiveEl('mech-customer-type') || document.getElementById('mech-customer-type');
    if (custTypeInput) {
        custTypeInput.value = type;
    }

    const btnExt = getActiveEl('btn-cust-type-external');
    const btnInt = getActiveEl('btn-cust-type-internal');
    const lookupSection = getActiveEl('mech-lookup-section');
    const statusSelect = getActiveEl('mech-customer-status');

    if (type === 'external') {
        if (btnExt) {
            btnExt.classList.remove('hidden');
            btnExt.className = 'flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 bg-blue-600 text-white shadow-md flex items-center justify-center';
        }
        if (btnInt) {
            btnInt.classList.remove('hidden');
            btnInt.className = 'flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 text-slate-400 hover:text-slate-200 flex items-center justify-center';
        }
        if (lookupSection) lookupSection.classList.add('hidden');

        if (statusSelect) {
            const curVal = statusSelect.value;
            statusSelect.innerHTML = `
                <option value="Retail">Retail</option>
                <option value="Sales">Sales</option>
                <option value="Cocoride">Cocoride</option>
                <option value="TPI">TPI</option>
            `;
            if (['Retail', 'Sales', 'Cocoride', 'TPI'].includes(curVal)) {
                statusSelect.value = curVal;
            }
        }
    } else {
        if (btnExt) {
            btnExt.classList.remove('hidden');
            btnExt.className = 'flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 text-slate-400 hover:text-slate-200 flex items-center justify-center';
        }
        if (btnInt) {
            btnInt.classList.remove('hidden');
            btnInt.className = 'flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 bg-blue-600 text-white shadow-md flex items-center justify-center';
        }
        if (lookupSection) lookupSection.classList.remove('hidden');

        if (statusSelect) {
            const curVal = statusSelect.value;
            statusSelect.innerHTML = `
                <option value="Gomolis">Gomolis</option>
                <option value="Operasional">Operasional</option>
            `;
            if (['Gomolis', 'Operasional', 'Operational'].includes(curVal)) {
                statusSelect.value = (curVal === 'Operational') ? 'Operasional' : curVal;
            } else {
                statusSelect.value = 'Gomolis';
            }
        }
    }

    if (window.onMechanicCustomerStatusChange) window.onMechanicCustomerStatusChange();
}

window.setMechanicCustomerType = setMechanicCustomerType;

function isChargedOptionStatus(status) {
    if (!status) return true;
    const clean = String(status).trim().toLowerCase();
    if (['gomolis', 'gomolis-b', 'operational', 'fleet', 'corporate', 'warranty', 'insurance', 'internal'].includes(clean)) {
        return true;
    }
    return !['retail', 'sales'].includes(clean);
}

function getActiveCustomerStatus() {
    const activePanel = (AppStore.shopAdminActiveTab === 'active-job')
        ? document.getElementById('shop-panel-active-job')
        : document.getElementById('shop-panel-intake');

    if (activePanel) {
        const val = activePanel.querySelector('#active-job-customer-status')?.value || 
                    activePanel.querySelector('#mech-customer-status')?.value;
        if (val && val.trim()) return val.trim();
    }

    const fallbackVal = document.getElementById('active-job-customer-status')?.value || 
                        document.getElementById('mech-customer-status')?.value || 
                        'Retail';
    return fallbackVal.trim();
}
window.getActiveCustomerStatus = getActiveCustomerStatus;

function isEditableCustomerStatus(status) {
    if (AppStore.shopAdminActiveTab === 'active-job') return false;
    return AppStore.mechCustomerType === 'external';
}

window.isChargedOptionStatus = isChargedOptionStatus;
window.isEditableCustomerStatus = isEditableCustomerStatus;

function updateMechanicCustomerFieldsState() {
    // 1. Dapatkan status customer & panel yang sedang aktif
    const status = getActiveCustomerStatus();

    // Status editable untuk data profil/biodata pelanggan & kendaraan
    const editable = isEditableCustomerStatus(status);

    const badge = getActiveEl('mech-editable-mode-badge');
    if (badge) badge.classList.toggle('hidden', !editable);

    // 2. Field profil customer/kendaraan yang dikontrol berdasarkan mode pencarian
    const profileFields = [
        'mech-display-name',
        'mech-display-phone',
        'mech-display-idcard',
        'mech-display-address',
        'mech-display-plate',
        'mech-display-type',
        'mech-display-frame',
        'mech-display-controller'
    ];

    profileFields.forEach(id => {
        const el = getActiveEl(id);
        if (el) {
            el.disabled = !editable;
            if (editable) {
                el.classList.remove('bg-slate-950', 'text-slate-100', 'border-slate-700', 'cursor-not-allowed');
                el.classList.add('bg-white', 'text-slate-900', 'border-slate-300', 'focus:border-blue-500');
            } else {
                el.classList.remove('bg-white', 'text-slate-900', 'border-slate-300', 'focus:border-blue-500');
                el.classList.add('bg-slate-950', 'text-slate-100', 'border-slate-700', 'cursor-not-allowed');
            }
        }
    });

    // 3. Pastikan Assigned Mechanic dan Customer Status SELALU EDITABLE di SUB TAB 3 (Intake)
    if (AppStore.shopAdminActiveTab !== 'active-job') {
        const mechanicSelect = getActiveEl('mech-assign-mechanic');
        if (mechanicSelect) {
            mechanicSelect.disabled = false;
            mechanicSelect.classList.remove('cursor-not-allowed', 'opacity-50');
        }

        const statusSelect = getActiveEl('mech-customer-status');
        if (statusSelect) {
            statusSelect.disabled = false;
            statusSelect.classList.remove('cursor-not-allowed', 'opacity-50');
        }
    }
}
window.updateMechanicCustomerFieldsState = updateMechanicCustomerFieldsState;

window.onMechanicCustomerStatusChange = function () {
    const statusVal = getActiveCustomerStatus();
    const showChargedOpt = isChargedOptionStatus(statusVal);

    document.querySelectorAll('#mech-part-charged-input-wrapper').forEach(el => el.classList.toggle('hidden', !showChargedOpt));
    document.querySelectorAll('#mech-service-charged-input-wrapper').forEach(el => el.classList.toggle('hidden', !showChargedOpt));
    document.querySelectorAll('#mech-other-service-charged-input-wrapper').forEach(el => el.classList.toggle('hidden', !showChargedOpt));

    const cleanStatus = String(statusVal).trim().toLowerCase();
    if (['gomolis', 'gomolis-b', 'operational'].includes(cleanStatus)) {
        document.querySelectorAll('#mech-part-charged-input').forEach(chk => { chk.checked = false; });
        document.querySelectorAll('#mech-service-charged-input').forEach(chk => { chk.checked = false; });
        document.querySelectorAll('#mech-other-service-charged-input').forEach(chk => { chk.checked = false; });
    }

    updateMechanicCustomerFieldsState();
    if (typeof renderMechAccumulatedParts === 'function') renderMechAccumulatedParts();
    if (typeof renderMechServicesAccumulator === 'function') renderMechServicesAccumulator();
    if (typeof renderMechOtherServicesAccumulator === 'function') renderMechOtherServicesAccumulator();
    if (typeof updateEstimatedGrandTotal === 'function') updateEstimatedGrandTotal();
};

window.onMechPartChargedInputToggle = function (isChecked) {
    const serviceChks = document.querySelectorAll('#mech-service-charged-input');
    serviceChks.forEach(chk => { chk.checked = isChecked; });
};

window.togglePartCharged = function (partId, isCharged) {
    const p = (AppStore.mechAccumulatedParts || []).find(item => item.inventory_id === partId);
    if (p) {
        p.is_charged = isCharged;

        // Sync connected service options in the services accumulator
        const partsCatalog = [...(AppStore.inventory || []), ...(AppStore.mechanicBranchParts || [])];
        const invItem = partsCatalog.find(i => i.id == partId);
        const connectedService = invItem ? invItem.connected_service : null;

        if (connectedService && connectedService.trim() !== '') {
            (AppStore.mechAccumulatedServices || []).forEach(s => {
                if ((s.name || '').toLowerCase().trim() === connectedService.toLowerCase().trim()) {
                    s.is_charged = isCharged;
                }
            });
            renderMechServicesAccumulator();
        }

        renderMechAccumulatedParts();
    }
};

window.toggleServiceCharged = function (id, isCharged) {
    const s = (AppStore.mechAccumulatedServices || []).find(item => item.id === id);
    if (s) {
        s.is_charged = isCharged;
        renderMechServicesAccumulator();
    }
};

window.toggleOtherServiceCharged = function (id, isCharged) {
    const o = (AppStore.mechAccumulatedOtherServices || []).find(item => item.id === id);
    if (o) {
        o.is_charged = isCharged;
        renderMechOtherServicesAccumulator();
    }
};

function addServiceToMechAccumulator() {
    const select = getActiveEl('mech-service-select') || document.getElementById('mech-service-select');
    if (!select || !select.value) {
        showToast('Please select a service option.', 'warning');
        return;
    }
    const name = select.value;
    const selectedOpt = select.options[select.selectedIndex];
    const sku = selectedOpt.getAttribute('data-sku') || 'JASA-000';
    const fee = parseFloat(selectedOpt.getAttribute('data-fee') || 0);

    const status = getActiveCustomerStatus();
    const showChargedOpt = isChargedOptionStatus(status);
    const isChargedInput = showChargedOpt ? !!getActiveEl('mech-service-charged-input')?.checked : true;

    AppStore.mechAccumulatedServices.push({
        id: Date.now() + Math.random(),
        name: name,
        sku: sku,
        fee: fee,
        is_charged: isChargedInput
    });

    select.value = '';
    renderMechServicesAccumulator();
}

function removeMechAccumulatedService(id) {
    AppStore.mechAccumulatedServices = AppStore.mechAccumulatedServices.filter(s => s.id !== id);
    renderMechServicesAccumulator();
}

function renderMechServicesAccumulator() {
    const container = getActiveEl('mech-services-accumulator-list') || getActiveEl('mech-accumulated-services-list');
    if (!container) return;
    const customerStatus = getActiveCustomerStatus();
    const showChargedOpt = isChargedOptionStatus(customerStatus);

    if (!Array.isArray(AppStore.mechAccumulatedServices) || AppStore.mechAccumulatedServices.length === 0) {
        container.innerHTML = '<span class="text-slate-600 text-[10px] block text-center py-2">No service options added.</span>';
    } else {
        container.innerHTML = AppStore.mechAccumulatedServices.map(s => {
            const isCharged = showChargedOpt ? !!s.is_charged : true;

            let feeDisplay = `<span class="font-bold font-mono text-emerald-400">${formatIDR(s.fee)}</span>`;
            if (showChargedOpt) {
                if (isCharged) {
                    feeDisplay = `<span class="font-bold font-mono text-cyan-400">${formatIDR(s.fee)}</span>`;
                } else {
                    feeDisplay = `<span class="font-bold font-mono text-slate-500">${formatIDR(0)}</span> <span class="text-[9px] text-slate-600 line-through">${formatIDR(s.fee)}</span>`;
                }
            }

            const chargedOptHtml = showChargedOpt ? `
                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs select-none">
                            <input type="checkbox" ${s.is_charged ? 'checked' : ''} onchange="toggleServiceCharged(${s.id}, this.checked)" class="w-4 h-4 accent-cyan-500 rounded border-slate-700 bg-slate-900 focus:ring-0 cursor-pointer">
                            <span class="${s.is_charged ? 'text-cyan-400 font-bold' : 'text-slate-400'}">Charged</span>
                        </label>
                    ` : '';

            const displayName = s.name || s.service_name || 'Service Option';
            const serviceLabelHtml = `${escapeHtml(displayName)}`;

            return `
                        <div class="flex items-center justify-between py-1 border-b border-slate-800/40 text-slate-300">
                            <span>${serviceLabelHtml}</span>
                            <div class="flex items-center gap-3">
                                ${feeDisplay}
                                ${chargedOptHtml}
                                <button type="button" onclick="removeMechAccumulatedService(${s.id})" class="text-rose-400 hover:text-rose-300 font-bold px-1"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>
                    `;
        }).join('');
    }
    updateEstimatedGrandTotal();
}

function addOtherServiceToMechAccumulator() {
    const select = getActiveEl('mech-other-service-select');
    if (!select || !select.value) {
        showToast('Please select an other service option.', 'warning');
        return;
    }
    const category = select.value;
    const selectedOpt = select.options[select.selectedIndex];
    const fee = parseFloat(selectedOpt.getAttribute('data-fee') || 0);
    const sku = selectedOpt.getAttribute('data-sku') || 'SRV-OTH';

    const status = getActiveCustomerStatus();
    const showChargedOpt = isChargedOptionStatus(status);
    const isChargedInput = showChargedOpt ? !!getActiveEl('mech-other-service-charged-input')?.checked : true;

    AppStore.mechAccumulatedOtherServices.push({
        id: Date.now() + Math.random(),
        category: category,
        sku: sku,
        fee: fee,
        is_charged: isChargedInput,
        is_other_service: true
    });

    select.value = '';
    renderMechOtherServicesAccumulator();
}

function removeMechAccumulatedOtherService(id) {
    AppStore.mechAccumulatedOtherServices = AppStore.mechAccumulatedOtherServices.filter(o => o.id !== id);
    renderMechOtherServicesAccumulator();
}

function renderMechOtherServicesAccumulator() {
    const container = getActiveEl('mech-other-services-accumulator-list') || getActiveEl('mech-accumulated-other-services-list');
    if (!container) return;

    const customerStatus = getActiveCustomerStatus();
    const showChargedOpt = isChargedOptionStatus(customerStatus);

    if (!Array.isArray(AppStore.mechAccumulatedOtherServices) || AppStore.mechAccumulatedOtherServices.length === 0) {
        container.innerHTML = '<span class="text-slate-600 text-[10px] block text-center py-2">No other services added.</span>';
    } else {
        container.innerHTML = AppStore.mechAccumulatedOtherServices.map(o => {
            const isCharged = showChargedOpt ? !!o.is_charged : true;

            let feeDisplay = `<span class="font-bold font-mono text-amber-400">${formatIDR(o.fee)}</span>`;
            if (showChargedOpt) {
                if (isCharged) {
                    feeDisplay = `<span class="font-bold font-mono text-amber-400">${formatIDR(o.fee)}</span>`;
                } else {
                    feeDisplay = `<span class="font-bold font-mono text-slate-500">${formatIDR(0)}</span> <span class="text-[9px] text-slate-600 line-through">${formatIDR(o.fee)}</span>`;
                }
            }

            const chargedOptHtml = showChargedOpt ? `
                <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs select-none">
                    <input type="checkbox" ${o.is_charged ? 'checked' : ''} onchange="toggleOtherServiceCharged(${o.id}, this.checked)" class="w-4 h-4 accent-amber-500 rounded border-slate-700 bg-slate-900 focus:ring-0 cursor-pointer">
                    <span class="${o.is_charged ? 'text-amber-400 font-bold' : 'text-slate-400'}">Charged</span>
                </label>
            ` : '';
            const skuStr = o.sku || 'FEES-MISC';
            const categoryLabelHtml = `<strong class="font-mono text-amber-400 mr-1">[${escapeHtml(skuStr)}]</strong>${escapeHtml(o.category)}`;
            return `
                <div class="flex items-center justify-between py-1 border-b border-slate-800/40 text-slate-300">
                    <span><i class="fa-solid fa-circle-notch text-amber-400 text-[8px] mr-1.5"></i>${categoryLabelHtml}</span>
                    <div class="flex items-center gap-3">
                        ${feeDisplay}
                        ${chargedOptHtml}
                        <button type="button" onclick="removeMechAccumulatedOtherService(${o.id})" class="text-rose-400 hover:text-rose-300 font-bold px-1"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
            `;
        }).join('');
    }
    updateEstimatedGrandTotal();
}

function updateEstimatedGrandTotal() {
    const activePanel = (AppStore.shopAdminActiveTab === 'active-job')
        ? document.getElementById('shop-panel-active-job')
        : document.getElementById('shop-panel-intake');

    if (!activePanel) return;

    const customerStatus = activePanel.querySelector('#mech-customer-status')?.value || activePanel.querySelector('#active-job-customer-status')?.value || getActiveEl('active-job-customer-status')?.value || 'Retail';
    const showChargedOpt = isChargedOptionStatus(customerStatus);

    const servicesFee = (AppStore.mechAccumulatedServices || []).reduce((acc, s) => {
        const isCharged = showChargedOpt ? !!s.is_charged : true;
        return acc + (isCharged ? (parseFloat(s.fee) || 0) : 0);
    }, 0);

    const otherFee = (AppStore.mechAccumulatedOtherServices || []).reduce((acc, o) => {
        const isCharged = showChargedOpt ? !!o.is_charged : true;
        return acc + (isCharged ? (parseFloat(o.fee) || 0) : 0);
    }, 0);

    let partsFee = 0;
    const partsCatalog = [...(AppStore.inventory || []), ...(AppStore.mechanicBranchParts || [])];

    (AppStore.mechAccumulatedParts || []).forEach(p => {
        const invItem = partsCatalog.find(i => i.id == p.inventory_id);
        const price = (parseFloat(p.price) > 0) ? parseFloat(p.price) : (invItem ? parseFloat(invItem.price || 0) : 0);
        let effectivePrice = showChargedOpt ? (p.is_charged ? price : 0) : price;
        if (p.is_claimed) {
            effectivePrice = 0;
        }
        partsFee += effectivePrice * (parseInt(p.qty) || 0);
    });

    const grandTotal = servicesFee + otherFee + partsFee;

    const sEl = activePanel.querySelector('#summary-services-fee');
    const oEl = activePanel.querySelector('#summary-other-fee');
    const pEl = activePanel.querySelector('#summary-parts-fee');
    const gEl = activePanel.querySelector('#summary-grand-total');

    if (sEl) sEl.innerText = formatIDR(servicesFee);
    if (oEl) oEl.innerText = formatIDR(otherFee);
    if (pEl) pEl.innerText = formatIDR(partsFee);
    if (gEl) gEl.innerText = formatIDR(grandTotal);
}

function renderMechAccumulatedParts() {
    // Tentukan kontainer induk berdasarkan sub-tab yang sedang aktif
    const activePanel = (AppStore.shopAdminActiveTab === 'active-job')
        ? document.getElementById('shop-panel-active-job')
        : document.getElementById('shop-panel-intake');

    if (!activePanel) return;

    const tbody = activePanel.querySelector('#mech-parts-accumulator-tbody');
    if (!tbody) return;

    const totalSpan = activePanel.querySelector('#mech-accumulated-total-count');
    const customerStatus = activePanel.querySelector('#mech-customer-status')?.value || activePanel.querySelector('#active-job-customer-status')?.value || getActiveEl('active-job-customer-status')?.value || 'Retail';
    const showChargedOpt = isChargedOptionStatus(customerStatus);

    const theadTr = activePanel.querySelector('#mech-parts-accumulator-thead-tr');
    if (theadTr) {
        if (showChargedOpt) {
            theadTr.innerHTML = `
                <th class="pb-1 font-semibold text-[10px] uppercase">Part SKU & Name</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-24 text-right">Price</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-20 text-center">Charged</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-20 text-center">Claimed</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right">Qty</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right">Delete</th>
            `;
        } else {
            theadTr.innerHTML = `
                <th class="pb-1 font-semibold text-[10px] uppercase">Part SKU & Name</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-24 text-right">Price</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-20 text-center">Claimed</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right">Qty</th>
                <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right">Delete</th>
            `;
        }
    }

    if (!Array.isArray(AppStore.mechAccumulatedParts) || AppStore.mechAccumulatedParts.length === 0) {
        const colSpan = showChargedOpt ? 6 : 5;
        tbody.innerHTML = `
            <tr>
                <td colspan="${colSpan}" class="py-3 text-center text-slate-600 text-[10px]">No spareparts accumulated.</td>
            </tr>
        `;
        if (totalSpan) totalSpan.innerText = '0';
        updateEstimatedGrandTotal();
        return;
    }

    const partsCatalog = [...(AppStore.inventory || []), ...(AppStore.mechanicBranchParts || [])];

    tbody.innerHTML = AppStore.mechAccumulatedParts.map(p => {
        const invItem = partsCatalog.find(i => i.id == p.inventory_id);
        const price = (parseFloat(p.price) > 0) ? parseFloat(p.price) : (invItem ? parseFloat(invItem.price || 0) : 0);
        const isCharged = showChargedOpt ? !!p.is_charged : true;

        const partWarrantyCat = p.warranty_category || (invItem ? invItem.warranty_category : 'Unclaimable / No Warranty') || 'Unclaimable / No Warranty';
        const isUnclaimableCategory = (partWarrantyCat === 'Unclaimable / No Warranty');

        let priceDisplay = `<span class="text-slate-400 font-mono font-semibold">${formatIDR(price)}</span>`;
        if (p.is_claimed) {
            priceDisplay = `<span class="text-amber-400 font-bold font-mono">${formatIDR(0)}</span> <span class="text-[9px] text-slate-600 line-through block">${formatIDR(price)}</span>`;
        } else if (showChargedOpt) {
            if (isCharged) {
                priceDisplay = `<span class="text-blue-400 font-bold font-mono">${formatIDR(price)}</span>`;
            } else {
                priceDisplay = `<span class="text-slate-500 font-bold font-mono">${formatIDR(0)}</span> <span class="text-[9px] text-slate-600 line-through block">${formatIDR(price)}</span>`;
            }
        }

        const chargedTd = showChargedOpt ? `
            <td class="py-2 text-center">
                <label class="inline-flex items-center gap-1 cursor-pointer select-none">
                    <input type="checkbox" ${p.is_charged ? 'checked' : ''} onchange="togglePartCharged(${p.inventory_id}, this.checked)" class="w-4 h-4 accent-blue-500 rounded border-slate-700 bg-slate-900 focus:ring-0 cursor-pointer">
                    <span class="text-[10px] ${p.is_charged ? 'text-blue-400 font-bold' : 'text-slate-400'}">Charged</span>
                </label>
            </td>
        ` : '';

        const claimedTd = isUnclaimableCategory ? `
            <td class="py-2 text-center">
                <span class="text-[10px] text-slate-600 font-mono italic">N/A</span>
            </td>
        ` : `
            <td class="py-2 text-center">
                <label class="inline-flex items-center gap-1 cursor-pointer select-none">
                    <input type="checkbox" ${p.is_claimed ? 'checked' : ''} onchange="togglePartClaimed(${p.inventory_id}, this.checked)" class="w-4 h-4 accent-amber-500 rounded border-slate-700 bg-slate-900 focus:ring-0 cursor-pointer">
                    <span class="text-[10px] ${p.is_claimed ? 'text-amber-400 font-bold' : 'text-slate-400'}">Claimed</span>
                </label>
            </td>
        `;

        return `
            <tr class="border-b border-slate-800/40 text-[11px] text-slate-300">
                <td class="py-2"><span class="font-bold font-mono text-blue-400 block">${escapeHtml(p.sku)}</span>${escapeHtml(p.part_name)}</td>
                <td class="py-2 text-right font-mono">${priceDisplay}</td>
                ${chargedTd}
                ${claimedTd}
                <td class="py-2 text-right font-bold text-slate-200">${p.qty}</td>
                <td class="py-2 text-right">
                    <button type="button" onclick="removeMechAccumulatedPart(${p.inventory_id})" class="text-rose-400 hover:text-rose-300 font-semibold px-2">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    if (totalSpan) totalSpan.innerText = AppStore.mechAccumulatedParts.length;
    updateEstimatedGrandTotal();
}

// --- Mechanics Time Tracker ---
// Gantikan fungsi getLocalSQLTimestamp di app.js dengan ini:
function getLocalSQLTimestamp(date = new Date()) {
    const pad = (num) => String(num).padStart(2, '0');
    const yyyy = date.getFullYear();
    const MM = pad(date.getMonth() + 1);
    const dd = pad(date.getDate());
    const hh = pad(date.getHours());
    const mm = pad(date.getMinutes());
    const ss = pad(date.getSeconds());
    return `${yyyy}-${MM}-${dd} ${hh}:${mm}:${ss}`;
}

async function startMechanicTimerClock() {
    // Dapatkan panel aktif (Sub-Tab 3 Intake)
    const activePanel = document.querySelector('#shop-panel-intake:not(.hidden)') || document.getElementById('shop-panel-intake');

    // Validasi pemilihan mekanik & kendaraan
    const mechSelect = activePanel ? activePanel.querySelector('#mech-assign-mechanic') : document.getElementById('mech-assign-mechanic');
    const mechanicId = (mechSelect && mechSelect.value) ? mechSelect.value : null;
    const isMechanicAssigned = !!mechanicId;

    const vehicleId = (activePanel ? activePanel.querySelector('#mech-selected-vehicle-id') : document.getElementById('mech-selected-vehicle-id'))?.value;
    const plateNo = (activePanel ? activePanel.querySelector('#mech-display-plate') : document.getElementById('mech-display-plate'))?.value;

    if (!vehicleId && !plateNo) {
        showToast('Please verify a customer or vehicle first.', 'warning');
        return;
    }

    const custTypeVal = (activePanel ? activePanel.querySelector('#mech-customer-type') : document.getElementById('mech-customer-type'))?.value || AppStore.mechCustomerType || 'internal';
    let custStatusVal = (activePanel ? activePanel.querySelector('#mech-customer-status') : document.getElementById('mech-customer-status'))?.value;
    if (custTypeVal === 'internal' && !['Gomolis', 'Operasional', 'Operational'].includes(custStatusVal)) {
        custStatusVal = 'Gomolis';
    }
    if (!custStatusVal) {
        custStatusVal = (custTypeVal === 'internal') ? 'Gomolis' : 'Retail';
    }

    const selectedMechName = (isMechanicAssigned && mechSelect && mechSelect.selectedIndex >= 0)
        ? mechSelect.options[mechSelect.selectedIndex].text
        : 'Unassigned';

    // Kumpulkan seluruh data form
    const checkedIssues = Array.from((activePanel || document).querySelectorAll('input[name="common_issue"]:checked')).map(chk => chk.value);
    const commonIssuesStr = checkedIssues.join(', ');

    const partsUsed = (AppStore.mechAccumulatedParts || []).map(p => ({
        inventory_id: p.inventory_id,
        part_name: p.part_name,
        sku: p.sku,
        qty: p.qty,
        quantity_used: p.qty,
        price_at_use: parseFloat(p.price) || 0,
        is_charged: !!p.is_charged,
        is_claimed: !!p.is_claimed
    }));

    const serviceOptions = (AppStore.mechAccumulatedServices || []).map(s => ({
        service_name: s.service_name || s.name,
        name: s.service_name || s.name,
        sku: s.sku || 'JASA-AUTO',
        fee: parseFloat(s.fee) || 0,
        is_charged: !!s.is_charged
    }));

    const otherServices = (AppStore.mechAccumulatedOtherServices || []).map(o => ({
        category: o.category,
        sku: o.sku || 'SRV-OTH',
        fee: parseFloat(o.fee) || 0,
        is_charged: !!o.is_charged
    }));

    const payload = {
        vehicle_id: vehicleId,
        mechanic_id: mechanicId,
        customer_status: custStatusVal,
        customer_type: custTypeVal,
        km_reached: (activePanel ? activePanel.querySelector('#mech-km-reached') : document.getElementById('mech-km-reached'))?.value || '',
        repair_category: (activePanel ? activePanel.querySelector('#mech-repair-category') : document.getElementById('mech-repair-category'))?.value || 'Repair',
        common_issues: commonIssuesStr,
        other_issues: (activePanel ? activePanel.querySelector('#mech-other-issues') : document.getElementById('mech-other-issues'))?.value || '',
        customer_name: (activePanel ? activePanel.querySelector('#mech-display-name') : document.getElementById('mech-display-name'))?.value.trim() || '',
        customer_phone: (activePanel ? activePanel.querySelector('#mech-display-phone') : document.getElementById('mech-display-phone'))?.value.trim() || '',
        customer_idcard: (activePanel ? activePanel.querySelector('#mech-display-idcard') : document.getElementById('mech-display-idcard'))?.value.trim() || '',
        customer_address: (activePanel ? activePanel.querySelector('#mech-display-address') : document.getElementById('mech-display-address'))?.value.trim() || '',
        license_plate: plateNo ? plateNo.trim() : '',
        vehicle_type: (activePanel ? activePanel.querySelector('#mech-display-type') : document.getElementById('mech-display-type'))?.value.trim() || '',
        frame_number: (activePanel ? activePanel.querySelector('#mech-display-frame') : document.getElementById('mech-display-frame'))?.value.trim() || '',
        controller_number: (activePanel ? activePanel.querySelector('#mech-display-controller') : document.getElementById('mech-display-controller'))?.value.trim() || '',
        parts_used: partsUsed,
        service_options: serviceOptions,
        other_services: otherServices
    };

    try {
        const res = await request('start_job', payload);
        const createdRecord = res.record || res.data || res;
        const activeRecordId = createdRecord.id || res.record_id;
        const generatedJobId = res.job_id || createdRecord?.job_id || `JOB-${activeRecordId}`;

        // Update Job ID header text on intake sheet
        const jobIdHeaderEl = (activePanel ? activePanel.querySelector('#mech-job-id') : document.getElementById('mech-job-id'));
        if (jobIdHeaderEl) {
            jobIdHeaderEl.innerText = generatedJobId;
        }

        const toastMsg = isMechanicAssigned 
            ? (res.message || `Data saved and job repair started! (${generatedJobId})`) 
            : (res.message || `Data saved successfully as Queue job (${generatedJobId})!`);
        showToast(toastMsg, 'success');

        if (activeRecordId) {
            AppStore.activeIntakeRecordId = activeRecordId;

            if (!Array.isArray(AppStore.allMaintenanceRecords)) {
                AppStore.allMaintenanceRecords = [];
            }

            const recordObject = {
                id: activeRecordId,
                job_id: generatedJobId,
                mechanic_id: mechanicId,
                mechanic_name: selectedMechName,
                status: isMechanicAssigned ? 'in_progress' : 'queue',
                customer_status: custStatusVal,
                customer_type: custTypeVal,
                customer_name: payload.customer_name,
                customer_phone: payload.customer_phone,
                customer_idcard: payload.customer_idcard,
                license_plate: payload.license_plate,
                vehicle_type: payload.vehicle_type,
                common_issues: payload.common_issues,
                other_issues: payload.other_issues,
                parts: partsUsed,
                service_options: serviceOptions,
                other_services: otherServices,
                created_at: getLocalSQLTimestamp(new Date()),
                start_time: isMechanicAssigned ? getLocalSQLTimestamp(new Date()) : null
            };

            const existingIdx = AppStore.allMaintenanceRecords.findIndex(r => r.id == activeRecordId);
            if (existingIdx >= 0) {
                AppStore.allMaintenanceRecords[existingIdx] = recordObject;
            } else {
                AppStore.allMaintenanceRecords.unshift(recordObject);
            }
        }

        if (isMechanicAssigned) {
            // Jalankan Timer jika mekanik diassign
            const now = new Date();
            AppStore.mechStartTime = getLocalSQLTimestamp(now);

            const startTsEl = document.getElementById('mech-start-timestamp');
            if (startTsEl) startTsEl.innerText = AppStore.mechStartTime;

            const btnStartSave = document.getElementById('btn-mech-start-and-save');
            if (btnStartSave) {
                btnStartSave.disabled = true;
                btnStartSave.classList.add('opacity-50', 'cursor-not-allowed');
                btnStartSave.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Fixing In Progress...';
            }

            const startTimeMs = now.getTime();
            AppStore.mechDurationSecs = 0;
            updateTimerDisplay(0);

            if (AppStore.mechTimerInterval) clearInterval(AppStore.mechTimerInterval);

            AppStore.mechTimerInterval = setInterval(() => {
                const currentMs = new Date().getTime();
                AppStore.mechDurationSecs = Math.floor((currentMs - startTimeMs) / 1000);
                updateTimerDisplay(AppStore.mechDurationSecs);
            }, 1000);
        } else {
            // Jika belum diassign, reset intake form agar dapat input data baru
            if (typeof resetIntakeForm === 'function') {
                resetIntakeForm();
            }
        }

        // Refresh kartu Active Jobs di bagian atas
        if (typeof loadActiveJobsList === 'function') {
            await loadActiveJobsList();
        }
    } catch (err) {
        showToast(err.message || 'Failed to save job.', 'error');
    }
}

// 🛠️ EDIT FUNGSI INI DI app.js
async function printCurrentWorkOrderAndReset() {
    const recordId = AppStore.activeIntakeRecordId;

    if (!recordId) {
        showToast('No active record found to print. Please click Start Fixing first.', 'warning');
        return;
    }

    // Sync latest form data to backend before printing to guarantee all entered data is present in print preview
    try {
        const partsUsed = (AppStore.mechAccumulatedParts || []).map(p => ({
            inventory_id: p.inventory_id,
            part_name: p.part_name,
            sku: p.sku,
            qty: p.qty,
            quantity_used: p.qty,
            price_at_use: parseFloat(p.price) || 0,
            price: parseFloat(p.price) || 0,
            is_charged: p.is_charged ? 1 : 0,
            is_claimed: p.is_claimed ? 1 : 0
        }));

        const serviceOptions = (AppStore.mechAccumulatedServices || []).map(s => ({
            service_name: s.service_name || s.name,
            name: s.service_name || s.name,
            sku: s.sku || 'JASA-AUTO',
            fee: parseFloat(s.fee) || 0,
            is_charged: s.is_charged ? 1 : 0
        }));

        const otherServices = (AppStore.mechAccumulatedOtherServices || []).map(o => ({
            category: o.category,
            sku: o.sku || 'SRV-OTH',
            fee: parseFloat(o.fee) || 0,
            is_charged: o.is_charged ? 1 : 0
        }));

        const checkedIssues = Array.from(document.querySelectorAll('input[name="common_issue"]:checked')).map(chk => chk.value);
        const commonIssuesStr = checkedIssues.join(', ');
        // Ambil elemen select mekanik dari panel aktif
        const activePanel = document.querySelector('#shop-panel-active-job:not(.hidden)') || document.querySelector('#shop-panel-intake:not(.hidden)');
        const mechSelect = activePanel ? activePanel.querySelector('#mech-assign-mechanic') : document.getElementById('mech-assign-mechanic');

        // Ambil ID dan Nama Mekanik yang DIPILIH di form (bukan user login)
        const mechanicId = mechSelect?.value || document.getElementById('mech-assign-mechanic')?.value || 1;
        const selectedMechName = mechSelect && mechSelect.selectedIndex >= 0 ? mechSelect.options[mechSelect.selectedIndex].text : '';

        const payload = {
            record_id: recordId,
            mechanic_id: mechanicId,
            km_reached: document.getElementById('mech-km-reached')?.value || '',
            repair_category: document.getElementById('mech-repair-category')?.value || 'Repair',
            common_issues: commonIssuesStr,
            other_issues: document.getElementById('mech-other-issues')?.value || '',
            customer_name: document.getElementById('mech-display-name')?.value.trim() || '',
            customer_phone: document.getElementById('mech-display-phone')?.value.trim() || '',
            customer_idcard: document.getElementById('mech-display-idcard')?.value.trim() || '',
            customer_address: document.getElementById('mech-display-address')?.value.trim() || '',
            license_plate: document.getElementById('mech-display-plate')?.value.trim() || '',
            vehicle_type: document.getElementById('mech-display-type')?.value.trim() || '',
            frame_number: document.getElementById('mech-display-frame')?.value.trim() || '',
            controller_number: document.getElementById('mech-display-controller')?.value.trim() || '',
            customer_status: document.getElementById('mech-customer-status')?.value || 'Retail',
            customer_type: AppStore.mechCustomerType || 'internal',
            parts_used: partsUsed,
            service_options: serviceOptions,
            other_services: otherServices
        };

        if (Array.isArray(AppStore.allMaintenanceRecords)) {
            const activeRec = AppStore.allMaintenanceRecords.find(r => r.id == recordId);
            if (activeRec) {
                activeRec.mechanic_id = mechanicId;
                activeRec.mechanic_name = selectedMechName;
            }
        }

        await request('start_job', payload);
    } catch (err) {
        console.warn('Failed to sync before print:', err);
    }

    // 1. Buka preview cetak Work Order di tab baru
    const printUrl = `/api/print-maintenance-record?record_id=${encodeURIComponent(recordId)}`;
    const printWindow = window.open(printUrl, '_blank');

    if (!printWindow) {
        showToast('Popup blocker prevented opening print window. Please allow popups.', 'error');
        return;
    }

    showToast('Work Order sent to print preview.', 'info');

    // 🛑 CATATAN: Semua data di form tetap dipertahankan dan tidak di-reset setelah print.
}
window.printCurrentWorkOrderAndReset = printCurrentWorkOrderAndReset;

// Fungsi ketika tombol 'Add New Job' di klik oleh Admin
async function handleAddNewJobClick() {
    if (AppStore.mechanicInProgress) {
        const proceed = confirm('Unsaved repair form progress will be cleared. Are you sure you want to start a new job intake?');
        if (!proceed) return;
    }

    AppStore.mechanicInProgress = false;
    localStorage.removeItem('mechanicDraft');

    await resetMechanicRepairForm();
    showToast('Intake form cleared. Ready for a new job intake.', 'info');
}
window.handleAddNewJobClick = handleAddNewJobClick;

async function stopMechanicTimerClock() {
    const now = new Date();
    AppStore.mechStopTime = getLocalSQLTimestamp(now);

    // 1. Hentikan interval timer lokal form
    if (AppStore.mechTimerInterval) {
        clearInterval(AppStore.mechTimerInterval);
    }

    const stopTsEl = getActiveEl('mech-stop-timestamp') || document.getElementById('mech-stop-timestamp');
    if (stopTsEl) stopTsEl.innerText = AppStore.mechStopTime;

    // 2. Atur status tombol
    const btnStop = getActiveEl('btn-mech-stop-timer') || document.getElementById('btn-mech-stop-timer');
    if (btnStop) {
        btnStop.disabled = true;
        btnStop.classList.add('opacity-50', 'cursor-not-allowed');
    }

    const btnSubmit = getActiveEl('btn-mech-submit-record') || document.getElementById('btn-mech-submit-record');
    if (btnSubmit) btnSubmit.disabled = false;

    // Enable Print Invoice Button when Timer is stopped
    const btnPrintInvoice = getActiveEl('btn-active-job-print-invoice') || document.getElementById('btn-active-job-print-invoice');
    if (btnPrintInvoice) {
        btnPrintInvoice.disabled = false;
        btnPrintInvoice.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    // 3. Update local state & save draft to backend so end_time is persisted
    if (AppStore.activeIntakeRecordId) {
        const activeCardTimer = document.querySelector(`.active-job-timer[data-record-id="${AppStore.activeIntakeRecordId}"]`);
        if (activeCardTimer) {
            activeCardTimer.setAttribute('data-end-time', AppStore.mechStopTime);
        }

        if (Array.isArray(AppStore.allMaintenanceRecords)) {
            const activeRecord = AppStore.allMaintenanceRecords.find(r => r.id == AppStore.activeIntakeRecordId);
            if (activeRecord) {
                activeRecord.end_time = AppStore.mechStopTime;
                activeRecord.stopped_at = AppStore.mechStopTime;
            }
        }

        try {
            await saveActiveJobDraft();
        } catch (e) { }
    }

    // 4. Refresh Active Jobs List (job remains active until submitted)
    if (typeof loadActiveJobsList === 'function') {
        await loadActiveJobsList();
    }

    showToast('Repair clock stopped. Print invoice is now available.', 'success');
}
window.stopMechanicTimerClock = stopMechanicTimerClock;
window.stopActiveJobTimer = stopMechanicTimerClock;

function printCurrentInvoice(recordId) {
    const titleEl = document.getElementById('record-detail-title');
    const targetId = recordId || titleEl?.dataset?.id || AppStore.activeIntakeRecordId;
    if (!targetId) {
        showToast('No active record selected to print invoice.', 'warning');
        return;
    }
    window.open(`/print-customer-invoice?record_id=${encodeURIComponent(targetId)}`, '_blank');
}
window.printCurrentInvoice = printCurrentInvoice;

function formatPhoneForWhatsApp(phone) {
    if (!phone) return '';
    let cleaned = String(phone).replace(/\D/g, '');
    if (cleaned.startsWith('0')) {
        cleaned = '62' + cleaned.substring(1);
    } else if (!cleaned.startsWith('62') && cleaned.length > 8) {
        cleaned = '62' + cleaned;
    }
    return cleaned;
}

function sendInvoiceWhatsApp(recordId) {
    const titleEl = document.getElementById('record-detail-title');
    const targetId = recordId || titleEl?.dataset?.id || AppStore.activeIntakeRecordId;
    if (!targetId) {
        showToast('No active record selected for WhatsApp invoice.', 'warning');
        return;
    }

    let record = null;
    if (Array.isArray(AppStore.allMaintenanceRecords)) {
        record = AppStore.allMaintenanceRecords.find(r => r.id == targetId || r.job_id == targetId);
    }

    const rawPhone = record?.customer_phone || record?.vehicle?.customer?.phone || getActiveEl('mech-display-phone')?.value || '';
    const formattedPhone = formatPhoneForWhatsApp(rawPhone);

    if (!formattedPhone) {
        showToast('Customer phone number not available.', 'warning');
        return;
    }

    const custName = record?.customer_name || record?.vehicle?.customer?.name || getActiveEl('mech-display-name')?.value || 'Pelanggan';
    const jobId = record?.job_id || targetId;
    const plate = record?.license_plate || record?.vehicle?.license_plate || getActiveEl('mech-display-plate')?.value || 'N/A';
    const total = record?.grand_total ? formatIDR(record.grand_total) : (record?.parts_labor_paid ? formatIDR(record.parts_labor_paid) : '');

    const pdfUrl = `${window.location.origin}/download-customer-invoice-pdf?record_id=${encodeURIComponent(targetId)}`;

    let message = `Halo Kak *${custName}*,\n\nBerikut adalah rincian Faktur Resmi perbaikan kendaraan Anda di *SGPM Service Center*:\n\n` +
        `📋 *No. Job:* ${jobId}\n` +
        `🛵 *No. Plat:* ${plate}\n` +
        (total ? `💰 *Total Pembayaran:* ${total}\n` : '') +
        `📄 *Unduh File PDF Faktur:* ${pdfUrl}\n\n` +
        `Terima kasih atas kepercayaan Anda di SGPM Service Center! 🙏`;

    const waUrl = `https://api.whatsapp.com/send?phone=${formattedPhone}&text=${encodeURIComponent(message)}`;

    // Buka percakapan WhatsApp secara langsung (Opsi 2)
    window.open(waUrl, '_blank');
    showToast('Membuka WhatsApp untuk mengirim pesan & tautan faktur resmi.', 'info');
}
window.sendInvoiceWhatsApp = sendInvoiceWhatsApp;

function printCurrentWorkOrderAndReset(recordId) {
    const targetId = recordId || AppStore.activeIntakeRecordId;
    if (!targetId) {
        showToast('No active record selected to print work order.', 'warning');
        return;
    }
    window.open(`/print-maintenance-record?record_id=${encodeURIComponent(targetId)}`, '_blank');
}
window.printCurrentWorkOrderAndReset = printCurrentWorkOrderAndReset;

function printMaintenanceRecord(recordId) {
    const targetId = recordId || AppStore.activeIntakeRecordId;
    if (!targetId) {
        showToast('No record ID found to print work order.', 'warning');
        return;
    }
    window.open(`/print-maintenance-record?record_id=${encodeURIComponent(targetId)}`, '_blank');
}
window.printMaintenanceRecord = printMaintenanceRecord;

function resetMechanicTimer() {
    AppStore.mechStartTime = null;
    AppStore.mechStopTime = null;
    AppStore.activeIntakeRecordId = null;
    AppStore.mechDurationSecs = 0;
    if (AppStore.mechTimerInterval) clearInterval(AppStore.mechTimerInterval);

    updateTimerDisplay(0);

    const startTs = getActiveEl('mech-start-timestamp') || document.getElementById('mech-start-timestamp');
    if (startTs) startTs.innerText = '-';

    const stopTs = getActiveEl('mech-stop-timestamp') || document.getElementById('mech-stop-timestamp');
    if (stopTs) stopTs.innerText = '-';

    const btnStart = getActiveEl('btn-mech-start-timer') || document.getElementById('btn-mech-start-timer');
    if (btnStart) btnStart.disabled = false;

    const btnStop = getActiveEl('btn-mech-stop-timer') || document.getElementById('btn-mech-stop-timer');
    if (btnStop) btnStop.disabled = true;

    const btnSubmit = getActiveEl('btn-mech-submit-record') || document.getElementById('btn-mech-submit-record');
    if (btnSubmit) btnSubmit.disabled = true;

    // Reset service options & other services accumulators
    AppStore.mechAccumulatedServices = [];
    AppStore.mechAccumulatedOtherServices = [];
    if (typeof renderMechServicesAccumulator === 'function') renderMechServicesAccumulator();
    if (typeof renderMechOtherServicesAccumulator === 'function') renderMechOtherServicesAccumulator();
    if (typeof updateEstimatedGrandTotal === 'function') updateEstimatedGrandTotal();
}

function updateTimerDisplay(secs) {
    const h = Math.floor(secs / 3600);
    const m = Math.floor((secs % 3600) / 60);
    const s = secs % 60;
    const displayStr = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
    const timerDisplay = getActiveEl('mech-timer-display') || document.getElementById('mech-timer-display');
    if (timerDisplay) timerDisplay.innerText = displayStr;
}

async function submitMechanicJobRecord(event) {
    if (event && event.preventDefault) event.preventDefault();

    const vehicleId = getActiveEl('mech-selected-vehicle-id')?.value || '';
    const km = getActiveEl('mech-km-reached')?.value || '';
    const other = getActiveEl('mech-other-issues')?.value || '';
    const custTypeVal = getActiveEl('mech-customer-type')?.value || AppStore.mechCustomerType || 'internal';
    let customerStatus = getActiveEl('mech-customer-status')?.value;
    if (custTypeVal === 'internal' && !['Gomolis', 'Operasional', 'Operational'].includes(customerStatus)) {
        customerStatus = 'Gomolis';
    }
    if (!customerStatus) {
        customerStatus = (custTypeVal === 'internal') ? 'Gomolis' : 'Retail';
    }
    const repairCategory = getActiveEl('mech-repair-category')?.value || 'Repair';
    const mechanicId = getActiveEl('mech-assign-mechanic')?.value || AppStore.mechanicId || 1;

    const jobText = getActiveEl('mech-job-id')?.innerText || '';
    const match = jobText.match(/\d+$/);
    const rawJobId = match ? parseInt(match[0]) : null;

    // Accumulate checkboxes from active panel
    const activePanel = document.querySelector('#shop-panel-active-job:not(.hidden)') || document.querySelector('#shop-panel-intake:not(.hidden)');
    const checkedIssues = activePanel ? Array.from(activePanel.querySelectorAll('input[name="common_issue"]:checked')).map(chk => chk.value) : [];
    const commonIssuesStr = checkedIssues.join(', ');

    const checkedFormItems = activePanel ? Array.from(activePanel.querySelectorAll('input[name="mechanic_form_item"]:checked')).map(chk => chk.value) : [];
    const formItemsStr = checkedFormItems.join(', ');

    const partsUsed = (AppStore.mechAccumulatedParts || AppStore.partsUsedAccumulator || []).map(p => ({
        inventory_id: p.inventory_id,
        qty: p.qty,
        is_charged: !!p.is_charged
    }));

    const payload = {
        record_id: AppStore.activeIntakeRecordId || null,
        job_id: jobText || rawJobId,
        mechanic_id: mechanicId,
        vehicle_id: vehicleId,
        km_reached: km,
        customer_status: customerStatus,
        customer_type: AppStore.mechCustomerType || 'internal',
        repair_category: repairCategory,
        common_issues: commonIssuesStr,
        mechanic_form_items: formItemsStr,
        other_issues: other,
        customer_name: getActiveEl('mech-display-name')?.value.trim() || '',
        customer_phone: getActiveEl('mech-display-phone')?.value.trim() || '',
        customer_idcard: getActiveEl('mech-display-idcard')?.value.trim() || '',
        customer_address: getActiveEl('mech-display-address')?.value.trim() || '',
        license_plate: getActiveEl('mech-display-plate')?.value.trim() || '',
        vehicle_type: getActiveEl('mech-display-type')?.value.trim() || '',
        frame_number: getActiveEl('mech-display-frame')?.value.trim() || '',
        controller_number: getActiveEl('mech-display-controller')?.value.trim() || '',
        service_options: (AppStore.mechAccumulatedServices || []).map(s => ({
            name: s.name,
            sku: s.sku,
            fee: s.fee,
            is_charged: !!s.is_charged
        })),
        other_services: (AppStore.mechAccumulatedOtherServices || []).map(o => ({
            category: o.category,
            name: o.category,
            sku: o.sku || 'SRV-OTH',
            fee: o.fee,
            is_charged: !!o.is_charged
        })),
        start_time: AppStore.mechStartTime,
        end_time: AppStore.mechStopTime,
        parts_used: partsUsed
    };

    try {
        const res = await request('submit_mechanic_job', payload);
        showToast(res.message || 'Job submitted successfully.', 'success');
        AppStore.mechanicInProgress = false;
        AppStore.activeIntakeRecordId = null;
        localStorage.removeItem('mechanicDraft');
        if (typeof resetMechanicRepairForm === 'function') {
            await resetMechanicRepairForm();
        } else if (typeof resetMechanicTimer === 'function') {
            resetMechanicTimer();
        }
        if (typeof setShopAdminTab === 'function') {
            await setShopAdminTab('intake');
        }
        if (typeof loadActiveJobsList === 'function') {
            await loadActiveJobsList();
        }
        if (typeof fetchMaintenanceRecords === 'function') {
            fetchMaintenanceRecords();
        }
    } catch (err) {
        showToast(err.message || 'Failed to submit job.', 'error');
    }
}
window.submitMechanicJobRecord = submitMechanicJobRecord;

// --- History Review tab functions ---
async function fetchMechanicHistoryRecords() {
    const query = document.getElementById('mech-history-query').value.trim();
    if (!query) {
        showToast('Please enter Customer ID or ID Card Number to view records.', 'warning');
        return;
    }

    try {
        const data = await request('get_customer_maintenance_records', { query: query }, 'GET');
        AppStore.calendarEvents = data;

        renderMechanicHistoryTable(data);
        renderMechanicHistoryCalendar();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function renderMechanicHistoryTable(records) {
    const tbody = document.getElementById('mech-history-list-tbody');
    if (records.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-slate-500">No maintenance records found for this customer.</td></tr>`;
        return;
    }

    tbody.innerHTML = records.map(r => {
        const dateStr = formatIndonesianDate(r.created_at);
        const issuesStr = [r.common_issues, r.other_issues].filter(Boolean).join(' | ') || 'N/A';

        let partsCost = formatIDR(0);
        let partsDetails = 'None';
        if (r.parts && r.parts.length > 0) {
            const totalCost = r.parts.reduce((acc, p) => acc + (parseFloat(p.price_at_use) * parseInt(p.quantity_used)), 0);
            partsCost = formatIDR(totalCost);
            partsDetails = r.parts.map(p => `${p.part_name} (x${p.quantity_used})`).join(', ');
        }

        return `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300">
                        <td class="p-4">${dateStr}</td>
                        <td class="p-4 font-bold text-slate-200">${r.branch_name}</td>
                        <td class="p-4 font-mono font-semibold text-slate-400">${r.license_plate}</td>
                        <td class="p-4 max-w-xs truncate text-slate-300" title="${issuesStr}">${issuesStr}</td>
                        <td class="p-4 font-mono">${r.km_reached ? parseInt(r.km_reached).toLocaleString() : 'N/A'} KM</td>
                        <td class="p-4 text-center">
                            <span class="font-bold text-slate-200 block">${partsCost}</span>
                            <span class="text-[10px] text-slate-400 block max-w-[150px] truncate mx-auto" title="${partsDetails}">${partsDetails}</span>
                        </td>
                        <td class="p-4">
                            <button onclick="viewRecordDetailsModal(${r.id})" class="text-blue-400 hover:text-blue-300 font-bold">
                                <i class="fa-solid fa-eye mr-1"></i>View
                            </button>
                        </td>
                    </tr>
                `;
    }).join('');
}

function setHistoryViewMode(mode) {
    AppStore.historyViewMode = mode;

    const btnTable = document.getElementById('btn-history-view-table');
    const btnCal = document.getElementById('btn-history-view-calendar');
    const elTable = document.getElementById('mech-history-table-view');
    const elCal = document.getElementById('mech-history-calendar-view');

    if (mode === 'table') {
        btnTable.className = "px-3 py-1.5 rounded font-bold text-xs transition duration-150 bg-slate-800 text-slate-200";
        btnCal.className = "px-3 py-1.5 rounded font-bold text-xs transition duration-150 text-slate-400 hover:text-slate-200";
        elTable.classList.remove('hidden');
        elCal.classList.add('hidden');
    } else {
        btnCal.className = "px-3 py-1.5 rounded font-bold text-xs transition duration-150 bg-slate-800 text-slate-200";
        btnTable.className = "px-3 py-1.5 rounded font-bold text-xs transition duration-150 text-slate-400 hover:text-slate-200";
        elCal.classList.remove('hidden');
        elTable.classList.add('hidden');
        renderMechanicHistoryCalendar();
    }
}

// --- Custom Calendar Renderer ---
const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function adjustCalendarMonth(dir) {
    if (AppStore.currentCalendarMonth === undefined) {
        AppStore.currentCalendarMonth = new Date().getMonth();
    }
    if (AppStore.currentCalendarYear === undefined) {
        AppStore.currentCalendarYear = new Date().getFullYear();
    }

    AppStore.currentCalendarMonth += dir;
    if (AppStore.currentCalendarMonth < 0) {
        AppStore.currentCalendarMonth = 11;
        AppStore.currentCalendarYear--;
    } else if (AppStore.currentCalendarMonth > 11) {
        AppStore.currentCalendarMonth = 0;
        AppStore.currentCalendarYear++;
    }

    // 🛠️ Render ulang kalender mana pun yang sedang aktif di UI
    if (typeof renderMechanicHistoryCalendar === 'function') {
        renderMechanicHistoryCalendar();
    }
    if (typeof renderShopHistoryCalendar === 'function') {
        renderShopHistoryCalendar();
    }
}
window.adjustCalendarMonth = adjustCalendarMonth;

function renderMechanicHistoryCalendar() {
    const grid = document.getElementById('calendar-days-grid');
    if (!grid) return;

    const year = AppStore.currentCalendarYear || new Date().getFullYear();
    const month = AppStore.currentCalendarMonth !== undefined ? AppStore.currentCalendarMonth : new Date().getMonth();

    AppStore.currentCalendarYear = year;
    AppStore.currentCalendarMonth = month;

    document.getElementById('calendar-month-year-label').innerText = `${MONTHS[month]} ${year}`;

    // Clear grid
    grid.innerHTML = '';

    // Get first day of month
    const firstDayIndex = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const prevMonthDays = new Date(year, month, 0).getDate();

    // Empty space helper
    for (let i = firstDayIndex - 1; i >= 0; i--) {
        const prevDay = prevMonthDays - i;
        grid.innerHTML += `<div class="p-3 bg-slate-900/10 text-slate-600 rounded-lg text-center cursor-not-allowed select-none">${prevDay}</div>`;
    }

    // Build current month calendar days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayDateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Check if calendarEvents match this date
        const matchingEvents = (AppStore.calendarEvents || []).filter(e => {
            const evtDate = e.created_at.split(' ')[0]; // format YYYY-MM-DD
            return evtDate === dayDateStr;
        });

        let dotHtml = '';
        let borderClass = 'border-slate-800/60 hover:bg-slate-900/40 cursor-pointer';
        let onclickAttr = '';

        if (matchingEvents.length > 0) {
            dotHtml = `<span class="w-1.5 h-1.5 rounded-full bg-blue-500 mx-auto block mt-1.5 animate-pulse"></span>`;
            borderClass = 'border-blue-500/40 bg-blue-600/5 text-slate-100 font-bold hover:bg-blue-600/15 cursor-pointer';

            // Onclick: Open detailed popup containing records list
            onclickAttr = `onclick="showCalendarDayRecords('${dayDateStr}')"`;
        }

        grid.innerHTML += `
                    <div ${onclickAttr} class="p-3 bg-slate-950/40 border ${borderClass} rounded-lg flex flex-col justify-between h-14">
                        <span class="block text-left text-[11px]">${day}</span>
                        ${dotHtml}
                    </div>
                `;
    }
}

function showCalendarDayRecords(dateStr) {
    const events = (AppStore.calendarEvents || []).filter(e => {
        const rawDate = e.repair_date || e.created_at || e.start_time;
        if (!rawDate) return false;
        return String(rawDate).startsWith(dateStr);
    });

    if (events.length === 0) return;

    // Buat HTML daftar perbaikan pada tanggal tersebut
    const recordsListHtml = events.map(e => {
        const details = [e.common_issues, e.other_issues].filter(Boolean).join(' | ') || 'Routine inspection / Maintenance.';
        const plate = e.license_plate || 'N/A';
        const custName = e.customer_name || 'N/A';
        const timeStr = e.created_at ? new Date(e.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : 'N/A';

        return `
            <div class="p-4 bg-slate-950 border border-slate-800 rounded-xl space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-blue-400 font-mono"><i class="fa-solid fa-clock mr-1"></i>${timeStr}</span>
                    <span class="font-semibold text-slate-400">Mech: ${escapeHtml(e.mechanic_name || 'Unassigned')}</span>
                </div>
                <div class="text-sm font-bold text-slate-100">${escapeHtml(e.branch_name || 'Branch Outlet')}</div>
                <div class="grid grid-cols-2 gap-2 text-slate-300">
                    <div>Customer: <strong class="text-slate-100">${escapeHtml(custName)}</strong></div>
                    <div>License Plate: <span class="font-mono text-blue-400 font-bold">${escapeHtml(plate)}</span></div>
                </div>
                <div class="text-slate-400 bg-slate-900/60 p-2 rounded border border-slate-800/80">
                    <span class="text-[10px] text-slate-500 font-bold uppercase block">Diagnostics / Issues:</span>
                    ${escapeHtml(details)}
                </div>
                <div class="text-right pt-1">
                    <button onclick="closeGlobalModal(); viewRecordDetailsModal(${e.id});" class="px-3 py-1.5 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 rounded-lg font-bold text-xs transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-eye"></i> View Full Detail Card
                    </button>
                </div>
            </div>
        `;
    }).join('<div class="my-3 border-t border-slate-900"></div>');

    const formattedDate = new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

    openGlobalModal(`Maintenance Records on ${formattedDate}`, `
        <div class="space-y-3 max-h-[420px] overflow-y-auto custom-scrollbar pr-1">
            ${recordsListHtml}
        </div>
    `);
}
window.showCalendarDayRecords = showCalendarDayRecords;

function viewRecordDetailsModal(recordId) {
    if (!recordId) {
        showToast('Record ID not found.', 'error');
        return;
    }

    // ➕ Cari record di seluruh sumber state AppStore
    const records = [
        ...(AppStore.calendarEvents || []),
        ...(AppStore.allMaintenanceRecords || []),
        ...(AppStore.filteredMaintenanceRecords || [])
    ];

    // Pencocokan fleksibel baik berdasarkan id maupun maintenance_record_id
    const r = records.find(item => item.id == recordId || item.maintenance_record_id == recordId);

    if (!r) {
        showToast(`Maintenance record #${recordId} details not found.`, 'error');
        return;
    }

    let partsSummary = '<span class="text-slate-500 italic text-xs">No spare parts billed.</span>';
    if (r.parts && Array.isArray(r.parts) && r.parts.length > 0) {
        partsSummary = r.parts.map(p => `
            <div class="flex items-center justify-between py-1.5 border-b border-slate-900 text-xs font-semibold">
                <span class="text-slate-300">${escapeHtml(p?.part_name || p?.name || 'Sparepart')} (${escapeHtml(p?.sku || 'N/A')})</span>
                <span class="font-bold text-slate-200 font-mono">x${p?.quantity_used || p?.qty || 1} @ ${formatIDR(p?.price_at_use || p?.price || 0)}</span>
            </div>
        `).join('');
    }

    const createdDate = (r.created_at || r.repair_date) ? new Date(r.created_at || r.repair_date).toLocaleString('id-ID') : 'N/A';
    const branchName = r.branch_name || 'Main Branch';
    const plate = r.license_plate || 'N/A';
    const vehType = r.vehicle_type || 'N/A';
    const frameNum = r.vin || r.frame_number || 'N/A';
    const ctrlNum = r.controller_number || 'N/A';
    const mechName = r.mechanic_name || r.mechanic_display_name || 'N/A';

    const custName = r.customer_name || 'N/A';
    const custPhone = r.customer_phone || r.phone || 'N/A';
    const custId = r.customer_id || r.customer_id_card || r.customer_idcard || 'N/A';

    const html = `
        <div class="space-y-4 text-sm text-slate-300">
            <div class="grid grid-cols-2 gap-4 bg-slate-900/60 p-3.5 rounded-xl border border-slate-800">
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Logged Date</span>
                    <span class="font-bold text-slate-200 font-mono">${createdDate}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Branch Location</span>
                    <span class="font-bold text-slate-200">${escapeHtml(branchName)}</span>
                </div>
            </div>
            
            <div class="border-t border-slate-800/80 pt-3">
                <span class="text-[10px] text-slate-500 uppercase font-bold block mb-2"><i class="fa-solid fa-user-tag text-blue-400 mr-1"></i> Customer Info</span>
                <div class="grid grid-cols-3 gap-3 text-xs bg-slate-950 p-3 rounded-xl border border-slate-900">
                    <div>
                        <span class="text-slate-500 block">Name:</span> 
                        <strong class="text-slate-100 block text-sm">${escapeHtml(custName)}</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Customer ID:</span> 
                        <span class="font-mono text-blue-400 font-bold block">#${escapeHtml(String(custId))}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Phone:</span> 
                        <span class="font-mono text-slate-200 font-bold block">${escapeHtml(String(custPhone))}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800/80 pt-3">
                <span class="text-[10px] text-slate-500 uppercase font-bold block mb-2"><i class="fa-solid fa-motorcycle text-emerald-400 mr-1"></i> Vehicle Specifications</span>
                <div class="grid grid-cols-2 gap-2 text-xs bg-slate-950 p-3 rounded-xl border border-slate-900">
                    <div>License Plate: <span class="font-mono text-blue-400 font-bold">${escapeHtml(plate)}</span></div>
                    <div>Vehicle Type: <span class="text-slate-300 font-semibold">${escapeHtml(vehType)}</span></div>
                    <div>Frame No (VIN): <span class="font-mono text-slate-400">${escapeHtml(frameNum)}</span></div>
                    <div>Controller No: <span class="font-mono text-slate-400">${escapeHtml(ctrlNum)}</span></div>
                </div>
            </div>

            <div class="border-t border-slate-800/80 pt-3">
                <span class="text-[10px] text-slate-500 uppercase font-bold block mb-1">Logged Diagnostics / Issues</span>
                <p class="text-slate-200 font-medium bg-slate-950 p-3 rounded-xl border border-slate-900 text-xs">${escapeHtml([r.common_issues, r.other_issues].filter(Boolean).join(' | ') || 'Routine inspection.')}</p>
            </div>

            <div class="border-t border-slate-800/80 pt-3">
                <span class="text-[10px] text-slate-500 uppercase font-bold block mb-1.5"><i class="fa-solid fa-boxes-stacked mr-1"></i> Billed Spare Parts Used</span>
                <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-900 space-y-1">
                    ${partsSummary}
                </div>
            </div>

            <div class="border-t border-slate-800/80 pt-3">
                <span class="text-[10px] text-slate-500 uppercase font-bold block mb-1.5"><i class="fa-solid fa-boxes-stacked mr-1"></i> Billed Spare Parts Used</span>
                <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-900 space-y-1">
                    ${partsSummary}
                </div>
            </div>

            <!-- ➕ Action Button Bar di Bawah Modal -->
            <div class="flex items-center justify-end gap-2 border-t border-slate-800/80 pt-4 mt-4">
                <button type="button" onclick="window.open('/api/print-maintenance-record?record_id=${recordId}', '_blank')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-lg shadow-emerald-600/20">
                    <i class="fa-solid fa-print"></i> Print Record Document
                </button>
                <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs transition">
                    Close
                </button>
            </div>
        </div>
    `;

    openGlobalModal(`Repair Record - JOB #${recordId}`, html);
}
window.viewRecordDetailsModal = viewRecordDetailsModal;

// Global Modal helper
function openGlobalModal(title, htmlContent) {
    let modal = document.getElementById('global-dynamic-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'global-dynamic-modal';
        modal.className = "fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center z-50 hidden";
        modal.innerHTML = `
                    <div class="glass-panel w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl relative border border-slate-800">
                        <div class="px-6 py-4 border-b border-slate-900 bg-slate-900/40 flex items-center justify-between">
                            <h3 id="global-modal-title" class="font-bold text-slate-100 text-sm">Modal Title</h3>
                            <button onclick="closeGlobalModal()" class="text-slate-400 hover:text-slate-200 text-xl font-bold px-2">&times;</button>
                        </div>
                        <div id="global-modal-body" class="p-6 font-semibold text-slate-300"></div>
                    </div>
                `;
        document.body.appendChild(modal);
    }

    document.getElementById('global-modal-title').innerText = title;
    document.getElementById('global-modal-body').innerHTML = htmlContent;
    modal.classList.remove('hidden');
}

function closeGlobalModal() {
    const modal = document.getElementById('global-dynamic-modal');
    if (modal) modal.classList.add('hidden');
}

// -------------------------------------------------------------
// Shop Admin Workflows (Spreadsheet ledger)
// -------------------------------------------------------------

// ✅ FIXED: Clean loadShopAdminDashboard function
async function loadShopAdminDashboard() {
    try {
        // Set branch badge
        const activeBranch = Array.isArray(AppStore.branches) ? AppStore.branches.find(b => b.id == AppStore.user.branch_id) : null;
        const badgeEl = document.getElementById('shop-admin-branch-badge');
        const addEl = document.getElementById('shop-admin-add-branch');
        if (badgeEl) badgeEl.innerText = activeBranch ? activeBranch.name : 'Unassigned';
        if (addEl) addEl.innerText = activeBranch ? activeBranch.name : '';

        // Populate branch filter dropdown
        const branchFilter = document.getElementById('shop-admin-branch-filter');
        if (branchFilter) {
            branchFilter.innerHTML = `<option value="">All Branches</option>`;
            (AppStore.branches || []).forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.textContent = b.name;
                branchFilter.appendChild(opt);
            });
        }

        await fetchShopInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// FIXED (Triggers pagination immediately on page load)
function fetchShopInventory() {
    return request('get_inventory', { branch_id: AppStore.user?.branch_id }, 'GET').then(data => {
        AppStore.inventory = data;
        filterShopAdminInventory(); // 👈 Calculates pagination and renders both table + pagination bars!
    }).catch(err => {
        showToast(err.message, 'error');
    });
}

function filterShopAdminInventory() {
    renderShopInventoryTableHead();
    const searchVal = (document.getElementById('shop-admin-inventory-search')?.value || '').toLowerCase().trim();
    const colField = document.getElementById('shop-admin-inv-column-select')?.value || '';
    const colVal = (document.getElementById('shop-admin-inv-column-val')?.value || '').toLowerCase().trim();
    const sortVal = document.getElementById('shop-admin-inv-sort-select')?.value || 'updated_at-desc';

    let items = (AppStore.inventory || []).filter(item => {
        if (searchVal) {
            const sku = (item.sku || '').toLowerCase();
            const name = (item.part_name || '').toLowerCase();
            const desc = (item.description || '').toLowerCase();
            if (!sku.includes(searchVal) && !name.includes(searchVal) && !desc.includes(searchVal)) return false;
        }
        if (colField && colVal) {
            if (colField === 'available_qty') {
                const minQty = parseInt(colVal);
                if (!isNaN(minQty) && parseInt(item.available_qty) < minQty) return false;
            } else if (colField === 'price') {
                const minPrice = parseFloat(colVal);
                if (!isNaN(minPrice) && parseFloat(item.price) < minPrice) return false;
            } else {
                const targetVal = (item[colField] || '').toString().toLowerCase();
                if (!targetVal.includes(colVal)) return false;
            }
        }
        // Branch filter
        const branchFilterVal = document.getElementById('shop-admin-branch-filter')?.value || '';
        if (branchFilterVal && String(item.branch_id) !== branchFilterVal) return false;
        return true;
    });

    // Sorting
    const [sortField, sortOrder] = sortVal.split('-');
    items.sort((a, b) => {
        let valA, valB;
        if (sortField === 'available_qty' || sortField === 'price') {
            valA = parseFloat(a[sortField] || 0);
            valB = parseFloat(b[sortField] || 0);
        } else if (sortField === 'updated_at') {
            valA = new Date(a.updated_at || 0).getTime();
            valB = new Date(b.updated_at || 0).getTime();
        } else {
            valA = (a[sortField] || '').toString().toLowerCase();
            valB = (b[sortField] || '').toString().toLowerCase();
        }

        if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
        if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
        return 0;
    });

    // Render Pagination UI
    const totalCount = items.length;
    renderPaginationUI('shopInventory', totalCount, 'shop-admin-inventory-pagination-top', 'shop-admin-inventory-pagination-bottom');

    const pageSize = getPageSize('shopInventory');
    const pageState = AppStore.pagination['shopInventory'] || { page: 1 };
    const pageNum = pageState.page || 1;
    const sliced = items.slice((pageNum - 1) * pageSize, pageNum * pageSize);

    renderShopInventoryTable(sliced);
    renderShopInventoryDashboard();
}

function renderShopInventoryDashboard() {
    const lowList = document.getElementById('shop-dash-low-stock-list');
    const availList = document.getElementById('shop-dash-top-avail-list');
    const spentList = document.getElementById('shop-dash-top-spent-list');
    if (!lowList && !availList && !spentList) return;

    const inventory = AppStore.inventory || [];
    const currentBranchId = AppStore.user?.branch_id;
    const branchItems = currentBranchId ? inventory.filter(i => i.branch_id == currentBranchId) : inventory;
    const history = AppStore.sparePartsHistory || [];

    // Helper: Filter records by time period dropdown
    function filterByPeriod(records, periodVal, dateKey = 'used_at') {
        const now = new Date();
        const todayStr = now.toISOString().slice(0, 10);

        return records.filter(r => {
            const d = parseTimestampToDate(r[dateKey]);
            if (!d) return false;

            if (periodVal === 'daily') {
                return d.toISOString().slice(0, 10) === todayStr;
            } else if (periodVal === 'yearly') {
                return d.getFullYear() === now.getFullYear();
            } else {
                // Default: Monthly
                return d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth();
            }
        });
    }

    // 1. Low Stock Parts (< 5 units)
    const lowPeriod = document.getElementById('shop-dash-low-stock-period')?.value || 'monthly';
    const lowStockItems = branchItems.filter(i => parseInt(i.available_qty) < 5)
        .sort((a, b) => parseInt(a.available_qty) - parseInt(b.available_qty));

    if (lowList) {
        if (lowStockItems.length === 0) {
            lowList.innerHTML = `<div class="p-3 text-center text-xs text-slate-500 font-semibold italic">${(typeof window.t === 'function' ? window.t('Semua stok aman (≥ 5 unit)', 'Semua stok aman (≥ 5 unit)') : 'Semua stok aman (≥ 5 unit)')}</div>`;
        } else {
            lowList.innerHTML = lowStockItems.map(item => `
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800 text-xs">
                    <div class="truncate max-w-[150px]">
                        <span class="font-bold text-slate-200 block truncate" title="${escapeHtml(item.part_name)}">${escapeHtml(item.part_name)}</span>
                        <span class="text-[10px] text-slate-400 font-mono block">${escapeHtml(item.sku)}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-400 font-bold font-mono border border-amber-500/20 text-xs shrink-0">${item.available_qty} unit</span>
                </div>
            `).join('');
        }
    }

    // 2. Top 5 Most Available Stocks
    const availPeriod = document.getElementById('shop-dash-top-avail-period')?.value || 'monthly';
    const topAvailItems = [...branchItems].sort((a, b) => parseInt(b.available_qty) - parseInt(a.available_qty)).slice(0, 5);

    if (availList) {
        if (topAvailItems.length === 0) {
            availList.innerHTML = `<div class="p-3 text-center text-xs text-slate-500 font-semibold italic">${(typeof window.t === 'function' ? window.t('Belum ada data stok tersedia', 'Belum ada data stok tersedia') : 'Belum ada data stok tersedia')}</div>`;
        } else {
            availList.innerHTML = topAvailItems.map(item => `
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800 text-xs">
                    <div class="truncate max-w-[150px]">
                        <span class="font-bold text-slate-200 block truncate" title="${escapeHtml(item.part_name)}">${escapeHtml(item.part_name)}</span>
                        <span class="text-[10px] text-slate-400 font-mono block">${escapeHtml(item.sku)}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-400 font-bold font-mono border border-emerald-500/20 text-xs shrink-0">${item.available_qty} unit</span>
                </div>
            `).join('');
        }
    }

    // 3. Top 5 Most Spent / Used Spare Parts
    const spentPeriod = document.getElementById('shop-dash-top-spent-period')?.value || 'monthly';
    const filteredHistory = filterByPeriod(history, spentPeriod);
    const spentMap = {};
    const skuMap = {};

    filteredHistory.forEach(r => {
        const qty = parseInt(r.quantity_used || 0);
        spentMap[r.part_name] = (spentMap[r.part_name] || 0) + qty;
        if (r.sku) skuMap[r.part_name] = r.sku;
    });

    const topSpentSorted = Object.entries(spentMap)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 5);

    if (spentList) {
        if (topSpentSorted.length === 0) {
            spentList.innerHTML = `<div class="p-3 text-center text-xs text-slate-500 font-semibold italic">${(typeof window.t === 'function' ? window.t('Tidak ada pemakaian di periode ini', 'Tidak ada pemakaian di periode ini') : 'Tidak ada pemakaian di periode ini')}</div>`;
        } else {
            spentList.innerHTML = topSpentSorted.map(([pName, qty]) => `
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800 text-xs">
                    <div class="truncate max-w-[150px]">
                        <span class="font-bold text-slate-200 block truncate" title="${escapeHtml(pName)}">${escapeHtml(pName)}</span>
                        <span class="text-[10px] text-slate-400 font-mono block">${escapeHtml(skuMap[pName] || 'N/A')}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-400 font-bold font-mono border border-cyan-500/20 text-xs shrink-0">x${qty} ${(typeof window.t === 'function' ? window.t('terpakai', 'used') : 'terpakai')}</span>
                </div>
            `).join('');
        }
    }
}
window.renderShopInventoryDashboard = renderShopInventoryDashboard;

function toggleSortShopInventory(field) {
    const select = document.getElementById('shop-admin-inv-sort-select');
    if (!select) return;
    const current = select.value;
    if (current.startsWith(field)) {
        select.value = current.endsWith('-asc') ? `${field}-desc` : `${field}-asc`;
    } else {
        select.value = `${field}-asc`;
    }
    filterShopAdminInventory();
}

// -------------------------------------------------------------
// Shop Admin Workspace & Spare Part Usage History Controllers
// -------------------------------------------------------------

async function setShopAdminTab(tabName) {
    AppStore.shopAdminActiveTab = tabName;
    let routeId = 'shop-inventory';
    if (tabName === 'history') routeId = 'shop-history';
    else if (tabName === 'intake') routeId = 'shop-intake';
    else if (tabName === 'active-job') routeId = 'shop-active-job';
    else if (tabName === 'maintenance') routeId = 'shop-maintenance';
    else if (tabName === 'review') routeId = 'shop-review';

    localStorage.setItem('sgpm_last_route', routeId);
    showSection('shop-admin-workspace');
    setActiveNavRoute(routeId);

    const panelInv = document.getElementById('shop-panel-inventory');
    const panelHist = document.getElementById('shop-panel-history');
    const panelIntake = document.getElementById('shop-panel-intake');
    const panelReview = document.getElementById('shop-panel-review');
    const panelActiveJob = document.getElementById('shop-panel-active-job');
    const panelMaint = document.getElementById('shop-panel-maintenance');

    const btnInv = document.getElementById('btn-shop-tab-inventory');
    const btnHist = document.getElementById('btn-shop-tab-history');
    const btnIntake = document.getElementById('btn-shop-tab-intake');
    const btnReview = document.getElementById('btn-shop-tab-review');
    const btnActiveJob = document.getElementById('btn-shop-tab-active-job');
    const btnMaint = document.getElementById('btn-shop-tab-maintenance');

    [panelInv, panelHist, panelIntake, panelReview, panelActiveJob, panelMaint].forEach(p => p && p.classList.add('hidden'));

    [btnInv, btnHist, btnIntake, btnReview, btnActiveJob, btnMaint].forEach(btn => {
        if (btn) btn.className = "px-4 py-2 font-bold text-xs rounded-lg transition duration-200 text-slate-400 hover:text-slate-200 hover:bg-slate-900/40 flex items-center gap-2 whitespace-nowrap";
    });

    if (tabName === 'inventory') {
        if (panelInv) panelInv.classList.remove('hidden');
        if (btnInv) btnInv.className = "px-4 py-2 font-bold text-xs rounded-lg transition duration-200 bg-blue-600/10 text-blue-400 border border-blue-500/20 flex items-center gap-2 whitespace-nowrap";
        await loadShopAdminDashboard();
    } else if (tabName === 'history') {
        if (panelHist) panelHist.classList.remove('hidden');
        if (btnHist) btnHist.className = "px-4 py-2 font-bold text-xs rounded-lg transition duration-200 bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-2 whitespace-nowrap";
        await fetchSparePartsHistory();
    } else if (tabName === 'intake') {
        if (panelIntake) panelIntake.classList.remove('hidden');
        if (btnIntake) btnIntake.className = "px-4 py-2 font-bold text-xs rounded-lg transition duration-200 bg-blue-600/10 text-blue-400 border border-blue-500/20 flex items-center gap-2 whitespace-nowrap";
        await loadShopAdminIntake();
    } else if (tabName === 'active-job') {
        if (panelActiveJob) panelActiveJob.classList.remove('hidden');
        if (btnActiveJob) {
            btnActiveJob.classList.remove('hidden');
            btnActiveJob.className = "px-4 py-2 font-bold text-xs rounded-lg transition duration-200 bg-amber-600/10 text-amber-400 border border-amber-500/20 flex items-center gap-2 whitespace-nowrap";
        }
        await fetchDynamicChecklists();
        await loadActiveJobsList();
    } else if (tabName === 'maintenance') {
        if (panelMaint) panelMaint.classList.remove('hidden');
        if (btnMaint) btnMaint.className = "px-4 py-2 font-bold text-xs rounded-lg transition duration-200 bg-blue-600/10 text-blue-400 border border-blue-500/20 flex items-center gap-2 whitespace-nowrap";
        await fetchShopMaintenanceRecords();
    } else if (tabName === 'review') {
        if (panelReview) panelReview.classList.remove('hidden');
        if (btnReview) btnReview.className = "px-4 py-2 font-bold text-xs rounded-lg transition duration-200 bg-blue-600/10 text-blue-400 border border-blue-500/20 flex items-center gap-2 whitespace-nowrap";
    }
}

// --- SHOP ADMIN CUSTOMER HISTORY LOGS FUNCTIONS ---

async function fetchShopHistoryRecords() {
    const query = document.getElementById('shop-history-query').value.trim();
    if (!query) {
        showToast('Please enter Customer ID, ID Card Number, or Plate Number.', 'warning');
        return;
    }

    try {
        const rawData = await request('get_customer_maintenance_records', { query: query }, 'GET');

        // Flatten maintenance records dari kendaraan/customer
        let records = [];
        if (Array.isArray(rawData)) {
            records = rawData;
        } else if (rawData && rawData.vehicles) {
            // Jika dikembalikan dalam bentuk objek Customer beserta array vehicles
            rawData.vehicles.forEach(v => {
                if (v.maintenance_records && Array.isArray(v.maintenance_records)) {
                    records.push(...v.maintenance_records);
                }
            });
        } else if (rawData && rawData.records) {
            records = rawData.records;
        }

        // Simpan ke AppStore untuk dipakai oleh fungsi tabel dan kalender
        AppStore.calendarEvents = records;

        renderShopHistoryTable(records);
        renderShopHistoryCalendar();

        showToast(`Found ${records.length} maintenance records.`, 'info');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function renderShopHistoryTable(records) {
    const tbody = document.getElementById('shop-history-list-tbody');
    if (!tbody) return;

    if (!Array.isArray(records) || records.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-slate-500 font-sans">No maintenance records found.</td></tr>`;
        return;
    }

    tbody.innerHTML = records.map(r => {
        const dateStr = new Date(r.created_at || r.repair_date).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        const issuesStr = [r.common_issues, r.other_issues].filter(Boolean).join(' | ') || 'N/A';

        // Tangkap ID record secara akurat
        const recordId = r.id || r.maintenance_record_id;

        const custName = r.customer_name || 'N/A';
        const custPhone = r.customer_phone || r.phone || 'N/A';
        const custId = r.customer_id || r.customer_id_card || 'N/A';

        let partsCost = formatIDR(0);
        let partsDetails = 'None';
        if (r.parts && r.parts.length > 0) {
            const totalCost = r.parts.reduce((acc, p) => acc + (parseFloat(p.price_at_use || p.price || 0) * parseInt(p.quantity_used || p.qty || 1)), 0);
            partsCost = formatIDR(totalCost);
            partsDetails = r.parts.map(p => `${p.part_name || p.name} (x${p.quantity_used || p.qty || 1})`).join(', ');
        }

        return `
            <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300">
                <td class="p-4 whitespace-nowrap">${dateStr}</td>
                <td class="p-4">
                    <span class="font-bold text-slate-100 block text-sm font-sans">${escapeHtml(custName)}</span>
                    <span class="text-[10px] text-blue-400 font-mono block">ID: #${escapeHtml(String(custId))}</span>
                    <span class="text-[10px] text-slate-400 font-mono block"><i class="fa-solid fa-phone text-[9px] mr-1"></i>${escapeHtml(String(custPhone))}</span>
                </td>
                <td class="p-4 font-bold text-slate-200">${escapeHtml(r.branch_name || 'N/A')}</td>
                <td class="p-4 font-mono font-bold text-blue-400">${escapeHtml(r.license_plate || 'N/A')}</td>
                <td class="p-4 max-w-xs truncate text-slate-300" title="${escapeHtml(issuesStr)}">${escapeHtml(issuesStr)}</td>
                <td class="p-4 font-mono">${r.km_reached ? parseInt(r.km_reached).toLocaleString() : 'N/A'} KM</td>
                <td class="p-4 text-center">
                    <span class="font-bold text-slate-200 block">${partsCost}</span>
                    <span class="text-[10px] text-slate-400 block max-w-[150px] truncate mx-auto" title="${escapeHtml(partsDetails)}">${escapeHtml(partsDetails)}</span>
                </td>
                <td class="p-4 whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <!-- Tombol View Detail -->
                        <button type="button" onclick="viewRecordDetailsModal(${recordId})" class="px-2.5 py-1.5 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 font-bold rounded-lg text-xs transition flex items-center gap-1">
                            <i class="fa-solid fa-eye text-[11px]"></i> View
                        </button>
                        
                        <!-- ➕ Tombol Print Record Baru -->
                        <button type="button" onclick="window.open('/api/print-maintenance-record?record_id=${recordId}', '_blank')" class="px-2.5 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 font-bold rounded-lg text-xs transition flex items-center gap-1">
                            <i class="fa-solid fa-print text-[11px]"></i> Print
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function setShopHistoryViewMode(mode) {
    const btnTable = document.getElementById('btn-shop-history-view-table');
    const btnCal = document.getElementById('btn-shop-history-view-calendar');
    const elTable = document.getElementById('shop-history-table-view');
    const elCal = document.getElementById('shop-history-calendar-view');

    if (mode === 'table') {
        btnTable.className = "flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 bg-slate-800 text-slate-200 flex items-center justify-center";
        btnCal.className = "flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 text-slate-400 hover:text-slate-200 flex items-center justify-center";
        elTable.classList.remove('hidden');
        elCal.classList.add('hidden');
    } else {
        btnCal.className = "flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 bg-slate-800 text-slate-200 flex items-center justify-center";
        btnTable.className = "flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 text-slate-400 hover:text-slate-200 flex items-center justify-center";
        elCal.classList.remove('hidden');
        elTable.classList.add('hidden');

        // 🛠️ Set nilai default saat pindah ke tampilan kalender
        if (AppStore.currentCalendarMonth === undefined) {
            AppStore.currentCalendarMonth = new Date().getMonth();
        }
        if (AppStore.currentCalendarYear === undefined) {
            AppStore.currentCalendarYear = new Date().getFullYear();
        }

        renderShopHistoryCalendar();
    }
}

function renderShopHistoryCalendar() {
    const grid = document.getElementById('shop-calendar-days-grid');
    if (!grid) return;

    const now = new Date();
    const year = AppStore.currentCalendarYear !== undefined ? AppStore.currentCalendarYear : now.getFullYear();
    const month = AppStore.currentCalendarMonth !== undefined ? AppStore.currentCalendarMonth : now.getMonth();

    AppStore.currentCalendarYear = year;
    AppStore.currentCalendarMonth = month;

    const monthNames = (typeof MONTHS !== 'undefined')
        ? MONTHS
        : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    const labelEl = document.getElementById('shop-calendar-month-year-label');
    if (labelEl) labelEl.innerText = `${monthNames[month]} ${year}`;

    grid.innerHTML = '';

    const firstDayIndex = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const prevMonthDays = new Date(year, month, 0).getDate();

    // Render hari dari bulan sebelumnya
    for (let i = firstDayIndex - 1; i >= 0; i--) {
        const prevDay = prevMonthDays - i;
        grid.innerHTML += `<div class="p-3 bg-slate-900/10 text-slate-600 rounded-lg text-center cursor-not-allowed select-none">${prevDay}</div>`;
    }

    // Render tanggal bulan ini
    for (let day = 1; day <= daysInMonth; day++) {
        const pad = (n) => String(n).padStart(2, '0');
        const dayDateStr = `${year}-${pad(month + 1)}-${pad(day)}`;

        // Filter event berdasarkan tanggal
        const matchingEvents = (AppStore.calendarEvents || []).filter(e => {
            const rawDate = e.repair_date || e.created_at || e.start_time;
            if (!rawDate) return false;

            const t = String(rawDate).split(/[- :T]/);
            if (t.length < 3) return false;

            const evtYear = parseInt(t[0]);
            const evtMonth = parseInt(t[1]) - 1;
            const evtDay = parseInt(t[2]);

            return evtYear === year && evtMonth === month && evtDay === day;
        });

        let dotHtml = '';
        let borderClass = 'border-slate-800/60 hover:bg-slate-900/40 cursor-default';
        let onclickAttr = '';

        // 🛠️ AKTIFKAN KLIK JIKA ADA MAINTENANCE RECORD
        if (matchingEvents.length > 0) {
            dotHtml = `<span class="w-2 h-2 rounded-full bg-blue-500 mx-auto block mt-1 animate-pulse shadow-md shadow-blue-500/50"></span>`;
            borderClass = 'border-blue-500/50 bg-blue-600/10 text-slate-100 font-bold hover:bg-blue-600/20 cursor-pointer shadow-lg shadow-blue-500/5 active:scale-95 transition-all';

            // Pasang fungsi klik pembuka popup modal
            onclickAttr = `onclick="showCalendarDayRecords('${dayDateStr}')"`;
        }

        grid.innerHTML += `
            <div ${onclickAttr} class="p-3 bg-slate-950/40 border ${borderClass} rounded-lg flex flex-col justify-between h-14 select-none">
                <span class="block text-left text-[11px]">${day}</span>
                ${dotHtml}
            </div>
        `;
    }
}

// Export ke objek Global Window
window.fetchShopHistoryRecords = fetchShopHistoryRecords;
window.setShopHistoryViewMode = setShopHistoryViewMode;
window.renderShopHistoryCalendar = renderShopHistoryCalendar;

function updateStartSaveButtonText() {
    const activePanel = document.querySelector('#shop-panel-intake:not(.hidden)') || document.getElementById('shop-panel-intake');
    const select = activePanel ? activePanel.querySelector('#mech-assign-mechanic') : document.getElementById('mech-assign-mechanic');
    const btn = document.getElementById('btn-mech-start-and-save');
    if (!btn) return;
    if (select && select.value) {
        btn.innerHTML = '<i class="fa-solid fa-play-circle mr-1.5"></i> <span data-i18n="shop.start_fixing_save">Start Fixing & Save Data</span>';
        btn.className = 'flex-1 lg:flex-none px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2';
    } else {
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-1.5"></i> <span>Save Data</span>';
        btn.className = 'flex-1 lg:flex-none px-6 py-3.5 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-sm transition shadow-lg shadow-amber-600/20 flex items-center justify-center gap-2';
    }
}
window.updateStartSaveButtonText = updateStartSaveButtonText;

async function loadShopAdminIntake() {
    try {
        const users = await request('get_all_users', {}, 'GET');
        const userBranchId = AppStore.user ? AppStore.user.branch_id : null;
        
        // Fetch active in_progress jobs to filter out busy mechanics
        const activeJobsParams = { status: 'in_progress' };
        if (userBranchId) activeJobsParams.branch_id = userBranchId;
        const activeRecords = await request('get_all_maintenance_records', activeJobsParams, 'GET');
        const busyMechanicIds = new Set(
            (Array.isArray(activeRecords) ? activeRecords : [])
                .map(r => r.mechanic_id)
                .filter(id => id !== null && id !== undefined)
        );

        const mechanics = Array.isArray(users) 
            ? users.filter(u => u.role === 'mechanic' && (!userBranchId || u.branch_id == userBranchId) && !busyMechanicIds.has(u.id)) 
            : [];
            
        const select = document.getElementById('mech-assign-mechanic');
        if (select) {
            select.innerHTML = '<option value="">-- Choose Mechanic --</option>' +
                mechanics.map(m => `<option value="${m.id}">${escapeHtml(m.display_name || m.username)}</option>`).join('');
        }
        updateStartSaveButtonText();
        await fetchMechanicBranchParts();
        await fetchDynamicChecklists();
        await loadActiveJobsList();
    } catch (err) {
        console.error(err);
        showToast('Failed to load mechanics.', 'error');
    }
}
window.loadShopAdminIntake = loadShopAdminIntake;

let activeJobsTimerInterval = null;
function startActiveJobsTimerTick() {
    if (activeJobsTimerInterval) clearInterval(activeJobsTimerInterval);

    activeJobsTimerInterval = setInterval(() => {
        document.querySelectorAll('.active-job-timer').forEach(el => {
            const startStr = el.getAttribute('data-start-time');
            const endStr = el.getAttribute('data-end-time');

            if (!startStr) return;

            const start = parseTimestampToDate(startStr);
            if (!start) return;

            const end = parseTimestampToDate(endStr);

            let secs = 0;
            if (end) {
                secs = Math.floor(Math.max(0, end.getTime() - start.getTime()) / 1000);
            } else {
                const now = new Date();
                secs = Math.floor(Math.max(0, now.getTime() - start.getTime()) / 1000);
            }

            const h = Math.floor(secs / 3600);
            const m = Math.floor((secs % 3600) / 60);
            const s = secs % 60;
            el.innerText = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        });
    }, 1000);
}

async function renderMechanicAvailabilityCards(activeJobs) {
    const container = document.getElementById('shop-mechanic-availability-list');
    const containerTab = document.getElementById('shop-mechanic-availability-list-tab');
    if (!container && !containerTab) return;

    try {
        if (!AppStore.allUsers || AppStore.allUsers.length === 0) {
            try {
                const users = await request('get_all_users', {}, 'GET');
                AppStore.allUsers = Array.isArray(users) ? users : [];
            } catch (uErr) { }
        }

        const userBranchId = AppStore.currentUser?.branch_id || AppStore.user?.branch_id || null;
        const userRole = (AppStore.currentUser?.role || AppStore.user?.role || '').toLowerCase();
        const isSuperAdmin = ['super_admin', 'superadmin'].includes(userRole);

        // Filter mechanics for branch
        let mechanics = (AppStore.allUsers || []).filter(u => {
            if ((u.role || '').toLowerCase() !== 'mechanic') return false;
            if (!isSuperAdmin && userBranchId && u.branch_id && u.branch_id != userBranchId) return false;
            return true;
        });

        if (mechanics.length === 0) {
            const emptyMsg = `<p class="text-xs text-slate-500 italic col-span-full">${t('shop.no_mechanics_registered', 'Tidak ada mekanik terdaftar di cabang ini.')}</p>`;
            if (container) container.innerHTML = emptyMsg;
            if (containerTab) containerTab.innerHTML = emptyMsg;
            return;
        }

        const cardsHtml = mechanics.map(m => {
            const mId = m.id;
            const mName = m.display_name || m.username || m.name || `Mechanic #${mId}`;

            // Check active jobs for this mechanic
            const mJobs = (activeJobs || []).filter(j => j.mechanic_id == mId);
            const inProgressJob = mJobs.find(j => (j.status || '').toLowerCase() === 'in_progress');
            const stoppedJob = mJobs.find(j => (j.status || '').toLowerCase() === 'stopped');

            let cardStyle = '';
            let cardBorder = '';
            let nameClass = 'text-slate-900 font-bold';
            let badgeClass = '';
            let statusText = '';
            let jobInfoText = '';

            if (inProgressJob) {
                // BUSY / SEDANG BEKERJA (Red / Pink - same as queue job container)
                cardStyle = 'background-color: rgb(246, 242, 244);';
                cardBorder = 'border-pink-300/80 hover:border-pink-400';
                nameClass = 'text-slate-900 font-bold';
                badgeClass = 'bg-pink-200/90 text-pink-950 border-pink-300 font-bold border';
                statusText = t('shop.status_busy', 'Sedang Bekerja');
                jobInfoText = `<div class="text-[11px] text-pink-900 font-semibold font-mono truncate"><i class="fa-solid fa-wrench text-[10px] mr-1"></i>${escapeHtml(inProgressJob.job_id || 'Active Job')}</div>`;
            } else if (stoppedJob) {
                // REST / STOPPED / DIHENTIKAN (Yellow - same as stopped active job container)
                cardStyle = 'background-color: rgb(254, 249, 235);';
                cardBorder = 'border-amber-300/80 hover:border-amber-400';
                nameClass = 'text-slate-900 font-bold';
                badgeClass = 'bg-amber-200/90 text-amber-950 border-amber-300 font-bold border';
                statusText = t('shop.status_rest_stopped', 'Istirahat / Dihentikan');
                jobInfoText = `<div class="text-[11px] text-amber-900 font-semibold font-mono truncate"><i class="fa-solid fa-pause text-[10px] mr-1"></i>${escapeHtml(stoppedJob.job_id || 'Paused Job')}</div>`;
            } else {
                // AVAILABLE / TERSEDIA (Green - same as in progress job container)
                cardStyle = 'background-color: rgb(235, 247, 241);';
                cardBorder = 'border-emerald-300/80 hover:border-emerald-400';
                nameClass = 'text-slate-900 font-bold';
                badgeClass = 'bg-emerald-200/90 text-emerald-950 border-emerald-300 font-bold border';
                statusText = t('shop.status_available', 'Tersedia');
                jobInfoText = `<div class="text-[11px] text-emerald-900 font-semibold"><i class="fa-solid fa-circle-check text-[10px] mr-1"></i>${t('shop.ready_for_assignment', 'Siap Bekerja')}</div>`;
            }

            return `
                <div style="${cardStyle}" class="p-3.5 border ${cardBorder} rounded-xl space-y-1.5 transition duration-150 text-left">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-bold ${nameClass} truncate">${escapeHtml(mName)}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full ${badgeClass} shrink-0">
                            ${statusText}
                        </span>
                    </div>
                    ${jobInfoText}
                </div>
            `;
        }).join('');

        if (container) container.innerHTML = cardsHtml;
        if (containerTab) containerTab.innerHTML = cardsHtml;
    } catch (err) {
        console.error('Failed to render mechanic availability:', err);
    }
}
window.renderMechanicAvailabilityCards = renderMechanicAvailabilityCards;

async function loadActiveJobsList() {
    try {
        const userBranchId = AppStore.user ? AppStore.user.branch_id : null;
        const reqParams = { status: 'in_progress,queue' };
        if (userBranchId) reqParams.branch_id = userBranchId;
        const records = await request('get_all_maintenance_records', reqParams, 'GET');
        const container = document.getElementById('shop-active-jobs-list');
        const containerTab = document.getElementById('shop-active-jobs-list-tab');
        if (!container && !containerTab) return;

        // Simpan data terbaru ke store lokal
        AppStore.allMaintenanceRecords = records;

        const recordsList = Array.isArray(records) ? records : [];

        // Sort ascending by daily_queue_number / ID (e.g. #01, #02, #03...) from left to right
        recordsList.sort((a, b) => {
            const qA = a.daily_queue_number || a.id || 0;
            const qB = b.daily_queue_number || b.id || 0;
            return qA - qB;
        });

        // Render Mechanic Availability Cards
        if (typeof renderMechanicAvailabilityCards === 'function') {
            renderMechanicAvailabilityCards(recordsList);
        }

        if (recordsList.length === 0) {
            const emptyHtml = '<p class="text-xs text-slate-500 italic col-span-full">No active jobs in progress or queue.</p>';
            if (container) container.innerHTML = emptyHtml;
            if (containerTab) containerTab.innerHTML = emptyHtml;
            return;
        }

        const cardsHtml = recordsList.map(r => {
            const jobId = r?.job_id || r?.id || 'JOB-PENDING';
            const plate = r?.vehicle?.license_plate || r?.license_plate || 'N/A';
            const custName = r?.vehicle?.customer?.name || r?.customer_name || 'N/A';
            const rawMechName = r?.mechanic?.display_name || r?.mechanic?.username || r?.mechanic_name;
            const startTime = r?.start_time || '';
            const endTime = r?.end_time || r?.stopped_at || '';
            const recStatus = (r?.status || '').toLowerCase();

            const isQueue = (recStatus === 'queue' || recStatus === 'pending' || !r?.mechanic_id);
            const isStopped = !isQueue && !!endTime;

            let displayDuration = '00:00:00';
            if (isQueue) {
                displayDuration = '--:--:--';
            } else {
                const dStart = parseTimestampToDate(startTime);
                const dEnd = parseTimestampToDate(endTime);
                if (dStart && dEnd) {
                    const secs = Math.floor(Math.max(0, dEnd.getTime() - dStart.getTime()) / 1000);
                    const h = Math.floor(secs / 3600);
                    const m = Math.floor((secs % 3600) / 60);
                    const s = secs % 60;
                    displayDuration = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
                }
            }

            const lang = typeof getAppLanguage === 'function' ? getAppLanguage() : 'id';
            let statusLabel = '';
            let statusBadgeClass = '';
            let cardStyle = '';
            let cardBorderClass = '';
            let jobIdClass = '';
            let labelClass = 'text-slate-600 font-bold';
            let valueClass = 'text-slate-900 font-bold';
            let dividerClass = '';
            let timerClass = '';
            let btnPrintClass = '';
            let btnOpenClass = '';
            let mechTextHtml = '';

            if (isQueue) {
                statusLabel = (lang === 'en' ? 'Queue' : 'Antrean');
                cardStyle = 'background-color: rgb(246, 242, 244);';
                cardBorderClass = 'border-pink-300/80 shadow-md hover:border-pink-400';
                jobIdClass = 'text-pink-900 font-bold font-mono';
                statusBadgeClass = 'bg-pink-200/90 text-pink-950 border-pink-300 font-bold border';
                dividerClass = 'border-pink-300/60';
                timerClass = 'text-pink-900 font-mono font-bold text-xs';
                btnPrintClass = 'px-2 py-1 bg-emerald-700 hover:bg-emerald-800 text-white border border-emerald-600 rounded-lg text-[10px] font-bold transition flex items-center gap-1';
                btnOpenClass = 'px-2.5 py-1 bg-pink-700 hover:bg-pink-800 text-white border border-pink-600 rounded-lg text-[10px] font-bold transition';
                mechTextHtml = `<span class="text-pink-900 italic font-semibold">Unassigned (Queue)</span>`;
            } else if (isStopped) {
                statusLabel = (lang === 'en' ? 'Stopped' : 'Dihentikan');
                cardStyle = 'background-color: rgb(254, 249, 235);';
                cardBorderClass = 'border-amber-300/80 shadow-md hover:border-amber-400';
                jobIdClass = 'text-amber-900 font-bold font-mono';
                statusBadgeClass = 'bg-amber-200/90 text-amber-950 border-amber-300 font-bold border';
                dividerClass = 'border-amber-300/60';
                timerClass = 'text-amber-900 font-mono font-bold text-xs';
                btnPrintClass = 'px-2 py-1 bg-emerald-700 hover:bg-emerald-800 text-white border border-emerald-600 rounded-lg text-[10px] font-bold transition flex items-center gap-1';
                btnOpenClass = 'px-2.5 py-1 bg-amber-700 hover:bg-amber-800 text-white border border-amber-600 rounded-lg text-[10px] font-bold transition';
                mechTextHtml = `<span class="text-slate-900 font-bold">${escapeHtml(String(rawMechName || 'Mechanic'))}</span>`;
            } else {
                statusLabel = (lang === 'en' ? 'In Progress' : 'Sedang Berjalan');
                cardStyle = 'background-color: rgb(235, 247, 241);'; // Mint green / Light green
                cardBorderClass = 'border-emerald-300/80 shadow-md hover:border-emerald-400';
                jobIdClass = 'text-emerald-900 font-bold font-mono';
                statusBadgeClass = 'bg-emerald-200/90 text-emerald-950 border-emerald-300 font-bold border';
                dividerClass = 'border-emerald-300/60';
                timerClass = 'text-emerald-900 font-mono font-bold text-xs';
                btnPrintClass = 'px-2 py-1 bg-emerald-700 hover:bg-emerald-800 text-white border border-emerald-600 rounded-lg text-[10px] font-bold transition flex items-center gap-1';
                btnOpenClass = 'px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white border border-emerald-600 rounded-lg text-[10px] font-bold transition';
                mechTextHtml = `<span class="text-slate-900 font-bold">${escapeHtml(String(rawMechName || 'Mechanic'))}</span>`;
            }

            const labelPlate = lang === 'en' ? 'Plate:' : 'Plat:';
            const labelCust = lang === 'en' ? 'Cust:' : 'Pelanggan:';
            const labelMech = lang === 'en' ? 'Mech:' : 'Mekanik:';
            const btnPrint = lang === 'en' ? 'Print' : 'Cetak';
            const btnOpen = lang === 'en' ? 'Open' : 'Buka';
            const btnCancel = lang === 'en' ? 'Cancel' : 'Batal';

            const qNumFormatted = r?.daily_queue_formatted || (r?.daily_queue_number ? `#${String(r.daily_queue_number).padStart(2, '0')}` : '#--');

            return `
                <div style="${cardStyle}" class="w-[285px] min-w-[285px] shrink-0 p-3.5 border ${cardBorderClass} rounded-xl space-y-2 relative transition duration-150 text-left">
                    <div class="flex items-center justify-between gap-1.5">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="text-sm font-black font-sans px-2 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-950 border border-emerald-400/50 shadow-sm shrink-0 tracking-tight">${escapeHtml(qNumFormatted)}</span>
                            <span class="text-xs ${jobIdClass} truncate">${escapeHtml(String(jobId))}</span>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded-full ${statusBadgeClass} shrink-0">
                            ${statusLabel}
                        </span>
                    </div>
                    <div class="text-[11px] space-y-1">
                        <div><span class="${labelClass}">${labelPlate}</span> <span class="font-mono ${valueClass}">${escapeHtml(String(plate))}</span></div>
                        <div><span class="${labelClass}">${labelCust}</span> <span class="${valueClass}">${escapeHtml(String(custName))}</span></div>
                        <div><span class="${labelClass}">${labelMech}</span> ${mechTextHtml}</div>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t ${dividerClass} gap-2">
                        <div class="${timerClass} ${isQueue ? '' : 'active-job-timer'}" 
                            data-record-id="${r?.id}" 
                            data-start-time="${startTime}" 
                            data-end-time="${endTime}">
                            ${displayDuration}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" onclick="window.open('/api/print-maintenance-record?record_id=${r?.id}', '_blank')" class="${btnPrintClass}">
                                <i class="fa-solid fa-print"></i> ${btnPrint}
                            </button>
                            <button type="button" onclick="resumeActiveJob(${r?.id})" class="${btnOpenClass}">
                                ${btnOpen}
                            </button>
                            ${isQueue ? `
                                <button type="button" onclick="cancelActiveJobFromList(${r?.id})" class="px-2 py-1 bg-pink-700 hover:bg-pink-800 text-white border border-pink-600 rounded-lg text-[10px] font-bold transition flex items-center gap-1">
                                    <i class="fa-solid fa-ban"></i> ${btnCancel}
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        if (container) container.innerHTML = cardsHtml;
        if (containerTab) containerTab.innerHTML = cardsHtml;

        startActiveJobsTimerTick();
    } catch (err) {
        console.error('Failed to load active jobs:', err);
    }
}

async function resumeActiveJob(recordId) {
    try {
        // Switch Tampilan ke Sub-Tab Active Job Details
        if (typeof setShopAdminTab === 'function') {
            await setShopAdminTab('active-job');
        }

        const activeJobPanel = document.getElementById('shop-panel-active-job');
        if (!activeJobPanel) {
            showToast('Active Job Details panel not found.', 'error');
            return;
        }

        // Helper untuk mengambil elemen internal dari sub-tab Active Job Details
        const getActiveEl = (id) => activeJobPanel.querySelector(`#${id}`);

        // Populate ulang opsi dropdown mekanik, parts, dan checklist di kontainer Active Job
        const users = await request('get_all_users', {}, 'GET');
        const activeJobsParams = { status: 'in_progress' };
        const userBranchId = AppStore.user ? AppStore.user.branch_id : null;
        if (userBranchId) activeJobsParams.branch_id = userBranchId;
        const activeRecords = await request('get_all_maintenance_records', activeJobsParams, 'GET');
        
        // Find current record mechanic id
        const currentRecord = (Array.isArray(activeRecords) ? activeRecords : []).find(r => r.id == recordId);
        const currentMechId = currentRecord ? currentRecord.mechanic_id : null;

        const busyMechanicIds = new Set(
            (Array.isArray(activeRecords) ? activeRecords : [])
                .filter(r => r.id != recordId) // Exclude current job from busy set
                .map(r => r.mechanic_id)
                .filter(id => id !== null && id !== undefined)
        );

        const mechanics = Array.isArray(users) 
            ? users.filter(u => u.role === 'mechanic' && (!userBranchId || u.branch_id == userBranchId) && (!busyMechanicIds.has(u.id) || u.id == currentMechId)) 
            : [];
            
        const mechSelect = getActiveEl('mech-assign-mechanic');
        if (mechSelect) {
            mechSelect.innerHTML = '<option value="">-- Choose Mechanic Specialist --</option>' +
                mechanics.map(m => `<option value="${m.id}">${escapeHtml(m.display_name || m.username)}</option>`).join('');
        }

        // Load dynamic checklists (Common Issues & Required Tasks)
        const [issues, items, serviceOpts, otherOpts] = await Promise.all([
            request('get_common_issues', {}, 'GET'),
            request('get_mechanic_form_items', {}, 'GET'),
            request('get_service_options', {}, 'GET'),
            request('get_other_services', {}, 'GET')
        ]);

        const safeIssues = Array.isArray(issues) ? issues : [];
        const safeItems = Array.isArray(items) ? items : [];

        const commonIssuesContainer = getActiveEl('mech-common-issues-container');
        if (commonIssuesContainer) {
            commonIssuesContainer.innerHTML = safeIssues.map(issue => `
                <label class="flex items-center gap-2 cursor-pointer p-2 bg-slate-900/40 rounded border border-slate-800/60 hover:bg-slate-800/40 transition">
                    <input type="checkbox" name="common_issue" value="${issue.name}"> ${issue.name}
                </label>
            `).join('');
        }

        const itemsContainer = getActiveEl('mech-form-items-container');
        if (itemsContainer) {
            itemsContainer.innerHTML = safeItems.map(item => `
                <label class="flex items-center gap-2 cursor-pointer p-2 bg-slate-900/40 rounded border border-slate-800/60 hover:bg-slate-800/40 transition">
                    <input type="checkbox" name="mechanic_form_item" value="${item.label}"> ${item.label}
                </label>
            `).join('');
        }

        // Load Service Options & Other Services Dropdowns
        const sSelect = getActiveEl('mech-service-select');
        if (sSelect) {
            sSelect.innerHTML = '<option value="">-- Choose Service Option --</option>' +
                (Array.isArray(serviceOpts) ? serviceOpts : []).map(s => `<option value="${escapeHtml(s.name)}" data-sku="${escapeHtml(s.sku || 'JASA-000')}" data-fee="${s.fee}">${escapeHtml(s.name)} (${formatIDR(s.fee)})</option>`).join('');
        }

        const oSelect = getActiveEl('mech-other-service-select');
        if (oSelect) {
            oSelect.innerHTML = '<option value="">-- Choose Other Service --</option>' +
                (Array.isArray(otherOpts) ? otherOpts : []).map(o => `<option value="${escapeHtml(o.name)}" data-sku="${o.sku ? escapeHtml(o.sku) : 'SRV-OTH'}" data-fee="${o.fee}">${o.sku ? `[${escapeHtml(o.sku)}] ` : ''}${escapeHtml(o.name)} (${formatIDR(o.fee)})</option>`).join('');
        }

        // Reuse already-fetched activeRecords to find the job — no redundant request
        let job = Array.isArray(activeRecords) ? activeRecords.find(item => item.id == recordId || item.job_id == recordId) : null;
        if (!job) {
            const allRecords = await request('get_all_maintenance_records', {}, 'GET');
            job = Array.isArray(allRecords) ? allRecords.find(item => item.id == recordId || item.job_id == recordId) : null;
        }

        if (!job) {
            showToast('Job record not found or not in progress.', 'error');
            return;
        }

        AppStore.activeIntakeRecordId = job.id;

        // Render Field Informasi Pelanggan & Kendaraan
        const jCode = job.job_id || job.id || 'JOB-PENDING';
        const jobIdEl = getActiveEl('mech-job-id');
        if (jobIdEl) jobIdEl.innerText = jCode;
        const jobIdElTab = document.getElementById('mech-job-id-tab');
        if (jobIdElTab) jobIdElTab.innerText = jCode;

        const qVal = job.daily_queue_formatted || (job.daily_queue_number ? `#${String(job.daily_queue_number).padStart(2, '0')}` : '#--');
        const queueNumEl = getActiveEl('mech-daily-queue-num');
        if (queueNumEl) queueNumEl.innerText = qVal;
        const queueNumElTab = document.getElementById('mech-daily-queue-num-tab');
        if (queueNumElTab) queueNumElTab.innerText = qVal;

        // Ensure Spare Parts inventory dropdown is populated
        if (typeof fetchMechanicBranchParts === 'function') {
            await fetchMechanicBranchParts();
        } else if (typeof filterMechanicPartOptions === 'function') {
            filterMechanicPartOptions();
        }

        const vehIdEl = getActiveEl('mech-selected-vehicle-id');
        if (vehIdEl) vehIdEl.value = job.vehicle_id || '';

        const nameEl = getActiveEl('mech-display-name');
        if (nameEl) nameEl.value = job.vehicle?.customer?.name || job.customer_name || '';

        const phoneEl = getActiveEl('mech-display-phone');
        if (phoneEl) phoneEl.value = job.vehicle?.customer?.phone || job.customer_phone || '';

        const idcardEl = getActiveEl('mech-display-idcard');
        if (idcardEl) idcardEl.value = job.vehicle?.customer?.id_card_number || job.customer_idcard || '';

        const addressEl = getActiveEl('mech-display-address');
        if (addressEl) addressEl.value = job.vehicle?.customer?.address || job.customer_address || '';

        const savedCustStatus = job.customer_status || job.vehicle?.customer?.customer_status || 'Retail';
        const normCustStatus = (savedCustStatus === 'Operational') ? 'Operasional' : savedCustStatus;
        const isInternalStatus = ['Gomolis', 'Operasional', 'Operational'].includes(normCustStatus);
        const savedCustType = job.customer_type || (isInternalStatus ? 'internal' : 'external');

        if (typeof setMechanicCustomerType === 'function') {
            setMechanicCustomerType(savedCustType);
        }

        const statusEl = getActiveEl('mech-customer-status') || getActiveEl('active-job-customer-status');
        if (statusEl) {
            statusEl.value = normCustStatus;
        }

        const isQueueJob = (job.status === 'queue' || job.status === 'pending' || !job.start_time);

        const assignedMechEl = getActiveEl('mech-assign-mechanic');
        if (assignedMechEl) {
            if (isQueueJob) {
                assignedMechEl.disabled = false;
                assignedMechEl.classList.remove('opacity-50', 'cursor-not-allowed');
                assignedMechEl.innerHTML = '<option value="">-- Choose Mechanic Specialist --</option>' +
                    mechanics.map(m => `<option value="${m.id}" ${job.mechanic_id == m.id ? 'selected' : ''}>${escapeHtml(m.display_name || m.username)}</option>`).join('');
                if (job.mechanic_id) {
                    assignedMechEl.value = String(job.mechanic_id);
                }
            } else {
                assignedMechEl.disabled = true;
                assignedMechEl.classList.add('opacity-50', 'cursor-not-allowed');
                const assignedName = job.mechanic?.display_name || job.mechanic?.username || job.mechanic_name || 'Assigned Mechanic';
                const assignedId = job.mechanic_id || '';
                assignedMechEl.innerHTML = `<option value="${assignedId}" selected>${escapeHtml(assignedName)}</option>`;
                assignedMechEl.value = String(assignedId);
            }
        }

        const plateEl = getActiveEl('mech-display-plate');
        if (plateEl) plateEl.value = job.vehicle?.license_plate || job.license_plate || '';

        const typeEl = getActiveEl('mech-display-type');
        if (typeEl) typeEl.value = job.vehicle?.vehicle_type || job.vehicle_type || '';

        const frameEl = getActiveEl('mech-display-frame');
        if (frameEl) frameEl.value = job.vehicle?.vin || job.frame_number || '';

        const ctrlEl = getActiveEl('mech-display-controller');
        if (ctrlEl) ctrlEl.value = job.vehicle?.controller_number || job.controller_number || '';

        const actDateEl = getActiveEl('mech-display-activate-date');
        const jobActDate = job.vehicle?.activate_date || job.vehicle?.created_at || job.created_at || null;
        if (actDateEl) actDateEl.value = jobActDate || '';
        AppStore.activeVehicleActivateDate = jobActDate;

        const kmEl = getActiveEl('mech-km-reached');
        if (kmEl) kmEl.value = job.km_reached || '';

        if (typeof filterMechanicPartOptions === 'function') filterMechanicPartOptions();

        const activeJobMechInput = activeJobPanel.querySelector('#active-job-assigned-mechanic');
        if (activeJobMechInput) {
            const assignedName = job.mechanic?.display_name || job.mechanic?.username || job.mechanic_name || (isQueueJob ? 'Unassigned (Queue)' : 'Mechanic');
            activeJobMechInput.value = assignedName;
        }

        const activeJobStatusInput = activeJobPanel.querySelector('#active-job-customer-status');
        if (activeJobStatusInput) {
            activeJobStatusInput.value = normCustStatus;
        }

        const catEl = getActiveEl('mech-repair-category');
        if (catEl) catEl.value = job.repair_category || 'Repair';

        const otherEl = getActiveEl('mech-other-issues');
        if (otherEl) {
            const rawNotes = job.other_issues || job.notes || (job.description && job.description !== 'General Intake Repair Service' ? job.description : '');
            otherEl.value = rawNotes || '';
        }

        // Centang Checkbox Diagnostic Log
        const commonIssuesArray = (job.common_issues || '').split(',').map(s => s.trim());
        activeJobPanel.querySelectorAll('input[name="common_issue"]').forEach(chk => {
            chk.checked = commonIssuesArray.includes(chk.value);
        });

        const checklistArray = (job.mechanic_form_items || '').split(',').map(s => s.trim());
        activeJobPanel.querySelectorAll('input[name="mechanic_form_item"]').forEach(chk => {
            chk.checked = checklistArray.includes(chk.value);
        });

        // Set Timestamp Timer
        AppStore.mechStartTime = job.start_time;
        AppStore.mechStopTime = job.end_time || job.stopped_at || null;

        const startTsEl = getActiveEl('mech-start-timestamp');
        if (startTsEl) startTsEl.innerText = job.start_time || '-';

        const stopTsEl = getActiveEl('mech-stop-timestamp');
        if (stopTsEl) stopTsEl.innerText = AppStore.mechStopTime || '-';

        // Render Akumulasi Sparepart
        AppStore.mechAccumulatedParts = (job.parts || []).map(p => {
            const inv = p.inventory || {};
            return {
                inventory_id: p.inventory_id || p.pivot?.inventory_id || p.id,
                sku: inv.sku || p.sku || '',
                part_name: inv.part_name || p.part_name || p.name || 'Spare Part',
                qty: p.quantity_used || p.pivot?.quantity_used || 1,
                is_charged: p.pivot?.is_charged !== undefined ? !!p.pivot?.is_charged : true,
                price: parseFloat(p.price_at_use || inv.price || p.price || 0)
            };
        });
        if (typeof renderMechAccumulatedParts === 'function') renderMechAccumulatedParts();

        // Render Akumulasi Services & Other Fees
        AppStore.mechAccumulatedServices = [];
        let rawServices = job.service_options || job.services || [];
        if (typeof rawServices === 'string') {
            try { rawServices = JSON.parse(rawServices); } catch (e) { rawServices = []; }
        }

        if (Array.isArray(rawServices) && rawServices.length > 0) {
            AppStore.mechAccumulatedServices = rawServices.map((s, idx) => ({
                id: Date.now() + Math.random() + idx,
                sku: s.sku || 'JASA-AUTO',
                name: s.name || s.service_name || 'Service Option',
                service_name: s.name || s.service_name || 'Service Option',
                fee: parseFloat(s.fee || s.price || 0),
                is_charged: s.is_charged !== undefined ? !!s.is_charged : true
            }));
        } else if (job.service_sku || job.service_name) {
            const skus = (job.service_sku || '').split(',').map(s => s.trim());
            const names = (job.service_name || '').split(',').map(s => s.trim());
            const maxLen = Math.max(skus.length, names.length);
            const totalFee = parseFloat(job.labor_fee) || 0;
            const feePerItem = maxLen > 0 ? (totalFee / maxLen) : 0;
            for (let idx = 0; idx < maxLen; idx++) {
                const sku = skus[idx] || '';
                const name = names[idx] || sku || 'Service Option';
                if (sku || name) {
                    AppStore.mechAccumulatedServices.push({
                        id: Date.now() + Math.random() + idx,
                        sku: sku,
                        name: name,
                        service_name: name,
                        fee: feePerItem,
                        is_charged: true
                    });
                }
            }
        }
        if (typeof renderMechServicesAccumulator === 'function') renderMechServicesAccumulator();

        AppStore.mechAccumulatedOtherServices = [];
        let rawOther = job.other_services || job.other_expenses || [];
        if (typeof rawOther === 'string') {
            try { rawOther = JSON.parse(rawOther); } catch (e) { rawOther = []; }
        }

        const skuLookupMap = {};
        const oSelectEl = getActiveEl('mech-other-service-select');
        if (oSelectEl) {
            Array.from(oSelectEl.options).forEach(opt => {
                if (opt.value) {
                    skuLookupMap[opt.value.trim().toLowerCase()] = opt.getAttribute('data-sku') || opt.dataset.sku;
                }
            });
        }
        if (Array.isArray(AppStore.allOtherServices)) {
            AppStore.allOtherServices.forEach(item => {
                if (item.name && item.sku) {
                    skuLookupMap[item.name.trim().toLowerCase()] = item.sku;
                }
            });
        }

        if (Array.isArray(rawOther) && rawOther.length > 0) {
            AppStore.mechAccumulatedOtherServices = rawOther.map((o, idx) => {
                const catName = o.category || o.name || 'Other Fee';
                const foundSku = (o.sku && o.sku !== 'FEES-MISC') ? o.sku : (skuLookupMap[catName.trim().toLowerCase()] || 'SRV-OTH');
                return {
                    id: Date.now() + Math.random() + idx,
                    category: catName,
                    sku: foundSku,
                    fee: parseFloat(o.fee || o.price || 0),
                    is_charged: o.is_charged !== undefined ? !!o.is_charged : true,
                    is_other_service: true
                };
            });
        } else if (job.other_expenses_category) {
            const cats = job.other_expenses_category.split(',').map(c => c.trim());
            const totalOtherFee = parseFloat(job.other_expenses_fee) || 0;
            const feePerCat = cats.length > 0 ? (totalOtherFee / cats.length) : 0;
            cats.forEach((cat, idx) => {
                if (cat) {
                    const foundSku = skuLookupMap[cat.toLowerCase()] || 'SRV-OTH';
                    AppStore.mechAccumulatedOtherServices.push({
                        id: Date.now() + Math.random() + idx,
                        category: cat,
                        sku: foundSku,
                        fee: feePerCat,
                        is_charged: true,
                        is_other_service: true
                    });
                }
            });
        }
        if (typeof renderMechOtherServicesAccumulator === 'function') renderMechOtherServicesAccumulator();

        const isStopped = !!AppStore.mechStopTime;

        // Jalankan Timer Clock di Sub-tab Active Job
        if (AppStore.mechTimerInterval) clearInterval(AppStore.mechTimerInterval);

        if (job.start_time && !isStopped) {
            const startDate = parseTimestampToDate(job.start_time);
            if (startDate) {
                AppStore.mechTimerInterval = setInterval(() => {
                    const now = new Date();
                    const diffMs = Math.max(0, now.getTime() - startDate.getTime());
                    AppStore.mechDurationSecs = Math.floor(diffMs / 1000);

                    const timerDisplay = getActiveEl('mech-timer-display');
                    if (timerDisplay) {
                        const h = Math.floor(AppStore.mechDurationSecs / 3600);
                        const m = Math.floor((AppStore.mechDurationSecs % 3600) / 60);
                        const s = AppStore.mechDurationSecs % 60;
                        timerDisplay.innerText = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
                    }
                }, 1000);
            }
        } else if (isStopped) {
            // Tampilkan durasi statis dari waktu start sampai stop
            const startDate = parseTimestampToDate(job.start_time);
            const stopDate = parseTimestampToDate(AppStore.mechStopTime);
            if (startDate && stopDate) {
                const diffMs = Math.max(0, stopDate.getTime() - startDate.getTime());
                const secs = Math.floor(diffMs / 1000);
                const h = Math.floor(secs / 3600);
                const m = Math.floor((secs % 3600) / 60);
                const s = secs % 60;
                const timerDisplay = getActiveEl('mech-timer-display');
                if (timerDisplay) {
                    timerDisplay.innerText = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
                }
            }
        }

        // Update Status Tombol Aksional
        const btnStart = getActiveEl('btn-mech-start-timer');
        if (btnStart) {
            if (isQueueJob) {
                btnStart.disabled = false;
                btnStart.classList.remove('opacity-50', 'cursor-not-allowed');
                btnStart.innerHTML = '<i class="fa-solid fa-play"></i> Start Timer';
            } else {
                btnStart.disabled = true;
                btnStart.classList.add('opacity-50', 'cursor-not-allowed');
                btnStart.innerHTML = '<i class="fa-solid fa-play"></i> In Progress';
            }
        }

        const btnStop = getActiveEl('btn-mech-stop-timer');
        if (btnStop) {
            btnStop.disabled = (isQueueJob || isStopped);
            if (isQueueJob || isStopped) {
                btnStop.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnStop.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        const btnCancelJob = getActiveEl('btn-mech-cancel-job');
        if (btnCancelJob) {
            if (isQueueJob) {
                btnCancelJob.classList.remove('hidden');
            } else {
                btnCancelJob.classList.add('hidden');
            }
        }

        const btnSubmit = getActiveEl('btn-mech-submit-record');
        if (btnSubmit) {
            btnSubmit.disabled = false; // Selalu aktifkan tombol Submit
        }

        const btnPrintInvoice = getActiveEl('btn-active-job-print-invoice') || document.getElementById('btn-active-job-print-invoice');
        if (btnPrintInvoice) {
            btnPrintInvoice.disabled = !isStopped;
            if (!isStopped) {
                btnPrintInvoice.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btnPrintInvoice.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        if (typeof onMechanicCustomerStatusChange === 'function') {
            onMechanicCustomerStatusChange();
        }

        showToast('Active job details loaded successfully.', 'info');
    } catch (err) {
        console.error(err);
        showToast('Failed to resume active job.', 'error');
    }
}



async function saveActiveJobDraft() {
    const recordId = AppStore.activeIntakeRecordId;
    if (!recordId) {
        showToast('No active job found to save draft.', 'warning');
        return;
    }

    try {
        const partsUsed = (AppStore.mechAccumulatedParts || []).map(p => ({
            inventory_id: p.inventory_id,
            part_name: p.part_name,
            sku: p.sku,
            qty: p.qty,
            quantity_used: p.qty,
            price_at_use: parseFloat(p.price) || 0,
            price: parseFloat(p.price) || 0,
            is_charged: p.is_charged ? 1 : 0,
            is_claimed: p.is_claimed ? 1 : 0
        }));

        const serviceOptions = (AppStore.mechAccumulatedServices || []).map(s => ({
            service_name: s.service_name || s.name,
            name: s.service_name || s.name,
            sku: s.sku || 'JASA-AUTO',
            fee: parseFloat(s.fee) || 0,
            is_charged: s.is_charged ? 1 : 0
        }));

        const otherServices = (AppStore.mechAccumulatedOtherServices || []).map(o => ({
            category: o.category,
            name: o.category,
            sku: o.sku || 'SRV-OTH',
            fee: parseFloat(o.fee) || 0,
            is_charged: o.is_charged ? 1 : 0
        }));

        const activePanel = document.querySelector('#shop-panel-active-job:not(.hidden)') || document.querySelector('#shop-panel-intake:not(.hidden)');
        const checkedIssues = activePanel ? Array.from(activePanel.querySelectorAll('input[name="common_issue"]:checked')).map(chk => chk.value) : [];
        const commonIssuesStr = checkedIssues.join(', ');

        const checkedFormItems = activePanel ? Array.from(activePanel.querySelectorAll('input[name="mechanic_form_item"]:checked')).map(chk => chk.value) : [];
        const formItemsStr = checkedFormItems.join(', ');

        const mechanicId = getActiveEl('mech-assign-mechanic')?.value || AppStore.mechanicId || 1;

        const payload = {
            record_id: recordId,
            mechanic_id: mechanicId,
            km_reached: getActiveEl('mech-km-reached')?.value || '',
            repair_category: getActiveEl('mech-repair-category')?.value || 'Repair',
            common_issues: commonIssuesStr,
            mechanic_form_items: formItemsStr,
            other_issues: getActiveEl('mech-other-issues')?.value || '',
            customer_name: getActiveEl('mech-display-name')?.value.trim() || '',
            customer_phone: getActiveEl('mech-display-phone')?.value.trim() || '',
            customer_idcard: getActiveEl('mech-display-idcard')?.value.trim() || '',
            customer_address: getActiveEl('mech-display-address')?.value.trim() || '',
            license_plate: getActiveEl('mech-display-plate')?.value.trim() || '',
            vehicle_type: getActiveEl('mech-display-type')?.value.trim() || '',
            frame_number: getActiveEl('mech-display-frame')?.value.trim() || '',
            controller_number: getActiveEl('mech-display-controller')?.value.trim() || '',
            customer_status: getActiveEl('mech-customer-status')?.value || 'Retail',
            customer_type: AppStore.mechCustomerType || 'internal',
            start_time: AppStore.mechStartTime || null,
            end_time: AppStore.mechStopTime || null,
            stopped_at: AppStore.mechStopTime || null,
            parts_used: partsUsed,
            service_options: serviceOptions,
            other_services: otherServices
        };

        await request('start_job', payload);
        showToast('Active job draft saved successfully.', 'success');
        if (typeof loadActiveJobsList === 'function') {
            await loadActiveJobsList();
        }
    } catch (err) {
        showToast(err.message || 'Failed to save draft.', 'error');
    }
}
async function cancelActiveJobFromList(recordId) {
    if (!recordId) return;
    const lang = typeof getAppLanguage === 'function' ? getAppLanguage() : 'id';
    const confirmMsg = lang === 'en'
        ? 'Are you sure you want to cancel this job record? Any allocated spare parts stock will be returned to inventory.'
        : 'Apakah Anda yakin ingin membatalkan pekerjaan/antrean ini? Stok sparepart yang dialokasikan akan dikembalikan ke inventaris.';

    if (!confirm(confirmMsg)) return;

    try {
        const res = await request('cancel_active_job', { record_id: recordId });
        showToast(res.message || 'Job cancelled successfully.', 'success');

        if (AppStore.activeIntakeRecordId == recordId) {
            AppStore.activeIntakeRecordId = null;
            if (AppStore.mechTimerInterval) clearInterval(AppStore.mechTimerInterval);
        }

        if (typeof loadActiveJobsList === 'function') {
            await loadActiveJobsList();
        }
    } catch (err) {
        showToast(err.message || 'Failed to cancel job.', 'error');
    }
}

async function cancelActiveJob() {
    const recordId = AppStore.activeIntakeRecordId;
    if (!recordId) {
        showToast('No active job selected to cancel.', 'warning');
        return;
    }

    await cancelActiveJobFromList(recordId);
}

window.cancelActiveJobFromList = cancelActiveJobFromList;
async function startActiveJobTimer() {
    const activePanel = document.getElementById('shop-panel-active-job');
    if (!activePanel) return;

    const recordId = AppStore.activeIntakeRecordId;
    if (!recordId) {
        showToast('No active job record found to start timer.', 'warning');
        return;
    }

    const getActiveEl = (id) => activePanel.querySelector(`#${id}`);
    const mechSelect = getActiveEl('mech-assign-mechanic');
    const mechanicId = mechSelect?.value || null;

    if (!mechanicId) {
        showToast('Please choose/assign a mechanic specialist first.', 'warning');
        return;
    }

    const now = new Date();
    const startTimeStr = getLocalSQLTimestamp(now);

    const partsUsed = (AppStore.mechAccumulatedParts || []).map(p => ({
        inventory_id: p.inventory_id,
        part_name: p.part_name,
        sku: p.sku,
        qty: p.qty,
        quantity_used: p.qty,
        price_at_use: parseFloat(p.price) || 0,
        price: parseFloat(p.price) || 0,
        is_charged: p.is_charged ? 1 : 0
    }));

    const serviceOptions = (AppStore.mechAccumulatedServices || []).map(s => ({
        service_name: s.service_name || s.name,
        name: s.service_name || s.name,
        sku: s.sku || 'JASA-AUTO',
        fee: parseFloat(s.fee) || 0,
        is_charged: s.is_charged ? 1 : 0
    }));

    const otherServices = (AppStore.mechAccumulatedOtherServices || []).map(o => ({
        category: o.category,
        sku: o.sku || 'SRV-OTH',
        fee: parseFloat(o.fee) || 0,
        is_charged: o.is_charged ? 1 : 0
    }));

    const checkedIssues = Array.from(activePanel.querySelectorAll('input[name="common_issue"]:checked')).map(chk => chk.value);
    const commonIssuesStr = checkedIssues.join(', ');

    const checkedFormItems = Array.from(activePanel.querySelectorAll('input[name="mechanic_form_item"]:checked')).map(chk => chk.value);
    const formItemsStr = checkedFormItems.join(', ');

    const payload = {
        record_id: recordId,
        mechanic_id: mechanicId,
        status: 'in_progress',
        start_time: startTimeStr,
        km_reached: getActiveEl('mech-km-reached')?.value || '',
        repair_category: getActiveEl('mech-repair-category')?.value || 'Repair',
        common_issues: commonIssuesStr,
        mechanic_form_items: formItemsStr,
        other_issues: getActiveEl('mech-other-issues')?.value || '',
        customer_name: getActiveEl('mech-display-name')?.value.trim() || '',
        customer_phone: getActiveEl('mech-display-phone')?.value.trim() || '',
        customer_idcard: getActiveEl('mech-display-idcard')?.value.trim() || '',
        customer_address: getActiveEl('mech-display-address')?.value.trim() || '',
        license_plate: getActiveEl('mech-display-plate')?.value.trim() || '',
        vehicle_type: getActiveEl('mech-display-type')?.value.trim() || '',
        frame_number: getActiveEl('mech-display-frame')?.value.trim() || '',
        controller_number: getActiveEl('mech-display-controller')?.value.trim() || '',
        parts_used: partsUsed,
        service_options: serviceOptions,
        other_services: otherServices
    };

    try {
        const res = await request('start_job', payload);
        showToast(res.message || 'Job repair started! Status changed to In Progress.', 'success');

        AppStore.mechStartTime = startTimeStr;
        AppStore.mechStopTime = null;

        const startTsEl = activePanel.querySelector('#mech-start-timestamp');
        if (startTsEl) startTsEl.innerText = startTimeStr;

        const stopTsEl = activePanel.querySelector('#mech-stop-timestamp');
        if (stopTsEl) stopTsEl.innerText = '-';

        if (mechSelect) {
            mechSelect.disabled = true;
            mechSelect.classList.add('opacity-50', 'cursor-not-allowed');
            const chosenName = (mechSelect.selectedIndex >= 0 && mechSelect.options[mechSelect.selectedIndex].text !== '-- Choose Mechanic Specialist --')
                ? mechSelect.options[mechSelect.selectedIndex].text
                : 'Assigned Mechanic';
            mechSelect.innerHTML = `<option value="${mechanicId}" selected>${escapeHtml(chosenName)}</option>`;
        }

        const btnStart = activePanel.querySelector('#btn-mech-start-timer');
        if (btnStart) {
            btnStart.disabled = true;
            btnStart.classList.add('opacity-50', 'cursor-not-allowed');
            btnStart.innerHTML = '<i class="fa-solid fa-play"></i> In Progress';
        }

        const btnStop = activePanel.querySelector('#btn-mech-stop-timer');
        if (btnStop) {
            btnStop.disabled = false;
            btnStop.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        // Start live timer display tick
        if (AppStore.mechTimerInterval) clearInterval(AppStore.mechTimerInterval);
        const startDate = now;
        AppStore.mechTimerInterval = setInterval(() => {
            const currentMs = new Date().getTime();
            const diffMs = Math.max(0, currentMs - startDate.getTime());
            AppStore.mechDurationSecs = Math.floor(diffMs / 1000);

            const timerDisplay = activePanel.querySelector('#mech-timer-display');
            if (timerDisplay) {
                const h = Math.floor(AppStore.mechDurationSecs / 3600);
                const m = Math.floor((AppStore.mechDurationSecs % 3600) / 60);
                const s = AppStore.mechDurationSecs % 60;
                timerDisplay.innerText = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
            }
        }, 1000);

        if (typeof loadActiveJobsList === 'function') {
            await loadActiveJobsList();
        }
    } catch (err) {
        showToast(err.message || 'Failed to start timer.', 'error');
    }
}
window.startActiveJobTimer = startActiveJobTimer;

async function stopActiveJobTimer() {
    const activePanel = document.getElementById('shop-panel-active-job');
    if (!activePanel) return;

    const recordId = AppStore.activeIntakeRecordId;
    if (!recordId) {
        showToast('No active job record found to stop timer.', 'warning');
        return;
    }

    // 1. Hentikan interval timer lokal
    if (AppStore.mechTimerInterval) {
        clearInterval(AppStore.mechTimerInterval);
        AppStore.mechTimerInterval = null;
    }

    // 2. Catat timestamp waktu stop lokal (Format: YYYY-MM-DD HH:MM:SS)
    const now = new Date();
    AppStore.mechStopTime = getLocalSQLTimestamp(now);

    // Update tampilan UI timestamp stop
    const stopTsEl = activePanel.querySelector('#mech-stop-timestamp');
    if (stopTsEl) stopTsEl.innerText = AppStore.mechStopTime;

    // Matikan tombol stop timer agar tidak di-klik ganda
    const btnStop = activePanel.querySelector('#btn-mech-stop-timer');
    if (btnStop) btnStop.disabled = true;

    // 3. Kumpulkan seluruh data form dari panel Active Job untuk disimpan ke Backend
    const getActiveEl = (id) => activePanel.querySelector(`#${id}`);

    const partsUsed = (AppStore.mechAccumulatedParts || []).map(p => ({
        inventory_id: p.inventory_id,
        part_name: p.part_name,
        sku: p.sku,
        qty: p.qty,
        quantity_used: p.qty,
        price_at_use: parseFloat(p.price) || 0,
        price: parseFloat(p.price) || 0,
        is_charged: p.is_charged ? 1 : 0,
        is_claimed: p.is_claimed ? 1 : 0
    }));

    const serviceOptions = (AppStore.mechAccumulatedServices || []).map(s => ({
        service_name: s.service_name || s.name,
        name: s.service_name || s.name,
        sku: s.sku || 'JASA-AUTO',
        fee: parseFloat(s.fee) || 0,
        is_charged: s.is_charged ? 1 : 0
    }));

    const otherServices = (AppStore.mechAccumulatedOtherServices || []).map(o => ({
        category: o.category,
        sku: o.sku || 'SRV-OTH',
        fee: parseFloat(o.fee) || 0,
        is_charged: o.is_charged ? 1 : 0
    }));

    const checkedIssues = Array.from(activePanel.querySelectorAll('input[name="common_issue"]:checked')).map(chk => chk.value);
    const commonIssuesStr = checkedIssues.join(', ');

    const checkedFormItems = Array.from(activePanel.querySelectorAll('input[name="mechanic_form_item"]:checked')).map(chk => chk.value);
    const formItemsStr = checkedFormItems.join(', ');

    const payload = {
        record_id: recordId,
        mechanic_id: getActiveEl('mech-assign-mechanic')?.value || AppStore.mechanicId || 1,
        km_reached: getActiveEl('mech-km-reached')?.value || '',
        repair_category: getActiveEl('mech-repair-category')?.value || 'Repair',
        common_issues: commonIssuesStr,
        mechanic_form_items: formItemsStr,
        other_issues: getActiveEl('mech-other-issues')?.value || '',
        customer_name: getActiveEl('mech-display-name')?.value.trim() || '',
        customer_phone: getActiveEl('mech-display-phone')?.value.trim() || '',
        customer_idcard: getActiveEl('mech-display-idcard')?.value.trim() || '',
        customer_address: getActiveEl('mech-display-address')?.value.trim() || '',
        license_plate: getActiveEl('mech-display-plate')?.value.trim() || '',
        vehicle_type: getActiveEl('mech-display-type')?.value.trim() || '',
        frame_number: getActiveEl('mech-display-frame')?.value.trim() || '',
        controller_number: getActiveEl('mech-display-controller')?.value.trim() || '',
        customer_status: getActiveEl('mech-customer-status')?.value || 'Retail',
        customer_type: AppStore.mechCustomerType || 'internal',
        start_time: AppStore.mechStartTime,
        end_time: AppStore.mechStopTime,       // 👈 Mengirimkan stop time yang baru dihentikan
        stopped_at: AppStore.mechStopTime,     // 👈 Mengirimkan pendukung untuk field stopped_at
        parts_used: partsUsed,
        service_options: serviceOptions,
        other_services: otherServices
    };

    try {
        // 4. Kirim update ke server untuk menyimpan stop time
        const res = await request('start_job', payload);

        // Update cache lokal di AppStore
        if (Array.isArray(AppStore.allMaintenanceRecords)) {
            const activeRecord = AppStore.allMaintenanceRecords.find(r => r.id == recordId);
            if (activeRecord) {
                activeRecord.end_time = AppStore.mechStopTime;
                activeRecord.stopped_at = AppStore.mechStopTime;
            }
        }

        // Refresh kartu Active Jobs di sub-tab Intake agar statusnya berubah menjadi "Stopped"
        if (typeof loadActiveJobsList === 'function') {
            await loadActiveJobsList();
        }

        showToast('Repair timer stopped and stop time successfully saved!', 'success');
    } catch (err) {
        console.error('Failed to save stop time:', err);
        showToast('Timer stopped locally, but failed to sync to database: ' + err.message, 'warning');
    }
}
window.stopActiveJobTimer = stopActiveJobTimer;

async function fetchSparePartsHistory() {
    try {
        const raw = await request('get_spare_parts_history', {}, 'GET');
        AppStore.sparePartsHistory = Array.isArray(raw) ? raw : [];

        // Populate Category Filter
        const catSelect = document.getElementById('parts-hist-category-select');
        if (catSelect) {
            const categories = Array.from(new Set(AppStore.sparePartsHistory.map(r => r.category || 'General')));
            let catHtml = `<option value="">${t('table.all_categories', 'Semua Kategori')}</option>`;
            categories.forEach(cat => {
                catHtml += `<option value="${escapeHtml(cat)}">${escapeHtml(cat)}</option>`;
            });
            catSelect.innerHTML = catHtml;
        }

        filterPartsHistory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function setPartsHistoryMode(mode) {
    AppStore.partsHistoryMode = mode;

    const listCont = document.getElementById('parts-history-list-container');
    const monthlyCont = document.getElementById('parts-history-monthly-container');
    const btnList = document.getElementById('btn-parts-hist-mode-list');
    const btnMonthly = document.getElementById('btn-parts-hist-mode-monthly');

    if (listCont) listCont.classList.add('hidden');
    if (monthlyCont) monthlyCont.classList.add('hidden');

    if (btnList) btnList.className = "px-3.5 py-1.5 font-bold text-xs rounded-lg transition text-slate-400 hover:text-slate-200 hover:bg-slate-800 flex items-center gap-1.5";
    if (btnMonthly) btnMonthly.className = "px-3.5 py-1.5 font-bold text-xs rounded-lg transition text-slate-400 hover:text-slate-200 hover:bg-slate-800 flex items-center gap-1.5";

    if (mode === 'list') {
        if (listCont) listCont.classList.remove('hidden');
        if (btnList) btnList.className = "px-3.5 py-1.5 font-bold text-xs rounded-lg transition bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-1.5";
    } else {
        if (monthlyCont) monthlyCont.classList.remove('hidden');
        if (btnMonthly) btnMonthly.className = "px-3.5 py-1.5 font-bold text-xs rounded-lg transition bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-1.5";
    }

    filterPartsHistory();
}

function initFlatpickrDateInputs() {
    if (typeof flatpickr !== 'undefined') {
        const lang = (typeof getAppLanguage === 'function' ? getAppLanguage() : 'id');
        const fpLocale = (lang === 'en') ? 'default' : ((typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default');
        const fpConfig = {
            dateFormat: 'd/m/Y',
            allowInput: true,
            locale: fpLocale,
            theme: 'dark'
        };
        ['parts-hist-single-date', 'parts-hist-range-start', 'parts-hist-range-end', 'global-parts-hist-single-date', 'global-parts-hist-range-start', 'global-parts-hist-range-end'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                if (el._flatpickr) el._flatpickr.destroy();
                flatpickr(el, fpConfig);
            }
        });
    }
}
window.initFlatpickrDateInputs = initFlatpickrDateInputs;

function handlePartsHistoryPeriodChange() {
    const period = document.getElementById('parts-hist-period-select')?.value || 'all';
    const singleWrap = document.getElementById('parts-hist-date-single-wrap');
    const rangeWrap = document.getElementById('parts-hist-date-range-wrap');

    if (singleWrap) singleWrap.classList.add('hidden');
    if (rangeWrap) rangeWrap.classList.add('hidden');

    if (period === 'custom_date') {
        if (singleWrap) singleWrap.classList.remove('hidden');
        initFlatpickrDateInputs();
    } else if (period === 'custom_range') {
        if (rangeWrap) rangeWrap.classList.remove('hidden');
        initFlatpickrDateInputs();
    }

    filterPartsHistory();
}

function filterPartsHistory() {
    const searchVal = (document.getElementById('parts-hist-search')?.value || '').toLowerCase().trim();
    const period = document.getElementById('parts-hist-period-select')?.value || 'all';
    const categoryVal = document.getElementById('parts-hist-category-select')?.value || '';
    const singleDate = document.getElementById('parts-hist-single-date')?.value || '';
    const rangeStart = document.getElementById('parts-hist-range-start')?.value || '';
    const rangeEnd = document.getElementById('parts-hist-range-end')?.value || '';

    const todayStr = new Date().toISOString().slice(0, 10);

    const yesterdayDate = new Date();
    yesterdayDate.setDate(yesterdayDate.getDate() - 1);
    const yesterdayStr = yesterdayDate.toISOString().slice(0, 10);

    // Week start (Monday)
    const now = new Date();
    const dayOfWeek = now.getDay() || 7;
    const mondayDate = new Date(now);
    mondayDate.setDate(now.getDate() - dayOfWeek + 1);
    mondayDate.setHours(0, 0, 0, 0);

    const history = AppStore.sparePartsHistory || [];

    function parseDdmmyyyyToIso(val) {
        if (!val) return '';
        if (val.includes('/')) {
            const p = val.split('/');
            if (p.length === 3) {
                return `${p[2].padStart(4, '20')}-${p[1].padStart(2, '0')}-${p[0].padStart(2, '0')}`;
            }
        }
        return val;
    }

    const singleDateIso = parseDdmmyyyyToIso(singleDate);
    const rangeStartIso = parseDdmmyyyyToIso(rangeStart);
    const rangeEndIso = parseDdmmyyyyToIso(rangeEnd);

    const filtered = history.filter(row => {
        const rowDate = parseTimestampToDate(row.used_at);
        const rowDateStr = rowDate ? rowDate.toISOString().slice(0, 10) : '';

        // Category Filter
        if (categoryVal && (row.category || 'General').toLowerCase() !== categoryVal.toLowerCase()) {
            return false;
        }

        // Search Filter
        if (searchVal) {
            const matchName = (row.part_name || '').toLowerCase().includes(searchVal);
            const matchSku = (row.sku || '').toLowerCase().includes(searchVal);
            const matchPlate = (row.license_plate || '').toLowerCase().includes(searchVal);
            const matchCust = (row.customer_name || '').toLowerCase().includes(searchVal);
            const matchMech = (row.mechanic_name || '').toLowerCase().includes(searchVal);
            const matchJob = (`JOB-${row.maintenance_record_id}`).toLowerCase().includes(searchVal);
            if (!matchName && !matchSku && !matchPlate && !matchCust && !matchMech && !matchJob) {
                return false;
            }
        }

        // Period Filter (Day, Week, Custom Date / Dates)
        if (period === 'today') {
            if (rowDateStr !== todayStr) return false;
        } else if (period === 'yesterday') {
            if (rowDateStr !== yesterdayStr) return false;
        } else if (period === 'this_week') {
            if (!rowDate || rowDate < mondayDate) return false;
        } else if (period === 'custom_date') {
            if (singleDateIso && rowDateStr !== singleDateIso) return false;
        } else if (period === 'custom_range') {
            if (rangeStartIso && rowDateStr < rangeStartIso) return false;
            if (rangeEndIso && rowDateStr > rangeEndIso) return false;
        }

        return true;
    });

    renderPartsHistoryListTable(filtered);
    renderPartsHistoryMonthlyView(filtered);
}

function renderPartsHistoryListTable(data) {
    const tbody = document.getElementById('parts-history-list-tbody');
    if (!tbody) return;

    if (data.length === 0) {
        tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-500 font-semibold">No spare part usage records found for selected filters.</td>
                    </tr>
                `;
        return;
    }

    tbody.innerHTML = data.map(r => {
        const parsedDate = parseTimestampToDate(r.used_at);
        const dateOnlyStr = parsedDate ? parsedDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : (r.used_at ? r.used_at.split('T')[0] : 'N/A');
        const timeOnlyStr = parsedDate ? parsedDate.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }) : '';
        const totalCost = parseFloat(r.total_billed || (r.quantity_used * r.price_at_use));

        return `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                        <td class="p-3 text-center">
                            <span class="font-bold text-slate-200 block text-center font-sans">${dateOnlyStr}</span>
                            <span class="text-[10px] text-slate-400 font-mono block text-center">${timeOnlyStr} WIB</span>
                        </td>
                        <td class="p-3.5 text-center whitespace-nowrap">
                            <button onclick="openRecordDetailsModal(${r.maintenance_record_id})" class="inline-block whitespace-nowrap px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 font-bold rounded border border-blue-500/20 font-mono text-xs transition">
                                ${formatJobCode(r.maintenance_record_id, r.used_at)}
                            </button>
                        </td>
                        <td class="p-3 text-center truncate">
                            <span class="font-bold text-slate-200 block text-center truncate px-1" title="${escapeHtml(r.part_name)}">${escapeHtml(r.part_name)}</span>
                            <div class="flex items-center justify-center gap-1.5 mt-1 overflow-hidden">
                                <span class="font-mono text-[10px] text-slate-400 font-semibold truncate" title="${escapeHtml(r.sku || 'N/A')}">${escapeHtml(r.sku || 'N/A')}</span>
                                <span class="px-1.5 py-0.5 rounded bg-purple-500/10 text-purple-400 font-bold text-[10px] border border-purple-500/20 shrink-0">${escapeHtml(r.category || 'General')}</span>
                            </div>
                        </td>
                        <td class="p-4 font-bold text-slate-100 text-center text-sm font-mono">x${r.quantity_used}</td>
                        <td class="p-4 font-mono text-slate-300 text-center">${formatIDR(r.price_at_use)}</td>
                        <td class="p-4 font-mono font-bold text-emerald-400 text-center text-sm">${formatIDR(totalCost)}</td>
                        <td class="p-4 text-center">
                            <span class="font-bold text-slate-200 block text-center">${escapeHtml(r.customer_name || 'N/A')}</span>
                            <span class="text-[10px] text-blue-400 font-mono block text-center">${escapeHtml(r.license_plate || 'N/A')}</span>
                        </td>
                        <td class="p-4 text-center text-xs font-semibold text-slate-300">${escapeHtml(r.mechanic_name || 'Unassigned')}</td>
                    </tr>
                `;
    }).join('');
}

function renderPartsHistoryMonthlyView(data) {
    const container = document.getElementById('parts-history-monthly-cards');
    if (!container) return;

    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="glass-panel p-8 text-center text-slate-500 font-semibold rounded-2xl">
                No monthly spare part usage data available.
            </div>
        `;
        return;
    }

    // Group by Month (YYYY-MM)
    const grouped = {};
    data.forEach(r => {
        const dateObj = parseTimestampToDate(r.used_at) || new Date();
        const monthKey = `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}`;
        if (!grouped[monthKey]) {
            grouped[monthKey] = [];
        }
        grouped[monthKey].push(r);
    });

    const sortedMonths = Object.keys(grouped).sort((a, b) => b.localeCompare(a));
    const totalMonths = sortedMonths.length;

    if (!AppStore.pagination) AppStore.pagination = {};
    if (!AppStore.pagination.partsHistoryMonthlyPage || AppStore.pagination.partsHistoryMonthlyPage < 1) {
        AppStore.pagination.partsHistoryMonthlyPage = 1;
    }
    if (AppStore.pagination.partsHistoryMonthlyPage > totalMonths) {
        AppStore.pagination.partsHistoryMonthlyPage = totalMonths;
    }

    const pageIndex = AppStore.pagination.partsHistoryMonthlyPage - 1;
    const currentMonthKey = sortedMonths[pageIndex];
    const rows = grouped[currentMonthKey] || [];

    const parts = currentMonthKey.split('-');
    const year = parseInt(parts[0]);
    const monthNum = parseInt(parts[1]);
    const lang = (typeof getAppLanguage === 'function' ? getAppLanguage() : 'id');
    const locale = lang === 'en' ? 'en-US' : 'id-ID';
    const monthName = new Date(year, monthNum - 1, 1).toLocaleString(locale, { month: 'long', year: 'numeric' });

    let monthTotalQty = 0;
    let monthTotalBilled = 0;
    const partUsageMap = {};

    rows.forEach(r => {
        const qty = parseInt(r.quantity_used);
        const cost = parseFloat(r.total_billed || (r.quantity_used * r.price_at_use));
        monthTotalQty += qty;
        monthTotalBilled += cost;

        partUsageMap[r.part_name] = (partUsageMap[r.part_name] || 0) + qty;
    });

    let topPartName = 'None';
    let topPartQty = 0;
    Object.entries(partUsageMap).forEach(([pName, count]) => {
        if (count > topPartQty) {
            topPartQty = count;
            topPartName = pName;
        }
    });

    const tableRows = rows.map(r => {
        const parsedDate = parseTimestampToDate(r.used_at);
        const dateOnlyStr = parsedDate ? parsedDate.toLocaleDateString(locale, { day: 'numeric', month: 'short', year: 'numeric' }) : (r.used_at ? r.used_at.split('T')[0] : 'N/A');
        const timeOnlyStr = parsedDate ? parsedDate.toLocaleTimeString(locale, { hour: '2-digit', minute: '2-digit', hour12: false }) : '';
        const totalCost = parseFloat(r.total_billed || (r.quantity_used * r.price_at_use));

        return `
            <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                <td class="p-3 text-center whitespace-nowrap">
                    <span class="font-bold text-slate-200 block text-center font-sans">${dateOnlyStr}</span>
                    <span class="text-[10px] text-slate-400 font-mono block text-center">${timeOnlyStr} WIB</span>
                </td>
                <td class="p-3 text-center whitespace-nowrap">
                    <button onclick="openRecordDetailsModal(${r.maintenance_record_id})" class="px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 font-bold rounded border border-blue-500/20 font-mono text-xs">
                        ${formatJobCode(r.maintenance_record_id, r.used_at)}
                    </button>
                </td>
                <td class="p-3 text-center max-w-[220px] truncate">
                    <span class="font-bold text-slate-200 block text-center truncate" title="${escapeHtml(r.part_name)}">${escapeHtml(r.part_name)}</span>
                    <span class="text-[10px] text-slate-400 font-mono block text-center">SKU: ${escapeHtml(r.sku || 'N/A')}</span>
                </td>
                <td class="p-3 text-center font-bold font-mono text-slate-100">x${r.quantity_used}</td>
                <td class="p-3 text-center font-mono font-bold text-emerald-400 whitespace-nowrap">${formatIDR(totalCost)}</td>
                <td class="p-3 text-center text-xs">
                    <span class="font-bold text-slate-200 block text-center">${escapeHtml(r.customer_name || 'N/A')}</span>
                    <span class="text-[10px] text-blue-400 font-mono block text-center">${escapeHtml(r.license_plate || 'N/A')}</span>
                </td>
                <td class="p-3 text-center text-xs font-semibold text-slate-300">${escapeHtml(r.mechanic_name || 'Unassigned')}</td>
            </tr>
        `;
    }).join('');

    const monthOptions = sortedMonths.map((mK, i) => {
        const p = mK.split('-');
        const y = parseInt(p[0]);
        const m = parseInt(p[1]);
        const mLabel = new Date(y, m - 1, 1).toLocaleString(locale, { month: 'long', year: 'numeric' });
        const selected = (i + 1) === AppStore.pagination.partsHistoryMonthlyPage ? 'selected' : '';
        return `<option value="${i + 1}" ${selected}>${mLabel} (${grouped[mK].length} records)</option>`;
    }).join('');

    const isFirstPage = AppStore.pagination.partsHistoryMonthlyPage <= 1;
    const isLastPage = AppStore.pagination.partsHistoryMonthlyPage >= totalMonths;

    container.innerHTML = `
        <div class="glass-panel rounded-2xl p-6 shadow-2xl space-y-6">
            <!-- Monthly Navigation & Summary Bar -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 border-b border-slate-800/80 pb-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xl shadow-inner">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg sm:text-xl font-bold text-slate-100">${monthName}</h3>
                            <span class="px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-[10px] font-mono font-bold">Month ${AppStore.pagination.partsHistoryMonthlyPage} of ${totalMonths}</span>
                        </div>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">${rows.length} spare part usage transactions logged</p>
                    </div>
                </div>

                <!-- Monthly Pagination Controls -->
                <div class="flex items-center justify-between sm:justify-end gap-2 w-full lg:w-auto">
                    <button onclick="navigateMonthlyPartsHistory(-1)" ${isFirstPage ? 'disabled class="px-3 py-2 bg-slate-900/60 text-slate-600 border border-slate-800 rounded-xl text-xs font-bold cursor-not-allowed flex items-center gap-1.5"' : 'class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-cyan-400 border border-slate-800 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md cursor-pointer"'}>
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Newer Month
                    </button>

                    <select onchange="selectMonthlyPartsHistory(this.value)" class="px-3 py-2 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-bold max-w-[200px]">
                        ${monthOptions}
                    </select>

                    <button onclick="navigateMonthlyPartsHistory(1)" ${isLastPage ? 'disabled class="px-3 py-2 bg-slate-900/60 text-slate-600 border border-slate-800 rounded-xl text-xs font-bold cursor-not-allowed flex items-center gap-1.5"' : 'class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-cyan-400 border border-slate-800 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md cursor-pointer"'}>
                        Older Month <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-900/70 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block tracking-wider">Total Quantity Taken</span>
                        <span class="text-base sm:text-lg font-mono font-bold text-cyan-400 mt-0.5 block">${monthTotalQty} units</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>

                <div class="bg-slate-900/70 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block tracking-wider">Total Billing Value</span>
                        <span class="text-base sm:text-lg font-mono font-bold text-emerald-400 mt-0.5 block">${formatIDR(monthTotalBilled)}</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>

                <div class="bg-slate-900/70 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block tracking-wider">Most Used Part</span>
                        <span class="text-xs font-bold text-amber-400 truncate block max-w-[160px] mt-0.5" title="${escapeHtml(topPartName)}">${escapeHtml(topPartName)}</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>
            </div>

            <!-- Month Usage Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full text-center text-xs border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase text-[10px]">
                            <th class="p-3 text-center">Date & Time</th>
                            <th class="p-3 text-center">Job ID</th>
                            <th class="p-3 text-center">Part Name & SKU</th>
                            <th class="p-3 text-center">Qty Used</th>
                            <th class="p-3 text-center">Subtotal Cost</th>
                            <th class="p-3 text-center">Customer / Vehicle</th>
                            <th class="p-3 text-center">Mechanic</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function navigateMonthlyPartsHistory(direction) {
    if (!AppStore.pagination) AppStore.pagination = {};
    AppStore.pagination.partsHistoryMonthlyPage = (AppStore.pagination.partsHistoryMonthlyPage || 1) + direction;
    filterPartsHistory();
}
window.navigateMonthlyPartsHistory = navigateMonthlyPartsHistory;

function selectMonthlyPartsHistory(pageVal) {
    if (!AppStore.pagination) AppStore.pagination = {};
    AppStore.pagination.partsHistoryMonthlyPage = parseInt(pageVal) || 1;
    filterPartsHistory();
}
window.selectMonthlyPartsHistory = selectMonthlyPartsHistory;

window.setShopAdminTab = setShopAdminTab;
window.fetchSparePartsHistory = fetchSparePartsHistory;
window.setPartsHistoryMode = setPartsHistoryMode;
window.handlePartsHistoryPeriodChange = handlePartsHistoryPeriodChange;
window.filterPartsHistory = filterPartsHistory;
window.toggleShopInvColumn = toggleShopInvColumn;

function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash text-xs text-blue-400';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye text-xs';
    }
}
window.togglePasswordVisibility = togglePasswordVisibility;

const defaultShopInvCols = {
    sku: true,
    part_name: true,
    description: true,
    category: true,
    available_qty: true,
    price: true,
    updated_at: true,
    actions: true
};

function toggleShopInvColumn(colName, isVisible) {
    if (!AppStore.shopInvColumnVisibility) {
        AppStore.shopInvColumnVisibility = { ...defaultShopInvCols };
    }
    AppStore.shopInvColumnVisibility[colName] = isVisible;
    renderShopInventoryTableHead();
    renderShopInventoryTable();
}

function renderShopInventoryTableHead() {
    const tr = document.getElementById('shop-admin-inventory-thead-tr');
    if (!tr) return;

    const cols = AppStore.shopInvColumnVisibility || defaultShopInvCols;
    let ths = '';

    if (cols.sku) {
        ths += `<th onclick="toggleSortShopInventory('sku')" class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap cursor-pointer select-none hover:text-blue-400 transition text-center">SKU <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.part_name) {
        ths += `<th onclick="toggleSortShopInventory('part_name')" class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap cursor-pointer select-none hover:text-blue-400 transition text-center">Part Name <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.description) {
        ths += `<th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap text-center">Description</th>`;
    }
    if (cols.category) {
        ths += `<th onclick="toggleSortShopInventory('category')" class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap cursor-pointer select-none hover:text-blue-400 transition text-center">Category <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.available_qty) {
        ths += `<th onclick="toggleSortShopInventory('available_qty')" class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap cursor-pointer select-none hover:text-blue-400 transition text-center">Available Qty <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.price) {
        ths += `<th onclick="toggleSortShopInventory('price')" class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap cursor-pointer select-none hover:text-blue-400 transition text-center">Price (IDR) <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.updated_at) {
        ths += `<th onclick="toggleSortShopInventory('updated_at')" class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap cursor-pointer select-none hover:text-blue-400 transition text-center">Last Updated <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.actions) {
        ths += `<th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap text-center">Actions</th>`;
    }

    tr.innerHTML = ths;
}

function renderShopInventoryTable(itemsToRender = null) {
    const tbody = document.getElementById('shop-admin-inventory-tbody');
    if (!tbody) return;
    const allItems = itemsToRender !== null ? itemsToRender : (AppStore.inventory || []);
    // Filter items to the current user's branch
    const currentBranchId = AppStore.user?.branch_id;
    const items = currentBranchId ? allItems.filter(item => item.branch_id == currentBranchId) : allItems;

    const cols = AppStore.shopInvColumnVisibility || defaultShopInvCols;
    const visibleColCount = Object.values(cols).filter(Boolean).length || 1;

    if (items.length === 0) {
        tbody.innerHTML = `
                    <tr>
                        <td colspan="${visibleColCount}" class="p-6 text-center text-slate-500 text-xs">No inventory parts found.</td>
                    </tr>
                `;
        return;
    }

    tbody.innerHTML = items.map(item => {
        let tds = '';
        if (cols.sku) {
            tds += `<td class="p-4 font-mono text-xs text-slate-400 font-semibold text-center truncate max-w-0" title="${escapeHtml(item.sku)}">${escapeHtml(item.sku)}</td>`;
        }
        if (cols.part_name) {
            tds += `<td class="p-4 font-medium text-slate-200 text-center truncate max-w-0" title="${escapeHtml(item.part_name)}">${escapeHtml(item.part_name)}</td>`;
        }
        if (cols.description) {
            tds += `<td class="p-4 text-xs text-slate-400 text-center truncate max-w-0" title="${item.description ? escapeHtml(item.description) : ''}">${item.description ? escapeHtml(item.description) : '<span class="text-slate-600 font-normal">-</span>'}</td>`;
        }
        if (cols.category) {
            tds += `<td class="p-4 text-center truncate max-w-0" title="${escapeHtml(item.category || 'General')}"><span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-400 font-bold text-[11px] border border-purple-500/20 inline-block truncate max-w-full">${escapeHtml(item.category || 'General')}</span></td>`;
        }
        if (cols.available_qty) {
            tds += `<td class="p-4 text-center font-bold text-slate-200">${item.available_qty}</td>`;
        }
        if (cols.price) {
            tds += `<td class="p-4 text-center font-bold text-slate-200">${formatIDR(item.price)}</td>`;
        }
        if (cols.updated_at) {
            tds += `<td class="p-4 text-xs text-slate-400 text-center whitespace-nowrap">${formatIndonesianDate(item.updated_at, false)}</td>`;
        }
        if (cols.actions) {
            if (hasPermission('edit_inventory')) {
                tds += `
                    <td class="p-4 text-center whitespace-nowrap">
                        <button onclick="openShopAdminEditInventoryModal(${item.id})" class="px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded text-xs transition">
                            <i class="fa-solid fa-edit mr-1"></i> Edit
                        </button>
                    </td>
                `;
            } else {
                tds += `<td class="p-4 text-center text-slate-500 text-xs">-</td>`;
            }
        }
        return `
            <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-center">
                ${tds}
            </tr>
        `;
    }).join('');
}

function openShopAdminAddInventoryModal() {
    document.getElementById('shop-add-inv-sku').value = '';
    document.getElementById('shop-add-inv-name').value = '';
    document.getElementById('shop-add-inv-qty').value = '0';
    document.getElementById('shop-add-inv-price').value = '0';

    const catSelect = document.getElementById('shop-add-inv-category');
    if (catSelect) {
        catSelect.innerHTML = getCategoryOptionsHtml('General');
    }

    const modal = document.getElementById('shop-admin-add-inventory-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeShopAdminAddInventoryModal() {
    const modal = document.getElementById('shop-admin-add-inventory-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

async function handleSubmitShopAdminAddInventory(e) {
    e.preventDefault();
    const sku = document.getElementById('shop-add-inv-sku').value.trim();
    const partName = document.getElementById('shop-add-inv-name').value.trim();
    const category = document.getElementById('shop-add-inv-category')?.value || 'General';
    const qty = parseInt(document.getElementById('shop-add-inv-qty').value) || 0;
    const price = parseFloat(document.getElementById('shop-add-inv-price').value) || 0;

    try {
        const res = await request('add_inventory_item', {
            sku: sku,
            part_name: partName,
            category: category,
            available_qty: qty,
            price: price
        });
        showToast(res.message, 'success');
        closeShopAdminAddInventoryModal();
        await fetchShopInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openShopAdminEditInventoryModal(itemId) {
    let item = (AppStore.inventory || []).find(i => i.id == itemId);
    if (!item) {
        item = (AppStore.masterInventory || []).find(i => i.id == itemId);
    }
    if (!item) return;

    // Populate basic fields
    document.getElementById('shop-edit-inv-id').value = item.id;
    document.getElementById('shop-edit-inv-subtitle').innerText = `Editing: ${item.part_name} (${item.sku})`;
    document.getElementById('shop-edit-inv-qty').value = item.available_qty;
    document.getElementById('shop-edit-inv-price').value = item.price;
    const descEl = document.getElementById('shop-edit-inv-description');
    if (descEl) descEl.value = item.description || '';

    // Category dropdown
    const catSelect = document.getElementById('shop-edit-inv-category');
    if (catSelect) {
        catSelect.innerHTML = getCategoryOptionsHtml(item.category || 'General');
    }

    // Connected Service dropdown
    const connSelect = document.getElementById('shop-edit-inv-connected-service');
    if (connSelect) {
        // Reset options
        connSelect.innerHTML = '<option value="">-- Choose Connected Service (Optional) --</option>';
        // Load Service Options asynchronously
        (async () => {
            try {
                const services = await request('get_service_options', {}, 'GET');
                (services || []).forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.name;
                    opt.textContent = s.name;
                    if (item.connected_service && s.name === item.connected_service) {
                        opt.selected = true;
                    }
                    connSelect.appendChild(opt);
                });
            } catch (err) {
                console.error('Failed to load Service Options', err);
                showToast(err.message, 'error');
            }
        })();
    }

    // Show the modal
    const modal = document.getElementById('shop-admin-edit-inventory-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeShopAdminEditInventoryModal() {
    const modal = document.getElementById('shop-admin-edit-inventory-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

async function handleSubmitShopAdminEditInventory(e) {
    e.preventDefault();
    const itemId = document.getElementById('shop-edit-inv-id').value;
    const qty = parseInt(document.getElementById('shop-edit-inv-qty').value);
    const price = parseFloat(document.getElementById('shop-edit-inv-price').value);
    const category = document.getElementById('shop-edit-inv-category')?.value || 'General';
    const description = document.getElementById('shop-edit-inv-description')?.value || '';

    try {
        const connectedService = document.getElementById('shop-edit-inv-connected-service')?.value || '';
        const res = await request('update_inventory', {
            id: itemId,
            inventory_id: itemId,
            available_qty: qty,
            price: price,
            category: category,
            connected_service: connectedService,
            description: description
        });
        showToast(res.message, 'success');
        closeShopAdminEditInventoryModal();
        if (AppStore.user && AppStore.user.role === 'super_admin') {
            await fetchGlobalInventory();
        } else {
            await loadShopAdminDashboard();
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openGlobalInventoryCSVModal() {
    const modal = document.getElementById('global-inventory-csv-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeGlobalInventoryCSVModal() {
    const modal = document.getElementById('global-inventory-csv-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function openSuperAdminAddInventoryModal() {
    const select = document.getElementById('super-add-inv-branch');
    if (select && AppStore.branches) {
        let html = '';
        AppStore.branches.forEach(b => {
            html += `<option value="${b.id}">${b.name}</option>`;
        });
        select.innerHTML = html;
    }

    const catSelect = document.getElementById('super-add-inv-category');
    if (catSelect) {
        catSelect.innerHTML = getCategoryOptionsHtml('General');
    }

    document.getElementById('super-add-inv-sku').value = '';
    document.getElementById('super-add-inv-name').value = '';
    document.getElementById('super-add-inv-qty').value = '0';
    document.getElementById('super-add-inv-price').value = '0';
    const superDesc = document.getElementById('super-add-inv-description');
    if (superDesc) superDesc.value = '';

    const modal = document.getElementById('super-admin-add-inventory-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeSuperAdminAddInventoryModal() {
    const modal = document.getElementById('super-admin-add-inventory-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

async function handleSubmitSuperAdminAddInventory(e) {
    e.preventDefault();
    const branchId = document.getElementById('super-add-inv-branch').value;
    const sku = document.getElementById('super-add-inv-sku').value.trim();
    const partName = document.getElementById('super-add-inv-name').value.trim();
    const category = document.getElementById('super-add-inv-category')?.value || 'General';
    const description = document.getElementById('super-add-inv-description')?.value || '';
    const qty = parseInt(document.getElementById('super-add-inv-qty').value) || 0;
    const price = parseFloat(document.getElementById('super-add-inv-price').value) || 0;

    try {
        const res = await request('add_inventory_item', {
            branch_id: branchId,
            sku: sku,
            part_name: partName,
            category: category,
            description: description,
            available_qty: qty,
            price: price
        });
        showToast(res.message, 'success');
        closeSuperAdminAddInventoryModal();
        await fetchGlobalInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

window.openShopAdminAddInventoryModal = openShopAdminAddInventoryModal;
window.closeShopAdminAddInventoryModal = closeShopAdminAddInventoryModal;
window.handleSubmitShopAdminAddInventory = handleSubmitShopAdminAddInventory;
window.openShopAdminEditInventoryModal = openShopAdminEditInventoryModal;
window.closeShopAdminEditInventoryModal = closeShopAdminEditInventoryModal;
window.handleSubmitShopAdminEditInventory = handleSubmitShopAdminEditInventory;
window.openGlobalInventoryCSVModal = openGlobalInventoryCSVModal;
window.closeGlobalInventoryCSVModal = closeGlobalInventoryCSVModal;
window.openSuperAdminAddInventoryModal = openSuperAdminAddInventoryModal;
window.closeSuperAdminAddInventoryModal = closeSuperAdminAddInventoryModal;
window.handleSubmitSuperAdminAddInventory = handleSubmitSuperAdminAddInventory;
window.filterMechanicPartOptions = filterMechanicPartOptions;
window.filterShopAdminInventory = filterShopAdminInventory;
window.toggleSortShopInventory = toggleSortShopInventory;
window.filterGlobalInventory = filterGlobalInventory;
window.toggleSortGlobalInventory = toggleSortGlobalInventory;
window.filterMaintenanceRecords = filterMaintenanceRecords;
window.exportMaintenanceRecordsCSV = exportMaintenanceRecordsCSV;
window.fetchMaintenanceRecords = fetchMaintenanceRecords;
window.openEditMaintenanceRecordModal = openEditMaintenanceRecordModal;
window.submitEditMaintenanceRecord = submitEditMaintenanceRecord;



// -------------------------------------------------------------
// Super Admin Workflows
// -------------------------------------------------------------

function setSuperAdminTab(tabName) {
    tabName = tabName || 'stats';
    AppStore.superAdminActiveTab = tabName;
    localStorage.setItem('sgpm_last_route', tabName);

    // Always ensure parent super-admin-workspace section inside app-main-content is visible
    showSection('super-admin-workspace');
    setActiveNavRoute(tabName);

    // Toggle active tabs
    const tabs = ['stats', 'reports', 'inventory', 'logs', 'maintenance', 'vehicles', 'customers', 'management'];
    tabs.forEach(name => {
        const el = document.getElementById(`super-admin-tab-${name}`);
        const btn = document.getElementById(`btn-tab-${name}`);
        if (el) el.classList.add('hidden');
        if (btn) btn.className = "px-5 py-2.5 font-bold text-sm rounded-lg transition duration-200 text-slate-400 hover:text-slate-200 hover:bg-slate-900/40 shrink-0";
    });

    const targetEl = document.getElementById(`super-admin-tab-${tabName}`);
    const targetBtn = document.getElementById(`btn-tab-${tabName}`);
    if (targetEl) targetEl.classList.remove('hidden');
    if (targetBtn) targetBtn.className = "px-5 py-2.5 font-bold text-sm rounded-lg transition duration-200 bg-purple-50 text-purple-700 border border-purple-200 shrink-0";

    if (tabName === 'stats') {
        loadSuperAdminStats();
    } else if (tabName === 'reports') {
        fetchReportSummary();
    } else if (tabName === 'inventory') {
        fetchGlobalInventory();
    } else if (tabName === 'logs') {
        fetchAuditLogs();
    } else if (tabName === 'maintenance') {
        fetchMaintenanceRecords();
    } else if (tabName === 'vehicles') {
        fetchVehiclesAndCustomers();
    } else if (tabName === 'customers') {
        fetchVehiclesAndCustomers();
    } else if (tabName === 'management') {
        loadManagementSidebarCounts();
        setManagementSubTab(AppStore.managementActiveSubTab || 'branches');
    }
}

async function loadManagementSidebarCounts() {
    try {
        const data = await request('get_management_counts', {}, 'GET');
        if (document.getElementById('badge-mgt-branches-count')) document.getElementById('badge-mgt-branches-count').innerText = data.branches || 0;
        if (document.getElementById('badge-mgt-users-count')) document.getElementById('badge-mgt-users-count').innerText = data.users || 0;
        if (document.getElementById('badge-mgt-customers-count')) document.getElementById('badge-mgt-customers-count').innerText = data.customers || 0;
        if (document.getElementById('badge-mgt-vehicles-count')) document.getElementById('badge-mgt-vehicles-count').innerText = data.vehicles || 0;
        if (document.getElementById('badge-mgt-common-issues-count')) document.getElementById('badge-mgt-common-issues-count').innerText = data['common-issues'] || 0;
        if (document.getElementById('badge-mgt-form-items-count')) document.getElementById('badge-mgt-form-items-count').innerText = data['form-items'] || 0;
        if (document.getElementById('badge-mgt-categories-count')) document.getElementById('badge-mgt-categories-count').innerText = data.categories || 0;
        if (document.getElementById('badge-mgt-service-options-count')) document.getElementById('badge-mgt-service-options-count').innerText = data['service-options'] || 0;
        if (document.getElementById('badge-mgt-other-services-count')) document.getElementById('badge-mgt-other-services-count').innerText = data['other-services'] || 0;
    } catch (err) {
        console.error('Failed to load sidebar counts', err);
    }
}

async function fetchMaintenanceRecords() {
    const tbody = document.getElementById('super-admin-maintenance-tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-slate-500">Loading records...</td></tr>`;

    // Populate branch filter options if empty
    const bFilter = document.getElementById('super-admin-records-branch-filter');
    if (bFilter && bFilter.options.length <= 1 && AppStore.branches) {
        let html = '<option value="">All Branches</option>';
        AppStore.branches.forEach(b => {
            html += `<option value="${b.id}">${escapeHtml(b.name)}</option>`;
        });
        bFilter.innerHTML = html;
    }

    try {
        const data = await request('get_all_maintenance_records', {}, 'GET');
        AppStore.allMaintenanceRecords = Array.isArray(data) ? data : [];
        filterMaintenanceRecords();
    } catch (err) {
        if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-rose-500 font-semibold">Error: ${escapeHtml(err.message)}</td></tr>`;
        showToast(err.message, 'error');
    }
}

const defaultRecordsCols = { date: true, branch: true, customer: true, vehicle: true, notes: true, status: true, billing: true, actions: true };
if (!AppStore.recordsColumnVisibility) {
    let saved = null;
    try {
        const raw = localStorage.getItem('rec_cols_vis');
        if (raw) saved = JSON.parse(raw);
    } catch (e) { }
    AppStore.recordsColumnVisibility = saved || { ...defaultRecordsCols };
}

window.toggleRecordsColumnMenu = function (e) {
    if (e) e.stopPropagation();
    const menu = document.getElementById('records-column-menu');
    if (menu) {
        menu.classList.toggle('hidden');
        syncRecordsColumnCheckboxes();
    }
};

window.toggleRecordsColumn = function (key, isVisible) {
    AppStore.recordsColumnVisibility[key] = !!isVisible;
    try { localStorage.setItem('rec_cols_vis', JSON.stringify(AppStore.recordsColumnVisibility)); } catch (e) { }
    filterMaintenanceRecords();
};

window.resetRecordsColumns = function () {
    AppStore.recordsColumnVisibility = { ...defaultRecordsCols };
    try { localStorage.setItem('rec_cols_vis', JSON.stringify(AppStore.recordsColumnVisibility)); } catch (e) { }
    syncRecordsColumnCheckboxes();
    filterMaintenanceRecords();
};

function syncRecordsColumnCheckboxes() {
    const cols = AppStore.recordsColumnVisibility || defaultRecordsCols;
    Object.keys(cols).forEach(k => {
        const chk = document.querySelector(`#records-column-menu input[data-rec-col="${k}"]`);
        if (chk) chk.checked = !!cols[k];
    });
}

function renderMaintenanceRecordsHeader() {
    const theadTr = document.getElementById('super-admin-maintenance-thead-tr');
    if (!theadTr) return;
    const cols = AppStore.recordsColumnVisibility || defaultRecordsCols;
    let html = '';
    if (cols.date) html += `<th onclick="toggleSortRecords('created_at')" class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center">Date Logged <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    if (cols.branch) html += `<th onclick="toggleSortRecords('branch_name')" class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center">Branch <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    if (cols.customer) html += `<th onclick="toggleSortRecords('customer_name')" class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center">Customer Owner <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    if (cols.vehicle) html += `<th class="p-4 font-semibold uppercase tracking-wider text-xs text-center">Vehicle Info</th>`;
    if (cols.notes) html += `<th class="p-4 font-semibold uppercase tracking-wider text-xs text-center">Job Notes</th>`;
    if (cols.status) html += `<th onclick="toggleSortRecords('status')" class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center">Job Status <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    if (cols.billing) html += `<th onclick="toggleSortRecords('parts_cost')" class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center">Total Billing <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    if (cols.actions) html += `<th class="p-4 font-semibold uppercase tracking-wider text-xs text-center">Actions</th>`;
    theadTr.innerHTML = html;
}

function filterMaintenanceRecords() {
    renderMaintenanceRecordsHeader();
    const searchVal = (document.getElementById('super-admin-records-search')?.value || '').toLowerCase().trim();
    const branchVal = document.getElementById('super-admin-records-branch-filter')?.value || '';
    const statusVal = document.getElementById('super-admin-records-status-filter')?.value || '';
    const colField = document.getElementById('super-admin-records-column-select')?.value || '';
    const colVal = (document.getElementById('super-admin-records-column-val')?.value || '').toLowerCase().trim();
    const sortVal = document.getElementById('super-admin-records-sort-select')?.value || 'created_at-desc';
    const dateFromVal = document.getElementById('super-admin-records-date-from')?.value || '';
    const dateToVal = document.getElementById('super-admin-records-date-to')?.value || '';

    let records = (AppStore.allMaintenanceRecords || []).filter(m => {
        if (branchVal && m.branch_id != branchVal) return false;
        if (statusVal && (m.status || '').toUpperCase() !== statusVal.toUpperCase()) return false;
        if (dateFromVal && m.created_at && m.created_at.split(' ')[0] < dateFromVal) return false;
        if (dateToVal && m.created_at && m.created_at.split(' ')[0] > dateToVal) return false;
        if (searchVal) {
            const matchQuery =
                (m.id && String(m.id).includes(searchVal)) ||
                (m.license_plate && String(m.license_plate).toLowerCase().includes(searchVal)) ||
                (m.customer_name && String(m.customer_name).toLowerCase().includes(searchVal)) ||
                (m.mechanic_name && String(m.mechanic_name).toLowerCase().includes(searchVal)) ||
                (m.description && String(m.description).toLowerCase().includes(searchVal)) ||
                (m.vin && String(m.vin).toLowerCase().includes(searchVal));
            if (!matchQuery) return false;
        }

        // Custom Column Filter
        if (colField && colVal) {
            if (colField === 'parts_cost') {
                let totalPartsCost = 0;
                if (m.parts && m.parts.length > 0) {
                    m.parts.forEach(p => { totalPartsCost += (parseFloat(p.price_at_use) || 0) * (parseInt(p.quantity_used) || 1); });
                }
                const minCost = parseFloat(colVal);
                if (!isNaN(minCost) && totalPartsCost < minCost) return false;
            } else {
                const targetFieldVal = (m[colField] || '').toString().toLowerCase();
                if (!targetFieldVal.includes(colVal)) return false;
            }
        }
        return true;
    });

    // Sorting logic
    const [sortField, sortOrder] = sortVal.split('-');
    records.sort((a, b) => {
        let valA, valB;
        if (sortField === 'parts_cost') {
            valA = 0;
            valB = 0;
            if (a.parts) a.parts.forEach(p => { valA += (parseFloat(p.price_at_use) || 0) * (parseInt(p.quantity_used) || 1); });
            if (b.parts) b.parts.forEach(p => { valB += (parseFloat(p.price_at_use) || 0) * (parseInt(p.quantity_used) || 1); });
        } else if (sortField === 'created_at') {
            valA = new Date(a.created_at || 0).getTime();
            valB = new Date(b.created_at || 0).getTime();
        } else {
            valA = (a[sortField] || '').toString().toLowerCase();
            valB = (b[sortField] || '').toString().toLowerCase();
        }

        if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
        if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
        return 0;
    });

    AppStore.filteredMaintenanceRecords = records;

    // Render Pagination UI
    const totalCount = records.length;
    renderPaginationUI('maintenanceRecords', totalCount, 'super-admin-maintenance-pagination-top', 'super-admin-maintenance-pagination-bottom');

    const pageSize = getPageSize('maintenanceRecords');
    const pageState = AppStore.pagination['maintenanceRecords'] || { page: 1 };
    const pageNum = pageState.page || 1;
    const sliced = records.slice((pageNum - 1) * pageSize, pageNum * pageSize);

    renderMaintenanceRecordsTable(sliced);
}

function toggleSortRecords(field) {
    const sortSelect = document.getElementById('super-admin-records-sort-select');
    if (!sortSelect) return;

    const current = sortSelect.value;
    if (current.startsWith(field)) {
        sortSelect.value = current.endsWith('-asc') ? `${field}-desc` : `${field}-asc`;
    } else {
        sortSelect.value = `${field}-asc`;
    }
    filterMaintenanceRecords();
}

window.toggleSortRecords = toggleSortRecords;

async function fetchShopMaintenanceRecords() {
    const tbody = document.getElementById('shop-maintenance-tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-slate-500">Loading branch records...</td></tr>`;

    try {
        const userBranchId = AppStore.user ? AppStore.user.branch_id : null;
        const data = await request('get_all_maintenance_records', userBranchId ? { branch_id: userBranchId } : {}, 'GET');
        AppStore.shopMaintenanceRecords = Array.isArray(data) ? data : [];
        filterShopMaintenanceRecords();
    } catch (err) {
        if (tbody) tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-rose-500 font-semibold">Error: ${escapeHtml(err.message)}</td></tr>`;
        showToast(err.message, 'error');
    }
}

function filterShopMaintenanceRecords() {
    const searchVal = (document.getElementById('shop-records-search')?.value || '').toLowerCase().trim();
    const statusVal = document.getElementById('shop-records-status-filter')?.value || '';
    const sortVal = document.getElementById('shop-records-sort-select')?.value || 'created_at-desc';
    const dateFromVal = document.getElementById('shop-records-date-from')?.value || '';
    const dateToVal = document.getElementById('shop-records-date-to')?.value || '';
    const userBranchId = AppStore.user ? AppStore.user.branch_id : null;

    let records = (AppStore.shopMaintenanceRecords || []).filter(m => {
        if (userBranchId && m.branch_id != userBranchId) return false;
        if (statusVal && (m.status || '').toUpperCase() !== statusVal.toUpperCase()) return false;
        if (dateFromVal && m.created_at && m.created_at.split(' ')[0] < dateFromVal) return false;
        if (dateToVal && m.created_at && m.created_at.split(' ')[0] > dateToVal) return false;
        if (searchVal) {
            const matchQuery =
                (m.id && String(m.id).includes(searchVal)) ||
                (m.license_plate && String(m.license_plate).toLowerCase().includes(searchVal)) ||
                (m.customer_name && String(m.customer_name).toLowerCase().includes(searchVal)) ||
                (m.mechanic_name && String(m.mechanic_name).toLowerCase().includes(searchVal)) ||
                (m.description && String(m.description).toLowerCase().includes(searchVal)) ||
                (m.vin && String(m.vin).toLowerCase().includes(searchVal));
            if (!matchQuery) return false;
        }
        return true;
    });

    const [sortField, sortOrder] = sortVal.split('-');
    records.sort((a, b) => {
        let valA, valB;
        if (sortField === 'parts_cost') {
            valA = 0; valB = 0;
            if (a.parts) a.parts.forEach(p => { valA += (parseFloat(p.price_at_use) || 0) * (parseInt(p.quantity_used) || 1); });
            if (b.parts) b.parts.forEach(p => { valB += (parseFloat(p.price_at_use) || 0) * (parseInt(p.quantity_used) || 1); });
        } else if (sortField === 'created_at') {
            valA = new Date(a.created_at || 0).getTime();
            valB = new Date(b.created_at || 0).getTime();
        } else {
            valA = (a[sortField] || '').toString().toLowerCase();
            valB = (b[sortField] || '').toString().toLowerCase();
        }

        if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
        if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
        return 0;
    });

    const totalCount = records.length;
    renderPaginationUI('shopMaintenanceRecords', totalCount, 'shop-maintenance-pagination-top', 'shop-maintenance-pagination-bottom');

    const pageSize = getPageSize('shopMaintenanceRecords');
    const pageState = AppStore.pagination['shopMaintenanceRecords'] || { page: 1 };
    const pageNum = pageState.page || 1;
    const sliced = records.slice((pageNum - 1) * pageSize, pageNum * pageSize);

    renderShopMaintenanceRecordsTable(sliced);
}
window.filterShopMaintenanceRecords = filterShopMaintenanceRecords;
window.fetchShopMaintenanceRecords = fetchShopMaintenanceRecords;

async function exportShopMaintenanceRecordsExcel() {
    const searchVal = (document.getElementById('shop-records-search')?.value || '').trim();
    const statusVal = (document.getElementById('shop-records-status-filter')?.value || '').trim();
    const dateFromVal = (document.getElementById('shop-records-date-from')?.value || '').trim();
    const dateToVal = (document.getElementById('shop-records-date-to')?.value || '').trim();
    const userBranchId = AppStore.user ? AppStore.user.branch_id : null;

    const params = new URLSearchParams();
    params.append('format', 'json');
    if (userBranchId) params.append('branch_id', userBranchId);
    if (searchVal) params.append('search', searchVal);
    if (statusVal) params.append('status', statusVal);
    if (dateFromVal) params.append('date_from', dateFromVal);
    if (dateToVal) params.append('date_to', dateToVal);

    try {
        const rows = await request('export_shop_maintenance_records', Object.fromEntries(params), 'GET');
        if (!Array.isArray(rows) || rows.length === 0) {
            showToast('No branch maintenance records available to export.', 'warning');
            return;
        }

        if (typeof XLSX === 'undefined') {
            showToast('SheetJS library not loaded.', 'error');
            return;
        }

        const worksheet = XLSX.utils.json_to_sheet(rows);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Branch Maintenance Records");
        const filename = `branch_maintenance_records_${dateStrFilename()}.xlsx`;
        XLSX.writeFile(workbook, filename);
        showToast('Exported Excel (.xlsx) file successfully!', 'success');
    } catch (err) {
        showToast('Failed to export Excel file: ' + err.message, 'error');
    }
}
window.exportShopMaintenanceRecordsExcel = exportShopMaintenanceRecordsExcel;

let currentExportScope = 'super_admin';

function openExportExcelModal(scope = 'super_admin') {
    currentExportScope = scope;
    const modal = document.getElementById('modal-export-excel-filter');
    const dateFrom = document.getElementById('export-modal-date-from');
    const dateTo = document.getElementById('export-modal-date-to');
    if (dateFrom) dateFrom.value = '';
    if (dateTo) dateTo.value = '';
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
window.openExportExcelModal = openExportExcelModal;

function closeExportExcelModal() {
    const modal = document.getElementById('modal-export-excel-filter');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}
window.closeExportExcelModal = closeExportExcelModal;

async function submitExportExcelWithDateFilter() {
    const dateFromVal = (document.getElementById('export-modal-date-from')?.value || '').trim();
    const dateToVal = (document.getElementById('export-modal-date-to')?.value || '').trim();

    closeExportExcelModal();

    const params = new URLSearchParams();
    params.append('format', 'json');

    if (currentExportScope === 'shop_admin') {
        const searchVal = (document.getElementById('shop-records-search')?.value || '').trim();
        const statusVal = (document.getElementById('shop-records-status-filter')?.value || '').trim();
        const userBranchId = AppStore.user ? AppStore.user.branch_id : null;

        if (userBranchId) params.append('branch_id', userBranchId);
        if (searchVal) params.append('search', searchVal);
        if (statusVal) params.append('status', statusVal);
    } else {
        const searchVal = (document.getElementById('super-admin-records-search')?.value || '').trim();
        const branchVal = (document.getElementById('super-admin-records-branch-filter')?.value || '').trim();
        const statusVal = (document.getElementById('super-admin-records-status-filter')?.value || '').trim();

        if (branchVal) params.append('branch_id', branchVal);
        if (searchVal) params.append('search', searchVal);
        if (statusVal) params.append('status', statusVal);
    }

    if (dateFromVal) params.append('date_from', dateFromVal);
    if (dateToVal) params.append('date_to', dateToVal);

    try {
        const rows = await request('export_shop_maintenance_records', Object.fromEntries(params), 'GET');
        if (!Array.isArray(rows) || rows.length === 0) {
            showToast('No maintenance records found for the selected date range.', 'warning');
            return;
        }

        if (typeof XLSX === 'undefined') {
            showToast('SheetJS library not loaded.', 'error');
            return;
        }

        const worksheet = XLSX.utils.json_to_sheet(rows);
        const workbook = XLSX.utils.book_new();
        const sheetName = currentExportScope === 'shop_admin' ? "Branch Maintenance Records" : "Global Maintenance Records";
        const filePrefix = currentExportScope === 'shop_admin' ? "branch_maintenance_records" : "global_maintenance_records";
        XLSX.utils.book_append_sheet(workbook, worksheet, sheetName);
        XLSX.writeFile(workbook, `${filePrefix}_${dateStrFilename()}.xlsx`);
        showToast('Exported Excel (.xlsx) file successfully!', 'success');
    } catch (err) {
        showToast('Failed to export Excel file: ' + err.message, 'error');
    }
}
window.submitExportExcelWithDateFilter = submitExportExcelWithDateFilter;

function renderShopMaintenanceRecordsTable(data) {
    const tbody = document.getElementById('shop-maintenance-tbody');
    if (!tbody) return;

    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-slate-500 font-semibold">No matching branch maintenance records found.</td></tr>`;
        return;
    }

    tbody.innerHTML = data.map(m => {
        const dateStr = formatIndonesianDate(m.created_at);
        const effectiveStart = m.start_time || m.created_at;
        const effectiveEnd = m.end_time || m.stopped_at;
        const durationStr = (effectiveStart && effectiveEnd) ? calculateDurationString(effectiveStart, effectiveEnd) : 'Active Job';

        const laborFee = parseFloat(m.labor_fee || 0);
        const otherFee = parseFloat(m.other_expenses_fee || 0);
        let totalPartsCost = 0;
        const breakdownItems = [];

        if (laborFee > 0) {
            const serviceLabel = m.service_name ? escapeHtml(m.service_name) : 'Labor Service';
            breakdownItems.push(`<span class="text-cyan-400">🔧 ${serviceLabel}: ${formatIDR(laborFee)}</span>`);
        }
        if (otherFee > 0) {
            const otherLabel = m.other_expenses_category ? escapeHtml(m.other_expenses_category) : 'Other Service';
            breakdownItems.push(`<span class="text-amber-400">🏷️ ${otherLabel}: ${formatIDR(otherFee)}</span>`);
        }
        if (m.parts && m.parts.length > 0) {
            m.parts.forEach(p => {
                const cost = (parseFloat(p.price_at_use) || 0) * (parseInt(p.quantity_used) || 1);
                totalPartsCost += cost;
                breakdownItems.push(`📦 ${escapeHtml(p.part_name || 'Part')} (x${p.quantity_used || 1}): ${formatIDR(cost)}`);
            });
        }

        const overallTotal = totalPartsCost + laborFee + otherFee;
        const totalBillingDisplay = formatIDR(overallTotal);
        const billingDetailsDisplay = breakdownItems.length > 0
            ? `<div class="space-y-0.5 text-[10px] text-slate-400 font-mono mt-1 text-left">${breakdownItems.join('<br>')}</div>`
            : '<span class="text-slate-500 block text-[10px]">No Billed Items</span>';

        const curStatus = (m.status || 'pending').toLowerCase();
        let statusClass = 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800';
        if (curStatus === 'completed') {
            statusClass = 'bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/80 dark:text-emerald-400 dark:border-emerald-800/40';
        } else if (curStatus === 'in_progress') {
            statusClass = 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/80 dark:text-amber-400 dark:border-amber-800/40';
        } else if (curStatus === 'cancelled') {
            statusClass = 'bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-950/80 dark:text-rose-400 dark:border-rose-800/40';
        }
        const photoHtml = m.photo_path ? `<a href="${escapeHtml(m.photo_path)}" target="_blank" class="text-blue-400 hover:underline block mt-1"><i class="fa-solid fa-image mr-1"></i>View Photo</a>` : '';

        const vinVal = escapeHtml(m.vin || m.frame_number || 'N/A');
        const makeVal = escapeHtml(m.make || '');
        const modelVal = escapeHtml(m.model || '');
        const plateVal = escapeHtml(m.license_plate || 'N/A');
        const typeVal = escapeHtml(m.vehicle_type || 'N/A');
        const ctrlVal = escapeHtml(m.controller_number || 'N/A');
        const custName = escapeHtml(m.customer_name || 'Unbound Customer');
        const branchName = escapeHtml(m.branch_name || 'Branch');
        const mechName = escapeHtml(m.mechanic_name || 'Unassigned');
        const descVal = escapeHtml(m.description || 'No description');

        const vInfo = `
            <div class="font-bold text-slate-200 text-center">${makeVal} ${modelVal}</div>
            <div class="text-[10px] text-slate-400 font-mono text-center">
                Plate: ${plateVal} | VIN: ${vinVal}<br>
                Type: ${typeVal} | Ctrl: ${ctrlVal}
            </div>
        `;

        return `
            <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                <td class="p-4 text-center font-mono">
                    <span class="font-bold text-blue-400 block text-xs">${formatJobCode(m.id, m.created_at)}</span>
                    <span class="text-[10px] text-slate-400 block mt-0.5">${dateStr}</span>
                </td>
                <td class="p-4 text-center">
                    <span class="font-bold text-slate-300 block">${branchName}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Mech: ${mechName}</span>
                </td>
                <td class="p-4 text-center font-semibold text-slate-200">${custName}</td>
                <td class="p-4 text-center">${vInfo}</td>
                <td class="p-4 text-center text-slate-300 max-w-xs truncate font-medium mx-auto" title="${descVal}">${descVal}</td>
                <td class="p-4 text-center">
                    <span class="px-2 py-0.5 rounded-full inline-block font-semibold text-[10px] ${statusClass}">${curStatus.toUpperCase().replace('_', ' ')}</span>
                    <span class="block text-[10px] text-slate-400 mt-1 font-mono">${durationStr}</span>
                    ${photoHtml}
                </td>
                <td class="p-4 text-center">
                    <span class="font-bold text-emerald-400 font-mono block text-sm">${totalBillingDisplay}</span>
                    ${billingDetailsDisplay}
                </td>
                <td class="p-4 text-center">
                    <div class="flex flex-col items-center justify-center gap-1.5 w-full">
                        <button onclick="openRecordDetailsModal(${m.id})" class="w-20 py-1 bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 font-bold rounded border border-cyan-500/20 text-xs transition flex items-center justify-center gap-1 mx-auto">
                            <i class="fa-solid fa-eye text-[11px]"></i> Details
                        </button>
                        <button onclick="printMaintenanceRecord(${m.id})" class="w-20 py-1 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-400 font-bold rounded border border-emerald-500/20 text-xs transition flex items-center justify-center gap-1.5 mx-auto">
                            <i class="fa-solid fa-print text-[11px]"></i> Print
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function renderMaintenanceRecordsTable(data) {
    const tbody = document.getElementById('super-admin-maintenance-tbody');
    if (!tbody) return;

    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-slate-500 font-semibold">No matching maintenance records found.</td></tr>`;
        return;
    }

    tbody.innerHTML = data.map(m => {
        const dateStr = formatIndonesianDate(m.created_at);
        const effectiveStart = m.start_time || m.created_at;
        const effectiveEnd = m.end_time || m.stopped_at;
        const durationStr = (effectiveStart && effectiveEnd) ? calculateDurationString(effectiveStart, effectiveEnd) : 'Active Job';

        // --- COMPLETE BILLING BREAKDOWN LOGIC ---
        const laborFee = parseFloat(m.labor_fee || 0);
        const otherFee = parseFloat(m.other_expenses_fee || 0);

        let totalPartsCost = 0;
        const breakdownItems = [];

        // 1. Format Service Options / Labor Fees
        if (laborFee > 0) {
            const serviceLabel = m.service_name ? escapeHtml(m.service_name) : 'Labor Service';
            breakdownItems.push(`<span class="text-cyan-400">🔧 ${serviceLabel}: ${formatIDR(laborFee)}</span>`);
        }

        // 2. Format Other Services / Fees
        if (otherFee > 0) {
            const otherLabel = m.other_expenses_category ? escapeHtml(m.other_expenses_category) : 'Other Service';
            breakdownItems.push(`<span class="text-amber-400">🏷️ ${otherLabel}: ${formatIDR(otherFee)}</span>`);
        }

        // 3. Format Spare Parts Billed
        if (m.parts && m.parts.length > 0) {
            m.parts.forEach(p => {
                const cost = (parseFloat(p.price_at_use) || 0) * (parseInt(p.quantity_used) || 1);
                totalPartsCost += cost;
                breakdownItems.push(`📦 ${escapeHtml(p.part_name || 'Part')} (x${p.quantity_used || 1}): ${formatIDR(cost)}`);
            });
        }

        // Compute Grand Total and Details string inside callback scope
        const overallTotal = totalPartsCost + laborFee + otherFee;
        const totalBillingDisplay = formatIDR(overallTotal);

        const billingDetailsDisplay = breakdownItems.length > 0
            ? `<div class="space-y-0.5 text-[10px] text-slate-400 font-mono mt-1 text-left">${breakdownItems.join('<br>')}</div>`
            : '<span class="text-slate-500 block text-[10px]">No Billed Items</span>';

        const curStatus = (m.status || 'pending').toLowerCase();
        let statusClass = 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800';
        if (curStatus === 'completed') {
            statusClass = 'bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/80 dark:text-emerald-400 dark:border-emerald-800/40';
        } else if (curStatus === 'in_progress') {
            statusClass = 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/80 dark:text-amber-400 dark:border-amber-800/40';
        } else if (curStatus === 'cancelled') {
            statusClass = 'bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-950/80 dark:text-rose-400 dark:border-rose-800/40';
        }
        const photoHtml = m.photo_path ? `<a href="${escapeHtml(m.photo_path)}" target="_blank" class="text-blue-400 hover:underline block mt-1"><i class="fa-solid fa-image mr-1"></i>View Photo</a>` : '';

        const vinVal = escapeHtml(m.vin || m.frame_number || 'N/A');
        const makeVal = escapeHtml(m.make || '');
        const modelVal = escapeHtml(m.model || '');
        const plateVal = escapeHtml(m.license_plate || 'N/A');
        const typeVal = escapeHtml(m.vehicle_type || 'N/A');
        const ctrlVal = escapeHtml(m.controller_number || 'N/A');
        const custName = escapeHtml(m.customer_name || 'Unbound Customer');
        const branchName = escapeHtml(m.branch_name || 'Global Branch');
        const mechName = escapeHtml(m.mechanic_name || 'Unassigned');
        const descVal = escapeHtml(m.description || 'No description');

        const vInfo = `
            <div class="font-bold text-slate-200 text-center">${makeVal} ${modelVal}</div>
            <div class="text-[10px] text-slate-400 font-mono text-center">
                Plate: ${plateVal} | VIN: ${vinVal}<br>
                Type: ${typeVal} | Ctrl: ${ctrlVal}
            </div>
        `;

        const cols = AppStore.recordsColumnVisibility || defaultRecordsCols;
        let rowHtml = '<tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">';
        if (cols.date) {
            rowHtml += `
                <td class="p-4 text-center font-mono">
                    <span class="font-bold text-blue-400 block text-xs">${formatJobCode(m.id, m.created_at)}</span>
                    <span class="text-[10px] text-slate-400 block mt-0.5">${dateStr}</span>
                </td>`;
        }
        if (cols.branch) {
            rowHtml += `
                <td class="p-4 text-center">
                    <span class="font-bold text-slate-300 block">${branchName}</span>
                    <span class="text-[10px] text-slate-400 block font-medium">Mech: ${mechName}</span>
                </td>`;
        }
        if (cols.customer) {
            rowHtml += `<td class="p-4 text-center font-semibold text-slate-200">${custName}</td>`;
        }
        if (cols.vehicle) {
            rowHtml += `<td class="p-4 text-center">${vInfo}</td>`;
        }
        if (cols.notes) {
            rowHtml += `<td class="p-4 text-center text-slate-300 max-w-xs truncate font-medium mx-auto" title="${descVal}">${descVal}</td>`;
        }
        if (cols.status) {
            rowHtml += `
                <td class="p-4 text-center">
                    <span class="px-2 py-0.5 rounded-full inline-block font-semibold text-[10px] ${statusClass}">${curStatus.toUpperCase().replace('_', ' ')}</span>
                    <span class="block text-[10px] text-slate-400 mt-1 font-mono">${durationStr}</span>
                    ${photoHtml}
                </td>`;
        }
        if (cols.billing) {
            rowHtml += `
                <td class="p-4 text-center">
                    <span class="font-bold text-emerald-400 font-mono block text-sm">${totalBillingDisplay}</span>
                    ${billingDetailsDisplay}
                </td>`;
        }
        if (cols.actions) {
            const editBtnHtml = hasPermission('edit_maintenance') ? `
                        <button onclick="openEditMaintenanceRecordModal(${m.id})" class="w-20 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 font-bold rounded border border-blue-500/20 text-xs transition flex items-center justify-center gap-1 mx-auto">
                            <i class="fa-solid fa-pen-to-square text-[11px]"></i> Edit
                        </button>` : '';

            rowHtml += `
                <td class="p-4 text-center">
                    <div class="flex flex-col items-center justify-center gap-1.5 w-full">
                        <button onclick="openRecordDetailsModal(${m.id})" class="w-20 py-1 bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 font-bold rounded border border-cyan-500/20 text-xs transition flex items-center justify-center gap-1 mx-auto">
                            <i class="fa-solid fa-eye text-[11px]"></i> Details
                        </button>
                        ${editBtnHtml}
                        <button onclick="printMaintenanceRecord(${m.id})" class="w-20 py-1 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-400 font-bold rounded border border-emerald-500/20 text-xs transition flex items-center justify-center gap-1.5 mx-auto">
                            <i class="fa-solid fa-print text-[11px]"></i> Print
                        </button>
                    </div>
                </td>`;
        }
        rowHtml += '</tr>';
        return rowHtml;
    }).join('');
}

function openRecordDetailsModal(recordId) {
    const records = AppStore.shopMaintenanceRecords || AppStore.allMaintenanceRecords || AppStore.filteredMaintenanceRecords || AppStore.calendarEvents || [];
    let m = records.find(r => r.id == recordId);
    if (!m && AppStore.allMaintenanceRecords) m = AppStore.allMaintenanceRecords.find(r => r.id == recordId);
    if (!m && AppStore.calendarEvents) m = AppStore.calendarEvents.find(r => r.id == recordId);
    if (!m) {
        showToast('Maintenance record not found.', 'error');
        return;
    }

    document.getElementById('record-detail-title').innerText = `Maintenance Record: ${formatJobCode(m.id, m.created_at)}`;
    document.getElementById('record-detail-title').dataset.id = m.id;
    document.getElementById('record-detail-subtitle').innerText = `Logged on ${formatIndonesianDate(m.created_at)}`;

    const vinVal = m.vin || m.frame_number || 'N/A';
    const durationStr = (m.end_time || m.stopped_at) ? calculateDurationString(m.created_at, m.end_time || m.stopped_at) : 'Active Job';
    const curRecStatus = (m.status || 'pending').toLowerCase();
    let statusClass = 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800';
    if (curRecStatus === 'completed') {
        statusClass = 'bg-emerald-100 text-emerald-800 border border-emerald-300 dark:bg-emerald-950/80 dark:text-emerald-400 dark:border-emerald-800/40';
    } else if (curRecStatus === 'in_progress') {
        statusClass = 'bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-950/80 dark:text-amber-400 dark:border-amber-800/40';
    } else if (curRecStatus === 'cancelled') {
        statusClass = 'bg-rose-100 text-rose-800 border border-rose-300 dark:bg-rose-950/80 dark:text-rose-400 dark:border-rose-800/40';
    }

    // Common issues badges
    let commonIssuesHtml = '<span class="text-slate-500 italic">None reported</span>';
    if (m.common_issues) {
        const issues = m.common_issues.split(',').map(i => i.trim()).filter(Boolean);
        if (issues.length > 0) {
            commonIssuesHtml = issues.map(iss => `<span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20 font-semibold text-[11px]">${escapeHtml(iss)}</span>`).join(' ');
        }
    }

    // Checklist / mechanic form items
    let formItemsHtml = '<span class="text-slate-500 italic">No checklist recorded</span>';
    if (m.mechanic_form_items) {
        const items = m.mechanic_form_items.split(',').map(i => i.trim()).filter(Boolean);
        if (items.length > 0) {
            formItemsHtml = `<ul class="list-disc list-inside space-y-1 text-slate-300">${items.map(it => `<li><i class="fa-solid fa-square-check text-emerald-400 mr-1.5"></i>${escapeHtml(it)}</li>`).join('')}</ul>`;
        }
    }

    // Parts table
    // 1. Calculate all fee components
    const laborFee = parseFloat(m.labor_fee || 0);
    const otherFee = parseFloat(m.other_expenses_fee || 0);

    let partsTotalCost = 0;
    let partsTableRowsHtml = '';

    // 2. Build parts table rows if parts exist
    if (m.parts && m.parts.length > 0) {
        partsTableRowsHtml = m.parts.map(p => {
            const cost = (parseFloat(p.price_at_use) || 0) * (parseInt(p.quantity_used) || 1);
            partsTotalCost += cost;
            return `
            <tr class="border-b border-slate-800/60 hover:bg-slate-900/30">
                <td class="p-2 font-mono text-slate-400">${escapeHtml(p.sku || 'N/A')}</td>
                <td class="p-2 font-bold text-slate-200">${escapeHtml(p.part_name)}</td>
                <td class="p-2 text-center font-bold text-slate-200">${p.quantity_used}</td>
                <td class="p-2 text-right font-mono text-slate-300">${formatIDR(p.price_at_use)}</td>
                <td class="p-2 text-right font-mono font-bold text-emerald-400">${formatIDR(cost)}</td>
            </tr>
        `;
        }).join('');
    }

    // 3. Compute overall Grand Total
    const modalGrandTotal = partsTotalCost + laborFee + otherFee;

    // Photo preview
    let photoPreviewHtml = '';
    if (m.photo_path) {
        photoPreviewHtml = `
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-800 space-y-2">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block"><i class="fa-solid fa-camera text-blue-400 mr-1"></i> Inspection Photo Attachment</span>
                        <div class="relative max-w-sm rounded-lg overflow-hidden border border-slate-700/60">
                            <img src="${m.photo_path}" class="w-full max-h-48 object-cover rounded-lg" alt="Inspection Attachment">
                            <a href="${m.photo_path}" target="_blank" class="absolute bottom-2 right-2 px-2.5 py-1 bg-slate-950/80 hover:bg-slate-950 text-blue-400 text-[11px] font-bold rounded border border-slate-700 transition">
                                <i class="fa-solid fa-up-right-from-square mr-1"></i> Full View
                            </a>
                        </div>
                    </div>
                `;
    }

    const html = `
                <!-- Overview Header Card -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 bg-slate-200/60 p-4 rounded-xl border border-slate-800">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Status</span>
                        <span class="px-2 py-0.5 rounded-full inline-block font-bold text-[10px] mt-1 ${statusClass}">${(m.status || '').toUpperCase().replace('_', ' ')}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Operational Branch</span>
                        <span class="font-bold text-slate-200 block mt-0.5">${escapeHtml(m.branch_name || 'N/A')}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Assigned Mechanic</span>
                        <span class="font-bold text-slate-200 block mt-0.5">${escapeHtml(m.mechanic_display_name ? `${m.mechanic_display_name} (${m.mechanic_username})` : (m.mechanic_name || 'Unassigned'))}</span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Job Duration</span>
                        <span class="font-mono text-slate-300 block mt-0.5 font-bold">${durationStr}</span>
                    </div>
                </div>

                <!-- Customer & Vehicle Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Customer Information Card -->
                    <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/80 space-y-2">
                        <div class="flex items-center gap-2 border-b border-slate-800 pb-2">
                            <i class="fa-solid fa-user-tag text-blue-400"></i>
                            <h4 class="font-bold text-slate-200">Customer Details</h4>
                        </div>
                        <div class="space-y-1 text-slate-300">
                            <div><span class="text-slate-500">Customer Name:</span> <strong class="text-slate-200">${escapeHtml(m.customer_name || 'N/A')}</strong></div>
                            <div><span class="text-slate-500">Customer ID:</span> <span class="font-mono text-slate-300">${escapeHtml(m.customer_id_card || m.customer_id || 'N/A')}</span></div>
                        </div>
                    </div>

                    <!-- Vehicle Information Card -->
                    <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/80 space-y-2">
                        <div class="flex items-center gap-2 border-b border-slate-800 pb-2">
                            <i class="fa-solid fa-motorcycle text-emerald-400"></i>
                            <h4 class="font-bold text-slate-200">Vehicle Specifications</h4>
                        </div>
                        <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-slate-300">
                            <div><span class="text-slate-500 block">Make & Model:</span> <strong class="text-slate-200">${escapeHtml(m.make || '')} ${escapeHtml(m.model || '')}</strong></div>
                            <div><span class="text-slate-500 block">License Plate:</span> <span class="font-bold text-blue-400 font-mono">${escapeHtml(m.license_plate || 'N/A')}</span></div>
                            <div><span class="text-slate-500 block">Frame No (VIN):</span> <span class="font-mono text-slate-300">${escapeHtml(vinVal)}</span></div>
                            <div><span class="text-slate-500 block">Controller Code:</span> <span class="font-mono text-slate-300">${escapeHtml(m.controller_number || 'N/A')}</span></div>
                            <div><span class="text-slate-500 block">Vehicle Type:</span> <span>${escapeHtml(m.vehicle_type || 'N/A')}</span></div>
                            <div><span class="text-slate-500 block">Odometer:</span> <strong class="text-slate-200">${m.km_reached ? parseInt(m.km_reached).toLocaleString() : '0'} KM</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Job Details & Checklist -->
                <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/80 space-y-4">
                    <div class="flex items-center gap-2 border-b border-slate-800 pb-2">
                        <i class="fa-solid fa-clipboard-list text-purple-400"></i>
                        <h4 class="font-bold text-slate-200">Service & Inspection Notes</h4>
                    </div>

                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Job Description</span>
                        <div class="p-3 bg-slate-950 rounded-lg border border-slate-800 text-slate-200 leading-relaxed font-medium">
                            ${escapeHtml(m.description || 'No description provided')}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Common Issues Addressed</span>
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                ${commonIssuesHtml}
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Form Checklist Items</span>
                            <div class="pt-1">
                                ${formItemsHtml}
                            </div>
                        </div>
                    </div>

                    ${m.other_issues ? `
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Other Remarks / Issues</span>
                            <div class="p-2.5 bg-slate-950 rounded-lg border border-slate-800 text-slate-300">
                                ${escapeHtml(m.other_issues)}
                            </div>
                        </div>
                    ` : ''}

                    ${photoPreviewHtml}
                </div>

                <!-- Complete Billing & Services Breakdown Card -->
                <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-800/80 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-emerald-400"></i>
                            <h4 class="font-bold text-slate-200">Complete Billing & Services Breakdown</h4>
                        </div>
                        <span class="font-bold text-emerald-400 text-base font-mono">Grand Total: ${formatIDR(modalGrandTotal)}</span>
                    </div>

                    <!-- Service Options & Other Services Sub-grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Labor / Service Options -->
                        <div class="bg-slate-950 p-3 rounded-lg border border-slate-800 space-y-1">
                            <span class="text-[10px] text-cyan-400 uppercase font-bold block"><i class="fa-solid fa-wrench mr-1"></i> Billed Labor / Service Options</span>
                            <div class="text-xs font-semibold text-slate-200 flex justify-between items-center pt-1">
                                <span>${m.service_name ? escapeHtml(m.service_name) : 'No Labor Service'} ${m.service_sku ? `<span class="font-mono text-slate-500">(${escapeHtml(m.service_sku)})</span>` : ''}</span>
                                <span class="font-mono text-cyan-400 font-bold">${formatIDR(laborFee)}</span>
                            </div>
                        </div>

                        <!-- Other Expenses / Services -->
                        <div class="bg-slate-950 p-3 rounded-lg border border-slate-800 space-y-1">
                            <span class="text-[10px] text-amber-400 uppercase font-bold block"><i class="fa-solid fa-hand-holding-dollar mr-1"></i> Billed Other Services</span>
                            <div class="text-xs font-semibold text-slate-200 flex justify-between items-center pt-1">
                                <span>${m.other_expenses_category ? escapeHtml(m.other_expenses_category) : 'None'}</span>
                                <span class="font-mono text-amber-400 font-bold">${formatIDR(otherFee)}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Spare Parts Breakdown -->
                    <div class="space-y-2">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block"><i class="fa-solid fa-boxes-stacked mr-1"></i> Spare Parts Inventory Billed</span>
                        ${(m.parts && m.parts.length > 0) ? `
                            <div class="overflow-x-auto rounded-xl border border-slate-800">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase text-[10px]">
                                            <th class="p-2">SKU</th>
                                            <th class="p-2">Part Name</th>
                                            <th class="p-2 text-center">Qty</th>
                                            <th class="p-2 text-right">Unit Price</th>
                                            <th class="p-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60">
                                        ${partsTableRowsHtml}
                                    </tbody>
                                </table>
                            </div>
                        ` : '<p class="text-slate-500 italic text-xs">No spare parts used in this record.</p>'}
                    </div>
                </div>
            `;

    document.getElementById('record-detail-body').innerHTML = html;
    const modal = document.getElementById('maintenance-record-details-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeRecordDetailsModal() {
    const modal = document.getElementById('maintenance-record-details-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

window.openRecordDetailsModal = openRecordDetailsModal;
window.closeRecordDetailsModal = closeRecordDetailsModal;

function exportMaintenanceRecordsCSV() {
    const data = AppStore.filteredMaintenanceRecords || AppStore.allMaintenanceRecords || [];
    if (data.length === 0) {
        showToast('No maintenance records available to export.', 'warning');
        return;
    }

    let csv = 'Job ID,Date Created,Start Time,End Time,Duration,Branch,Mechanic,Customer Name,Customer ID Card,License Plate,Vehicle Type,Make & Model,Frame Number / VIN,Controller Number,KM Odometer,Common Issues,Form Checklist Items,Other Remarks,Job Description,Status,Parts Used Breakdown,Total Parts Cost (IDR),Photo URL\n';

    data.forEach(m => {
        const jobId = `"JOB-${m.id}"`;
        const dateStr = `"${m.created_at ? new Date(m.created_at).toLocaleString() : ''}"`;
        const startTime = `"${m.start_time ? new Date(m.start_time).toLocaleString() : ''}"`;
        const endTime = `"${(m.end_time || m.stopped_at) ? new Date(m.end_time || m.stopped_at).toLocaleString() : ''}"`;
        const duration = `"${(m.end_time || m.stopped_at) ? calculateDurationString(m.created_at, m.end_time || m.stopped_at) : 'Active Job'}"`;
        const branch = `"${(m.branch_name || '').replace(/"/g, '""')}"`;
        const mechanic = `"${(m.mechanic_name || '').replace(/"/g, '""')}"`;
        const customer = `"${(m.customer_name || '').replace(/"/g, '""')}"`;
        const customerIdCard = `"${(m.customer_id_card || '').replace(/"/g, '""')}"`;
        const plate = `"${(m.license_plate || '').replace(/"/g, '""')}"`;
        const vType = `"${(m.vehicle_type || '').replace(/"/g, '""')}"`;
        const makeModel = `"${(m.make || '')} ${(m.model || '')}"`.trim();
        const vin = `"${(m.vin || m.frame_number || '').replace(/"/g, '""')}"`;
        const controllerNum = `"${(m.controller_number || '').replace(/"/g, '""')}"`;
        const km = `"${m.km_reached !== null && m.km_reached !== undefined ? m.km_reached : ''}"`;
        const commonIssues = `"${(m.common_issues || '').replace(/"/g, '""')}"`;
        const formItems = `"${(m.mechanic_form_items || '').replace(/"/g, '""')}"`;
        const otherIssues = `"${(m.other_issues || '').replace(/"/g, '""')}"`;
        const desc = `"${(m.description || '').replace(/"/g, '""')}"`;
        const status = `"${(m.status || '').toUpperCase()}"`;

        let partsCost = 0;
        let partsBreakdownArr = [];
        if (m.parts && m.parts.length > 0) {
            m.parts.forEach(p => {
                const cost = parseFloat(p.price_at_use) * parseInt(p.quantity_used);
                partsCost += cost;
                partsBreakdownArr.push(`${p.part_name} (${p.sku}) x${p.quantity_used} @ Rp ${parseFloat(p.price_at_use).toLocaleString('id-ID')}`);
            });
        }
        const partsBreakdown = `"${partsBreakdownArr.join(' | ').replace(/"/g, '""')}"`;
        const photoUrl = `"${(m.photo_path || '').replace(/"/g, '""')}"`;

        csv += `${jobId},${dateStr},${startTime},${endTime},${duration},${branch},${mechanic},${customer},${customerIdCard},${plate},${vType},${makeModel},${vin},${controllerNum},${km},${commonIssues},${formItems},${otherIssues},${desc},${status},${partsBreakdown},${partsCost},${photoUrl}\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `Maintenance_Records_Complete_Export_${dateStrFilename()}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showToast('Complete Service & Repair records CSV exported successfully.', 'success');
}

function exportToExcel33() {
    const data = AppStore.filteredMaintenanceRecords || AppStore.allMaintenanceRecords || [];
    if (data.length === 0) {
        showToast('No maintenance records available to export.', 'warning');
        return;
    }

    const rows = [];
    let index = 1;
    data.forEach(m => {
        const parts = (m.parts && m.parts.length > 0) ? m.parts : [null];
        parts.forEach(p => {
            const row = {
                'No': m.job_id || formatJobCode(m.id, m.created_at),
                'Tanggal': m.created_at ? m.created_at.split(' ')[0] : '',
                'Nomor Polisi': m.license_plate || '',
                'Nama Pelanggan': m.customer_name || '',
                'Nomor Handphone': m.customer_phone || '',
                'Kategori': m.repair_category || 'Repair',
                'Status': m.customer_status || 'Retail',
                'Produk': m.make || '',
                'Model': m.model || '',
                'Warna': m.color || '',
                'No. Rangka': m.vin || m.frame_number || '',
                'No. Mesin': m.engine_number || '',
                'No. Controller': m.controller_number || '',
                'Keluhan': m.description || '',
                'Nomor Parts': p ? (p.sku || '') : '',
                'Nama Parts': p ? (p.part_name || '') : '',
                'Satuan': p ? (p.unit || 'Pcs') : '',
                'Quantity': p ? (p.quantity_used || 0) : 0,
                'Biaya Parts': p ? parseFloat(p.price_at_use || 0) : 0,
                'Jenis Pekerjaan': m.service_name || '',
                'Biaya Jasa': parseFloat(m.labor_fee || 0),
                'SKU JASA': m.service_sku || '',
                'Lain-lain': m.other_expenses_category || '',
                'Biaya Lain-lain': parseFloat(m.other_expenses_fee || 0),
                'Mekanik': m.mechanic_name || 'Unassigned',
                'Tanggal Repair': m.repair_date || (m.created_at ? m.created_at.split(' ')[0] : ''),
                'Check-in (hh:mm)': m.check_in_time ? m.check_in_time.substring(0, 5) : '',
                'Check-out (hh:mm)': m.check_out_time ? m.check_out_time.substring(0, 5) : '',
                'Catatan': m.notes || '',
                'Pembayaran Via': m.payment_method || 'Cash',
                'Spareparts + Jasa yang dibayarkan': parseFloat(m.parts_labor_paid || 0),
                'Grand Total': parseFloat(m.grand_total || 0),
                'BRANCH': m.branch_name || ''
            };
            rows.push(row);
        });
    });

    if (typeof XLSX === 'undefined') {
        showToast('SheetJS library not loaded.', 'error');
        return;
    }

    const worksheet = XLSX.utils.json_to_sheet(rows);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Maintenance Records");
    XLSX.writeFile(workbook, `Maintenance_Records_33Cols_${dateStrFilename()}.xlsx`);
    showToast('Exported 33-Column Excel file successfully!', 'success');
}

async function importFromExcel33(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async function (e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            const jsonRows = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

            if (jsonRows.length === 0) {
                showToast('Uploaded Excel file is empty.', 'warning');
                return;
            }

            const res = await request('import_excel_maintenance_records', { rows: jsonRows });
            showToast(res.message, 'success');
            await fetchMaintenanceRecords();
        } catch (err) {
            showToast('Failed to import Excel: ' + err.message, 'error');
        }
    };
    reader.readAsArrayBuffer(file);
}

window.exportToExcel33 = exportToExcel33;
window.importFromExcel33 = importFromExcel33;

function dateStrFilename() {
    const d = new Date();
    return d.getFullYear() + String(d.getMonth() + 1).padStart(2, '0') + String(d.getDate()).padStart(2, '0');
}

function renderEditModalParts() {
    const container = document.getElementById('edit-modal-parts-container');
    if (!container) return;
    const parts = AppStore.editModalPartsUsed || [];
    if (parts.length === 0) {
        container.innerHTML = '<span class="text-slate-500 block text-center py-2">No parts added yet.</span>';
        return;
    }
    container.innerHTML = parts.map(p => `
                <div class="flex items-center justify-between p-2 bg-slate-900 border border-slate-800/80 rounded mb-1">
                    <span class="text-slate-300 text-[11px]">${p.part_name} (${p.sku}) - ${p.branch}</span>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-slate-200">Qty: ${p.qty}</span>
                        <button type="button" onclick="removePartFromEditModalAccumulator(${p.inventory_id})" class="text-rose-500 hover:text-rose-400 font-bold">Remove</button>
                    </div>
                </div>
            `).join('');
}

window.removePartFromEditModalAccumulator = function (inventoryId) {
    AppStore.editModalPartsUsed = AppStore.editModalPartsUsed.filter(p => p.inventory_id != inventoryId);
    renderEditModalParts();
};

window.addPartToEditModalAccumulator = function () {
    const select = document.getElementById('edit-modal-part-select');
    const qtyInput = document.getElementById('edit-modal-part-qty');
    if (!select.value) return;
    const partId = parseInt(select.value);
    const qty = parseInt(qtyInput.value);
    if (isNaN(qty) || qty <= 0) {
        showToast('Enter valid quantity.', 'warning');
        return;
    }
    const selectedOpt = select.options[select.selectedIndex];
    const name = selectedOpt.getAttribute('data-name');
    const sku = selectedOpt.getAttribute('data-sku');
    const branch = selectedOpt.getAttribute('data-branch');

    const existing = AppStore.editModalPartsUsed.find(p => p.inventory_id == partId);
    if (existing) {
        existing.qty += qty;
    } else {
        AppStore.editModalPartsUsed.push({
            inventory_id: partId,
            part_name: name,
            sku: sku,
            branch: branch,
            qty: qty
        });
    }
    renderEditModalParts();
    qtyInput.value = '1';
};

async function openEditMaintenanceRecordModal(recordId) {
    const r = (AppStore.allMaintenanceRecords || []).find(item => item.id == recordId);
    if (!r) {
        showToast('Record details not found.', 'error');
        return;
    }

    try {
        const vehiclesData = await request('get_all_vehicles', {}, 'GET');
        const vehicles = vehiclesData.vehicles || [];
        const users = await request('get_all_users', {}, 'GET');
        const mechanics = users.filter(u => u.role === 'mechanic');
        const commonIssuesList = await request('get_common_issues', {}, 'GET');
        const formItemsList = await request('get_mechanic_form_items', {}, 'GET');
        const inventory = await request('get_inventory', {}, 'GET');

        AppStore.editModalPartsUsed = (r.parts || []).map(p => {
            const inv = p.inventory || {};
            return {
                inventory_id: p.inventory_id || p.pivot?.inventory_id || p.id,
                part_name: inv.part_name || p.part_name || 'Spare Part',
                sku: inv.sku || p.sku || '',
                branch: inv.branch?.name || p.branch_name || '',
                qty: p.quantity_used || p.pivot?.quantity_used || 1,
                price: parseFloat(p.price_at_use || inv.price || p.price || 0)
            };
        });

        const currentIssues = (r.common_issues || '').split(',').map(s => s.trim());
        const issuesChecklistHtml = commonIssuesList.map(issue => {
            const checked = currentIssues.includes(issue.name) ? 'checked' : '';
            return `
                        <label class="flex items-center gap-1.5 cursor-pointer text-slate-300">
                            <input type="checkbox" name="edit-common-issue" value="${issue.name}" ${checked} class="rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-0">
                            <span>${issue.name}</span>
                        </label>
                    `;
        }).join('');

        const currentFormItems = (r.mechanic_form_items || '').split(',').map(s => s.trim());
        const formItemsChecklistHtml = formItemsList.map(item => {
            const checked = currentFormItems.includes(item.label) ? 'checked' : '';
            return `
                        <label class="flex items-center gap-1.5 cursor-pointer text-slate-300">
                            <input type="checkbox" name="edit-form-item" value="${item.label}" ${checked} class="rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-0">
                            <span>${item.label}</span>
                        </label>
                    `;
        }).join('');

        const vehiclesOptions = vehicles.map(v => `<option value="${v.id}" ${v.id == r.vehicle_id ? 'selected' : ''}>[Plate: ${v.license_plate}] ${v.make} ${v.model}</option>`).join('');
        const mechanicsOptions = `<option value="">No Mechanic Associated</option>` + mechanics.map(m => {
            const label = (m.display_name && m.display_name !== m.username) ? `${m.display_name} (${m.username})` : (m.display_name || m.username);
            return `<option value="${m.id}" ${m.id == r.mechanic_id ? 'selected' : ''}>${label}</option>`;
        }).join('');
        const branchesOptions = (Array.isArray(AppStore.branches) ? AppStore.branches : []).map(b => `<option value="${b.id}" ${b.id == r.branch_id ? 'selected' : ''}>${b.name}</option>`).join('');

        const partsSelectOptions = `<option value="">-- Choose Sparepart to Add --</option>` +
            inventory.map(p => `<option value="${p.id}" data-name="${p.part_name}" data-sku="${p.sku}" data-branch="${p.branch_name}">[Branch: ${p.branch_name}] ${p.part_name} (${p.sku})</option>`).join('');

        const html = `
                    <form id="edit-maintenance-record-form" onsubmit="submitEditMaintenanceRecord(event, ${recordId})" class="space-y-4 text-xs font-semibold text-slate-300 max-h-[70vh] overflow-y-auto pr-2 custom-scrollbar">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Job Description / Diagnostics (Required)</label>
                            <textarea id="edit-rec-desc" required rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">${r.description || ''}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">KM Reached</label>
                                <input type="number" id="edit-rec-km" required value="${r.km_reached || ''}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Assigned Vehicle</label>
                                <select id="edit-rec-vehicle-id" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                                    ${vehiclesOptions}
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Assigned Mechanic</label>
                                <select id="edit-rec-mechanic-id" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                                    ${mechanicsOptions}
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Branch Station</label>
                                <select id="edit-rec-branch-id" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                                    ${branchesOptions}
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Job Status</label>
                                <select id="edit-rec-status" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                                    <option value="in_progress" ${r.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                    <option value="completed" ${r.status === 'completed' ? 'selected' : ''}>Completed</option>
                                    <option value="cancelled" ${r.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Start Time</label>
                                <input type="datetime-local" id="edit-rec-start-time" value="${r.start_time ? r.start_time.replace(' ', 'T') : ''}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">End Time</label>
                                <input type="datetime-local" id="edit-rec-end-time" value="${r.end_time ? r.end_time.replace(' ', 'T') : ''}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1.5">Common Issues Checklist</label>
                                <div class="grid grid-cols-2 gap-2 bg-slate-950 p-3 rounded-lg border border-slate-900">
                                    ${issuesChecklistHtml || '<span class="text-slate-500 text-[10px]">No issues defined</span>'}
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1.5">Mechanic Checklist Checklist</label>
                                <div class="grid grid-cols-2 gap-2 bg-slate-950 p-3 rounded-lg border border-slate-900">
                                    ${formItemsChecklistHtml || '<span class="text-slate-500 text-[10px]">No form items defined</span>'}
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Other Issues Description</label>
                            <textarea id="edit-rec-other" rows="1" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">${r.other_issues || ''}</textarea>
                        </div>

                        <div class="border-t border-slate-800/80 pt-4">
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-2">Reconcile Spare Parts Inventory Used</label>
                            <div id="edit-modal-parts-container" class="space-y-1 mb-3">
                                <!-- Injected parts list -->
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <select id="edit-modal-part-select" class="flex-1 px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-200 focus:border-blue-500 focus:outline-none">
                                    ${partsSelectOptions}
                                </select>
                                <input type="number" id="edit-modal-part-qty" value="1" min="1" class="w-16 px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-200 focus:outline-none">
                                <button type="button" onclick="addPartToEditModalAccumulator()" class="px-4 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 font-bold rounded-lg text-xs border border-blue-500/20 transition">Add</button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between border-t border-slate-900 pt-4 mt-6">
                            <button type="button" onclick="deleteMaintenanceRecord(${recordId})" class="px-4 py-2 bg-rose-950 hover:bg-rose-900 text-rose-400 border border-rose-800/60 font-bold rounded-lg transition">Delete Record</button>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-500/20">Save Record & Reconcile Stock</button>
                            </div>
                        </div>
                    </form>
                `;

        openGlobalModal(`Edit Service Record - JOB #${recordId}`, html);
        renderEditModalParts();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function submitEditMaintenanceRecord(event, recordId) {
    event.preventDefault();

    const desc = document.getElementById('edit-rec-desc').value.trim();
    const km = document.getElementById('edit-rec-km').value;
    const vehicleId = document.getElementById('edit-rec-vehicle-id').value;
    const mechanicId = document.getElementById('edit-rec-mechanic-id').value;
    const branchId = document.getElementById('edit-rec-branch-id').value;
    const status = document.getElementById('edit-rec-status').value;

    // Get datetime values
    let startTime = document.getElementById('edit-rec-start-time').value;
    let endTime = document.getElementById('edit-rec-end-time').value;

    // Format ISO HTML input 'YYYY-MM-DDTHH:MM' to MySQL format 'YYYY-MM-DD HH:MM:SS'
    if (startTime) {
        startTime = startTime.replace('T', ' ');
        if (startTime.length === 16) startTime += ':00';
    }
    if (endTime) {
        endTime = endTime.replace('T', ' ');
        if (endTime.length === 16) endTime += ':00';
    }

    const other = document.getElementById('edit-rec-other').value.trim();

    // Checkboxes and parts processing...
    const checkedIssues = Array.from(document.querySelectorAll('input[name="edit-common-issue"]:checked')).map(chk => chk.value);
    const commonIssuesStr = checkedIssues.join(', ');

    const checkedFormItems = Array.from(document.querySelectorAll('input[name="edit-form-item"]:checked')).map(chk => chk.value);
    const formItemsStr = checkedFormItems.join(', ');

    const partsUsed = (AppStore.editModalPartsUsed || []).map(p => ({
        inventory_id: p.inventory_id,
        qty: p.qty
    }));

    try {
        const res = await request('edit_maintenance_record', {
            record_id: recordId,
            vehicle_id: vehicleId,
            mechanic_id: mechanicId || null,
            branch_id: branchId,
            description: desc,
            km_reached: km,
            common_issues: commonIssuesStr,
            mechanic_form_items: formItemsStr,
            other_issues: other,
            status: status,
            start_time: startTime || null,
            end_time: endTime || null,
            parts_used: partsUsed
        });

        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchMaintenanceRecords();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// ------------------------------------------------
// Print Maintenance Record Details
// ------------------------------------------------
function printMaintenanceRecord() {
    try {
        const titleEl = document.getElementById('record-detail-title');
        const recordId = titleEl ? titleEl.getAttribute('data-id') : null;

        if (!recordId) {
            showToast('Record ID not found – cannot print.', 'error');
            return;
        }

        window.open(`/api/print-maintenance-record?record_id=${encodeURIComponent(recordId)}`, '_blank');
    } catch (e) {
        console.error(e);
    }
}

function deleteMaintenanceRecord(recordId) {
    showConfirmDeleteModal({
        title: 'Delete Repair Record',
        message: 'Are you sure you want to permanently delete this repair record? This action will reverse any compliance audit state changes and cannot be undone.',
        btnText: 'Delete Record',
        onConfirm: async () => {
            try {
                const res = await request('delete_maintenance_record', { record_id: recordId });
                showToast(res.message, 'success');
                closeGlobalModal();
                await fetchMaintenanceRecords();
            } catch (err) {
                showToast(err.message, 'error');
            }
        }
    });
}

function calculateDurationString(start, end) {
    const diffMs = new Date(end) - new Date(start);
    const totalSecs = Math.floor(diffMs / 1000);
    if (totalSecs < 0) return '00:00:00';

    const hours = Math.floor(totalSecs / 3600);
    const minutes = Math.floor((totalSecs % 3600) / 60);
    const seconds = totalSecs % 60;

    return [hours, minutes, seconds].map(val => String(val).padStart(2, '0')).join(':');
}
function toggleVehColumnMenu(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const menu = document.getElementById('veh-column-menu');
    if (menu) menu.classList.toggle('hidden');
}

function toggleVehColumn(key, isVisible) {
    if (!AppStore.vehColumnVisibility) AppStore.vehColumnVisibility = { ...defaultVehCols };
    AppStore.vehColumnVisibility[key] = !!isVisible;
    renderVehiclesTableHeader();
    filterVehiclesAndCustomers();
}

function resetVehColumns() {
    AppStore.vehColumnVisibility = { ...defaultVehCols };
    syncVehColumnCheckboxes();
    renderVehiclesTableHeader();
    filterVehiclesAndCustomers();
}

function syncVehColumnCheckboxes() {
    const cols = AppStore.vehColumnVisibility || defaultVehCols;
    document.querySelectorAll('input[data-veh-col]').forEach(cb => {
        const colKey = cb.getAttribute('data-veh-col');
        cb.checked = cols[colKey] !== false;
    });
}

function renderVehiclesTableHeader() {
    const theadTr = document.getElementById('super-admin-vehicles-thead-tr');
    if (!theadTr) return;

    const cols = AppStore.vehColumnVisibility || defaultVehCols;
    let html = '';

    if (cols.id) html += `<th class="p-3 w-16 text-center whitespace-nowrap">ID</th>`;
    if (cols.license_plate) html += `<th class="p-3 w-28 text-center whitespace-nowrap" data-i18n="shop.license_plate">${t('shop.license_plate', 'License Plate')}</th>`;
    if (cols.brand_model) html += `<th class="p-3 w-36 text-center whitespace-nowrap" data-i18n="shop.veh_type">${t('shop.veh_type', 'Brand / Model')}</th>`;
    if (cols.color) html += `<th class="p-3 w-24 text-center whitespace-nowrap" data-i18n="veh.color">${t('veh.color', 'Color')}</th>`;
    if (cols.vin) html += `<th class="p-3 w-40 text-center whitespace-nowrap" data-i18n="shop.frame_num">${t('shop.frame_num', 'VIN (Frame No)')}</th>`;
    if (cols.engine) html += `<th class="p-3 w-36 text-center whitespace-nowrap" data-i18n="veh.engine_no">${t('veh.engine_no', 'Engine No')}</th>`;
    if (cols.controller) html += `<th class="p-3 w-36 text-center whitespace-nowrap" data-i18n="shop.controller_num">${t('shop.controller_num', 'Controller Code')}</th>`;
    if (cols.activate_date) html += `<th class="p-3 w-32 text-center whitespace-nowrap" data-i18n="veh.activate_date">${t('veh.activate_date', 'Activate Date')}</th>`;
    if (cols.branch) html += `<th class="p-3 w-28 text-center whitespace-nowrap" data-i18n="shop.branch">${t('shop.branch', 'Branch')}</th>`;
    if (cols.owner) html += `<th class="p-3 min-w-[180px] text-center whitespace-nowrap" data-i18n="veh.owner_name">${t('veh.owner_name', 'Assigned Owner')}</th>`;
    if (cols.actions && (hasPermission('edit_vehicles') || hasPermission('delete_vehicles'))) {
        html += `<th class="p-3 w-28 text-center whitespace-nowrap" data-i18n="table.actions">${t('table.actions', 'Actions')}</th>`;
    }

    theadTr.innerHTML = html;
}

async function fetchVehiclesAndCustomers() {
    const tbody = document.getElementById('super-admin-vehicles-tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="11" class="p-6 text-center text-slate-500">Loading vehicles...</td></tr>`;

    try {
        const data = await request('get_all_vehicles', {}, 'GET');
        AppStore.allVehicles = Array.isArray(data?.vehicles) ? data.vehicles : (Array.isArray(data) ? data : []);
        AppStore.allCustomers = Array.isArray(data?.customers) ? data.customers : [];

        // Ensure branches loaded
        if (!AppStore.branches || !Array.isArray(AppStore.branches) || AppStore.branches.length === 0) {
            try {
                const bRes = await request('get_branches', {}, 'GET');
                if (Array.isArray(bRes)) AppStore.branches = bRes;
            } catch (bErr) { }
        }

        // Populate Branch filter dropdown if empty
        const bFilter = document.getElementById('super-admin-vehicles-branch-filter');
        if (bFilter && bFilter.options.length <= 1 && AppStore.branches && Array.isArray(AppStore.branches)) {
            let html = '<option value="">All Branches</option>';
            AppStore.branches.forEach(b => {
                html += `<option value="${b.id}">${escapeHtml(b.name)}</option>`;
            });
            bFilter.innerHTML = html;
        }

        if (typeof filterCustomersTable === 'function') {
            try { filterCustomersTable(); } catch (e) { console.error(e); }
        }
        filterVehiclesAndCustomers();
    } catch (err) {
        if (tbody) tbody.innerHTML = `<tr><td colspan="11" class="p-6 text-center text-rose-500 font-semibold">Error: ${escapeHtml(err.message)}</td></tr>`;
        showToast(err.message, 'error');
    }
}

function filterVehiclesAndCustomers() {
    try {
        renderVehiclesTableHeader();
        const tbody = document.getElementById('super-admin-vehicles-tbody');
        if (!tbody) return;

        const searchVal = (document.getElementById('super-admin-vehicles-search')?.value || '').toLowerCase().trim();
        const branchVal = document.getElementById('super-admin-vehicles-branch-filter')?.value || '';
        const colField = document.getElementById('super-admin-vehicles-column-select')?.value || '';
        const colVal = (document.getElementById('super-admin-vehicles-column-val')?.value || '').toLowerCase().trim();
        const sortVal = document.getElementById('super-admin-vehicles-sort-select')?.value || 'id-desc';

        // Populate Branch filter dropdown if empty
        const bFilterVeh = document.getElementById('super-admin-vehicles-branch-filter');
        if (bFilterVeh && bFilterVeh.options.length <= 1 && AppStore.branches) {
            let html = '<option value="">All Branches</option>';
            html += '<option value="global">Global Only (All Outlets)</option>';
            AppStore.branches.forEach(b => {
                html += `<option value="${b.id}">${escapeHtml(b.name)}</option>`;
            });
            bFilterVeh.innerHTML = html;
        }

        let vehicles = (AppStore.allVehicles || []).filter(v => {
            if (branchVal) {
                if (branchVal === 'global') {
                    if (v.branch_id !== null && v.branch_id !== '' && v.branch_id !== undefined) return false;
                } else if (v.branch_id != branchVal) {
                    return false;
                }
            }
            const custName = v.customer?.name || v.customer_name || '';
            const custPhone = v.customer?.phone || v.customer_phone || '';
            const idCard = v.customer?.id_card_number || v.id_card_number || '';

            if (searchVal) {
                const match =
                    (v.license_plate && String(v.license_plate).toLowerCase().includes(searchVal)) ||
                    (v.vin && String(v.vin).toLowerCase().includes(searchVal)) ||
                    (v.engine_number && String(v.engine_number).toLowerCase().includes(searchVal)) ||
                    (v.controller_number && String(v.controller_number).toLowerCase().includes(searchVal)) ||
                    (v.make && String(v.make).toLowerCase().includes(searchVal)) ||
                    (v.color && String(v.color).toLowerCase().includes(searchVal)) ||
                    (v.model && String(v.model).toLowerCase().includes(searchVal)) ||
                    (custName && String(custName).toLowerCase().includes(searchVal)) ||
                    (custPhone && String(custPhone).toLowerCase().includes(searchVal)) ||
                    (idCard && String(idCard).toLowerCase().includes(searchVal)) ||
                    (v.id && String(v.id).toLowerCase().includes(searchVal));
                if (!match) return false;
            }

            if (colField && colVal) {
                if (colField === 'make') {
                    const makeModel = `${v.make || ''} ${v.model || ''}`.toLowerCase();
                    if (!makeModel.includes(colVal)) return false;
                } else if (colField === 'customer_name') {
                    if (!custName.toLowerCase().includes(colVal)) return false;
                } else {
                    const targetVal = (v[colField] || '').toString().toLowerCase();
                    if (!targetVal.includes(colVal)) return false;
                }
            }
            return true;
        });

        // Sorting
        const [sortField, sortOrder] = sortVal.split('-');
        vehicles.sort((a, b) => {
            let valA, valB;
            if (sortField === 'id') {
                valA = (a.id || '').toString().toLowerCase();
                valB = (b.id || '').toString().toLowerCase();
            } else {
                valA = (a[sortField] || '').toString().toLowerCase();
                valB = (b[sortField] || '').toString().toLowerCase();
            }

            if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
            if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
            return 0;
        });

        // Render Pagination UI
        const totalCount = vehicles.length;
        renderPaginationUI('vehicles', totalCount, 'super-admin-vehicles-pagination-top', 'super-admin-vehicles-pagination-bottom');

        const pageSize = getPageSize('vehicles');
        const pageState = AppStore.pagination['vehicles'] || { page: 1 };
        const pageNum = pageState.page || 1;
        const sliced = vehicles.slice((pageNum - 1) * pageSize, pageNum * pageSize);

        if (sliced.length === 0) {
            tbody.innerHTML = `<tr><td colspan="10" class="p-6 text-center text-slate-500 font-semibold">No vehicles matching filter.</td></tr>`;
            return;
        }

        const cols = AppStore.vehColumnVisibility || defaultVehCols;
        tbody.innerHTML = sliced.map(v => {
            const dropdownOptions = `<option value="">Unbound (Bind to None)</option>` + (AppStore.allCustomers || []).map(c => {
                const selected = c.id == v.customer_id ? 'selected' : '';
                return `<option value="${c.id}" ${selected}>${escapeHtml(c.name)} (${c.id})</option>`;
            }).join('');

            const makeModelText = [v.make, v.model].filter(Boolean).join(' ') || 'N/A';

            let rowHtml = '<tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">';
            if (cols.id) rowHtml += `<td class="p-3 font-mono text-slate-400 text-center text-xs">${escapeHtml(v.id)}</td>`;
            if (cols.license_plate) rowHtml += `<td class="p-3 font-mono font-bold text-slate-200 text-xs text-center">${escapeHtml(v.license_plate)}</td>`;
            if (cols.brand_model) rowHtml += `<td class="p-3 text-slate-300 font-semibold text-xs text-center">${escapeHtml(makeModelText)}</td>`;
            if (cols.color) rowHtml += `<td class="p-3 text-slate-300 font-semibold text-xs text-center">${escapeHtml(v.color || 'N/A')}</td>`;
            if (cols.vin) rowHtml += `<td class="p-3 font-mono text-xs text-slate-400 text-center">${escapeHtml(v.vin || v.frame_number || 'N/A')}</td>`;
            if (cols.engine) rowHtml += `<td class="p-3 font-mono text-xs text-slate-400 text-center">${escapeHtml(v.engine_number || 'N/A')}</td>`;
            if (cols.controller) rowHtml += `<td class="p-3 font-mono text-xs text-slate-400 text-center">${escapeHtml(v.controller_number || 'N/A')}</td>`;
            if (cols.activate_date) {
                const actDateText = v.activate_date ? String(v.activate_date).substring(0, 10) : 'N/A';
                rowHtml += `<td class="p-3 font-mono text-xs text-slate-300 text-center">${escapeHtml(actDateText)}</td>`;
            }
            if (cols.branch) {
                const bName = v.branch?.name || (v.branch_id ? (AppStore.branches?.find(b => b.id == v.branch_id)?.name || `Branch #${v.branch_id}`) : 'Global');
                const bBadge = v.branch_id ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30' : 'bg-slate-800 text-slate-400 border-slate-700';
                rowHtml += `<td class="p-3 text-center"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${bBadge}">${escapeHtml(bName)}</span></td>`;
            }
            if (cols.owner) {
                if (hasPermission('edit_vehicles')) {
                    rowHtml += `<td class="p-3 text-center">
                        <select onchange="bindVehicleOwner('${v.id}', this.value)" class="w-full px-2 py-1.5 rounded bg-slate-950 border border-slate-800 text-slate-300 text-xs focus:border-blue-500 focus:outline-none font-semibold">
                            ${dropdownOptions}
                        </select>
                    </td>`;
                } else {
                    const ownerName = v.customer?.name || v.customer_name || 'Unbound';
                    rowHtml += `<td class="p-3 text-center font-bold ${v.customer_id ? 'text-cyan-400' : 'text-slate-500'} text-xs">${escapeHtml(ownerName)}</td>`;
                }
            }
            if (cols.actions && (hasPermission('edit_vehicles') || hasPermission('delete_vehicles'))) {
                rowHtml += `<td class="p-3 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <button onclick="openEditVehicleModal('${v.id}')" class="px-2.5 py-1 bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 border border-cyan-500/30 font-bold rounded-lg text-xs transition" title="Edit Vehicle Details">
                            <i class="fa-solid fa-pen text-[10px]"></i> Edit
                        </button>
                        <button onclick="deleteVehicle('${v.id}', '${escapeHtml(v.license_plate)}')" class="px-2.5 py-1 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 border border-rose-500/30 font-bold rounded-lg text-xs transition" title="Delete Vehicle">
                            <i class="fa-solid fa-trash text-[10px]"></i> Delete
                        </button>
                    </div>
                </td>`;
            }
            rowHtml += '</tr>';
            return rowHtml;
        }).join('');
    } catch (err) {
        console.error('Error rendering vehicles:', err);
    }
}

const defaultCustCols = { id: true, name: true, phone: true, id_card: true, customer_status: true, branch: true, address: true, vehicles: true, warranty_category: true, actions: true };
if (!AppStore.custColumnVisibility) {
    let saved = null;
    try {
        const raw = localStorage.getItem('cust_cols_vis');
        if (raw) saved = JSON.parse(raw);
    } catch (e) { }
    AppStore.custColumnVisibility = saved || { ...defaultCustCols };
}

window.toggleCustColumnMenu = function (e) {
    if (e) e.stopPropagation();
    const menu = document.getElementById('cust-column-menu');
    if (menu) {
        menu.classList.toggle('hidden');
        syncCustColumnCheckboxes();
    }
};

window.toggleCustColumn = function (key, isVisible) {
    AppStore.custColumnVisibility[key] = !!isVisible;
    try { localStorage.setItem('cust_cols_vis', JSON.stringify(AppStore.custColumnVisibility)); } catch (e) { }
    filterCustomersTable();
};

window.resetCustColumns = function () {
    AppStore.custColumnVisibility = { ...defaultCustCols };
    try { localStorage.setItem('cust_cols_vis', JSON.stringify(AppStore.custColumnVisibility)); } catch (e) { }
    syncCustColumnCheckboxes();
    filterCustomersTable();
};

window.rebindVehiclesRule = async function () {
    try {
        const res = await request('rebind_vehicles', {}, 'POST');
        showToast(res.message || 'Vehicle bindings cleaned up successfully.', 'success');
        if (typeof fetchVehiclesAndCustomers === 'function') await fetchVehiclesAndCustomers();
    } catch (err) {
        showToast(err.message || 'Re-bind failed.', 'error');
    }
};

function syncCustColumnCheckboxes() {
    const cols = AppStore.custColumnVisibility || defaultCustCols;
    Object.keys(cols).forEach(k => {
        const chk = document.querySelector(`#cust-column-menu input[data-cust-col="${k}"]`);
        if (chk) chk.checked = !!cols[k];
    });
}

function renderCustomersTableHeader() {
    const theadTr = document.getElementById('super-admin-customers-thead-tr');
    if (!theadTr) return;
    const cols = AppStore.custColumnVisibility || defaultCustCols;
    let html = '';
    if (cols.id) html += `<th class="p-3 w-20 text-center">Cust ID</th>`;
    if (cols.name) html += `<th class="p-3 w-44 text-center" data-i18n="shop.cust_name">${t('shop.cust_name', 'Customer Name')}</th>`;
    if (cols.phone) html += `<th class="p-3 w-32 text-center" data-i18n="shop.cust_phone">${t('shop.cust_phone', 'Phone Number')}</th>`;
    if (cols.id_card) html += `<th class="p-3 w-36 text-center" data-i18n="shop.id_card_num">${t('shop.id_card_num', 'ID Card Number')}</th>`;
    if (cols.customer_status) html += `<th class="p-3 w-28 text-center" data-i18n="shop.cust_status">${t('shop.cust_status', 'Status')}</th>`;
    if (cols.branch) html += `<th class="p-3 w-28 text-center" data-i18n="shop.branch">${t('shop.branch', 'Branch')}</th>`;
    if (cols.address) html += `<th class="p-3 min-w-[200px] text-center" data-i18n="shop.cust_address">${t('shop.cust_address', 'Home Address')}</th>`;
    if (cols.vehicles) html += `<th class="p-3 w-24 text-center" data-i18n="cust.vehicles">${t('cust.vehicles', 'Vehicles')}</th>`;
    if (cols.warranty_category) html += `<th class="p-3 w-32 text-center" data-i18n="shop.warranty_category">${t('shop.warranty_category', 'Warranty Category')}</th>`;
    if (cols.actions && (hasPermission('edit_customers') || hasPermission('delete_customers'))) {
        html += `<th class="p-3 w-28 text-center" data-i18n="table.actions">${t('table.actions', 'Actions')}</th>`;
    }
    theadTr.innerHTML = html;
}

function filterCustomersTable() {
    renderCustomersTableHeader();
    const tbody = document.getElementById('super-admin-customers-tbody');
    if (!tbody) return;

    const searchVal = (document.getElementById('super-admin-customers-search')?.value || '').toLowerCase().trim();
    const branchVal = document.getElementById('super-admin-customers-branch-filter')?.value || '';
    const statusVal = document.getElementById('super-admin-customers-status-filter')?.value || '';
    const colField = document.getElementById('super-admin-customers-column-select')?.value || '';
    const colVal = (document.getElementById('super-admin-customers-column-val')?.value || '').toLowerCase().trim();
    const sortVal = document.getElementById('super-admin-customers-sort-select')?.value || 'id-desc';

    // Populate Branch filter dropdown if empty
    const bFilter = document.getElementById('super-admin-customers-branch-filter');
    if (bFilter && bFilter.options.length <= 1 && AppStore.branches) {
        let html = '<option value="">All Branches</option>';
        html += '<option value="global">Global Only (All Outlets)</option>';
        AppStore.branches.forEach(b => {
            html += `<option value="${b.id}">${escapeHtml(b.name)}</option>`;
        });
        bFilter.innerHTML = html;
    }

    let customers = (AppStore.allCustomers || []).filter(c => {
        if (branchVal) {
            if (branchVal === 'global') {
                if (c.branch_id !== null && c.branch_id !== '' && c.branch_id !== undefined) return false;
            } else if (c.branch_id != branchVal) {
                return false;
            }
        }
        if (statusVal && (c.customer_status || 'Retail') !== statusVal) return false;
        if (searchVal) {
            const match =
                (c.name && c.name.toLowerCase().includes(searchVal)) ||
                (c.phone && c.phone.toLowerCase().includes(searchVal)) ||
                (c.id_card_number && c.id_card_number.toLowerCase().includes(searchVal)) ||
                (c.address && c.address.toLowerCase().includes(searchVal)) ||
                (c.id && String(c.id).toLowerCase().includes(searchVal));
            if (!match) return false;
        }

        if (colField && colVal) {
            const targetVal = (c[colField] || '').toString().toLowerCase();
            if (!targetVal.includes(colVal)) return false;
        }
        return true;
    });

    // Sorting
    const [sortField, sortOrder] = sortVal.split('-');
    const isAsc = sortOrder === 'asc';
    customers.sort((a, b) => {
        let cmp = 0;
        if (sortField === 'id') {
            const strA = String(a.id || a.created_at || '');
            const strB = String(b.id || b.created_at || '');
            cmp = strA.localeCompare(strB, undefined, { numeric: true, sensitivity: 'base' });
        } else if (sortField === 'vehicles') {
            const countA = a.vehicle_count || (Array.isArray(a.vehicles) ? a.vehicles.length : 0);
            const countB = b.vehicle_count || (Array.isArray(b.vehicles) ? b.vehicles.length : 0);
            cmp = countA - countB;
        } else {
            const strA = (a[sortField] || '').toString();
            const strB = (b[sortField] || '').toString();
            cmp = strA.localeCompare(strB, undefined, { numeric: true, sensitivity: 'base' });
        }
        return isAsc ? cmp : -cmp;
    });

    // Render Pagination UI
    const totalCount = customers.length;
    renderPaginationUI('customers_table', totalCount, 'super-admin-customers-pagination-top', 'super-admin-customers-pagination-bottom');

    const pageSize = getPageSize('customers_table');
    const pageState = AppStore.pagination['customers_table'] || { page: 1 };
    const pageNum = pageState.page || 1;
    const sliced = customers.slice((pageNum - 1) * pageSize, pageNum * pageSize);

    if (sliced.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-slate-500 font-semibold">No customers matching filter.</td></tr>`;
        return;
    }

    const cols = AppStore.custColumnVisibility || defaultCustCols;
    tbody.innerHTML = sliced.map(c => {
        const vCount = c.vehicle_count || 0;
        let rowHtml = '<tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">';
        if (cols.id) rowHtml += `<td class="p-3 font-mono font-bold text-slate-200 text-xs text-center">${escapeHtml(c.id)}</td>`;
        if (cols.name) rowHtml += `<td class="p-3 font-bold text-slate-100 text-xs text-center">${escapeHtml(c.name)}</td>`;
        if (cols.phone) rowHtml += `<td class="p-3 font-mono text-slate-300 text-xs text-center">${escapeHtml(c.phone || 'N/A')}</td>`;
        if (cols.id_card) rowHtml += `<td class="p-3 font-mono text-slate-400 text-xs text-center">${escapeHtml(c.id_card_number || 'N/A')}</td>`;
        if (cols.customer_status) {
            const status = c.customer_status || 'Retail';
            let badgeClass = 'bg-slate-800 text-slate-300 border-slate-700';
            if (['Gomolis', 'Operasional', 'Operational'].includes(status)) {
                badgeClass = 'bg-blue-500/20 text-blue-300 border-blue-500/30';
            } else if (['Retail', 'Sales'].includes(status)) {
                badgeClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
            } else if (['Cocoride', 'TPI'].includes(status)) {
                badgeClass = 'bg-purple-500/20 text-purple-300 border-purple-500/30';
            } else if (['Fleet', 'Gomolis-RF', 'VIP'].includes(status)) {
                badgeClass = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
            }
            rowHtml += `<td class="p-3 text-center"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${badgeClass}">${escapeHtml(status)}</span></td>`;
        }
        if (cols.branch) {
            const bName = c.branch?.name || (c.branch_id ? (AppStore.branches?.find(b => b.id == c.branch_id)?.name || `Branch #${c.branch_id}`) : 'Global');
            const bBadge = c.branch_id ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30' : 'bg-slate-800 text-slate-400 border-slate-700';
            rowHtml += `<td class="p-3 text-center"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${bBadge}">${escapeHtml(bName)}</span></td>`;
        }
        if (cols.address) rowHtml += `<td class="p-3 text-slate-400 truncate max-w-xs text-xs text-center">${escapeHtml(c.address || 'N/A')}</td>`;
        if (cols.vehicles) {
            let platesText = 'None';
            if (Array.isArray(c.vehicles) && c.vehicles.length > 0) {
                platesText = c.vehicles.map(v => escapeHtml(v.license_plate || '')).filter(Boolean).join(', ');
            } else if (vCount > 0) {
                platesText = `${vCount} Vehicle(s)`;
            }
            rowHtml += `<td class="p-3 text-center font-semibold text-cyan-400 text-xs font-mono">${platesText || 'None'}</td>`;
        }
        if (cols.warranty_category) {
            const custVehicles = (AppStore.allVehicles || []).filter(v => v.customer_id == c.id);
            const vehList = (Array.isArray(c.vehicles) && c.vehicles.length > 0) ? c.vehicles : custVehicles;
            let wCat = 'Unclaimable / No Warranty';
            if (vehList.length > 0) {
                const cats = vehList.map(v => determineVehicleWarrantyCategory(v.activate_date || v.created_at, 0));
                if (cats.includes('Warranty A')) wCat = 'Warranty A';
                else if (cats.includes('Warranty B')) wCat = 'Warranty B';
                else if (cats.includes('Warranty C')) wCat = 'Warranty C';
            }
            let wBadgeClass = 'bg-slate-800 text-slate-400 border-slate-700';
            if (wCat === 'Warranty A') wBadgeClass = 'bg-purple-500/20 text-purple-300 border-purple-500/30';
            else if (wCat === 'Warranty B') wBadgeClass = 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30';
            else if (wCat === 'Warranty C') wBadgeClass = 'bg-blue-500/20 text-blue-300 border-blue-500/30';
            rowHtml += `<td class="p-3 text-center"><span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${wBadgeClass}">${escapeHtml(wCat)}</span></td>`;
        }
        if (cols.actions && (hasPermission('edit_customers') || hasPermission('delete_customers'))) {
            rowHtml += `<td class="p-3 text-center">
                <div class="flex items-center justify-center gap-1.5">
                    <button onclick="openEditCustomerModal('${c.id}')" class="px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/30 font-bold rounded-lg text-xs transition">
                        <i class="fa-solid fa-pen text-[10px]"></i> Edit
                    </button>
                    <button onclick="deleteCustomer('${c.id}', '${escapeHtml(c.name)}')" class="px-2.5 py-1 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 border border-rose-500/30 font-bold rounded-lg text-xs transition">
                        <i class="fa-solid fa-trash text-[10px]"></i> Delete
                    </button>
                </div>
            </td>`;
        }
        rowHtml += '</tr>';
        return rowHtml;
    }).join('');
}
window.filterCustomersTable = filterCustomersTable;

let pendingConfirmCallback = null;

function showConfirmDeleteModal({ title = 'Confirm Deletion', message = 'Are you sure you want to proceed?', btnText = 'Delete Permanently', onConfirm }) {
    const modal = document.getElementById('custom-confirm-modal');
    if (!modal) {
        if (confirm(message)) {
            if (onConfirm) onConfirm();
        }
        return;
    }

    const titleEl = document.getElementById('confirm-modal-title');
    const msgEl = document.getElementById('confirm-modal-message');
    const actionBtn = document.getElementById('confirm-modal-action-btn');

    if (titleEl) titleEl.innerText = title;
    if (msgEl) msgEl.innerText = message;
    if (actionBtn) {
        actionBtn.innerHTML = `<i class="fa-solid fa-trash"></i> ${btnText}`;
        actionBtn.onclick = async () => {
            const cb = pendingConfirmCallback;
            closeConfirmDeleteModal();
            if (cb && typeof cb === 'function') {
                await cb();
            }
        };
    }

    pendingConfirmCallback = onConfirm;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.style.display = 'flex';
}

function closeConfirmDeleteModal() {
    const modal = document.getElementById('custom-confirm-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.style.display = 'none';
    }
    pendingConfirmCallback = null;
}

window.showConfirmDeleteModal = showConfirmDeleteModal;
window.closeConfirmDeleteModal = closeConfirmDeleteModal;

function deleteVehicle(vehicleId, plate) {
    showConfirmDeleteModal({
        title: 'Delete Vehicle Record',
        message: `Are you sure you want to delete vehicle '${plate}' (${vehicleId})? This action cannot be undone.`,
        btnText: 'Delete Vehicle',
        onConfirm: async () => {
            try {
                const res = await request('delete_vehicle', { vehicle_id: vehicleId });
                showToast(res.message, 'success');
                await fetchVehiclesAndCustomers();
                if (typeof filterCustomersTable === 'function') filterCustomersTable();
                if (typeof filterVehiclesAndCustomers === 'function') filterVehiclesAndCustomers();
            } catch (err) {
                showToast(err.message, 'error');
            }
        }
    });
}

function deleteCustomer(customerId, name) {
    showConfirmDeleteModal({
        title: 'Delete Customer Profile',
        message: `Are you sure you want to delete customer '${name}' (${customerId})? Associated vehicles will be unbound. This action cannot be undone.`,
        btnText: 'Delete Customer',
        onConfirm: async () => {
            try {
                const res = await request('delete_customer', { customer_id: customerId });
                showToast(res.message, 'success');
                await fetchVehiclesAndCustomers();
                if (typeof fetchManagementCustomers === 'function') await fetchManagementCustomers();
                if (typeof filterCustomersTable === 'function') filterCustomersTable();
                if (typeof filterVehiclesAndCustomers === 'function') filterVehiclesAndCustomers();
            } catch (err) {
                showToast(err.message, 'error');
            }
        }
    });
}

function openEditCustomerModal(customerId) {
    const customer = (AppStore.allCustomers || []).find(c => String(c.id).trim() === String(customerId).trim()) ||
        (AppStore.managementCustomers || []).find(c => String(c.id).trim() === String(customerId).trim());
    if (!customer) {
        showToast('Customer record not found.', 'error');
        return;
    }

    if (document.getElementById('edit-cust-id')) document.getElementById('edit-cust-id').value = customer.id;
    if (document.getElementById('edit-cust-name')) document.getElementById('edit-cust-name').value = customer.name || '';
    if (document.getElementById('edit-cust-phone')) document.getElementById('edit-cust-phone').value = customer.phone || '';
    if (document.getElementById('edit-cust-idcard')) document.getElementById('edit-cust-idcard').value = customer.id_card_number || '';
    if (document.getElementById('edit-cust-status')) document.getElementById('edit-cust-status').value = customer.customer_status || 'Retail';
    if (document.getElementById('edit-cust-address')) document.getElementById('edit-cust-address').value = customer.address || '';

    // Populate branch dropdown
    const bSelect = document.getElementById('edit-cust-branch');
    if (bSelect) {
        let html = '<option value="">Global / All Branches</option>';
        (AppStore.branches || []).forEach(b => {
            const sel = b.id == customer.branch_id ? 'selected' : '';
            html += `<option value="${b.id}" ${sel}>${escapeHtml(b.name)}</option>`;
        });
        bSelect.innerHTML = html;
    }

    const modal = document.getElementById('super-admin-edit-customer-modal');
    if (modal) {
        console.log('Opening Edit Customer modal for ID', customerId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.display = 'flex';
    } else {
        console.error('Edit Customer modal element not found');
    }
}

function closeEditCustomerModal() {
    const modal = document.getElementById('super-admin-edit-customer-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.style.display = 'none';
    }
}

async function handleEditCustomerSubmit(e) {
    if (e && e.preventDefault) e.preventDefault();
    const customerId = document.getElementById('edit-cust-id').value;
    const name = document.getElementById('edit-cust-name').value.trim();
    const phone = document.getElementById('edit-cust-phone').value.trim();
    const idCard = document.getElementById('edit-cust-idcard').value.trim();
    const customerStatus = document.getElementById('edit-cust-status').value;
    const address = document.getElementById('edit-cust-address').value.trim();
    const branchId = document.getElementById('edit-cust-branch').value;

    try {
        const res = await request('edit_customer', {
            customer_id: customerId,
            name: name,
            phone: phone,
            id_card_number: idCard,
            customer_status: customerStatus,
            address: address,
            branch_id: branchId
        });

        showToast(res.message, 'success');
        closeEditCustomerModal();
        await fetchVehiclesAndCustomers();
        if (typeof fetchManagementCustomers === 'function') fetchManagementCustomers();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openEditVehicleModal(vehicleId) {
    const vehicle = (AppStore.allVehicles || []).find(v => String(v.id).trim() === String(vehicleId).trim()) ||
        (AppStore.managementVehicles || []).find(v => String(v.id).trim() === String(vehicleId).trim());
    if (!vehicle) {
        showToast('Vehicle record not found.', 'error');
        return;
    }

    if (document.getElementById('edit-veh-id')) document.getElementById('edit-veh-id').value = vehicle.id;
    if (document.getElementById('edit-veh-plate')) document.getElementById('edit-veh-plate').value = vehicle.license_plate || '';
    if (document.getElementById('edit-veh-type')) document.getElementById('edit-veh-type').value = vehicle.vehicle_type || '';
    if (document.getElementById('edit-veh-make')) document.getElementById('edit-veh-make').value = vehicle.make || '';
    if (document.getElementById('edit-veh-model')) document.getElementById('edit-veh-model').value = vehicle.model || '';
    if (document.getElementById('edit-veh-color')) document.getElementById('edit-veh-color').value = vehicle.color || '';
    if (document.getElementById('edit-veh-year')) document.getElementById('edit-veh-year').value = vehicle.year || new Date().getFullYear();
    if (document.getElementById('edit-veh-vin')) document.getElementById('edit-veh-vin').value = vehicle.vin || vehicle.frame_number || '';
    if (document.getElementById('edit-veh-engine')) document.getElementById('edit-veh-engine').value = vehicle.engine_number || '';
    if (document.getElementById('edit-veh-controller')) document.getElementById('edit-veh-controller').value = vehicle.controller_number || '';
    if (document.getElementById('edit-veh-activate-date')) document.getElementById('edit-veh-activate-date').value = vehicle.activate_date ? String(vehicle.activate_date).substring(0, 10) : '';

    // Populate branch select
    const bSelect = document.getElementById('edit-veh-branch');
    if (bSelect) {
        let html = '<option value="">Global / All Branches</option>';
        (AppStore.branches || []).forEach(b => {
            const sel = b.id == vehicle.branch_id ? 'selected' : '';
            html += `<option value="${b.id}" ${sel}>${escapeHtml(b.name)}</option>`;
        });
        bSelect.innerHTML = html;
    }

    // Populate customer owner select
    const cSelect = document.getElementById('edit-veh-owner');
    if (cSelect) {
        let html = '<option value="">-- No Customer Bound --</option>';
        const customersList = (AppStore.allCustomers && AppStore.allCustomers.length > 0) ? AppStore.allCustomers : (AppStore.managementCustomers || []);
        customersList.forEach(c => {
            const sel = c.id == vehicle.customer_id ? 'selected' : '';
            html += `<option value="${c.id}" ${sel}>${escapeHtml(c.name)} (${c.id})</option>`;
        });
        cSelect.innerHTML = html;
    }

    const modal = document.getElementById('super-admin-edit-vehicle-modal');
    if (modal) {
        console.log('Opening Edit Vehicle modal for ID', vehicleId);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.display = 'flex';
    } else {
        console.error('Edit Vehicle modal element not found');
    }
}

function closeEditVehicleModal() {
    const modal = document.getElementById('super-admin-edit-vehicle-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.style.display = 'none';
    }
}

async function handleEditVehicleSubmit(e) {
    if (e && e.preventDefault) e.preventDefault();
    const vehId = document.getElementById('edit-veh-id')?.value || '';
    const plate = document.getElementById('edit-veh-plate')?.value?.trim() || '';
    const vType = document.getElementById('edit-veh-type')?.value?.trim() || '';
    const make = document.getElementById('edit-veh-make')?.value?.trim() || '';
    const model = document.getElementById('edit-veh-model')?.value?.trim() || '';
    const color = document.getElementById('edit-veh-color')?.value?.trim() || '';
    const year = document.getElementById('edit-veh-year')?.value || '';
    const vin = document.getElementById('edit-veh-vin')?.value?.trim() || '';
    const engine = document.getElementById('edit-veh-engine')?.value?.trim() || '';
    const controller = document.getElementById('edit-veh-controller')?.value?.trim() || '';
    const activateDate = document.getElementById('edit-veh-activate-date')?.value || '';
    const branchId = document.getElementById('edit-veh-branch')?.value || '';
    const customerId = document.getElementById('edit-veh-owner')?.value || '';

    try {
        const res = await request('edit_vehicle', {
            vehicle_id: vehId,
            license_plate: plate,
            vehicle_type: vType,
            make: make,
            model: model,
            color: color,
            year: year,
            vin: vin,
            engine_number: engine,
            controller_number: controller,
            activate_date: activateDate,
            branch_id: branchId,
            customer_id: customerId
        });

        showToast(res.message, 'success');
        closeEditVehicleModal();
        await fetchVehiclesAndCustomers();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

window.openEditCustomerModal = openEditCustomerModal;
window.closeEditCustomerModal = closeEditCustomerModal;
window.handleEditCustomerSubmit = handleEditCustomerSubmit;
window.openEditVehicleModal = openEditVehicleModal;
window.closeEditVehicleModal = closeEditVehicleModal;
window.handleEditVehicleSubmit = handleEditVehicleSubmit;




window.printMaintenanceRecord = function (recordId) {
    if (!recordId) {
        const titleEl = document.getElementById('record-detail-title');
        recordId = titleEl ? titleEl.dataset.id : null;
    }
    if (!recordId) {
        showToast('Record ID not found – cannot print.', 'error');
        return;
    }
    // Open printable view in a new tab
    const url = `/api/print-maintenance-record?record_id=${encodeURIComponent(recordId)}`;
    window.open(url, '_blank');
};




async function handleSubmitAddCustomer(e) {
    if (e && e.preventDefault) e.preventDefault();
    const custId = document.getElementById('add-cust-id')?.value.trim();
    const name = document.getElementById('add-cust-name')?.value.trim();
    const phone = document.getElementById('add-cust-phone')?.value.trim();
    const idCard = document.getElementById('add-cust-idcard')?.value.trim();
    const status = document.getElementById('add-cust-status')?.value || 'Retail';
    const branchId = document.getElementById('add-cust-branch')?.value || null;
    const address = document.getElementById('add-cust-address')?.value.trim();

    try {
        const res = await request('add_customer', {
            customer_id: custId || null,
            name: name,
            phone: phone,
            id_card_number: idCard,
            customer_status: status,
            branch_id: branchId,
            address: address
        });
        showToast(res.message, 'success');
        if (typeof closeAddCustomerModal === 'function') closeAddCustomerModal();

        if (document.getElementById('add-cust-id')) document.getElementById('add-cust-id').value = '';
        if (document.getElementById('add-cust-name')) document.getElementById('add-cust-name').value = '';
        if (document.getElementById('add-cust-phone')) document.getElementById('add-cust-phone').value = '';
        if (document.getElementById('add-cust-idcard')) document.getElementById('add-cust-idcard').value = '';
        if (document.getElementById('add-cust-address')) document.getElementById('add-cust-address').value = '';

        await fetchVehiclesAndCustomers();
        if (typeof fetchManagementCustomers === 'function') await fetchManagementCustomers();
        if (typeof filterCustomersTable === 'function') filterCustomersTable();
        if (typeof filterVehiclesAndCustomers === 'function') filterVehiclesAndCustomers();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function handleSubmitAddVehicle(e) {
    if (e && e.preventDefault) e.preventDefault();
    const vehId = document.getElementById('add-veh-id')?.value.trim();
    const plate = document.getElementById('add-veh-plate')?.value.trim();
    const vType = document.getElementById('add-veh-type')?.value.trim();
    const make = document.getElementById('add-veh-make')?.value.trim();
    const model = document.getElementById('add-veh-model')?.value.trim();
    const color = document.getElementById('add-veh-color')?.value.trim();
    const year = document.getElementById('add-veh-year')?.value || new Date().getFullYear();
    const vin = document.getElementById('add-veh-vin')?.value.trim();
    const engine = document.getElementById('add-veh-engine')?.value.trim();
    const controller = document.getElementById('add-veh-controller')?.value.trim();

    try {
        const res = await request('add_vehicle', {
            vehicle_id: vehId || null,
            license_plate: plate,
            vehicle_type: vType,
            make: make,
            model: model,
            color: color,
            year: year,
            vin: vin,
            engine_number: engine,
            controller_number: controller
        });
        showToast(res.message, 'success');
        if (typeof closeAddVehicleModal === 'function') closeAddVehicleModal();

        if (document.getElementById('add-veh-id')) document.getElementById('add-veh-id').value = '';
        if (document.getElementById('add-veh-plate')) document.getElementById('add-veh-plate').value = '';
        if (document.getElementById('add-veh-type')) document.getElementById('add-veh-type').value = '';
        if (document.getElementById('add-veh-make')) document.getElementById('add-veh-make').value = '';
        if (document.getElementById('add-veh-model')) document.getElementById('add-veh-model').value = '';
        if (document.getElementById('add-veh-color')) document.getElementById('add-veh-color').value = '';
        if (document.getElementById('add-veh-vin')) document.getElementById('add-veh-vin').value = '';
        if (document.getElementById('add-veh-engine')) document.getElementById('add-veh-engine').value = '';
        if (document.getElementById('add-veh-controller')) document.getElementById('add-veh-controller').value = '';

        await fetchVehiclesAndCustomers();
        if (typeof filterCustomersTable === 'function') filterCustomersTable();
        if (typeof filterVehiclesAndCustomers === 'function') filterVehiclesAndCustomers();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

window.handleSubmitAddCustomer = handleSubmitAddCustomer;
window.handleSubmitAddVehicle = handleSubmitAddVehicle;

window.setSuperAdminTab = setSuperAdminTab;
window.openCustomersCSVModal = openCustomersCSVModal;
window.closeCustomersCSVModal = closeCustomersCSVModal;
window.openVehiclesCSVModal = openVehiclesCSVModal;
window.closeVehiclesCSVModal = closeVehiclesCSVModal;
window.downloadCustomersBatchTemplate = downloadCustomersBatchTemplate;
window.downloadVehiclesBatchTemplate = downloadVehiclesBatchTemplate;
window.parseCSVImport = parseCSVImport;
window.submitImportBatch = submitImportBatch;

window.filterCustomersTable = filterCustomersTable;

window.toggleVehColumnMenu = toggleVehColumnMenu;
window.toggleVehColumn = toggleVehColumn;
window.resetVehColumns = resetVehColumns;
window.filterVehiclesAndCustomers = filterVehiclesAndCustomers;
window.deleteVehicle = deleteVehicle;
window.deleteCustomer = deleteCustomer;
window.bindVehicleOwner = bindVehicleOwner;

async function bindVehicleOwner(vehicleId, customerId) {
    try {
        const res = await request('bind_vehicle_customer', {
            vehicle_id: vehicleId,
            customer_id: customerId
        });
        showToast(res.message, 'success');
        await fetchVehiclesAndCustomers();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// --- Client-Side CSV Parsing Engine ---
function parseCSVLine(line) {
    let result = [];
    let insideQuote = false;
    let current = '';
    for (let i = 0; i < line.length; i++) {
        let char = line[i];
        if (char === '"') {
            insideQuote = !insideQuote;
        } else if (char === ',' && !insideQuote) {
            result.push(current.trim());
            current = '';
        } else {
            current += char;
        }
    }
    result.push(current.trim());
    return result;
}

function downloadInventoryBatchTemplate() {
    if (typeof XLSX === 'undefined') {
        showToast('SheetJS library is loading, please try again.', 'warning');
        return;
    }

    const templateData = [
        {
            "SKU": "BRK-001",
            "Sparepart Name": "Front Brake Pad Set",
            "Description": "High durability ceramic brake pads for 110cc-125cc scooters",
            "Category": "Brake System",
            "Connected Service": "Brake Service & Replacement",
            "Service Price": 50000,
            "Available Qty": 25,
            "Price": 150000,
            "Branch": "Downtown Main Office"
        },
        {
            "SKU": "BAT-48V",
            "Sparepart Name": "Lithium Battery Pack 48V 20Ah",
            "Description": "Original lithium battery module with smart BMS protection",
            "Category": "Electrical",
            "Connected Service": "Battery Replacement",
            "Service Price": 50000,
            "Available Qty": 10,
            "Price": 3500000,
            "Branch": "Downtown Main Office"
        },
        {
            "SKU": "TYR-14IN",
            "Sparepart Name": "Tubeless Tire 14 Inch",
            "Description": "All-weather anti-slip tubeless tire (90/90-14)",
            "Category": "General",
            "Connected Service": "Tire Replacement",
            "Service Price": 30000,
            "Available Qty": 40,
            "Price": 220000,
            "Branch": "Northside Auto Care"
        }
    ];

    const worksheet = XLSX.utils.json_to_sheet(templateData);
    worksheet['!cols'] = [
        { wch: 15 }, // SKU
        { wch: 32 }, // Sparepart Name
        { wch: 45 }, // Description
        { wch: 20 }, // Category
        { wch: 15 }, // Available Qty
        { wch: 15 }, // Price
        { wch: 20 }  // Branch
    ];

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Global Inventory Template");
    XLSX.writeFile(workbook, "Global_Inventory_Batch_Template.xlsx");
    showToast('Global inventory template downloaded successfully!', 'success');
}

function downloadCustomersBatchTemplate() {
    if (typeof XLSX === 'undefined') {
        showToast('SheetJS library is loading, please try again.', 'warning');
        return;
    }

    const templateData = [
        {
            "Customer ID": "CUST-001",
            "Customer Name": "Budi Santoso",
            "Phone": "081234567890",
            "ID Card Number": "3201987654320001",
            "Address": "Jl. Merdeka No. 45, Jakarta",
            "Customer Status": "Gomolis",
            "Branch": "Downtown Main Office"
        },
        {
            "Customer ID": "CUST-002",
            "Customer Name": "Siti Aminah",
            "Phone": "085678901234",
            "ID Card Number": "3201987654320002",
            "Address": "Jl. Mawar No. 12, Bandung",
            "Customer Status": "Retail",
            "Branch": "Northside Auto Care"
        }
    ];

    const worksheet = XLSX.utils.json_to_sheet(templateData);
    worksheet['!cols'] = [
        { wch: 16 }, // Customer ID
        { wch: 22 }, // Customer Name
        { wch: 16 }, // Phone
        { wch: 22 }, // ID Card Number
        { wch: 35 }, // Address
        { wch: 18 }, // Customer Status
        { wch: 20 }  // Branch
    ];

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Customers Template");
    XLSX.writeFile(workbook, "Customers_Batch_Template.xlsx");
    showToast('Customers template downloaded successfully!', 'success');
}

function downloadVehiclesBatchTemplate() {
    if (typeof XLSX === 'undefined') {
        showToast('SheetJS library is loading, please try again.', 'warning');
        return;
    }

    const templateData = [
        {
            "License Plate": "B 1234 ABC",
            "Vehicle Type": "EV Scooter Gesits",
            "Make": "Gesits",
            "Model": "G1",
            "Color": "Black Matte",
            "Year": 2024,
            "Frame Number / VIN": "MH1GESITS2024001",
            "Engine Number": "ENG-998811",
            "Controller Number": "CTL-445566",
            "Customer ID": "CUST-001",
            "ID Card Number": "3201987654320001",
            "Customer Name": "Budi Santoso",
            "Branch": "Downtown Main Office"
        },
        {
            "License Plate": "D 5678 XYZ",
            "Vehicle Type": "EV Scooter Volta",
            "Make": "Volta",
            "Model": "401",
            "Color": "Green Mint",
            "Year": 2023,
            "Frame Number / VIN": "MH1VOLTA2023002",
            "Engine Number": "ENG-332211",
            "Controller Number": "CTL-778899",
            "Customer ID": "CUST-002",
            "ID Card Number": "3201987654320002",
            "Customer Name": "Siti Aminah",
            "Branch": "Northside Auto Care"
        }
    ];

    const worksheet = XLSX.utils.json_to_sheet(templateData);
    worksheet['!cols'] = [
        { wch: 16 }, // License Plate
        { wch: 18 }, // Vehicle Type
        { wch: 14 }, // Make
        { wch: 14 }, // Model
        { wch: 14 }, // Color
        { wch: 10 }, // Year
        { wch: 24 }, // Frame Number / VIN
        { wch: 18 }, // Engine Number
        { wch: 18 }, // Controller Number
        { wch: 16 }, // Customer ID
        { wch: 22 }, // ID Card Number
        { wch: 22 }, // Customer Name
        { wch: 20 }  // Branch
    ];

    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Vehicles Template");
    XLSX.writeFile(workbook, "Vehicles_Batch_Template.xlsx");
    showToast('Vehicles template downloaded successfully!', 'success');
}

function openCustomersCSVModal() {
    const modal = document.getElementById('customers-csv-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.display = 'flex';
    }
}

function closeCustomersCSVModal() {
    const modal = document.getElementById('customers-csv-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.style.display = 'none';
    }
    clearImportPreview();
}

function openVehiclesCSVModal() {
    const modal = document.getElementById('vehicles-csv-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.display = 'flex';
    }
}

function closeVehiclesCSVModal() {
    const modal = document.getElementById('vehicles-csv-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.style.display = 'none';
    }
    clearImportPreview();
}

function parseCSVImport(type) {
    let fileInputId = 'csv-import-inventory-file';
    if (type === 'customers') fileInputId = 'csv-import-customers-file';
    else if (type === 'vehicles') fileInputId = 'csv-import-vehicles-file';

    const fileInput = document.getElementById(fileInputId);
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) return;

    const file = fileInput.files[0];
    const fileName = file.name.toLowerCase();
    const isExcel = fileName.endsWith('.xlsx') || fileName.endsWith('.xls');

    // UI Progress Elements for Modal
    let modalPrefix = 'batch';
    if (type === 'customers') modalPrefix = 'cust';
    else if (type === 'vehicles') modalPrefix = 'veh';

    const progressContainer = document.getElementById(`${modalPrefix}-import-progress-container`) || document.getElementById('batch-import-progress-container');
    const progressStatusText = document.getElementById(`${modalPrefix}-progress-status-text`) || document.getElementById('batch-progress-status-text');
    const progressPercent = document.getElementById(`${modalPrefix}-progress-percent`) || document.getElementById('batch-progress-percent');
    const progressBar = document.getElementById(`${modalPrefix}-progress-bar`) || document.getElementById('batch-progress-bar');
    const progressDetails = document.getElementById(`${modalPrefix}-progress-details`) || document.getElementById('batch-progress-details');
    const successBanner = document.getElementById(`${modalPrefix}-import-success-banner`) || document.getElementById('batch-import-success-banner');

    if (progressContainer) {
        if (successBanner) successBanner.classList.add('hidden');
        progressContainer.classList.remove('hidden');
        if (progressPercent) progressPercent.innerText = '15%';
        if (progressBar) progressBar.style.width = '15%';
        if (progressStatusText) progressStatusText.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-cyan-400"></i> Reading File...';
        if (progressDetails) progressDetails.innerText = `Loading file '${file.name}' (${(file.size / 1024).toFixed(1)} KB)...`;
    }

    const reader = new FileReader();

    const processRowsData = (rawJsonRows) => {
        if (!rawJsonRows || rawJsonRows.length === 0) {
            showToast('File is empty or contains no valid rows.', 'error');
            fileInput.value = '';
            if (progressContainer) progressContainer.classList.add('hidden');
            return;
        }

        if (progressContainer) {
            if (progressPercent) progressPercent.innerText = '60%';
            if (progressBar) progressBar.style.width = '60%';
            if (progressStatusText) progressStatusText.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-cyan-400"></i> Validating & Mapping Headers...';
            if (progressDetails) progressDetails.innerText = `Parsed ${rawJsonRows.length} raw data rows. Checking schema compatibility...`;
        }

        const headers = Object.keys(rawJsonRows[0]);
        const findKey = (candidates) => {
            const found = headers.find(h => candidates.includes(h.trim().toLowerCase()));
            return found || null;
        };

        const skuKey = findKey(['sku', 'sku code', 'part sku', 'code']);
        const nameKey = findKey(['sparepart name', 'spare part name', 'part name', 'item name', 'name']);
        const qtyKey = findKey(['available qty', 'available_qty', 'qty', 'quantity', 'stock', 'available quantity']);
        const connServiceKey = findKey(['connected service', 'service name', 'connected_service', 'service']);
        const servicePriceKey = findKey(['service price', 'service fee', 'labor fee', 'service_price', 'fee']);
        const priceKey = findKey(['price', 'price (idr)', 'unit price', 'cost']);
        const branchKey = findKey(['branch', 'branch name', 'shop', 'location']);

        const catKey = findKey(['category', 'part category', 'sparepart category']);
        const descKey = findKey(['description', 'spare parts description', 'sparepart description', 'details', 'notes']);

        const custIdKey = findKey(['customer id', 'customer_id', 'cust id', 'cust_id', 'id customer', 'customer_code']);
        const custNameKey = findKey(['customer name', 'name', 'nama customer', 'customer', 'owner']);
        const custPhoneKey = findKey(['phone', 'phone number', 'no hp', 'telp', 'telephone', 'mobile']);
        const custIdCardKey = findKey(['id card number', 'ktp', 'nik', 'id card', 'no ktp']);
        const custAddressKey = findKey(['address', 'alamat']);
        const custStatusKey = findKey(['customer status', 'status', 'customer_status', 'tipe customer', 'status customer', 'kategori status', 'customer_type']);
        const plateKey = findKey(['license plate', 'plate', 'plat nomor', 'no polisi', 'nopol']);
        const vehTypeKey = findKey(['vehicle type', 'type', 'tipe kendaraan', 'tipe']);
        const makeKey = findKey(['make', 'merk', 'brand']);
        const modelKey = findKey(['model', 'tipe model']);
        const yearKey = findKey(['year', 'tahun']);
        const frameKey = findKey(['frame number / vin', 'frame number', 'vin', 'no rangka']);
        const engineKey = findKey(['engine number', 'engine no', 'no mesin']);
        const controllerKey = findKey(['controller number', 'controller no', 'controller code', 'no controller']);
        const colorKey = findKey(['color', 'warna', 'vehicle color']);

        const rows = [];
        rawJsonRows.forEach(rawRow => {
            if (type === 'inventory') {
                const skuVal = rawRow[skuKey] !== undefined ? String(rawRow[skuKey]).trim() : '';
                const nameVal = rawRow[nameKey] !== undefined ? String(rawRow[nameKey]).trim() : '';
                const branchVal = branchKey && rawRow[branchKey] !== undefined ? String(rawRow[branchKey]).trim() : '';
                let rawQtyStr = rawRow[qtyKey] !== undefined ? String(rawRow[qtyKey]).replace(/[^0-9-]/g, '') : '0';
                let rawPriceStr = rawRow[priceKey] !== undefined ? String(rawRow[priceKey]).replace(/[^0-9.-]/g, '') : '0';
                const connServiceVal = connServiceKey && rawRow[connServiceKey] !== undefined ? String(rawRow[connServiceKey]).trim() : '';
                let rawServicePriceStr = servicePriceKey && rawRow[servicePriceKey] !== undefined ? String(rawRow[servicePriceKey]).replace(/[^0-9.-]/g, '') : '0';
                const servicePriceVal = parseFloat(rawServicePriceStr) || 0;
                const qtyVal = parseInt(rawQtyStr) || 0;
                const priceVal = parseFloat(rawPriceStr) || 0;
                const rawCatVal = catKey && rawRow[catKey] !== undefined ? String(rawRow[catKey]).trim() : '';
                let catVal = 'General';
                if (rawCatVal) {
                    const foundCat = (AppStore.sparepartCategories || []).find(c => (c.name || '').toLowerCase().trim() === rawCatVal.toLowerCase());
                    catVal = foundCat ? foundCat.name : 'General';
                }
                const descVal = descKey && rawRow[descKey] !== undefined ? String(rawRow[descKey]).trim() : '';

                if (skuVal || nameVal || branchVal) {
                    rows.push({
                        'SKU': skuVal,
                        'Sparepart Name': nameVal,
                        'Available Qty': qtyVal,
                        'Price': priceVal,
                        'Connected Service': connServiceVal,
                        'Service Price': servicePriceVal,
                        'Branch': branchVal,
                        'Category': catVal,
                        'Description': descVal
                    });
                }
            } else if (type === 'customers') {
                const cId = custIdKey && rawRow[custIdKey] !== undefined ? String(rawRow[custIdKey]).trim() : '';
                const cName = custNameKey && rawRow[custNameKey] !== undefined ? String(rawRow[custNameKey]).trim() : '';
                const cPhone = custPhoneKey && rawRow[custPhoneKey] !== undefined ? String(rawRow[custPhoneKey]).trim() : '';
                const cIdCard = custIdCardKey && rawRow[custIdCardKey] !== undefined ? String(rawRow[custIdCardKey]).trim() : '';
                const cAddress = custAddressKey && rawRow[custAddressKey] !== undefined ? String(rawRow[custAddressKey]).trim() : '';
                const cStatus = custStatusKey && rawRow[custStatusKey] !== undefined ? String(rawRow[custStatusKey]).trim() : 'Retail';
                const branch = branchKey && rawRow[branchKey] !== undefined ? String(rawRow[branchKey]).trim() : '';

                if (cId || cName || cPhone || cIdCard) {
                    rows.push({
                        'Customer ID': cId,
                        'Customer Name': cName,
                        'Phone': cPhone,
                        'ID Card Number': cIdCard,
                        'Address': cAddress,
                        'Customer Status': cStatus || 'Retail',
                        'Branch': branch
                    });
                }
            } else if (type === 'vehicles') {
                const plate = plateKey && rawRow[plateKey] !== undefined ? String(rawRow[plateKey]).trim() : '';
                const vType = vehTypeKey && rawRow[vehTypeKey] !== undefined ? String(rawRow[vehTypeKey]).trim() : '';
                const make = makeKey && rawRow[makeKey] !== undefined ? String(rawRow[makeKey]).trim() : '';
                const model = modelKey && rawRow[modelKey] !== undefined ? String(rawRow[modelKey]).trim() : '';
                const color = colorKey && rawRow[colorKey] !== undefined ? String(rawRow[colorKey]).trim() : '';
                const year = yearKey && rawRow[yearKey] !== undefined ? String(rawRow[yearKey]).trim() : '';
                const frame = frameKey && rawRow[frameKey] !== undefined ? String(rawRow[frameKey]).trim() : '';
                const engine = engineKey && rawRow[engineKey] !== undefined ? String(rawRow[engineKey]).trim() : '';
                const controller = controllerKey && rawRow[controllerKey] !== undefined ? String(rawRow[controllerKey]).trim() : '';
                const cId = custIdKey && rawRow[custIdKey] !== undefined ? String(rawRow[custIdKey]).trim() : '';
                const cIdCard = custIdCardKey && rawRow[custIdCardKey] !== undefined ? String(rawRow[custIdCardKey]).trim() : '';
                const cName = custNameKey && rawRow[custNameKey] !== undefined ? String(rawRow[custNameKey]).trim() : '';
                const branch = branchKey && rawRow[branchKey] !== undefined ? String(rawRow[branchKey]).trim() : '';

                if (plate || frame || controller) {
                    rows.push({
                        'License Plate': plate,
                        'Vehicle Type': vType,
                        'Make': make,
                        'Model': model,
                        'Color': color,
                        'Year': year,
                        'Frame Number / VIN': frame,
                        'Engine Number': engine,
                        'Controller Number': controller,
                        'Customer ID': cId,
                        'ID Card Number': cIdCard,
                        'Customer Name': cName,
                        'Branch': branch
                    });
                }
            }
        });

        AppStore.pendingImport = { type, rows };

        setTimeout(() => {
            if (progressContainer) {
                if (progressPercent) progressPercent.innerText = '100%';
                if (progressBar) progressBar.style.width = '100%';
                if (progressStatusText) progressStatusText.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400"></i> Spreadsheet Parsed Successfully!';
                if (progressDetails) progressDetails.innerText = `Ready to commit ${rows.length} valid records to database.`;
            }
            renderImportPreview(type, rows);
        }, 300);
    };

    if (isExcel && typeof XLSX !== 'undefined') {
        reader.onload = function (e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonRows = XLSX.utils.sheet_to_json(worksheet, { defval: '' });
                processRowsData(jsonRows);
            } catch (err) {
                showToast(`Failed to parse Excel file: ${err.message}`, 'error');
                fileInput.value = '';
                if (progressContainer) progressContainer.classList.add('hidden');
            }
        };
        reader.readAsArrayBuffer(file);
    } else {
        reader.onload = function (e) {
            const text = e.target.result;
            const lines = text.split(/\r\n|\n/);
            if (lines.length === 0) return;

            const headers = parseCSVLine(lines[0]);
            const headerMap = {};
            headers.forEach((h, idx) => { headerMap[h.toLowerCase()] = idx; });

            const rawJsonRows = [];
            for (let i = 1; i < lines.length; i++) {
                const fields = parseCSVLine(lines[i]);
                if (fields.length <= 1 && fields[0] === '') continue;
                const rowObj = {};
                headers.forEach((h, idx) => { rowObj[h] = fields[idx] || ''; });
                rawJsonRows.push(rowObj);
            }
            processRowsData(rawJsonRows);
        };
        reader.readAsText(file);
    }
}

function renderImportPreview(type, rows) {
    let headers = [];
    if (type === 'customers') {
        headers = ['Branch', 'Customer Name', 'Phone', 'ID Card Number', 'Customer Status', 'Address'];
    } else if (type === 'vehicles') {
        headers = ['Branch', 'License Plate', 'Vehicle Type', 'Make', 'Model', 'Color', 'Year', 'Frame Number / VIN', 'Controller Number', 'Customer Name'];
    } else {
        headers = ['SKU', 'Sparepart Name', 'Description', 'Connected Service', 'Service Price', 'Available Qty', 'Price', 'Branch'];
    }

    const tableHeaderHtml = `
                <tr class="bg-slate-900 text-slate-400 border-b border-slate-800">
                    <th class="p-3 text-[10px] font-bold uppercase tracking-wider w-16">Status</th>
                    ${headers.map(h => `<th class="p-3 text-[10px] font-bold uppercase tracking-wider">${h}</th>`).join('')}
                </tr>
            `;

    if (rows.length === 0) {
        const emptyHtml = `<tr><td colspan="${headers.length + 1}" class="p-6 text-center text-slate-500">No valid rows found in file.</td></tr>`;
        const tbody = document.getElementById('import-preview-tbody');
        if (tbody) tbody.innerHTML = emptyHtml;
        return;
    }

    const rowsHtml = rows.map((row) => {
        let isValid = true;
        let warnings = [];

        if (type === 'customers') {
            if (!row['Customer Name'] && !row['Phone'] && !row['ID Card Number']) { isValid = false; warnings.push('Customer name or phone missing'); }
        } else if (type === 'vehicles') {
            if (!row['License Plate'] && !row['Frame Number / VIN']) { isValid = false; warnings.push('Plate or VIN missing'); }
        } else {
            if (!row['SKU']) { isValid = false; warnings.push('SKU missing'); }
            if (!row['Sparepart Name']) { isValid = false; warnings.push('Name missing'); }
        }

        const statusIcon = isValid
            ? `<i class="fa-solid fa-circle-check text-emerald-500" title="Valid"></i>`
            : `<i class="fa-solid fa-circle-exclamation text-rose-500" title="Error: ${warnings.join(', ')}"></i>`;

        return `
                    <tr class="hover:bg-slate-900/20 border-b border-slate-800/40 text-[11px] text-slate-300">
                        <td class="p-3 text-center">${statusIcon}</td>
                        ${headers.map(h => `<td class="p-3 text-slate-300 font-medium">${row[h] || '<span class="text-slate-600 font-normal">N/A</span>'}</td>`).join('')}
                    </tr>
                `;
    }).join('');

    let modalPrefix = 'batch';
    if (type === 'customers') modalPrefix = 'cust';
    else if (type === 'vehicles') modalPrefix = 'veh';

    const modalPane = document.getElementById(`${modalPrefix}-import-preview-pane`) || document.getElementById('modal-import-preview-pane');
    const modalThead = document.getElementById(`${modalPrefix}-import-preview-thead`) || document.getElementById('modal-import-preview-thead');
    const modalTbody = document.getElementById(`${modalPrefix}-import-preview-tbody`) || document.getElementById('modal-import-preview-tbody');
    const modalSubmitBtn = document.getElementById(`btn-${modalPrefix}-submit-import-batch`) || document.getElementById('btn-modal-submit-import-batch');

    if (modalPane && modalThead && modalTbody) {
        modalPane.classList.remove('hidden');
        modalThead.innerHTML = tableHeaderHtml;
        modalTbody.innerHTML = rowsHtml;
        if (modalSubmitBtn) modalSubmitBtn.innerText = `Commit Import (${rows.length} rows)`;
    }
}

function clearImportPreview() {
    const custFile = document.getElementById('csv-import-customers-file');
    const vehFile = document.getElementById('csv-import-vehicles-file');
    const invFile = document.getElementById('csv-import-inventory-file');
    if (custFile) custFile.value = '';
    if (vehFile) vehFile.value = '';
    if (invFile) invFile.value = '';

    ['import-preview-pane', 'modal-import-preview-pane', 'cust-import-preview-pane', 'veh-import-preview-pane', 'batch-import-progress-container', 'cust-import-progress-container', 'veh-import-progress-container', 'batch-import-success-banner', 'cust-import-success-banner', 'veh-import-success-banner'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });

    AppStore.pendingImport = null;
}

async function submitImportBatch() {
    if (!AppStore.pendingImport || !AppStore.pendingImport.rows || AppStore.pendingImport.rows.length === 0) {
        showToast('No pending import rows to submit.', 'warning');
        return;
    }

    const type = AppStore.pendingImport.type;
    let modalPrefix = 'batch';
    if (type === 'customers') modalPrefix = 'cust';
    else if (type === 'vehicles') modalPrefix = 'veh';

    const submitBtn = document.getElementById('btn-submit-import-batch');
    const modalSubmitBtn = document.getElementById(`btn-${modalPrefix}-submit-import-batch`) || document.getElementById('btn-modal-submit-import-batch');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = 'Uploading batch to database...';
    }
    if (modalSubmitBtn) {
        modalSubmitBtn.disabled = true;
        modalSubmitBtn.innerText = 'Uploading to database...';
    }

    const progressContainer = document.getElementById(`${modalPrefix}-import-progress-container`) || document.getElementById('batch-import-progress-container');
    const progressStatusText = document.getElementById(`${modalPrefix}-progress-status-text`) || document.getElementById('batch-progress-status-text');
    const progressPercent = document.getElementById(`${modalPrefix}-progress-percent`) || document.getElementById('batch-progress-percent');
    const progressBar = document.getElementById(`${modalPrefix}-progress-bar`) || document.getElementById('batch-progress-bar');
    const progressDetails = document.getElementById(`${modalPrefix}-progress-details`) || document.getElementById('batch-progress-details');
    const successBanner = document.getElementById(`${modalPrefix}-import-success-banner`) || document.getElementById('batch-import-success-banner');

    if (progressContainer) {
        progressContainer.classList.remove('hidden');
        if (progressPercent) progressPercent.innerText = '80%';
        if (progressBar) progressBar.style.width = '80%';
        if (progressStatusText) progressStatusText.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-cyan-400"></i> Inserting & Updating Database...';
        if (progressDetails) progressDetails.innerText = `Committing ${AppStore.pendingImport.rows.length} rows to database...`;
    }

    let action = 'import_inventory_batch';
    if (type === 'customers') action = 'import_customers_batch';
    else if (type === 'vehicles') action = 'import_vehicles_batch';

    try {
        const res = await request(action, { rows: AppStore.pendingImport.rows });

        if (progressContainer) {
            if (progressPercent) progressPercent.innerText = '100%';
            if (progressBar) progressBar.style.width = '100%';
            if (progressStatusText) progressStatusText.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400"></i> Database Sync Complete!';
            if (progressDetails) progressDetails.innerText = res.message || 'Records inserted successfully.';
        }

        if (successBanner) {
            const msgEl = document.getElementById(`${modalPrefix}-success-message`) || document.getElementById('batch-success-message');
            if (msgEl) msgEl.innerText = res.message || 'Batch records successfully committed to database.';
            successBanner.classList.remove('hidden');
        }

        showToast(res.message, 'success');

        if (type === 'customers' || type === 'vehicles') {
            if (typeof fetchVehiclesAndCustomers === 'function') {
                await fetchVehiclesAndCustomers();
            }
        } else {
            if (typeof fetchGlobalInventory === 'function') {
                await fetchGlobalInventory();
            }
        }
    } catch (err) {
        if (progressContainer) {
            if (progressStatusText) progressStatusText.innerHTML = '<i class="fa-solid fa-circle-xmark text-rose-500"></i> Database Sync Failed';
            if (progressDetails) progressDetails.innerText = err.message;
        }
        showToast(err.message, 'error');
    } finally {
        if (submitBtn) submitBtn.disabled = false;
        if (modalSubmitBtn) modalSubmitBtn.disabled = false;
    }
}

window.downloadInventoryBatchTemplate = downloadInventoryBatchTemplate;
window.downloadCustomersBatchTemplate = downloadCustomersBatchTemplate;
window.downloadVehiclesBatchTemplate = downloadVehiclesBatchTemplate;
window.openCustomersCSVModal = openCustomersCSVModal;
window.closeCustomersCSVModal = closeCustomersCSVModal;
window.openVehiclesCSVModal = openVehiclesCSVModal;
window.closeVehiclesCSVModal = closeVehiclesCSVModal;
window.parseCSVImport = parseCSVImport;
window.clearImportPreview = clearImportPreview;
window.submitImportBatch = submitImportBatch;

function openAddCustomerModal() {
    if (document.getElementById('add-cust-name')) document.getElementById('add-cust-name').value = '';
    if (document.getElementById('add-cust-phone')) document.getElementById('add-cust-phone').value = '';
    if (document.getElementById('add-cust-idcard')) document.getElementById('add-cust-idcard').value = '';
    if (document.getElementById('add-cust-status')) document.getElementById('add-cust-status').value = 'Retail';
    if (document.getElementById('add-cust-address')) document.getElementById('add-cust-address').value = '';

    const branchSelect = document.getElementById('add-cust-branch');
    if (branchSelect) {
        let html = '<option value="">Global / All Branches</option>';
        (AppStore.branches || []).forEach(b => {
            html += `<option value="${b.id}">${escapeHtml(b.name)}</option>`;
        });
        branchSelect.innerHTML = html;
    }

    const modal = document.getElementById('super-admin-add-customer-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeAddCustomerModal() {
    const modal = document.getElementById('super-admin-add-customer-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

async function handleSubmitAddCustomer(e) {
    if (e && e.preventDefault) e.preventDefault();
    const customerId = document.getElementById('add-cust-id') ? document.getElementById('add-cust-id').value.trim() : '';
    const name = document.getElementById('add-cust-name').value.trim();
    const phone = document.getElementById('add-cust-phone').value.trim();
    const idCard = document.getElementById('add-cust-idcard').value.trim();
    const customerStatus = document.getElementById('add-cust-status').value;
    const address = document.getElementById('add-cust-address').value.trim();
    const branchId = document.getElementById('add-cust-branch').value;

    try {
        const res = await request('add_customer', {
            customer_id: customerId,
            name: name,
            phone: phone,
            id_card_number: idCard,
            customer_status: customerStatus,
            address: address,
            branch_id: branchId
        });

        showToast(res.message, 'success');
        closeAddCustomerModal();

        if (typeof fetchVehiclesAndCustomers === 'function') {
            await fetchVehiclesAndCustomers();
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openAddVehicleModal() {
    if (document.getElementById('add-veh-plate')) document.getElementById('add-veh-plate').value = '';
    if (document.getElementById('add-veh-type')) document.getElementById('add-veh-type').value = '';
    if (document.getElementById('add-veh-make')) document.getElementById('add-veh-make').value = '';
    if (document.getElementById('add-veh-model')) document.getElementById('add-veh-model').value = '';
    if (document.getElementById('add-veh-color')) document.getElementById('add-veh-color').value = '';
    if (document.getElementById('add-veh-year')) document.getElementById('add-veh-year').value = new Date().getFullYear();
    if (document.getElementById('add-veh-vin')) document.getElementById('add-veh-vin').value = '';
    if (document.getElementById('add-veh-engine')) document.getElementById('add-veh-engine').value = '';
    if (document.getElementById('add-veh-controller')) document.getElementById('add-veh-controller').value = '';

    const branchSelect = document.getElementById('add-veh-branch');
    if (branchSelect) {
        let html = '<option value="">Global / All Branches</option>';
        (AppStore.branches || []).forEach(b => {
            html += `<option value="${b.id}">${escapeHtml(b.name)}</option>`;
        });
        branchSelect.innerHTML = html;
    }

    const ownerSelect = document.getElementById('add-veh-owner');
    if (ownerSelect) {
        let html = '<option value="">-- No Customer Bound --</option>';
        (AppStore.allCustomers || []).forEach(c => {
            html += `<option value="${c.id}">${escapeHtml(c.name)} (${c.id})</option>`;
        });
        ownerSelect.innerHTML = html;
    }

    const modal = document.getElementById('super-admin-add-vehicle-modal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeAddVehicleModal() {
    const modal = document.getElementById('super-admin-add-vehicle-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

async function handleSubmitAddVehicle(e) {
    if (e && e.preventDefault) e.preventDefault();
    const vehicleId = document.getElementById('add-veh-id') ? document.getElementById('add-veh-id').value.trim() : '';
    const plate = document.getElementById('add-veh-plate').value.trim();
    const vType = document.getElementById('add-veh-type').value.trim();
    const make = document.getElementById('add-veh-make').value.trim();
    const model = document.getElementById('add-veh-model').value.trim();
    const color = document.getElementById('add-veh-color').value.trim();
    const year = document.getElementById('add-veh-year').value;
    const vin = document.getElementById('add-veh-vin').value.trim();
    const engine = document.getElementById('add-veh-engine').value.trim();
    const controller = document.getElementById('add-veh-controller').value.trim();
    const activateDate = document.getElementById('add-veh-activate-date')?.value || '';
    const branchId = document.getElementById('add-veh-branch').value;
    const customerId = document.getElementById('add-veh-owner').value;

    try {
        const res = await request('add_vehicle', {
            vehicle_id: vehicleId,
            license_plate: plate,
            vehicle_type: vType,
            make: make,
            model: model,
            color: color,
            year: year,
            vin: vin,
            engine_number: engine,
            controller_number: controller,
            activate_date: activateDate,
            branch_id: branchId,
            customer_id: customerId
        });

        showToast(res.message, 'success');
        closeAddVehicleModal();

        if (typeof fetchVehiclesAndCustomers === 'function') {
            await fetchVehiclesAndCustomers();
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

window.openAddCustomerModal = openAddCustomerModal;
window.closeAddCustomerModal = closeAddCustomerModal;
window.handleSubmitAddCustomer = handleSubmitAddCustomer;
window.openAddVehicleModal = openAddVehicleModal;
window.closeAddVehicleModal = closeAddVehicleModal;
window.handleSubmitAddVehicle = handleSubmitAddVehicle;
window.handleSubmitAddVehicle = handleSubmitAddVehicle;

async function loadSuperAdminStats() {
    try {
        const stats = await request('get_dashboard_stats', {}, 'GET');
        AppStore.stats = stats;

        // Write Counters
        if (document.getElementById('stat-total-branches')) document.getElementById('stat-total-branches').innerText = stats?.counters?.total_branches ?? 0;
        if (document.getElementById('stat-service-revenue')) document.getElementById('stat-service-revenue').innerText = formatIDR(stats?.counters?.service_revenue ?? 0);
        if (document.getElementById('stat-active-jobs')) document.getElementById('stat-active-jobs').innerText = stats?.counters?.active_jobs ?? 0;
        if (document.getElementById('stat-completed-jobs')) document.getElementById('stat-completed-jobs').innerText = stats?.counters?.completed_jobs ?? 0;
        if (document.getElementById('stat-parts-revenue')) document.getElementById('stat-parts-revenue').innerText = formatIDR(stats?.counters?.parts_revenue ?? 0);

        // Render Low Stock Warnings
        const warningList = document.getElementById('low-stock-panel-list');
        const lowStockAlerts = Array.isArray(stats.low_stock_alerts) ? stats.low_stock_alerts : [];
        if (lowStockAlerts.length === 0) {
            warningList.innerHTML = `
                        <div class="text-center py-8 text-slate-500">
                            <i class="fa-solid fa-circle-check text-2xl text-emerald-500 mb-2"></i>
                            <p>Semua jumlah stok dalam kondisi optimal.</p>
                        </div>
                    `;
        } else {
            warningList.innerHTML = lowStockAlerts.map(item => `
                        <div class="flex items-center justify-between p-3 bg-rose-950/20 border border-rose-900/30 rounded-lg text-slate-200">
                            <div>
                                <span class="font-bold text-xs uppercase text-rose-400">[Cabang: ${escapeHtml(item.branch_name)}]</span>
                                <h4 class="font-semibold text-sm mt-0.5">${escapeHtml(item.part_name)}</h4>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 block font-semibold">Sisa Stok:</span>
                                <span class="font-bold font-sans text-sm text-rose-500">${item.available_qty}</span>
                            </div>
                        </div>
                    `).join('');
        }

        // Render Chart.js Graph
        renderStatsChart(stats.branch_performance || []);
        renderSparePartsVsDatesChart(stats.spare_parts_vs_dates || []);
        renderMechanicTimeChart(stats.mechanic_record_time || []);
        renderMechanicScoreboard(stats.mechanic_scoreboard || []);
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function updateSparePartsChartPeriod() {
    const period = document.getElementById('spare-parts-period-filter')?.value || 'daily';
    try {
        const stats = await request('get_dashboard_stats', { period }, 'GET');
        renderSparePartsVsDatesChart(stats.spare_parts_vs_dates || []);
    } catch (err) {
        showToast(err.message, 'error');
    }
}
window.updateSparePartsChartPeriod = updateSparePartsChartPeriod;

function getChartTextColor() {
    const isLight = document.body.classList.contains('light-mode');
    return isLight ? '#334155' : '#f1f5f9'; // slate-700 in Light mode, slate-100 in Dark mode
}

function getChartGridColor() {
    const isLight = document.body.classList.contains('light-mode');
    return isLight ? 'rgba(0, 0, 0, 0.08)' : 'rgba(255, 255, 255, 0.05)';
}

function renderStatsChart(data) {
    const ctx = document.getElementById('branchPerformanceChart')?.getContext('2d');
    if (!ctx) return;

    // Clean up previous charts
    if (AppStore.chartInstance) {
        AppStore.chartInstance.destroy();
    }

    data = Array.isArray(data) ? data : [];
    AppStore.branchPerformanceData = data;
    const textColor = getChartTextColor();
    const gridColor = getChartGridColor();

    const labels = data.map(b => b.branch_name);
    const totalJobs = data.map(b => parseInt(b.total_jobs));
    const revenue = data.map(b => parseFloat(b.parts_billing));

    AppStore.chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: t('dash.total_jobs_logged', 'Total Jobs Logged'),
                    data: totalJobs,
                    backgroundColor: 'rgba(59, 130, 246, 0.45)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1.5,
                    yAxisID: 'y'
                },
                {
                    label: t('dash.parts_revenue_idr', 'Spare Parts Revenue (IDR)'),
                    data: revenue,
                    backgroundColor: 'rgba(139, 92, 246, 0.45)',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 1.5,
                    yAxisID: 'y1',
                    type: 'line',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: {
                        color: textColor,
                        callback: function (value) { return 'Rp. ' + value.toLocaleString('id-ID'); }
                    }
                },
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                }
            },
            plugins: {
                legend: {
                    labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: 'bold' } }
                }
            }
        }
    });
}

function renderSparePartsVsDatesChart(data) {
    const ctx = document.getElementById('sparePartsVsDatesChart')?.getContext('2d');
    if (!ctx) return;
    if (AppStore.sparePartsChartInstance) {
        AppStore.sparePartsChartInstance.destroy();
    }

    data = Array.isArray(data) ? data : [];
    AppStore.sparePartsVsDatesData = data;
    const textColor = getChartTextColor();
    const gridColor = getChartGridColor();

    const labels = data.map(d => d.date);
    const totalParts = data.map(d => parseInt(d.total_parts));

    AppStore.sparePartsChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: t('dash.total_parts_used', 'Total Spare Parts Used'),
                data: totalParts,
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                },
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                }
            },
            plugins: {
                legend: { labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: 'bold' } } },
                tooltip: {
                    callbacks: {
                        title: function (context) {
                            const index = context[0].dataIndex;
                            const item = data[index];
                            if (item && item.date_range) {
                                return `${item.date} (${item.date_range})`;
                            }
                            return item ? item.date : '';
                        }
                    }
                }
            }
        }
    });
}

function renderMechanicTimeChart(data) {
    const filter = document.getElementById('mechanic-chart-filter');
    if (filter) {
        let html = '<option value="all">All Mechanics</option>';
        data.forEach(m => {
            html += `<option value="${m.mechanic_id}">${escapeHtml(m.alias_name)} (${escapeHtml(m.real_name)})</option>`;
        });
        filter.innerHTML = html;
    }

    AppStore.mechanicRecordTimeData = data;
    updateMechanicDurationChart();
}

function updateMechanicDurationChart() {
    const filterVal = document.getElementById('mechanic-chart-filter')?.value || 'all';
    const data = AppStore.mechanicRecordTimeData || [];

    const ctx = document.getElementById('mechanicRecordTimeChart')?.getContext('2d');
    if (!ctx) return;

    if (AppStore.mechanicTimeChartInstance) {
        AppStore.mechanicTimeChartInstance.destroy();
    }

    const textColor = getChartTextColor();
    const gridColor = getChartGridColor();

    const safeData = Array.isArray(data) ? data : [];
    let labels = [];
    let durations = [];
    let tooltipDetails = [];

    if (filterVal === 'all') {
        // X-axis: Mechanics A, Mechanics B, etc.
        // Y-axis: Average repair duration in minutes
        labels = safeData.map(m => m.alias_name);
        durations = safeData.map(m => parseInt(m.avg_minutes || 0));
        tooltipDetails = safeData.map(m => `${m.real_name} (Avg ${m.avg_minutes} mins across ${m.total_jobs} completed jobs)`);
    } else {
        // X-axis: JOB 1, JOB 2, ... max 5 last jobs for selected mechanic
        const selectedMech = safeData.find(m => m.mechanic_id == filterVal);
        if (selectedMech && selectedMech.last_jobs && selectedMech.last_jobs.length > 0) {
            labels = selectedMech.last_jobs.map(j => j.job_label);
            durations = selectedMech.last_jobs.map(j => parseInt(j.duration_minutes));
            tooltipDetails = selectedMech.last_jobs.map(j => `${selectedMech.real_name} - JOB #${j.record_id}: ${j.duration_minutes} mins`);
        } else {
            labels = ['No Jobs'];
            durations = [0];
            tooltipDetails = ['No completed repair jobs recorded for this mechanic yet.'];
        }
    }

    AppStore.mechanicTimeChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: filterVal === 'all' ? 'Average Duration (Minutes)' : 'Job Duration (Minutes)',
                data: durations,
                backgroundColor: filterVal === 'all' ? 'rgba(59, 130, 246, 0.6)' : 'rgba(16, 185, 129, 0.6)',
                borderColor: filterVal === 'all' ? 'rgba(59, 130, 246, 1)' : 'rgba(16, 185, 129, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Minutes', color: textColor },
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                },
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const index = context.dataIndex;
                            return `Duration: ${context.parsed.y} mins (${tooltipDetails[index] || ''})`;
                        }
                    }
                },
                legend: { labels: { color: textColor, font: { family: 'Plus Jakarta Sans', weight: 'bold' } } }
            }
        }
    });
}

function updateChartsThemeColors() {
    if (AppStore.branchPerformanceData) {
        renderStatsChart(AppStore.branchPerformanceData);
    }
    if (AppStore.sparePartsVsDatesData) {
        renderSparePartsVsDatesChart(AppStore.sparePartsVsDatesData);
    }
    if (AppStore.mechanicRecordTimeData) {
        updateMechanicDurationChart();
    }
}
window.updateChartsThemeColors = updateChartsThemeColors;

window.updateMechanicDurationChart = updateMechanicDurationChart;

function renderMechanicScoreboard(scoreboard) {
    const tbody = document.getElementById('mechanic-scoreboard-tbody');
    if (!tbody) return;
    if (scoreboard.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-4 text-center text-slate-500 font-semibold">No performance scores logged yet.</td></tr>`;
        return;
    }
    tbody.innerHTML = scoreboard.map((row, index) => {
        let rankBadge = '';
        if (index === 0) rankBadge = '👑 <span class="text-amber-400 font-bold">1st</span>';
        else if (index === 1) rankBadge = '⭐ <span class="text-slate-300 font-bold">2nd</span>';
        else if (index === 2) rankBadge = '👍 <span class="text-amber-600 font-bold">3rd</span>';
        else rankBadge = `<span class="text-slate-500">${index + 1}</span>`;

        return `
                    <tr class="hover:bg-slate-900/40 transition">
                        <td class="p-3 text-center font-bold">${rankBadge}</td>
                        <td class="p-3 font-semibold text-slate-100">${row.mechanic_name}</td>
                        <td class="p-3 text-center font-mono">${row.total_jobs}</td>
                        <td class="p-3 text-center font-mono">${row.total_parts_used}</td>
                        <td class="p-3 text-center font-mono">${row.avg_duration_minutes}m</td>
                        <td class="p-3 text-right pr-6 font-bold text-amber-400 font-mono">${row.score}</td>
                    </tr>
                `;
    }).join('');
}

async function fetchGlobalInventory() {
    const bSelect = document.getElementById('super-admin-branch-filter');
    const bId = bSelect ? bSelect.value : '';
    const payload = bId ? { branch_id: bId } : {};

    try {
        const data = await request('get_inventory', payload, 'GET');
        AppStore.inventory = data;
        filterGlobalInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

let savedInvCols = null;
try {
    const raw = localStorage.getItem('inv_cols_vis');
    if (raw) savedInvCols = JSON.parse(raw);
} catch (e) { }

if (savedInvCols) {
    AppStore.invColumnVisibility = savedInvCols;
}

// Save draft inputs regularly
function saveMechanicDraft() {
    const draft = {
        vehicleId: document.getElementById('mech-selected-vehicle-id')?.value || null,
        repairCategory: document.getElementById('mech-repair-category')?.value || null,
        kmReached: document.getElementById('mech-km-reached')?.value || null,
        otherIssues: document.getElementById('mech-other-issues')?.value || null,
        commonIssues: Array.from(document.querySelectorAll('#mech-common-issues-container input[type=checkbox]:checked')).map(cb => cb.value),
        partsUsed: AppStore.partsUsedAccumulator || []
    };
    localStorage.setItem('mechanicDraft', JSON.stringify(draft));
}

function loadMechanicDraft() {
    const raw = localStorage.getItem('mechanicDraft');
    if (!raw) return;
    try {
        const draft = JSON.parse(raw);
        if (draft.vehicleId) document.getElementById('mech-selected-vehicle-id').value = draft.vehicleId;
        if (draft.repairCategory) document.getElementById('mech-repair-category').value = draft.repairCategory;
        if (draft.kmReached) document.getElementById('mech-km-reached').value = draft.kmReached;
        if (draft.otherIssues) document.getElementById('mech-other-issues').value = draft.otherIssues;
        if (draft.commonIssues && Array.isArray(draft.commonIssues)) {
            draft.commonIssues.forEach(val => {
                const cb = document.querySelector(`#mech-common-issues-container input[value="${val}"]`);
                if (cb) cb.checked = true;
            });
        }
        if (draft.partsUsed && Array.isArray(draft.partsUsed)) {
            AppStore.partsUsedAccumulator = draft.partsUsed;
            // Re‑render parts table if needed
            renderMechanicPartsAccumulator();
        }
    } catch (e) { console.error('Failed to load mechanic draft', e); }
}

// Hook draft saving to relevant input events
function attachMechanicDraftListeners() {
    const inputs = ['mech-repair-category', 'mech-km-reached', 'mech-other-issues'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', saveMechanicDraft);
    });
    const checks = document.querySelectorAll('#mech-common-issues-container input[type=checkbox]');
    checks.forEach(cb => cb.addEventListener('change', saveMechanicDraft));
}

document.addEventListener('DOMContentLoaded', () => {
    if (AppStore.user?.role === 'mechanic') {
        loadMechanicDraft();
        attachMechanicDraftListeners();
    }
});

function saveInvColumnVisibility() {
    try {
        localStorage.setItem('inv_cols_vis', JSON.stringify(AppStore.invColumnVisibility));
    } catch (e) { }
}

window.toggleInvColumnMenu = function (e) {
    if (e) e.stopPropagation();
    const menu = document.getElementById('inv-column-menu');
    if (menu) {
        menu.classList.toggle('hidden');
        syncInvColumnCheckboxes();
    }
};

window.toggleShopInvColumnMenu = function (e) {
    if (e) e.stopPropagation();
    const menu = document.getElementById('shop-admin-inv-column-menu');
    if (menu) {
        menu.classList.toggle('hidden');
        syncShopInvColumnCheckboxes();
    }
};

window.resetShopInvColumns = function () {
    AppStore.shopInvColumnVisibility = { sku: true, part_name: true, category: true, connected_service: true, available_qty: true, price: true, updated_at: true, actions: true };
    try { localStorage.setItem('shop_inv_cols_vis', JSON.stringify(AppStore.shopInvColumnVisibility)); } catch (e) { }
    syncShopInvColumnCheckboxes();
    if (typeof filterShopAdminInventory === 'function') filterShopAdminInventory();
};

function syncShopInvColumnCheckboxes() {
    const cols = AppStore.shopInvColumnVisibility || {};
    Object.keys(cols).forEach(k => {
        const chk = document.querySelector(`#shop-admin-inv-column-menu input[data-shop-col="${k}"]`);
        if (chk) chk.checked = !!cols[k];
    });
}

document.addEventListener('click', function (e) {
    const menus = [
        { menuId: 'inv-column-menu', btnId: 'btn-inv-column-toggle' },
        { menuId: 'shop-admin-inv-column-menu', btnId: 'btn-shop-inv-column-toggle' },
        { menuId: 'veh-column-menu', btnId: 'btn-veh-column-toggle' },
        { menuId: 'records-column-menu', btnId: 'btn-records-column-toggle' },
        { menuId: 'cust-column-menu', btnId: 'btn-cust-column-toggle' }
    ];
    menus.forEach(({ menuId, btnId }) => {
        const menu = document.getElementById(menuId);
        const btn = document.getElementById(btnId);
        if (menu && !menu.classList.contains('hidden')) {
            if (!menu.contains(e.target) && (!btn || !btn.contains(e.target))) {
                menu.classList.add('hidden');
            }
        }
    });
});

window.toggleInvColumn = function (key, isVisible) {
    AppStore.invColumnVisibility[key] = !!isVisible;
    saveInvColumnVisibility();
    filterGlobalInventory();
};

window.resetInvColumns = function () {
    AppStore.invColumnVisibility = { ...defaultInvCols };
    saveInvColumnVisibility();
    syncInvColumnCheckboxes();
    filterGlobalInventory();
};

function syncInvColumnCheckboxes() {
    const cols = AppStore.invColumnVisibility || defaultInvCols;
    Object.keys(cols).forEach(k => {
        const chk = document.querySelector(`#inv-column-menu input[data-col="${k}"]`);
        if (chk) chk.checked = !!cols[k];
    });
}

function renderGlobalInventoryHeader() {
    const theadTr = document.getElementById('super-admin-inventory-thead-tr');
    if (!theadTr) return;

    const cols = AppStore.invColumnVisibility || defaultInvCols;
    let html = '';
    if (hasPermission('edit_inventory')) {
        html += `<th class="p-4 text-center w-12"><input type="checkbox" id="inv-select-all-checkbox" onchange="toggleSelectAllInventory(this.checked)" class="w-4 h-4 accent-blue-500 rounded bg-slate-900 border-slate-700"></th>`;
    }

    if (cols.branch) {
        html += `<th onclick="toggleSortGlobalInventory('branch_name')" class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center">Branch <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.sku) {
        html += `<th onclick="toggleSortGlobalInventory('sku')" class="p-4 font-semibold uppercase tracking-wider text-xs w-44 cursor-pointer select-none hover:text-blue-400 transition text-center">SKU <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.part_name) {
        html += `<th onclick="toggleSortGlobalInventory('part_name')" class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center">Part Name <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.description) {
        html += `<th class="p-4 font-semibold uppercase tracking-wider text-xs text-center">Description</th>`;
    }
    if (cols.category) {
        html += `<th onclick="toggleSortGlobalInventory('category')" class="p-4 font-semibold uppercase tracking-wider text-xs w-36 cursor-pointer select-none hover:text-blue-400 transition text-center">Category <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.warranty_category) {
        html += `<th onclick="toggleSortGlobalInventory('warranty_category')" class="p-4 font-semibold uppercase tracking-wider text-xs w-44 cursor-pointer select-none hover:text-blue-400 transition text-center">Warranty Category <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.connected_service) {
        html += `<th class="p-4 font-semibold uppercase tracking-wider text-xs w-44 text-center">Connected Service</th>`;
    }
    if (cols.available_qty) {
        html += `<th onclick="toggleSortGlobalInventory('available_qty')" class="p-4 font-semibold uppercase tracking-wider text-xs w-36 cursor-pointer select-none hover:text-blue-400 transition text-center">Available Qty <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.price) {
        html += `<th onclick="toggleSortGlobalInventory('price')" class="p-4 font-semibold uppercase tracking-wider text-xs w-36 cursor-pointer select-none hover:text-blue-400 transition text-center">Price (IDR) <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.updated_at) {
        html += `<th onclick="toggleSortGlobalInventory('updated_at')" class="p-4 font-semibold uppercase tracking-wider text-xs w-48 cursor-pointer select-none hover:text-blue-400 transition text-center">Last Updated <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i></th>`;
    }
    if (cols.actions && (hasPermission('edit_inventory') || hasPermission('delete_inventory'))) {
        html += `<th class="p-4 font-semibold uppercase tracking-wider text-xs w-40 text-center">Actions</th>`;
    }

    theadTr.innerHTML = html;
}

if (!AppStore.selectedInventoryIds) {
    AppStore.selectedInventoryIds = new Set();
}

function updateSelectedInventoryCount() {
    const selectedSize = AppStore.selectedInventoryIds ? AppStore.selectedInventoryIds.size : 0;
    const countEl = document.getElementById('inv-selected-count');
    if (countEl) countEl.innerText = selectedSize;

    const countBtnEl = document.getElementById('inv-selected-count-btn');
    if (countBtnEl) countBtnEl.innerText = selectedSize;

    const toolbar = document.getElementById('inv-bulk-action-toolbar');
    if (toolbar) {
        if (selectedSize > 0) {
            toolbar.classList.remove('hidden');
        } else {
            toolbar.classList.add('hidden');
        }
    }
}

function toggleSelectInventoryItem(id, isChecked) {
    if (!AppStore.selectedInventoryIds) AppStore.selectedInventoryIds = new Set();
    if (isChecked) AppStore.selectedInventoryIds.add(id);
    else AppStore.selectedInventoryIds.delete(id);
    updateSelectedInventoryCount();
}

function toggleSelectAllInventory(isChecked) {
    if (!AppStore.selectedInventoryIds) AppStore.selectedInventoryIds = new Set();
    const checkboxes = document.querySelectorAll('.inv-row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = isChecked;
        const id = parseInt(cb.value);
        if (isChecked) AppStore.selectedInventoryIds.add(id);
        else AppStore.selectedInventoryIds.delete(id);
    });
    updateSelectedInventoryCount();
}

async function deleteSelectedInventoryItems() {
    const ids = Array.from(AppStore.selectedInventoryIds || []);
    if (ids.length === 0) {
        showToast('No inventory items selected. Use the checkboxes to select items.', 'warning');
        return;
    }

    if (!confirm(`Are you sure you want to bulk delete ${ids.length} selected inventory items?`)) return;

    try {
        const res = await request('bulk_delete_inventory', { ids });
        showToast(res.message, 'success');
        AppStore.selectedInventoryIds.clear();
        updateSelectedInventoryCount();
        await fetchGlobalInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function deleteAllCurrentBranchInventory() {
    const branchSelect = document.getElementById('super-admin-branch-filter');
    const branchId = branchSelect ? branchSelect.value : '';
    const selectedOptText = branchSelect && branchSelect.options[branchSelect.selectedIndex] ? branchSelect.options[branchSelect.selectedIndex].text : 'Selected Branch';

    if (!branchId) {
        showToast('Please select a specific branch in the branch filter first before clearing branch stock.', 'warning');
        return;
    }

    if (!confirm(`CRITICAL WARNING: Are you sure you want to DELETE ALL inventory items for '${selectedOptText}'? This action cannot be undone.`)) {
        return;
    }

    try {
        const res = await request('bulk_delete_inventory', { branch_id: branchId });
        showToast(res.message, 'success');
        if (AppStore.selectedInventoryIds) AppStore.selectedInventoryIds.clear();
        updateSelectedInventoryCount();
        await fetchGlobalInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

window.toggleSelectAllInventory = toggleSelectAllInventory;
window.toggleSelectInventoryItem = toggleSelectInventoryItem;
window.deleteSelectedInventoryItems = deleteSelectedInventoryItems;
window.deleteAllCurrentBranchInventory = deleteAllCurrentBranchInventory;

function filterGlobalInventory() {
    renderGlobalInventoryHeader();

    const searchVal = (document.getElementById('super-admin-inventory-search')?.value || '').toLowerCase().trim();
    const colField = document.getElementById('super-admin-inv-column-select')?.value || '';
    const colVal = (document.getElementById('super-admin-inv-column-val')?.value || '').toLowerCase().trim();
    const sortVal = document.getElementById('super-admin-inv-sort-select')?.value || 'updated_at-desc';
    const data = AppStore.inventory || [];

    let filtered = data.filter(item => {
        if (searchVal) {
            const sku = (item.sku || '').toLowerCase();
            const name = (item.part_name || '').toLowerCase();
            const desc = (item.description || '').toLowerCase();
            if (!sku.includes(searchVal) && !name.includes(searchVal) && !desc.includes(searchVal)) return false;
        }
        if (colField && colVal) {
            if (colField === 'available_qty') {
                const minQty = parseInt(colVal);
                if (!isNaN(minQty) && parseInt(item.available_qty) < minQty) return false;
            } else if (colField === 'price') {
                const minPrice = parseFloat(colVal);
                if (!isNaN(minPrice) && parseFloat(item.price) < minPrice) return false;
            } else {
                const targetVal = (item[colField] || '').toString().toLowerCase();
                if (!targetVal.includes(colVal)) return false;
            }
        }
        return true;
    });

    // Sorting logic
    const [sortField, sortOrder] = sortVal.split('-');
    filtered.sort((a, b) => {
        let valA, valB;
        if (sortField === 'branch_name') {
            const bA = Array.isArray(AppStore.branches) ? AppStore.branches.find(br => br.id == a.branch_id) : null;
            const bB = Array.isArray(AppStore.branches) ? AppStore.branches.find(br => br.id == b.branch_id) : null;
            valA = (bA ? bA.name : '').toLowerCase();
            valB = (bB ? bB.name : '').toLowerCase();
        } else if (sortField === 'available_qty' || sortField === 'price') {
            valA = parseFloat(a[sortField] || 0);
            valB = parseFloat(b[sortField] || 0);
        } else if (sortField === 'updated_at') {
            valA = new Date(a.updated_at || 0).getTime();
            valB = new Date(b.updated_at || 0).getTime();
        } else {
            valA = (a[sortField] || '').toString().toLowerCase();
            valB = (b[sortField] || '').toString().toLowerCase();
        }

        if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
        if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
        return 0;
    });

    // Render Pagination UI
    const totalCount = filtered.length;
    renderPaginationUI('globalInventory', totalCount, 'super-admin-inventory-pagination-top', 'super-admin-inventory-pagination-bottom');

    const pageSize = getPageSize('globalInventory');
    const pageState = AppStore.pagination['globalInventory'] || { page: 1 };
    const pageNum = pageState.page || 1;
    const sliced = filtered.slice((pageNum - 1) * pageSize, pageNum * pageSize);

    const cols = AppStore.invColumnVisibility || defaultInvCols;
    const visibleColCount = Object.values(cols).filter(Boolean).length || 1;

    const tbody = document.getElementById('super-admin-inventory-tbody');
    if (sliced.length === 0) {
        tbody.innerHTML = `
                    <tr>
                        <td colspan="${visibleColCount}" class="p-6 text-center text-slate-500 text-xs">No records matching search query.</td>
                    </tr>
                `;
        return;
    }

    tbody.innerHTML = sliced.map(item => {
        const branch = Array.isArray(AppStore.branches) ? AppStore.branches.find(b => b.id == item.branch_id) : null;
        const isChecked = AppStore.selectedInventoryIds && AppStore.selectedInventoryIds.has(item.id) ? 'checked' : '';

        let tds = '';
        if (hasPermission('edit_inventory')) {
            tds += `<td class="p-4 text-center w-12"><input type="checkbox" value="${item.id}" ${isChecked} onchange="toggleSelectInventoryItem(${item.id}, this.checked)" class="inv-row-checkbox w-4 h-4 accent-blue-500 rounded bg-slate-900 border-slate-700"></td>`;
        }
        if (cols.branch) {
            tds += `<td class="p-4 text-xs font-bold text-slate-400 text-center truncate max-w-0" title="${branch ? escapeHtml(branch.name) : 'Unknown'}">${branch ? escapeHtml(branch.name) : 'Unknown'}</td>`;
        }
        if (cols.sku) {
            tds += `<td class="p-4 font-mono text-xs font-semibold text-slate-400 text-center truncate max-w-0" title="${escapeHtml(item.sku)}">${escapeHtml(item.sku)}</td>`;
        }
        if (cols.part_name) {
            tds += `<td class="p-4 font-medium text-slate-200 text-center truncate max-w-0" title="${escapeHtml(item.part_name)}">${escapeHtml(item.part_name)}</td>`;
        }
        if (cols.description) {
            tds += `<td class="p-4 text-xs text-slate-400 text-center truncate max-w-0" title="${item.description ? escapeHtml(item.description) : ''}">${item.description ? escapeHtml(item.description) : '<span class="text-slate-600 font-normal">-</span>'}</td>`;
        }
        if (cols.category) {
            tds += `<td class="p-4 text-center truncate max-w-0" title="${escapeHtml(item.category || 'General')}"><span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-400 font-bold text-[11px] border border-purple-500/20 inline-block truncate max-w-full">${escapeHtml(item.category || 'General')}</span></td>`;
        }
        if (cols.warranty_category) {
            const wCat = item.warranty_category || 'Unclaimable / No Warranty';
            let wBadgeClass = 'bg-slate-800 text-slate-400 border-slate-700';
            if (wCat === 'Warranty A') wBadgeClass = 'bg-purple-500/10 text-purple-400 border-purple-500/20';
            else if (wCat === 'Warranty B') wBadgeClass = 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20';
            else if (wCat === 'Warranty C') wBadgeClass = 'bg-blue-500/10 text-blue-400 border-blue-500/20';
            tds += `<td class="p-4 text-center truncate max-w-0" title="${escapeHtml(wCat)}"><span class="px-2 py-0.5 rounded font-bold text-[11px] border ${wBadgeClass} inline-block truncate max-w-full">${escapeHtml(wCat)}</span></td>`;
        }
        if (cols.connected_service) {
            tds += `<td class="p-4 text-center truncate max-w-0" title="${escapeHtml(item.connected_service || '')}">${item.connected_service ? `<span class="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-400 font-bold text-[11px] border border-cyan-500/20 inline-block truncate max-w-full">${escapeHtml(item.connected_service)}</span>` : '<span class="text-slate-600 text-[10px]">-</span>'}</td>`;
        }
        if (cols.available_qty) {
            tds += `<td class="p-4 text-center font-bold text-slate-200">${item.available_qty}</td>`;
        }
        if (cols.price) {
            tds += `<td class="p-4 text-center font-bold text-slate-200">${formatIDR(item.price)}</td>`;
        }
        if (cols.updated_at) {
            tds += `<td class="p-4 text-xs text-slate-400 text-center whitespace-nowrap">${formatIndonesianDate(item.updated_at, false)}</td>`;
        }
        const canEdit = hasPermission('edit_inventory');
        const canDelete = hasPermission('delete_inventory');
        if (cols.actions && (canEdit || canDelete)) {
            let actionBtns = '';
            if (canEdit) {
                actionBtns += `<button onclick="openShopAdminEditInventoryModal(${item.id})" class="px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded text-xs transition"><i class="fa-solid fa-edit mr-1"></i> Edit</button>`;
            }
            if (canDelete) {
                actionBtns += `<button onclick="deleteInventoryItem(${item.id})" class="px-2.5 py-1 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 border border-rose-500/20 rounded text-xs transition"><i class="fa-solid fa-trash-can mr-1"></i> Delete</button>`;
            }
            tds += `<td class="p-4 text-center whitespace-nowrap"><div class="flex items-center justify-center gap-1.5">${actionBtns}</div></td>`;
        }

        return `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-center">
                        ${tds}
                    </tr>
                `;
    }).join('');

    renderGlobalInventoryDashboard();
    updateSelectedInventoryCount();
}

function renderGlobalInventoryDashboard() {
    const lowList = document.getElementById('global-dash-low-stock-list');
    const availList = document.getElementById('global-dash-top-avail-list');
    const spentList = document.getElementById('global-dash-top-spent-list');
    if (!lowList && !availList && !spentList) return;

    const inventory = AppStore.inventory || [];
    const history = AppStore.globalSparePartsHistory || AppStore.sparePartsHistory || [];

    function filterByPeriod(records, periodVal, dateKey = 'used_at') {
        const now = new Date();
        const todayStr = now.toISOString().slice(0, 10);

        return records.filter(r => {
            const d = parseTimestampToDate(r[dateKey]);
            if (!d) return false;

            if (periodVal === 'daily') {
                return d.toISOString().slice(0, 10) === todayStr;
            } else if (periodVal === 'yearly') {
                return d.getFullYear() === now.getFullYear();
            } else {
                return d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth();
            }
        });
    }

    // 1. Low Stock Parts (< 5 units)
    const lowStockItems = inventory.filter(i => parseInt(i.available_qty) < 5)
        .sort((a, b) => parseInt(a.available_qty) - parseInt(b.available_qty));

    if (lowList) {
        if (lowStockItems.length === 0) {
            lowList.innerHTML = `<div class="p-3 text-center text-xs text-slate-500 font-semibold italic">Semua stok aman (&ge; 5 unit)</div>`;
        } else {
            lowList.innerHTML = lowStockItems.map(item => {
                const branch = Array.isArray(AppStore.branches) ? AppStore.branches.find(b => b.id == item.branch_id) : null;
                return `
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800 text-xs">
                        <div class="truncate max-w-[150px]">
                            <span class="font-bold text-slate-200 block truncate" title="${escapeHtml(item.part_name)}">${escapeHtml(item.part_name)}</span>
                            <span class="text-[10px] text-slate-400 font-mono block">${escapeHtml(item.sku)} ${branch ? `• ${escapeHtml(branch.name)}` : ''}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-400 font-bold font-mono border border-amber-500/20 text-xs shrink-0">${item.available_qty} unit</span>
                    </div>
                `;
            }).join('');
        }
    }

    // 2. Top 5 Most Available Stocks
    const topAvailItems = [...inventory].sort((a, b) => parseInt(b.available_qty) - parseInt(a.available_qty)).slice(0, 5);

    if (availList) {
        if (topAvailItems.length === 0) {
            availList.innerHTML = `<div class="p-3 text-center text-xs text-slate-500 font-semibold italic">Belum ada data stok tersedia</div>`;
        } else {
            availList.innerHTML = topAvailItems.map(item => {
                const branch = Array.isArray(AppStore.branches) ? AppStore.branches.find(b => b.id == item.branch_id) : null;
                return `
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800 text-xs">
                        <div class="truncate max-w-[150px]">
                            <span class="font-bold text-slate-200 block truncate" title="${escapeHtml(item.part_name)}">${escapeHtml(item.part_name)}</span>
                            <span class="text-[10px] text-slate-400 font-mono block">${escapeHtml(item.sku)} ${branch ? `• ${escapeHtml(branch.name)}` : ''}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-400 font-bold font-mono border border-emerald-500/20 text-xs shrink-0">${item.available_qty} unit</span>
                    </div>
                `;
            }).join('');
        }
    }

    // 3. Top 5 Most Spent / Used Spare Parts
    const spentPeriod = document.getElementById('global-dash-top-spent-period')?.value || 'monthly';
    const filteredHistory = filterByPeriod(history, spentPeriod);
    const spentMap = {};
    const skuMap = {};

    filteredHistory.forEach(r => {
        const qty = parseInt(r.quantity_used || 0);
        spentMap[r.part_name] = (spentMap[r.part_name] || 0) + qty;
        if (r.sku) skuMap[r.part_name] = r.sku;
    });

    const topSpentSorted = Object.entries(spentMap)
        .sort((a, b) => b[1] - a[1])
        .slice(0, 5);

    if (spentList) {
        if (topSpentSorted.length === 0) {
            spentList.innerHTML = `<div class="p-3 text-center text-xs text-slate-500 font-semibold italic">Tidak ada pemakaian di periode ini</div>`;
        } else {
            spentList.innerHTML = topSpentSorted.map(([pName, qty]) => `
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/80 border border-slate-800 text-xs">
                    <div class="truncate max-w-[150px]">
                        <span class="font-bold text-slate-200 block truncate" title="${escapeHtml(pName)}">${escapeHtml(pName)}</span>
                        <span class="text-[10px] text-slate-400 font-mono block">${escapeHtml(skuMap[pName] || 'N/A')}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-400 font-bold font-mono border border-cyan-500/20 text-xs shrink-0">x${qty} terpakai</span>
                </div>
            `).join('');
        }
    }
}
window.renderGlobalInventoryDashboard = renderGlobalInventoryDashboard;

async function deleteInventoryItem(invId) {
    const item = (AppStore.inventory || []).find(i => i.id == invId);
    const nameStr = item ? `'${item.part_name}' (${item.sku})` : 'this inventory item';

    if (!confirm(`Are you sure you want to delete ${nameStr}? This will permanently remove the spare part and all associated usage records.`)) {
        return;
    }

    try {
        const res = await request('delete_inventory_item', { id: invId });
        showToast(res.message, 'success');
        await fetchGlobalInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

window.deleteInventoryItem = deleteInventoryItem;

function toggleSortGlobalInventory(field) {
    const select = document.getElementById('super-admin-inv-sort-select');
    if (!select) return;
    const current = select.value;
    if (current.startsWith(field)) {
        select.value = current.endsWith('-asc') ? `${field}-desc` : `${field}-asc`;
    } else {
        select.value = `${field}-asc`;
    }
    filterGlobalInventory();
}

// -------------------------------------------------------------
// Global Inventory Master & Usage History Controllers
// -------------------------------------------------------------

async function setGlobalInventoryTab(tabName) {
    AppStore.globalInventoryTab = tabName;

    const panelStock = document.getElementById('global-inv-panel-stock');
    const panelHist = document.getElementById('global-inv-panel-history');
    const btnStock = document.getElementById('btn-global-tab-stock');
    const btnHist = document.getElementById('btn-global-tab-history');

    if (panelStock) panelStock.classList.add('hidden');
    if (panelHist) panelHist.classList.add('hidden');

    if (btnStock) btnStock.className = "px-5 py-2.5 font-bold text-xs rounded-xl transition duration-200 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60 flex items-center gap-2";
    if (btnHist) btnHist.className = "px-5 py-2.5 font-bold text-xs rounded-xl transition duration-200 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60 flex items-center gap-2";

    if (tabName === 'stock') {
        if (panelStock) panelStock.classList.remove('hidden');
        if (btnStock) btnStock.className = "px-5 py-2.5 font-bold text-xs rounded-xl transition duration-200 bg-blue-600/10 text-blue-400 border border-blue-500/20 shadow-md flex items-center gap-2";
        await fetchGlobalInventory();
    } else if (tabName === 'history') {
        if (panelHist) panelHist.classList.remove('hidden');
        if (btnHist) btnHist.className = "px-5 py-2.5 font-bold text-xs rounded-xl transition duration-200 bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 shadow-md flex items-center gap-2";
        await fetchGlobalSparePartsHistory();
    }
}

async function fetchGlobalSparePartsHistory() {
    try {
        if (!AppStore.branches || AppStore.branches.length === 0) {
            await fetchBranches();
        }

        const data = await request('get_spare_parts_history', {}, 'GET');
        AppStore.globalSparePartsHistory = data || [];

        // Populate Branch Filter
        const branchSelect = document.getElementById('global-parts-hist-branch-select');
        if (branchSelect) {
            const currentVal = branchSelect.value;
            let branchHtml = `<option value="">${t('table.all_branches', 'Semua Cabang')}</option>`;
            (AppStore.branches || []).forEach(b => {
                branchHtml += `<option value="${b.id}">${escapeHtml(b.name)}</option>`;
            });
            branchSelect.innerHTML = branchHtml;
            if (currentVal) branchSelect.value = currentVal;
        }

        // Populate Category Filter
        const catSelect = document.getElementById('global-parts-hist-category-select');
        if (catSelect) {
            const currentCat = catSelect.value;
            const categories = Array.from(new Set(AppStore.globalSparePartsHistory.map(r => r.category || 'General')));
            let catHtml = `<option value="">${t('table.all_categories', 'Semua Kategori')}</option>`;
            categories.forEach(cat => {
                catHtml += `<option value="${escapeHtml(cat)}">${escapeHtml(cat)}</option>`;
            });
            catSelect.innerHTML = catHtml;
            if (currentCat) catSelect.value = currentCat;
        }

        filterGlobalPartsHistory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function setGlobalPartsHistoryMode(mode) {
    AppStore.globalPartsHistoryMode = mode;

    const listCont = document.getElementById('global-parts-history-list-container');
    const monthlyCont = document.getElementById('global-parts-history-monthly-container');
    const btnList = document.getElementById('btn-global-parts-hist-mode-list');
    const btnMonthly = document.getElementById('btn-global-parts-hist-mode-monthly');

    if (listCont) listCont.classList.add('hidden');
    if (monthlyCont) monthlyCont.classList.add('hidden');

    if (btnList) btnList.className = "px-3.5 py-1.5 font-bold text-xs rounded-lg transition text-slate-400 hover:text-slate-200 hover:bg-slate-800 flex items-center gap-1.5";
    if (btnMonthly) btnMonthly.className = "px-3.5 py-1.5 font-bold text-xs rounded-lg transition text-slate-400 hover:text-slate-200 hover:bg-slate-800 flex items-center gap-1.5";

    if (mode === 'list') {
        if (listCont) listCont.classList.remove('hidden');
        if (btnList) btnList.className = "px-3.5 py-1.5 font-bold text-xs rounded-lg transition bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-1.5";
    } else {
        if (monthlyCont) monthlyCont.classList.remove('hidden');
        if (btnMonthly) btnMonthly.className = "px-3.5 py-1.5 font-bold text-xs rounded-lg transition bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-1.5";
    }

    filterGlobalPartsHistory();
}

function handleGlobalPartsHistoryPeriodChange() {
    const period = document.getElementById('global-parts-hist-period-select')?.value || 'all';
    const singleWrap = document.getElementById('global-parts-hist-date-single-wrap');
    const rangeWrap = document.getElementById('global-parts-hist-date-range-wrap');

    if (singleWrap) singleWrap.classList.add('hidden');
    if (rangeWrap) rangeWrap.classList.add('hidden');

    if (period === 'custom_date') {
        if (singleWrap) singleWrap.classList.remove('hidden');
        if (typeof initFlatpickrDateInputs === 'function') initFlatpickrDateInputs();
    } else if (period === 'custom_range') {
        if (rangeWrap) rangeWrap.classList.remove('hidden');
        if (typeof initFlatpickrDateInputs === 'function') initFlatpickrDateInputs();
    }

    filterGlobalPartsHistory();
}

function filterGlobalPartsHistory() {
    const searchVal = (document.getElementById('global-parts-hist-search')?.value || '').toLowerCase().trim();
    const branchVal = document.getElementById('global-parts-hist-branch-select')?.value || '';
    const period = document.getElementById('global-parts-hist-period-select')?.value || 'all';
    const categoryVal = document.getElementById('global-parts-hist-category-select')?.value || '';
    const sortVal = document.getElementById('global-parts-hist-sort-select')?.value || 'used_at-desc';
    const singleDate = document.getElementById('global-parts-hist-single-date')?.value || '';
    const rangeStart = document.getElementById('global-parts-hist-range-start')?.value || '';
    const rangeEnd = document.getElementById('global-parts-hist-range-end')?.value || '';

    const todayStr = new Date().toISOString().slice(0, 10);

    const yesterdayDate = new Date();
    yesterdayDate.setDate(yesterdayDate.getDate() - 1);
    const yesterdayStr = yesterdayDate.toISOString().slice(0, 10);

    // Week start (Monday)
    const now = new Date();
    const dayOfWeek = now.getDay() || 7;
    const mondayDate = new Date(now);
    mondayDate.setDate(now.getDate() - dayOfWeek + 1);
    mondayDate.setHours(0, 0, 0, 0);

    const history = AppStore.globalSparePartsHistory || [];

    function parseDdmmyyyyToIso(val) {
        if (!val) return '';
        if (val.includes('/')) {
            const p = val.split('/');
            if (p.length === 3) {
                return `${p[2].padStart(4, '20')}-${p[1].padStart(2, '0')}-${p[0].padStart(2, '0')}`;
            }
        }
        return val;
    }

    const singleDateIso = parseDdmmyyyyToIso(singleDate);
    const rangeStartIso = parseDdmmyyyyToIso(rangeStart);
    const rangeEndIso = parseDdmmyyyyToIso(rangeEnd);

    let filtered = history.filter(row => {
        const rowDate = parseTimestampToDate(row.used_at);
        const rowDateStr = rowDate ? rowDate.toISOString().slice(0, 10) : '';

        // Branch Filter
        if (branchVal && String(row.branch_id) !== String(branchVal)) {
            return false;
        }

        // Category Filter
        if (categoryVal && (row.category || 'General').toLowerCase() !== categoryVal.toLowerCase()) {
            return false;
        }

        // Search Filter
        if (searchVal) {
            const matchName = (row.part_name || '').toLowerCase().includes(searchVal);
            const matchSku = (row.sku || '').toLowerCase().includes(searchVal);
            const matchBranch = (row.branch_name || '').toLowerCase().includes(searchVal);
            const matchPlate = (row.license_plate || '').toLowerCase().includes(searchVal);
            const matchCust = (row.customer_name || '').toLowerCase().includes(searchVal);
            const matchMech = (row.mechanic_name || '').toLowerCase().includes(searchVal);
            const matchJob = (`JOB-${row.maintenance_record_id}`).toLowerCase().includes(searchVal);
            if (!matchName && !matchSku && !matchBranch && !matchPlate && !matchCust && !matchMech && !matchJob) {
                return false;
            }
        }

        // Period Filter
        if (period === 'today') {
            if (rowDateStr !== todayStr) return false;
        } else if (period === 'yesterday') {
            if (rowDateStr !== yesterdayStr) return false;
        } else if (period === 'this_week') {
            if (!rowDate || rowDate < mondayDate) return false;
        } else if (period === 'custom_date') {
            if (singleDateIso && rowDateStr !== singleDateIso) return false;
        } else if (period === 'custom_range') {
            if (rangeStartIso && rowDateStr < rangeStartIso) return false;
            if (rangeEndIso && rowDateStr > rangeEndIso) return false;
        }

        return true;
    });

    // Sorting
    const [sortField, sortOrder] = sortVal.split('-');
    filtered.sort((a, b) => {
        let valA, valB;
        if (sortField === 'total_billed') {
            valA = parseFloat(a.total_billed || (a.quantity_used * a.price_at_use));
            valB = parseFloat(b.total_billed || (b.quantity_used * b.price_at_use));
        } else if (sortField === 'quantity_used') {
            valA = parseInt(a.quantity_used || 0);
            valB = parseInt(b.quantity_used || 0);
        } else if (sortField === 'used_at') {
            valA = new Date(a.used_at || 0).getTime();
            valB = new Date(b.used_at || 0).getTime();
        } else {
            valA = (a[sortField] || '').toString().toLowerCase();
            valB = (b[sortField] || '').toString().toLowerCase();
        }

        if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
        if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
        return 0;
    });

    renderGlobalPartsHistoryListTable(filtered);
    renderGlobalPartsHistoryMonthlyView(filtered);
}

function renderGlobalPartsHistoryListTable(data) {
    const tbody = document.getElementById('global-parts-history-list-tbody');
    if (!tbody) return;

    if (data.length === 0) {
        tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="p-8 text-center text-slate-500 font-semibold">No global spare part usage records found for selected filters.</td>
                    </tr>
                `;
        return;
    }

    tbody.innerHTML = data.map(r => {
        const parsedDate = parseTimestampToDate(r.used_at);
        const dateStr = formatIndonesianDate(r.used_at);
        const totalCost = parseFloat(r.total_billed || (r.quantity_used * r.price_at_use));

        let bName = r.branch_name;
        if ((!bName || bName === 'N/A') && r.branch_id && Array.isArray(AppStore.branches)) {
            const foundB = AppStore.branches.find(b => b.id == r.branch_id);
            if (foundB) bName = foundB.name;
        }
        if (!bName) bName = 'N/A';

        return `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                        <td class="p-4 font-mono text-slate-400 text-center">${dateStr}</td>
                        <td class="p-4 text-center">
                            <button onclick="openRecordDetailsModal(${r.maintenance_record_id})" class="px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 font-bold rounded border border-blue-500/20 font-mono text-xs transition">
                                ${formatJobCode(r.maintenance_record_id, r.used_at)}
                            </button>
                        </td>
                        <td class="p-4 text-center font-bold text-slate-200">${escapeHtml(bName)}</td>
                        <td class="p-4 text-center">
                            <span class="font-bold text-slate-200 block text-center">${escapeHtml(r.part_name)}</span>
                            <div class="flex items-center justify-center gap-2 mt-0.5">
                                <span class="font-mono text-[10px] text-slate-400 font-semibold">${escapeHtml(r.sku || 'N/A')}</span>
                                <span class="px-1.5 py-0.2 rounded bg-purple-500/10 text-purple-400 font-bold text-[10px] border border-purple-500/20">${escapeHtml(r.category || 'General')}</span>
                            </div>
                        </td>
                        <td class="p-4 font-bold text-slate-100 text-center text-sm font-mono">x${r.quantity_used}</td>
                        <td class="p-4 font-mono text-slate-300 text-center">${formatIDR(r.price_at_use)}</td>
                        <td class="p-4 font-mono font-bold text-emerald-400 text-center text-sm">${formatIDR(totalCost)}</td>
                        <td class="p-4 text-center">
                            <span class="font-bold text-slate-200 block text-center">${escapeHtml(r.customer_name || 'N/A')}</span>
                            <span class="text-[10px] text-blue-400 font-mono block text-center">${escapeHtml(r.license_plate || 'N/A')}</span>
                        </td>
                        <td class="p-4 text-center text-xs font-semibold text-slate-300">${escapeHtml(r.mechanic_name || 'Unassigned')}</td>
                    </tr>
                `;
    }).join('');
}

function renderGlobalPartsHistoryMonthlyView(data) {
    const container = document.getElementById('global-parts-history-monthly-cards');
    if (!container) return;

    if (!data || data.length === 0) {
        container.innerHTML = `
            <div class="glass-panel p-8 text-center text-slate-500 font-semibold rounded-2xl">
                No monthly spare part usage data available.
            </div>
        `;
        return;
    }

    // Group by Month (YYYY-MM)
    const grouped = {};
    data.forEach(r => {
        const dateObj = parseTimestampToDate(r.used_at) || new Date();
        const monthKey = `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}`;
        if (!grouped[monthKey]) {
            grouped[monthKey] = [];
        }
        grouped[monthKey].push(r);
    });

    const sortedMonths = Object.keys(grouped).sort((a, b) => b.localeCompare(a));
    const totalMonths = sortedMonths.length;

    if (!AppStore.pagination) AppStore.pagination = {};
    if (!AppStore.pagination.globalPartsHistoryMonthlyPage || AppStore.pagination.globalPartsHistoryMonthlyPage < 1) {
        AppStore.pagination.globalPartsHistoryMonthlyPage = 1;
    }
    if (AppStore.pagination.globalPartsHistoryMonthlyPage > totalMonths) {
        AppStore.pagination.globalPartsHistoryMonthlyPage = totalMonths;
    }

    const pageIndex = AppStore.pagination.globalPartsHistoryMonthlyPage - 1;
    const currentMonthKey = sortedMonths[pageIndex];
    const rows = grouped[currentMonthKey] || [];

    const parts = currentMonthKey.split('-');
    const year = parseInt(parts[0]);
    const monthNum = parseInt(parts[1]);
    const lang = (typeof getAppLanguage === 'function' ? getAppLanguage() : 'id');
    const locale = lang === 'en' ? 'en-US' : 'id-ID';
    const monthName = new Date(year, monthNum - 1, 1).toLocaleString(locale, { month: 'long', year: 'numeric' });

    let monthTotalQty = 0;
    let monthTotalBilled = 0;
    const partUsageMap = {};

    rows.forEach(r => {
        const qty = parseInt(r.quantity_used);
        const cost = parseFloat(r.total_billed || (r.quantity_used * r.price_at_use));
        monthTotalQty += qty;
        monthTotalBilled += cost;

        partUsageMap[r.part_name] = (partUsageMap[r.part_name] || 0) + qty;
    });

    let topPartName = 'None';
    let topPartQty = 0;
    Object.entries(partUsageMap).forEach(([pName, count]) => {
        if (count > topPartQty) {
            topPartQty = count;
            topPartName = pName;
        }
    });

    const tableRows = rows.map(r => {
        const parsedDate = parseTimestampToDate(r.used_at);
        const dateOnlyStr = parsedDate ? parsedDate.toLocaleDateString(locale, { day: 'numeric', month: 'short', year: 'numeric' }) : (r.used_at ? r.used_at.split('T')[0] : 'N/A');
        const timeOnlyStr = parsedDate ? parsedDate.toLocaleTimeString(locale, { hour: '2-digit', minute: '2-digit', hour12: false }) : '';
        const totalCost = parseFloat(r.total_billed || (r.quantity_used * r.price_at_use));

        let bName = r.branch_name;
        if ((!bName || bName === 'N/A') && r.branch_id && Array.isArray(AppStore.branches)) {
            const foundB = AppStore.branches.find(b => b.id == r.branch_id);
            if (foundB) bName = foundB.name;
        }
        if (!bName) bName = 'N/A';

        return `
            <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                <td class="p-3 text-center whitespace-nowrap">
                    <span class="font-bold text-slate-200 block text-center font-sans">${dateOnlyStr}</span>
                    <span class="text-[10px] text-slate-400 font-mono block text-center">${timeOnlyStr} WIB</span>
                </td>
                <td class="p-3 text-center whitespace-nowrap">
                    <button onclick="openRecordDetailsModal(${r.maintenance_record_id})" class="px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 font-bold rounded border border-blue-500/20 font-mono text-xs">
                        ${formatJobCode(r.maintenance_record_id, r.used_at)}
                    </button>
                </td>
                <td class="p-3 text-center font-semibold text-slate-300 whitespace-nowrap">${escapeHtml(bName)}</td>
                <td class="p-3 text-center max-w-[220px] truncate">
                    <span class="font-bold text-slate-200 block text-center truncate" title="${escapeHtml(r.part_name)}">${escapeHtml(r.part_name)}</span>
                    <span class="text-[10px] text-slate-400 font-mono block text-center">SKU: ${escapeHtml(r.sku || 'N/A')}</span>
                </td>
                <td class="p-3 text-center font-bold font-mono text-slate-100">x${r.quantity_used}</td>
                <td class="p-3 text-center font-mono font-bold text-emerald-400 whitespace-nowrap">${formatIDR(totalCost)}</td>
                <td class="p-3 text-center text-xs">
                    <span class="font-bold text-slate-200 block text-center">${escapeHtml(r.customer_name || 'N/A')}</span>
                    <span class="text-[10px] text-blue-400 font-mono block text-center">${escapeHtml(r.license_plate || 'N/A')}</span>
                </td>
                <td class="p-3 text-center text-xs font-semibold text-slate-300">${escapeHtml(r.mechanic_name || 'Unassigned')}</td>
            </tr>
        `;
    }).join('');

    const monthOptions = sortedMonths.map((mK, i) => {
        const p = mK.split('-');
        const y = parseInt(p[0]);
        const m = parseInt(p[1]);
        const mLabel = new Date(y, m - 1, 1).toLocaleString(locale, { month: 'long', year: 'numeric' });
        const selected = (i + 1) === AppStore.pagination.globalPartsHistoryMonthlyPage ? 'selected' : '';
        return `<option value="${i + 1}" ${selected}>${mLabel} (${grouped[mK].length} records)</option>`;
    }).join('');

    const isFirstPage = AppStore.pagination.globalPartsHistoryMonthlyPage <= 1;
    const isLastPage = AppStore.pagination.globalPartsHistoryMonthlyPage >= totalMonths;

    container.innerHTML = `
        <div class="glass-panel rounded-2xl p-6 shadow-2xl space-y-6">
            <!-- Monthly Navigation & Summary Bar -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 border-b border-slate-800/80 pb-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xl shadow-inner">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg sm:text-xl font-bold text-slate-100">${monthName}</h3>
                            <span class="px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-[10px] font-mono font-bold">Month ${AppStore.pagination.globalPartsHistoryMonthlyPage} of ${totalMonths}</span>
                        </div>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">${rows.length} spare part usage transactions logged</p>
                    </div>
                </div>

                <!-- Monthly Pagination Controls -->
                <div class="flex items-center justify-between sm:justify-end gap-2 w-full lg:w-auto">
                    <button onclick="navigateGlobalMonthlyPartsHistory(-1)" ${isFirstPage ? 'disabled class="px-3 py-2 bg-slate-900/60 text-slate-600 border border-slate-800 rounded-xl text-xs font-bold cursor-not-allowed flex items-center gap-1.5"' : 'class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-cyan-400 border border-slate-800 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md cursor-pointer"'}>
                        <i class="fa-solid fa-chevron-left text-[10px]"></i> Newer Month
                    </button>

                    <select onchange="selectGlobalMonthlyPartsHistory(this.value)" class="px-3 py-2 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-bold max-w-[200px]">
                        ${monthOptions}
                    </select>

                    <button onclick="navigateGlobalMonthlyPartsHistory(1)" ${isLastPage ? 'disabled class="px-3 py-2 bg-slate-900/60 text-slate-600 border border-slate-800 rounded-xl text-xs font-bold cursor-not-allowed flex items-center gap-1.5"' : 'class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-cyan-400 border border-slate-800 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md cursor-pointer"'}>
                        Older Month <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-slate-900/70 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block tracking-wider">Total Quantity Taken</span>
                        <span class="text-base sm:text-lg font-mono font-bold text-cyan-400 mt-0.5 block">${monthTotalQty} units</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>

                <div class="bg-slate-900/70 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block tracking-wider">Total Billing Value</span>
                        <span class="text-base sm:text-lg font-mono font-bold text-emerald-400 mt-0.5 block">${formatIDR(monthTotalBilled)}</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>

                <div class="bg-slate-900/70 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block tracking-wider">Most Used Part</span>
                        <span class="text-xs font-bold text-amber-400 truncate block max-w-[160px] mt-0.5" title="${escapeHtml(topPartName)}">${escapeHtml(topPartName)}</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>
            </div>

            <!-- Month Usage Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full text-center text-xs border-collapse min-w-[950px]">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase text-[10px]">
                            <th class="p-3 text-center">Date & Time</th>
                            <th class="p-3 text-center">Job ID</th>
                            <th class="p-3 text-center">Branch</th>
                            <th class="p-3 text-center">Part Name & SKU</th>
                            <th class="p-3 text-center">Qty Used</th>
                            <th class="p-3 text-center">Subtotal Cost</th>
                            <th class="p-3 text-center">Customer / Vehicle</th>
                            <th class="p-3 text-center">Mechanic</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        </div>
    `;
}

function navigateGlobalMonthlyPartsHistory(direction) {
    if (!AppStore.pagination) AppStore.pagination = {};
    AppStore.pagination.globalPartsHistoryMonthlyPage = (AppStore.pagination.globalPartsHistoryMonthlyPage || 1) + direction;
    filterGlobalPartsHistory();
}
window.navigateGlobalMonthlyPartsHistory = navigateGlobalMonthlyPartsHistory;

function selectGlobalMonthlyPartsHistory(pageVal) {
    if (!AppStore.pagination) AppStore.pagination = {};
    AppStore.pagination.globalPartsHistoryMonthlyPage = parseInt(pageVal) || 1;
    filterGlobalPartsHistory();
}
window.selectGlobalMonthlyPartsHistory = selectGlobalMonthlyPartsHistory;

window.setGlobalInventoryTab = setGlobalInventoryTab;
window.fetchGlobalSparePartsHistory = fetchGlobalSparePartsHistory;
window.setGlobalPartsHistoryMode = setGlobalPartsHistoryMode;
window.handleGlobalPartsHistoryPeriodChange = handleGlobalPartsHistoryPeriodChange;
window.filterGlobalPartsHistory = filterGlobalPartsHistory;

async function fetchAuditLogs() {
    try {
        const logs = await request('get_audit_logs', {}, 'GET');
        AppStore.auditLogs = logs;

        const totalCount = logs.length;
        renderPaginationUI('auditLogs', totalCount, 'super-admin-logs-pagination-top', 'super-admin-logs-pagination-bottom');

        const pageSize = getPageSize('auditLogs');
        const pageState = AppStore.pagination['auditLogs'] || { page: 1 };
        const pageNum = pageState.page || 1;
        const sliced = logs.slice((pageNum - 1) * pageSize, pageNum * pageSize);

        const tbody = document.getElementById('super-admin-logs-tbody');
        if (sliced.length === 0) {
            tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500 text-xs">No audit logs written.</td>
                        </tr>
                    `;
            return;
        }

        tbody.innerHTML = sliced.map(log => {
            // Check if stock change triggers warning flag (dropped stock without associated repair record)
            // In real production we query job_id connections, here we parse old/new json states.
            let suspicious = false;
            let isDeduction = false;

            try {
                const oldVal = JSON.parse(log.old_value);
                const newVal = JSON.parse(log.new_value);
                if (oldVal && newVal && oldVal.available_qty !== undefined && newVal.available_qty !== undefined) {
                    if (newVal.available_qty < oldVal.available_qty) {
                        isDeduction = true;
                    }
                }
            } catch (e) { }

            // Highlight if stock is adjusted downwards but user role was admin directly, not mechanic action
            // (Mock rule: direct updates to inventory table raise suspicion)
            if (isDeduction && log.action_type === 'UPDATE' && log.target_table === 'inventory') {
                // Normally deductions are made during job stop_timer which logs it, but manual database adjustments might lack Job records
                suspicious = true;
            }

            const rowClass = suspicious ? "bg-amber-950/20 text-amber-300 border-l-4 border-amber-500" : "hover:bg-slate-900/30";
            const formattedDate = formatIndonesianDate(log.timestamp);
            const userStr = escapeHtml(log.username || 'System');
            const badgeClass = log.action_type === 'CREATE' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                log.action_type === 'UPDATE' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' :
                log.action_type === 'UPLOAD' ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' :
                'bg-rose-500/10 text-rose-400 border border-rose-500/20';

            const moduleLoc = log.module_location ? log.module_location : (
                log.target_table === 'inventory' ? 'Inventory Master' :
                log.target_table === 'users' ? 'Data Management > Users' :
                log.target_table === 'customers' ? 'Customers Directory' :
                log.target_table === 'vehicles' ? 'Vehicles Directory' :
                log.target_table === 'maintenance_records' ? 'Maintenance Records' :
                log.target_table
            );

            function formatAuditState(val) {
                if (!val) return 'None';
                if (typeof val === 'string') {
                    if (val.trim().startsWith('{') || val.trim().startsWith('[')) {
                        try { val = JSON.parse(val); } catch (e) { return val; }
                    } else {
                        return val;
                    }
                }
                if (typeof val === 'object') {
                    const keys = Object.keys(val).filter(k => k !== 'updated_at' && k !== 'created_at' && k !== 'password_hash');
                    if (keys.length === 0) return JSON.stringify(val);
                    return keys.map(k => `${k}: ${val[k] !== null && val[k] !== undefined ? val[k] : 'null'}`).join(', ');
                }
                return String(val);
            }

            function getAuditDiffDetails(oldValObj, newValObj) {
                if (!oldValObj || !newValObj) return null;
                let o = oldValObj, n = newValObj;
                if (typeof o === 'string') { try { o = JSON.parse(o); } catch (e) {} }
                if (typeof n === 'string') { try { n = JSON.parse(n); } catch (e) {} }

                if (typeof o === 'object' && typeof n === 'object' && o && n) {
                    const oldChangedProps = [];
                    const newChangedProps = [];
                    const allKeys = Array.from(new Set([...Object.keys(o), ...Object.keys(n)])).filter(k => k !== 'updated_at' && k !== 'created_at' && k !== 'password_hash');
                    
                    allKeys.forEach(k => {
                        const valOld = o[k];
                        const valNew = n[k];
                        if (String(valOld) !== String(valNew)) {
                            oldChangedProps.push(`${k}: ${valOld !== undefined ? valOld : 'null'}`);
                            newChangedProps.push(`${k}: ${valNew !== undefined ? valNew : 'null'}`);
                        }
                    });
                    if (oldChangedProps.length > 0) {
                        return {
                            oldSummary: oldChangedProps.join(', '),
                            newSummary: newChangedProps.join(', ')
                        };
                    }
                }
                return null;
            }

            let oldStateFormatted = formatAuditState(log.old_value);
            let newStateFormatted = formatAuditState(log.new_value);
            const diffDetails = getAuditDiffDetails(log.old_value, log.new_value);

            if (log.action_type === 'UPDATE' && diffDetails) {
                oldStateFormatted = diffDetails.oldSummary;
                newStateFormatted = diffDetails.newSummary;
            }

            return `
                        <tr class="${rowClass} border-b border-slate-800/50">
                            <td class="p-4 font-sans text-[11px] text-slate-400 whitespace-nowrap">${formattedDate}</td>
                            <td class="p-4 font-sans font-semibold text-slate-200">${userStr}</td>
                            <td class="p-4 font-sans">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold ${badgeClass}">${log.action_type}</span>
                            </td>
                            <td class="p-4 text-cyan-400 font-semibold text-xs whitespace-nowrap"><i class="fa-solid fa-folder-tree text-[10px] mr-1 opacity-70"></i>${escapeHtml(moduleLoc)}</td>
                            <td class="p-4 text-slate-300 font-sans text-xs">${escapeHtml(log.target_table)}</td>
                            <td class="p-4 text-slate-300 font-bold font-sans text-xs">${log.record_id}</td>
                            <td class="p-4 max-w-xs truncate text-[11px] font-mono ${diffDetails ? 'text-amber-300 font-semibold' : 'text-slate-400'}" title="${escapeHtml(oldStateFormatted)}">${escapeHtml(oldStateFormatted)}</td>
                            <td class="p-4 max-w-xs truncate text-[11px] font-mono ${diffDetails ? 'text-cyan-300 font-semibold' : 'text-slate-300'}" title="${escapeHtml(newStateFormatted)}">${escapeHtml(newStateFormatted)}</td>
                        </tr>
                    `;
        }).join('');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function triggerBlockedLogEdit(logId) {
    try {
        // Attempt to update the log
        const res = await request('edit_audit_log', { log_id: logId });
        // If it resolves, it will alert. (Under standard logic it returns HTTP 400 with PDO Block exception)
        showToast('Success (Wait, this should be blocked!)', 'warning');
    } catch (err) {
        // Catches trigger signals successfully and displays dialog
        showToast(`COMPLIANCE BLOCK: ${err.message}`, 'error', 6000);
    }
}

window.openUserRegistrationModal = function () {
    const modal = document.getElementById('user-registration-modal');
    if (!modal) return;

    const u = document.getElementById('reg-username');
    const p = document.getElementById('reg-password');
    const r = document.getElementById('reg-role');
    const b = document.getElementById('reg-branch');
    const d = document.getElementById('reg-display-name');

    if (u) u.value = '';
    if (p) p.value = '';
    if (d) d.value = '';
    if (r) r.value = 'mechanic';
    if (b) b.value = '';

    if (b && Array.isArray(AppStore.branches)) {
        b.innerHTML = '<option value="">No Branch Association (Super Admin Only)</option>' +
            AppStore.branches.map(br => `<option value="${br.id}">${br.name}</option>`).join('');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.closeUserRegistrationModal = function () {
    const modal = document.getElementById('user-registration-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

async function handleUserRegistration(e) {
    e.preventDefault();
    const u = document.getElementById('reg-username').value;
    const d = document.getElementById('reg-display-name')?.value?.trim();
    const p = document.getElementById('reg-password').value;
    const r = document.getElementById('reg-role').value;
    const b = document.getElementById('reg-branch').value;

    try {
        const res = await request('register', {
            username: u,
            display_name: d || u,
            password: p,
            role: r,
            branch_id: b || null
        });
        showToast(res.message, 'success');

        document.getElementById('reg-username').value = '';
        if (document.getElementById('reg-display-name')) document.getElementById('reg-display-name').value = '';
        document.getElementById('reg-password').value = '';
        document.getElementById('reg-role').value = 'mechanic';
        document.getElementById('reg-branch').value = '';

        closeUserRegistrationModal();
        if (typeof fetchManagementUsers === 'function') {
            await fetchManagementUsers();
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function exportLogsCSV() {
    if (AppStore.auditLogs.length === 0) {
        showToast('No logs available to export.', 'warning');
        return;
    }

    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Timestamp,Authorized User,Action Type,Target Table,Record ID,Old State,New State\n";

    AppStore.auditLogs.forEach(log => {
        const row = [
            `"${log.timestamp}"`,
            `"${log.username || 'System'}"`,
            `"${log.action_type}"`,
            `"${log.target_table}"`,
            log.record_id,
            `"${(log.old_value || '').replace(/"/g, '""')}"`,
            `"${(log.new_value || '').replace(/"/g, '""')}"`
        ].join(",");
        csvContent += row + "\n";
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `sgpm_mechanic_compliance_audit_logs_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showToast('CSV Spreadsheet downloaded successfully.', 'success');
}

// -------------------------------------------------------------
// Super Admin Data Management CRUD Controllers
// -------------------------------------------------------------

async function setManagementSubTab(subTab) {
    const validSubTabs = ['branches', 'users', 'common-issues', 'form-items', 'categories', 'service-options', 'other-services'];
    if (!validSubTabs.includes(subTab)) {
        subTab = 'branches';
    }
    AppStore.managementActiveSubTab = subTab;

    validSubTabs.forEach(name => {
        const el = document.getElementById(`mgt-panel-${name}`);
        const btn = document.getElementById(`btn-mgt-${name}`);
        if (el) el.classList.add('hidden');
        if (btn) btn.className = "px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60 transition shrink-0 border border-transparent";
    });

    const targetEl = document.getElementById(`mgt-panel-${subTab}`);
    const targetBtn = document.getElementById(`btn-mgt-${subTab}`);
    if (targetEl) targetEl.classList.remove('hidden');
    if (targetBtn) targetBtn.className = "px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 bg-blue-600/10 text-blue-400 border border-blue-500/20 shadow-md transition shrink-0";

    if (subTab === 'branches') {
        await fetchManagementBranches();
    } else if (subTab === 'users') {
        await fetchManagementUsers();
    } else if (subTab === 'common-issues') {
        await fetchCommonIssuesMgt();
    } else if (subTab === 'form-items') {
        await fetchFormItemsMgt();
    } else if (subTab === 'categories') {
        await fetchManagementCategories();
    } else if (subTab === 'service-options') {
        await fetchManagementServiceOptions();
    } else if (subTab === 'other-services') {
        await fetchManagementOtherServices();
    }
}

// --- Service Options Management ---
async function fetchManagementServiceOptions() {
    const tbody = document.getElementById('mgt-service-options-tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-slate-500">Loading service options...</td></tr>`;
    try {
        const raw = await request('get_service_options', {}, 'GET');
        AppStore.serviceOptionsList = Array.isArray(raw) ? raw : (raw && Array.isArray(raw.data) ? raw.data : []);
        if (document.getElementById('badge-mgt-service-options-count')) {
            document.getElementById('badge-mgt-service-options-count').innerText = AppStore.serviceOptionsList.length;
        }
        renderManagementServiceOptionsTable();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function renderManagementServiceOptionsTable() {
    const tbody = document.getElementById('mgt-service-options-tbody');
    if (!tbody) return;
    if (!AppStore.serviceOptionsList || AppStore.serviceOptionsList.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-slate-500">No service options found. Click Add Service Option to create one.</td></tr>`;
        return;
    }

    tbody.innerHTML = AppStore.serviceOptionsList.map(s => `
                <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                    <td class="p-3 font-mono text-slate-500 text-center">${s.id}</td>
                    <td class="p-3 font-mono text-cyan-400 font-bold text-center">${escapeHtml(s.sku || 'N/A')}</td>
                    <td class="p-3 font-bold text-slate-200 text-center">${escapeHtml(s.name)}</td>
                    <td class="p-3 font-mono font-bold text-emerald-400 text-center">${formatIDR(s.fee)}</td>
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEditServiceOptionModal(${s.id}, '${escapeHtml(s.name)}', '${escapeHtml(s.sku || '')}', ${s.fee})" class="px-2.5 py-1 bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 font-bold rounded text-[11px] border border-cyan-500/20">Edit</button>
                            <button onclick="deleteServiceOption(${s.id})" class="px-2.5 py-1 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 font-bold rounded text-[11px] border border-rose-500/20">Delete</button>
                        </div>
                    </td>
                </tr>
            `).join('');
}

function openAddServiceOptionModal() {
    const html = `
                <form onsubmit="submitAddServiceOption(event)" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Service Name</label>
                        <input type="text" id="mgt-so-name" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-cyan-500 focus:outline-none text-slate-200" placeholder="e.g. Brake System Repair">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">SKU</label>
                            <input type="text" id="mgt-so-sku" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-cyan-500 focus:outline-none text-slate-200 font-mono" placeholder="e.g. JASA-009">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Fee (IDR)</label>
                            <input type="number" id="mgt-so-fee" required value="0" min="0" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-cyan-500 focus:outline-none text-slate-200 font-mono">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg shadow-lg shadow-cyan-600/20">Create Service Option</button>
                    </div>
                </form>
            `;
    openGlobalModal('Add New Service Option', html);
}

async function submitAddServiceOption(e) {
    e.preventDefault();
    const name = document.getElementById('mgt-so-name').value.trim();
    const sku = document.getElementById('mgt-so-sku').value.trim();
    const fee = parseFloat(document.getElementById('mgt-so-fee').value) || 0;
    try {
        const res = await request('add_service_option', { name, sku, fee });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementServiceOptions();
        await fetchDynamicChecklists();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openEditServiceOptionModal(id, name, sku, fee) {
    const html = `
                <form onsubmit="submitEditServiceOption(event, ${id})" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Service Name</label>
                        <input type="text" id="mgt-so-name-edit" required value="${escapeHtml(name)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-cyan-500 focus:outline-none text-slate-200">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">SKU</label>
                            <input type="text" id="mgt-so-sku-edit" value="${escapeHtml(sku)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-cyan-500 focus:outline-none text-slate-200 font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Fee (IDR)</label>
                            <input type="number" id="mgt-so-fee-edit" required value="${fee}" min="0" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-cyan-500 focus:outline-none text-slate-200 font-mono">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg shadow-lg shadow-cyan-600/20">Update Service Option</button>
                    </div>
                </form>
            `;
    openGlobalModal(`Edit Service Option #${id}`, html);
}

async function submitEditServiceOption(e, id) {
    e.preventDefault();
    const name = document.getElementById('mgt-so-name-edit').value.trim();
    const sku = document.getElementById('mgt-so-sku-edit').value.trim();
    const fee = parseFloat(document.getElementById('mgt-so-fee-edit').value) || 0;
    try {
        const res = await request('edit_service_option', { id, name, sku, fee });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementServiceOptions();
        await fetchDynamicChecklists();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function deleteServiceOption(id) {
    if (!confirm('Are you sure you want to delete this service option?')) return;
    try {
        const res = await request('delete_service_option', { id });
        showToast(res.message, 'success');
        await fetchManagementServiceOptions();
        await fetchDynamicChecklists();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// --- Other Services Management ---
async function fetchManagementOtherServices() {
    const tbody = document.getElementById('mgt-other-services-tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-500">Loading other services...</td></tr>`;
    try {
        const raw = await request('get_other_services', {}, 'GET');
        AppStore.otherServicesList = Array.isArray(raw) ? raw : (raw && Array.isArray(raw.data) ? raw.data : []);
        if (document.getElementById('badge-mgt-other-services-count')) {
            document.getElementById('badge-mgt-other-services-count').innerText = AppStore.otherServicesList.length;
        }
        renderManagementOtherServicesTable();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function renderManagementOtherServicesTable() {
    const tbody = document.getElementById('mgt-other-services-tbody');
    if (!tbody) return;
    if (!AppStore.otherServicesList || AppStore.otherServicesList.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-slate-500">No other services found. Click Add Other Service to create one.</td></tr>`;
        return;
    }

    tbody.innerHTML = AppStore.otherServicesList.map(o => `
                <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                    <td class="p-3 font-mono text-slate-500 text-center">${o.id}</td>
                    <td class="p-3 font-mono text-amber-400 font-bold text-center">${o.sku ? escapeHtml(o.sku) : '-'}</td>
                    <td class="p-3 font-bold text-slate-200 text-center">${escapeHtml(o.name)}</td>
                    <td class="p-3 font-mono font-bold text-amber-400 text-center">${formatIDR(o.fee)}</td>
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEditOtherServiceModal(${o.id}, '${escapeHtml(o.name)}', '${escapeHtml(o.sku || '')}', ${o.fee})" class="px-2.5 py-1 bg-amber-600/10 hover:bg-amber-600/20 text-amber-400 font-bold rounded text-[11px] border border-amber-500/20">Edit</button>
                            <button onclick="deleteOtherService(${o.id})" class="px-2.5 py-1 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 font-bold rounded text-[11px] border border-rose-500/20">Delete</button>
                        </div>
                    </td>
                </tr>
            `).join('');
}

function openAddOtherServiceModal() {
    const html = `
                <form onsubmit="submitAddOtherService(event)" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Service Name</label>
                        <input type="text" id="mgt-os-name" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-amber-500 focus:outline-none text-slate-200" placeholder="e.g. Towing / Pick Up Service">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Service SKU (Optional)</label>
                        <input type="text" id="mgt-os-sku" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-amber-500 focus:outline-none text-slate-200 font-mono" placeholder="e.g. SRV-OTH-001">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Fee (IDR)</label>
                        <input type="number" id="mgt-os-fee" required value="0" min="0" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-amber-500 focus:outline-none text-slate-200 font-mono">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg shadow-lg shadow-amber-600/20">Create Other Service</button>
                    </div>
                </form>
            `;
    openGlobalModal('Add New Other Service', html);
}

async function submitAddOtherService(e) {
    e.preventDefault();
    const name = document.getElementById('mgt-os-name').value.trim();
    const sku = document.getElementById('mgt-os-sku').value.trim();
    const fee = parseFloat(document.getElementById('mgt-os-fee').value) || 0;
    try {
        const res = await request('add_other_service', { name, sku, fee });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementOtherServices();
        await fetchDynamicChecklists();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openEditOtherServiceModal(id, name, sku, fee) {
    const html = `
                <form onsubmit="submitEditOtherService(event, ${id})" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Service Name</label>
                        <input type="text" id="mgt-os-name-edit" required value="${escapeHtml(name)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-amber-500 focus:outline-none text-slate-200">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Service SKU (Optional)</label>
                        <input type="text" id="mgt-os-sku-edit" value="${escapeHtml(sku || '')}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-amber-500 focus:outline-none text-slate-200 font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Fee (IDR)</label>
                        <input type="number" id="mgt-os-fee-edit" required value="${fee}" min="0" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-amber-500 focus:outline-none text-slate-200 font-mono">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg shadow-lg shadow-amber-600/20">Update Other Service</button>
                    </div>
                </form>
            `;
    openGlobalModal(`Edit Other Service #${id}`, html);
}

async function submitEditOtherService(e, id) {
    e.preventDefault();
    const name = document.getElementById('mgt-os-name-edit').value.trim();
    const sku = document.getElementById('mgt-os-sku-edit').value.trim();
    const fee = parseFloat(document.getElementById('mgt-os-fee-edit').value) || 0;
    try {
        const res = await request('edit_other_service', { id, name, sku, fee });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementOtherServices();
        await fetchDynamicChecklists();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function deleteOtherService(id) {
    if (!confirm('Are you sure you want to delete this other service?')) return;
    try {
        const res = await request('delete_other_service', { id });
        showToast(res.message, 'success');
        await fetchManagementOtherServices();
        await fetchDynamicChecklists();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

window.openAddServiceOptionModal = openAddServiceOptionModal;
window.openEditServiceOptionModal = openEditServiceOptionModal;
window.deleteServiceOption = deleteServiceOption;
window.openAddOtherServiceModal = openAddOtherServiceModal;
window.openEditOtherServiceModal = openEditOtherServiceModal;
window.deleteOtherService = deleteOtherService;

async function fetchManagementCategories() {
    const tbody = document.getElementById('mgt-categories-tbody');
    if (tbody) tbody.innerHTML = `<tr><td colspan="3" class="p-6 text-center text-slate-500">Loading categories...</td></tr>`;
    try {
        const raw = await request('get_sparepart_categories', {}, 'GET');
        AppStore.sparepartCategories = Array.isArray(raw) ? raw : (raw && Array.isArray(raw.data) ? raw.data : []);
        if (document.getElementById('badge-mgt-categories-count')) {
            document.getElementById('badge-mgt-categories-count').innerText = AppStore.sparepartCategories.length;
        }
        renderManagementCategoriesTable();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function renderManagementCategoriesTable() {
    const tbody = document.getElementById('mgt-categories-tbody');
    if (!tbody) return;
    if (!AppStore.sparepartCategories || AppStore.sparepartCategories.length === 0) {
        tbody.innerHTML = `<tr><td colspan="3" class="p-6 text-center text-slate-500">No categories found. Click Add Category to create one.</td></tr>`;
        return;
    }

    tbody.innerHTML = AppStore.sparepartCategories.map(c => `
                <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                    <td class="p-3 font-mono text-slate-500 text-center">${c.id}</td>
                    <td class="p-3 font-bold text-slate-200 text-center">${escapeHtml(c.name)}</td>
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <button onclick="openEditCategoryModal(${c.id}, '${escapeHtml(c.name)}')" class="px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 font-bold rounded text-[11px] border border-blue-500/20">Edit</button>
                            <button onclick="deleteCategory(${c.id})" class="px-2.5 py-1 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 font-bold rounded text-[11px] border border-rose-500/20">Delete</button>
                        </div>
                    </td>
                </tr>
            `).join('');
}

function openAddCategoryModal() {
    const html = `
                <form onsubmit="submitAddCategory(event)" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Category Name</label>
                        <input type="text" id="mgt-cat-name" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-purple-500 focus:outline-none text-slate-200" placeholder="e.g. Brake System">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-lg shadow-lg shadow-purple-600/20">Create Category</button>
                    </div>
                </form>
            `;
    openGlobalModal('Add New Sparepart Category', html);
}

async function submitAddCategory(e) {
    e.preventDefault();
    const name = document.getElementById('mgt-cat-name').value.trim();
    try {
        const res = await request('add_sparepart_category', { name });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementCategories();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openEditCategoryModal(id, currentName) {
    const html = `
                <form onsubmit="submitEditCategory(event, ${id})" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Category Name</label>
                        <input type="text" id="mgt-cat-name-edit" required value="${escapeHtml(currentName)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-purple-500 focus:outline-none text-slate-200">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-lg shadow-lg shadow-purple-600/20">Update Category</button>
                    </div>
                </form>
            `;
    openGlobalModal(`Edit Category #${id}`, html);
}

async function submitEditCategory(e, id) {
    e.preventDefault();
    const name = document.getElementById('mgt-cat-name-edit').value.trim();
    try {
        const res = await request('edit_sparepart_category', { category_id: id, name });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementCategories();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category?')) return;
    try {
        const res = await request('delete_sparepart_category', { category_id: id });
        showToast(res.message, 'success');
        await fetchManagementCategories();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function fetchCommonIssuesMgt() {
    try {
        const raw = await request('get_common_issues', {}, 'GET');
        const data = Array.isArray(raw) ? raw : [];
        document.getElementById('badge-mgt-common-issues-count').innerText = data.length;
        const tbody = document.getElementById('mgt-common-issues-tbody');
        if (tbody) {
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="p-4 text-center text-slate-500">No common issue diagnostics registered.</td></tr>`;
                return;
            }
            tbody.innerHTML = data.map(item => `
                        <tr class="hover:bg-slate-900/40 transition border-b border-slate-800/40 text-slate-300 text-center">
                            <td class="p-3 font-mono text-center">${item.id}</td>
                            <td class="p-3 font-semibold text-slate-100 text-center">${item.name}</td>
                            <td class="p-3 text-center flex items-center justify-center gap-2">
                                <button onclick="openEditCommonIssueModal(${item.id}, '${item.name.replace(/'/g, "\\'")}')" class="px-2 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 font-bold rounded text-[10px] border border-blue-500/10 transition">Rename</button>
                                <button onclick="handleDeleteCommonIssue(${item.id})" class="px-2 py-1 bg-red-600/10 hover:bg-red-600/20 text-red-400 font-bold rounded text-[10px] border border-red-500/10 transition">Delete</button>
                            </td>
                        </tr>
                    `).join('');
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function fetchFormItemsMgt() {
    try {
        const raw = await request('get_mechanic_form_items', {}, 'GET');
        const data = Array.isArray(raw) ? raw : [];
        document.getElementById('badge-mgt-form-items-count').innerText = data.length;
        const tbody = document.getElementById('mgt-form-items-tbody');
        if (tbody) {
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="p-4 text-center text-slate-500">No mechanic checklist items registered.</td></tr>`;
                return;
            }
            tbody.innerHTML = data.map(item => `
                        <tr class="hover:bg-slate-900/40 transition border-b border-slate-800/40 text-slate-300 text-center">
                            <td class="p-3 font-mono text-center">${item.id}</td>
                            <td class="p-3 font-semibold text-slate-100 text-center">${item.label}</td>
                            <td class="p-3 text-center flex items-center justify-center gap-2">
                                <button onclick="openEditMechanicFormItemModal(${item.id}, '${item.label.replace(/'/g, "\\'")}')" class="px-2 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 font-bold rounded text-[10px] border border-blue-500/10 transition">Rename</button>
                                <button onclick="handleDeleteFormItem(${item.id})" class="px-2 py-1 bg-red-600/10 hover:bg-red-600/20 text-red-400 font-bold rounded text-[10px] border border-red-500/10 transition">Delete</button>
                            </td>
                        </tr>
                    `).join('');
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}

window.openAddCommonIssueModal = function () {
    const modal = document.getElementById('add-common-issue-modal');
    if (!modal) return;
    const input = document.getElementById('mgt-add-issue-name');
    if (input) input.value = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};
window.closeAddCommonIssueModal = function () {
    const modal = document.getElementById('add-common-issue-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};
window.handleSubmitAddCommonIssue = async function (e) {
    e.preventDefault();
    const name = document.getElementById('mgt-add-issue-name')?.value?.trim();
    try {
        const res = await request('add_common_issue', { name });
        showToast(res.message, 'success');
        closeAddCommonIssueModal();
        if (typeof fetchCommonIssuesMgt === 'function') await fetchCommonIssuesMgt();
    } catch (err) {
        showToast(err.message, 'error');
    }
};

window.openEditCommonIssueModal = function (id, name) {
    const modal = document.getElementById('edit-common-issue-modal');
    if (!modal) return;
    const idInput = document.getElementById('mgt-edit-issue-id');
    const nameInput = document.getElementById('mgt-edit-issue-name');
    if (idInput) idInput.value = id;
    if (nameInput) nameInput.value = name;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};
window.closeEditCommonIssueModal = function () {
    const modal = document.getElementById('edit-common-issue-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};
window.handleSubmitEditCommonIssue = async function (e) {
    e.preventDefault();
    const id = document.getElementById('mgt-edit-issue-id')?.value;
    const name = document.getElementById('mgt-edit-issue-name')?.value?.trim();
    try {
        const res = await request('edit_common_issue', { id, name });
        showToast(res.message, 'success');
        closeEditCommonIssueModal();
        if (typeof fetchCommonIssuesMgt === 'function') await fetchCommonIssuesMgt();
    } catch (err) {
        showToast(err.message, 'error');
    }
};

window.handleDeleteCommonIssue = async function (id) {
    if (!confirm('Are you sure you want to delete this common issue diagnostic?')) return;
    try {
        const res = await request('delete_common_issue', { id });
        showToast(res.message, 'success');
        if (typeof fetchCommonIssuesMgt === 'function') await fetchCommonIssuesMgt();
    } catch (err) {
        showToast(err.message, 'error');
    }
};

window.openAddMechanicFormItemModal = function () {
    const modal = document.getElementById('add-form-item-modal');
    if (!modal) return;
    const input = document.getElementById('mgt-add-item-label');
    if (input) input.value = '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};
window.closeAddMechanicFormItemModal = function () {
    const modal = document.getElementById('add-form-item-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};
window.handleSubmitAddFormItem = async function (e) {
    e.preventDefault();
    const label = document.getElementById('mgt-add-item-label')?.value?.trim();
    try {
        const res = await request('add_mechanic_form_item', { label });
        showToast(res.message, 'success');
        closeAddMechanicFormItemModal();
        if (typeof fetchFormItemsMgt === 'function') await fetchFormItemsMgt();
    } catch (err) {
        showToast(err.message, 'error');
    }
};

window.openEditMechanicFormItemModal = function (id, label) {
    const modal = document.getElementById('edit-form-item-modal');
    if (!modal) return;
    const idInput = document.getElementById('mgt-edit-item-id');
    const labelInput = document.getElementById('mgt-edit-item-label');
    if (idInput) idInput.value = id;
    if (labelInput) labelInput.value = label;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};
window.closeEditMechanicFormItemModal = function () {
    const modal = document.getElementById('edit-form-item-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};
window.handleSubmitEditFormItem = async function (e) {
    e.preventDefault();
    const id = document.getElementById('mgt-edit-item-id')?.value;
    const label = document.getElementById('mgt-edit-item-label')?.value?.trim();
    try {
        const res = await request('edit_mechanic_form_item', { id, label });
        showToast(res.message, 'success');
        closeEditMechanicFormItemModal();
        if (typeof fetchFormItemsMgt === 'function') await fetchFormItemsMgt();
    } catch (err) {
        showToast(err.message, 'error');
    }
};

window.handleDeleteFormItem = async function (id) {
    if (!confirm('Are you sure you want to delete this form item?')) return;
    try {
        const res = await request('delete_mechanic_form_item', { id });
        showToast(res.message, 'success');
        await fetchFormItemsMgt();
    } catch (err) {
        showToast(err.message, 'error');
    }
};

async function fetchManagementBranches() {
    try {
        const raw = await request('get_branches', {}, 'GET');
        const data = Array.isArray(raw) ? raw : [];
        AppStore.branches = data;

        const badge = document.getElementById('badge-mgt-branches-count');
        if (badge) badge.innerText = data.length;
        const tbody = document.getElementById('mgt-branches-tbody');
        if (!tbody) return;

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-slate-500">No branches registered.</td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(b => `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/40 text-center">
                        <td class="p-3 font-mono text-center">${b.id}</td>
                        <td class="p-3 font-bold text-slate-200 text-center font-sans">${escapeHtml(b.name)}</td>
                        <td class="p-3 text-center"><span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-slate-900 border border-slate-800 text-indigo-400 font-mono">${escapeHtml(b.abbreviation || 'N/A')}</span></td>
                        <td class="p-3 text-center">
                            <button onclick="openEditBranchModal(${b.id}, '${escapeHtml(b.name)}', '${escapeHtml(b.abbreviation || '')}')" class="px-2 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded font-bold transition">
                                Edit
                            </button>
                        </td>
                    </tr>
                `).join('');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function fetchManagementUsers() {
    try {
        const raw = await request('get_all_users', {}, 'GET');
        const data = Array.isArray(raw) ? raw : [];
        AppStore.managementUsers = data;

        const badge = document.getElementById('badge-mgt-users-count');
        if (badge) badge.innerText = data.length;
        const tbody = document.getElementById('mgt-users-tbody');
        if (!tbody) return;

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-slate-500">No users found.</td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(u => {
            const branchName = u.branch_name || u.branch?.name;
            const displayName = u.display_name || u.username;
            return `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/40 text-center">
                        <td class="p-3 font-mono text-center">${u.id}</td>
                        <td class="p-3 font-bold text-slate-200 text-center font-mono">${escapeHtml(u.username)}</td>
                        <td class="p-3 font-semibold text-blue-400 text-center font-sans">${escapeHtml(displayName)}</td>
                        <td class="p-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900 border border-slate-800">${u.role.toUpperCase()}</span></td>
                        <td class="p-3 text-slate-400 font-semibold text-center">${branchName ? escapeHtml(branchName) : '<span class="text-slate-600">None (Full Access)</span>'}</td>
                        <td class="p-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditUserModal(${u.id})" class="px-2 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded font-bold transition">
                                    Edit User
                                </button>
                                ${u.role !== 'super_admin' ? `
                                    <button onclick="deleteUserAccount(${u.id}, '${escapeHtml(u.username)}')" class="px-2 py-1 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 border border-rose-500/20 rounded font-bold transition" title="Delete User">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
        }).join('');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function fetchManagementInventory() {
    try {
        const raw = await request('get_inventory', {}, 'GET');
        const data = Array.isArray(raw) ? raw : [];
        AppStore.managementInventory = data;

        const badge = document.getElementById('badge-mgt-inventory-count');
        if (badge) badge.innerText = data.length;
        const tbody = document.getElementById('mgt-inventory-tbody');
        if (!tbody) return;

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="p-4 text-center text-slate-500">No inventory found.</td></tr>`;
            return;
        }

        const branchNameMap = {};
        if (AppStore.branches) AppStore.branches.forEach(b => { branchNameMap[b.id] = b.name; });

        tbody.innerHTML = data.map(item => `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-slate-300 text-center">
                        <td class="p-3 font-mono text-slate-500 text-center">${item.id}</td>
                        <td class="p-3 font-semibold text-slate-400 text-center">${escapeHtml(branchNameMap[item.branch_id] || 'Unknown Branch')}</td>
                        <td class="p-3 font-mono text-slate-300 font-bold text-center w-52">${escapeHtml(item.sku)}</td>
                        <td class="p-3 font-bold text-slate-200 text-center">${escapeHtml(item.part_name)}</td>
                        <td class="p-3 text-center"><span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-400 font-bold text-[11px] border border-purple-500/20">${escapeHtml(item.category || 'General')}</span></td>
                        <td class="p-3 text-center font-bold text-slate-200">${item.available_qty}</td>
                        <td class="p-3 text-center font-mono font-bold text-slate-200">${formatIDR(item.price)}</td>
                        <td class="p-3 text-center">
                            <button onclick="openEditInventoryItemModal(${item.id})" class="px-2 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded font-bold transition">
                                Edit
                            </button>
                        </td>
                    </tr>
                `).join('');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function fetchManagementCustomers() {
    try {
        const raw = await request('get_all_customers', {}, 'GET');
        const data = Array.isArray(raw) ? raw : [];
        AppStore.managementCustomers = data;

        const badge = document.getElementById('badge-mgt-customers-count');
        if (badge) badge.innerText = data.length;

        const tbody = document.getElementById('mgt-customers-tbody');
        if (!tbody) return;

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="p-4 text-center text-slate-500">No customers found.</td></tr>`;
            return;
        }

        tbody.innerHTML = data.map(c => `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/40 text-slate-300 text-center">
                        <td class="p-3 font-mono text-center">${c.id}</td>
                        <td class="p-3 font-bold text-slate-200 text-center">${escapeHtml(c.name)}</td>
                        <td class="p-3 font-mono text-slate-300 text-center">${escapeHtml(c.phone || 'N/A')}</td>
                        <td class="p-3 font-mono text-slate-400 text-center">${escapeHtml(c.id_card_number || 'N/A')}</td>
                        <td class="p-3 text-slate-400 truncate max-w-xs text-center">${escapeHtml(c.address || 'N/A')}</td>
                        <td class="p-3 text-center font-bold text-blue-400">${c.vehicles_count ?? c.vehicle_count ?? 0}</td>
                        <td class="p-3 text-center">
                            <button onclick="openEditCustomerModal('${c.id}')" class="px-2 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded font-bold transition">
                                Edit
                            </button>
                        </td>
                    </tr>
                `).join('');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function fetchManagementVehicles() {
    try {
        const data = await request('get_all_vehicles', {}, 'GET');
        const vehiclesList = Array.isArray(data?.vehicles) ? data.vehicles : [];
        AppStore.managementVehicles = vehiclesList;
        AppStore.managementCustomers = Array.isArray(data?.customers) ? data.customers : [];

        const badge = document.getElementById('badge-mgt-vehicles-count');
        if (badge) badge.innerText = vehiclesList.length;

        const tbody = document.getElementById('mgt-vehicles-tbody');
        if (!tbody) return;

        if (vehiclesList.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="p-4 text-center text-slate-500">No vehicles registered.</td></tr>`;
            return;
        }

        tbody.innerHTML = vehiclesList.map(v => `
                    <tr class="hover:bg-slate-900/30 border-b border-slate-800/60 text-center">
                        <td class="p-3 font-mono text-slate-400 text-center">${v.id}</td>
                        <td class="p-3 font-mono font-bold text-slate-200 text-center">${escapeHtml(v.license_plate)}</td>
                        <td class="p-3 text-slate-300 text-center">${escapeHtml(v.make)} ${escapeHtml(v.model)}</td>
                        <td class="p-3 text-slate-300 font-semibold text-center">${escapeHtml(v.color || 'N/A')}</td>
                        <td class="p-3 font-mono text-xs text-slate-400 text-center">${escapeHtml(v.vin || 'N/A')}</td>
                        <td class="p-3 font-mono text-xs text-slate-400 text-center">${escapeHtml(v.engine_number || 'N/A')}</td>
                        <td class="p-3 font-mono text-xs text-slate-400 text-center">${escapeHtml(v.controller_number || 'N/A')}</td>
                        <td class="p-3 font-semibold text-blue-400 text-center">${escapeHtml(v.customer?.name || v.customer_name || 'Unassigned')} (${escapeHtml(v.customer_id || v.id)})</td>
                        <td class="p-3 text-center">
                            <button onclick="openEditVehicleModal('${v.id}')" class="px-2.5 py-1 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded font-bold text-xs transition">
                                <i class="fa-solid fa-edit mr-1"></i> Edit
                            </button>
                        </td>
                    </tr>
                `).join('');
    } catch (err) {
        showToast(err.message, 'error');
    }
}



// --- Branch Modals ---
function openAddBranchModal() {
    const html = `
                <form onsubmit="submitAddBranch(event)" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Branch Name</label>
                        <input type="text" id="mgt-branch-name" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="e.g. Makassar">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Branch Abbreviation (e.g. MKS)</label>
                        <input type="text" id="mgt-branch-abbr" maxlength="10" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200 font-mono uppercase" placeholder="e.g. MKS">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-500/20">Add Branch</button>
                    </div>
                </form>
            `;
    openGlobalModal('Add New Branch Outlet', html);
}

async function submitAddBranch(e) {
    e.preventDefault();
    const name = document.getElementById('mgt-branch-name').value.trim();
    const abbreviation = document.getElementById('mgt-branch-abbr').value.trim().toUpperCase();
    try {
        const res = await request('add_branch', { name, abbreviation });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementBranches();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openEditBranchModal(id, currentName, currentAbbr = '') {
    const html = `
                <form onsubmit="submitEditBranch(event, ${id})" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Branch Name</label>
                        <input type="text" id="mgt-branch-name-edit" required value="${escapeHtml(currentName)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Branch Abbreviation (e.g. MKS)</label>
                        <input type="text" id="mgt-branch-abbr-edit" maxlength="10" value="${escapeHtml(currentAbbr)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200 font-mono uppercase" placeholder="e.g. MKS">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-500/20">Save Changes</button>
                    </div>
                </form>
            `;
    openGlobalModal(`Edit Branch (ID: ${id})`, html);
}

async function submitEditBranch(e, id) {
    e.preventDefault();
    const name = document.getElementById('mgt-branch-name-edit').value.trim();
    const abbreviation = document.getElementById('mgt-branch-abbr-edit').value.trim().toUpperCase();
    try {
        const res = await request('edit_branch', { branch_id: id, name, abbreviation });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementBranches();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// --- User Modals ---
function openEditUserModal(userId) {
    const u = (AppStore.managementUsers || []).find(item => item.id == userId);
    if (!u) return;

    const currentBranchId = u.branch_id ?? u.branch?.id;
    const branchOptions = (AppStore.branches || []).map(b => {
        const selected = b.id == currentBranchId ? 'selected' : '';
        return `<option value="${b.id}" ${selected}>${escapeHtml(b.name)}</option>`;
    }).join('');

    const html = `
                <form onsubmit="submitEditUser(event, ${userId})" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Username (Login Handle)</label>
                            <input type="text" id="mgt-user-username" required value="${escapeHtml(u.username)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="username">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Display Name</label>
                            <input type="text" id="mgt-user-display-name" required value="${escapeHtml(u.display_name || u.username)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="Display Name">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Role Type</label>
                            <select id="mgt-user-role" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                                <option value="mechanic" ${u.role === 'mechanic' ? 'selected' : ''}>Mechanic</option>
                                <option value="shop_admin" ${u.role === 'shop_admin' ? 'selected' : ''}>Shop Admin</option>
                                <option value="inventory_admin" ${u.role === 'inventory_admin' ? 'selected' : ''}>Inventory Admin</option>
                                <option value="manager" ${u.role === 'manager' ? 'selected' : ''}>Manager (Read-Only)</option>
                                <option value="super_admin" ${u.role === 'super_admin' ? 'selected' : ''}>Super Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Branch Association</label>
                            <select id="mgt-user-branch" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                                <option value="">No Branch Association (Super Admin Only)</option>
                                ${branchOptions}
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Change Password (Leave blank to keep current)</label>
                        <input type="password" id="mgt-user-password" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-900 mt-6">
                        <div>
                            ${u.role !== 'super_admin' ? `
                                <button type="button" onclick="deleteUserAccount(${userId}, '${escapeHtml(u.username)}')" class="px-3.5 py-2 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 border border-rose-500/20 hover:border-rose-500/40 font-bold rounded-lg transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-trash-can"></i> Delete User
                                </button>
                            ` : `
                                <span class="text-[10px] text-slate-500 font-semibold italic"><i class="fa-solid fa-shield-halved mr-1"></i>Super Admin accounts cannot be deleted</span>
                            `}
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-500/20">Save User Details</button>
                        </div>
                    </div>
                </form>
            `;
    openGlobalModal(`Edit User Account: ${u.username}`, html);
}
window.openEditUserModal = openEditUserModal;

async function submitEditUser(e, id) {
    e.preventDefault();
    const username = document.getElementById('mgt-user-username').value.trim();
    const displayName = document.getElementById('mgt-user-display-name').value.trim();
    const role = document.getElementById('mgt-user-role').value;
    const branchId = document.getElementById('mgt-user-branch').value;
    const password = document.getElementById('mgt-user-password').value;

    try {
        const res = await request('edit_user', {
            user_id: id,
            username,
            display_name: displayName,
            role,
            branch_id: branchId || null,
            password: password || null
        });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementUsers();
    } catch (err) {
        showToast(err.message, 'error');
    }
}
window.submitEditUser = submitEditUser;

function deleteUserAccount(id, username) {
    showConfirmDeleteModal({
        title: 'Delete User Account',
        message: `Are you sure you want to permanently delete the user account '${username}'? This action cannot be undone.`,
        btnText: 'Delete Account',
        onConfirm: async () => {
            try {
                const res = await request('delete_user', { user_id: id });
                showToast(res.message, 'success');
                closeGlobalModal();
                await fetchManagementUsers();
                if (typeof loadManagementSidebarCounts === 'function') {
                    await loadManagementSidebarCounts();
                }
            } catch (err) {
                showToast(err.message, 'error');
            }
        }
    });
}
window.deleteUserAccount = deleteUserAccount;


// --- Inventory Modals ---
function openAddInventoryItemModal() {
    const branchOptions = AppStore.branches.map(b => `<option value="${b.id}">${b.name}</option>`).join('');

    const html = `
                <form onsubmit="submitAddInventoryItem(event)" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Part SKU</label>
                            <input type="text" id="mgt-inv-sku" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="e.g. SP-BRK-09">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Target Branch Depot</label>
                            <select id="mgt-inv-branch" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                                <option value="">-- Select Branch --</option>
                                ${branchOptions}
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Sparepart Name</label>
                        <input type="text" id="mgt-inv-name" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="e.g. Front Disc Brake Pads">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Available Qty</label>
                            <input type="number" id="mgt-inv-qty" required value="0" min="0" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Unit Price (IDR)</label>
                            <input type="number" id="mgt-inv-price" required value="0" min="0" step="1" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-500/20">Add Sparepart</button>
                    </div>
                </form>
            `;
    openGlobalModal('Add New Sparepart Stock Record', html);
}

async function submitAddInventoryItem(e) {
    e.preventDefault();
    const sku = document.getElementById('mgt-inv-sku').value.trim();
    const branchId = document.getElementById('mgt-inv-branch').value;
    const partName = document.getElementById('mgt-inv-name').value.trim();
    const qty = document.getElementById('mgt-inv-qty').value;
    const price = document.getElementById('mgt-inv-price').value;

    try {
        const res = await request('add_inventory_item', {
            sku,
            branch_id: branchId,
            part_name: partName,
            available_qty: qty,
            price
        });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function openEditInventoryItemModal(itemId) {
    const item = (AppStore.managementInventory || []).find(item => item.id == itemId);
    if (!item) return;

    const branchOptions = AppStore.branches.map(b => {
        const selected = b.id == item.branch_id ? 'selected' : '';
        return `<option value="${b.id}" ${selected}>${b.name}</option>`;
    }).join('');

    const html = `
                <form onsubmit="submitEditInventoryItem(event, ${itemId})" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Part SKU</label>
                            <input type="text" id="mgt-inv-sku-edit" required value="${escapeHtml(item.sku)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Depot Branch Outlet</label>
                            <select id="mgt-inv-branch-edit" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                                ${branchOptions}
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Sparepart Name</label>
                        <input type="text" id="mgt-inv-name-edit" required value="${escapeHtml(item.part_name)}" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Available Qty</label>
                            <input type="number" id="mgt-inv-qty-edit" required value="${item.available_qty}" min="0" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Unit Price (IDR)</label>
                            <input type="number" id="mgt-inv-price-edit" required value="${item.price}" min="0" step="1" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-500/20">Save Inventory Item</button>
                    </div>
                </form>
            `;
    openGlobalModal(`Edit Inventory Ledger Record #${itemId}`, html);
}

async function submitEditInventoryItem(e, id) {
    e.preventDefault();
    const sku = document.getElementById('mgt-inv-sku-edit').value.trim();
    const branchId = document.getElementById('mgt-inv-branch-edit').value;
    const partName = document.getElementById('mgt-inv-name-edit').value.trim();
    const qty = document.getElementById('mgt-inv-qty-edit').value;
    const price = document.getElementById('mgt-inv-price-edit').value;

    try {
        const res = await request('edit_inventory_item', {
            id: id,
            inventory_id: id,
            sku,
            branch_id: branchId,
            part_name: partName,
            available_qty: qty,
            price
        });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementInventory();
    } catch (err) {
        showToast(err.message, 'error');
    }
}

// --- Customer Modals ---
function openAddCustomerModal() {
    const html = `
                <form onsubmit="submitAddCustomer(event)" class="space-y-4 text-xs font-semibold text-slate-300">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Customer ID (Optional - alphanumeric and dash)</label>
                        <input type="text" id="mgt-cust-id" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="e.g. CUST-101">
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Customer Full Name (Required)</label>
                        <input type="text" id="mgt-cust-name" required class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="e.g. John Doe">
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Phone Number</label>
                        <input type="text" id="mgt-cust-phone" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="e.g. 08123456789">
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">ID Card Number</label>
                        <input type="text" id="mgt-cust-idcard" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="e.g. 320123456789">
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Home Address</label>
                        <textarea id="mgt-cust-address" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-800 text-sm focus:border-blue-500 focus:outline-none text-slate-200" placeholder="Home Street Address..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900 mt-6">
                        <button type="button" onclick="closeGlobalModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 font-bold rounded-lg">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg shadow-blue-500/20">Add Customer</button>
                    </div>
                </form>
            `;
    openGlobalModal('Add Customer Profile', html);
}

async function submitAddCustomer(e) {
    e.preventDefault();
    const custId = document.getElementById('mgt-cust-id').value.trim();
    const name = document.getElementById('mgt-cust-name').value.trim();
    const phone = document.getElementById('mgt-cust-phone').value.trim();
    const idCard = document.getElementById('mgt-cust-idcard').value.trim();
    const address = document.getElementById('mgt-cust-address').value.trim();

    try {
        const res = await request('add_customer', {
            customer_id: custId || null,
            name,
            phone,
            id_card_number: idCard,
            address
        });
        showToast(res.message, 'success');
        closeGlobalModal();
        await fetchManagementCustomers();
    } catch (err) {
        showToast(err.message, 'error');
    }
}







// -------------------------------------------------------------
// Toast UI Message Notifications
// -------------------------------------------------------------

function showToast(message, type = 'success', duration = 4000) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');

    let bgClass = 'bg-slate-900 border-emerald-500/40 text-emerald-400';
    let icon = '<i class="fa-solid fa-circle-check"></i>';

    if (type === 'error') {
        bgClass = 'bg-slate-900 border-rose-500/40 text-rose-400';
        icon = '<i class="fa-solid fa-circle-exclamation"></i>';
    } else if (type === 'warning') {
        bgClass = 'bg-slate-900 border-amber-500/40 text-amber-400';
        icon = '<i class="fa-solid fa-circle-info"></i>';
    }

    toast.className = `flex items-center gap-3 px-4 py-3 rounded-lg border shadow-2xl transition duration-300 transform translate-x-20 opacity-0 ${bgClass}`;
    toast.innerHTML = `
                <div class="text-lg">${icon}</div>
                <div class="text-xs font-semibold tracking-wide">${message}</div>
            `;

    container.appendChild(toast);

    // Trigger animation in next frame
    setTimeout(() => {
        toast.classList.remove('translate-x-20', 'opacity-0');
    }, 10);

    // Remove after delay
    setTimeout(() => {
        toast.classList.add('translate-x-20', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, duration);
}



window.openAddCategoryModal = openAddCategoryModal;
window.submitAddCategory = submitAddCategory;
window.openEditCategoryModal = openEditCategoryModal;
window.submitEditCategory = submitEditCategory;
window.deleteCategory = deleteCategory;
window.fetchManagementCategories = fetchManagementCategories;
window.fetchManagementServiceOptions = fetchManagementServiceOptions;
window.openAddServiceOptionModal = openAddServiceOptionModal;
window.submitAddServiceOption = submitAddServiceOption;
window.openEditServiceOptionModal = openEditServiceOptionModal;
window.submitEditServiceOption = submitEditServiceOption;
window.deleteServiceOption = deleteServiceOption;
window.fetchManagementOtherServices = fetchManagementOtherServices;
window.openAddOtherServiceModal = openAddOtherServiceModal;
window.submitAddOtherService = submitAddOtherService;
window.openEditOtherServiceModal = openEditOtherServiceModal;
window.submitEditOtherService = submitEditOtherService;
window.deleteOtherService = deleteOtherService;

// --- Reports Module ---
async function fetchReportSummary() {
    try {
        const branchSelect = document.getElementById('report-branch-select');
        const startDate = document.getElementById('report-start-date')?.value || '';
        const endDate = document.getElementById('report-end-date')?.value || '';
        const selectedBranch = branchSelect ? branchSelect.value : 'all';

        // Ensure branches are loaded and populate dropdown if empty
        if (!AppStore.branches || AppStore.branches.length === 0) {
            await fetchBranches();
        }
        if (branchSelect && branchSelect.options.length <= 1 && AppStore.branches) {
            AppStore.branches.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.innerText = b.name;
                branchSelect.appendChild(opt);
            });
            branchSelect.value = selectedBranch;
        }

        const data = await request('get_reports_summary', {
            branch_id: selectedBranch,
            start_date: startDate,
            end_date: endDate
        }, 'GET');

        if (!data) return;
        AppStore.reportData = data;

        // Render Vehicle Fleet & Rental Status Cards Widget
        renderFleetWidgetCards();
        const sum = data.summary || {};
        if (document.getElementById('report-kpi-revenue')) {
            document.getElementById('report-kpi-revenue').innerText = formatIDR(sum.grand_total_revenue || 0);
        }
        if (document.getElementById('report-kpi-labor')) {
            document.getElementById('report-kpi-labor').innerText = formatIDR(sum.total_labor_fee || 0);
        }
        if (document.getElementById('report-kpi-parts')) {
            document.getElementById('report-kpi-parts').innerText = formatIDR(sum.total_parts_billed || 0);
        }
        if (document.getElementById('report-kpi-jobs')) {
            document.getElementById('report-kpi-jobs').innerText = sum.total_jobs || 0;
        }
        if (document.getElementById('report-kpi-completed')) {
            document.getElementById('report-kpi-completed').innerText = sum.completed_jobs || 0;
        }
        if (document.getElementById('report-kpi-inprogress')) {
            document.getElementById('report-kpi-inprogress').innerText = sum.in_progress_jobs || 0;
        }
        if (document.getElementById('report-kpi-parts-qty')) {
            document.getElementById('report-kpi-parts-qty').innerText = (sum.total_parts_qty_used || 0) + ' Unit';
        }
        if (document.getElementById('report-kpi-rate')) {
            const rate = sum.total_jobs > 0 ? Math.round((sum.completed_jobs / sum.total_jobs) * 100) : 0;
            document.getElementById('report-kpi-rate').innerText = rate + '%';
        }

        // Render Branch Comparison Table (Always visible, highlight selected branch)
        const tbody = document.getElementById('report-branch-table-tbody');
        if (tbody) {
            const bList = data.branch_summary || [];
            if (bList.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="p-4 text-center text-slate-500">No branch data available.</td></tr>`;
            } else {
                tbody.innerHTML = bList.map(b => {
                    const isSelected = selectedBranch !== 'all' && (b.branch_id == selectedBranch);
                    const rowClass = isSelected
                        ? 'bg-blue-950/60 border-l-4 border-l-blue-500 border-b border-slate-800 text-center font-bold ring-1 ring-blue-500/40'
                        : 'hover:bg-slate-900/40 border-b border-slate-800/40 text-center';
                    return `
                    <tr class="${rowClass}">
                        <td class="p-3 font-mono text-center text-slate-500">${b.branch_id}</td>
                        <td class="p-3 font-bold ${isSelected ? 'text-blue-300' : 'text-slate-200'} text-center font-sans">
                            ${escapeHtml(b.branch_name)}
                            ${isSelected ? '<span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] bg-blue-500 text-white font-bold">SELECTED</span>' : ''}
                        </td>
                        <td class="p-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900 border border-slate-800 text-indigo-400 font-mono">${escapeHtml(b.abbreviation)}</span></td>
                        <td class="p-3 font-bold text-blue-400 text-center">${b.total_jobs}</td>
                        <td class="p-3 font-bold text-emerald-400 text-center">${b.completed_jobs}</td>
                        <td class="p-3 font-mono font-bold text-slate-300 text-center">${formatIDR(b.labor_fee)}</td>
                        <td class="p-3 font-mono font-bold text-slate-300 text-center">${formatIDR(b.parts_billed)}</td>
                        <td class="p-3 font-mono font-bold text-emerald-400 text-center">${formatIDR(b.total_revenue)}</td>
                    </tr>
                `;
                }).join('');
            }
        }

        // Render Vehicle Fleet Breakdown Table (Grouped by Status -> Models)
        const vThead = document.getElementById('report-vehicle-table-thead');
        const vTbody = document.getElementById('report-vehicle-table-tbody');
        const vModels = data.vehicle_models || [];
        const vList = data.vehicle_breakdown || [];

        if (vThead && vTbody) {
            // Grouped 2-Row Table Header
            let theadRow1 = `<tr class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase font-semibold text-[10px]">
                <th rowspan="2" class="p-3 text-left border-r border-slate-800">Nama Cabang</th>
                <th colspan="${vModels.length}" class="p-2 text-center border-r border-slate-800 bg-cyan-950/40 text-cyan-300 font-bold border-b border-cyan-800/50">RENTED VEHICLES (BOUND)</th>
                <th colspan="${vModels.length}" class="p-2 text-center border-r border-slate-800 bg-emerald-950/40 text-emerald-300 font-bold border-b border-emerald-800/50">READY TO RENT (UNBOUND)</th>
                <th colspan="${vModels.length}" class="p-2 text-center border-r border-slate-800 bg-amber-950/40 text-amber-300 font-bold border-b border-amber-800/50">UNDER REPAIR (IN PROGRESS)</th>
                <th colspan="${vModels.length}" class="p-2 text-center border-r border-slate-800 bg-purple-950/40 text-purple-300 font-bold border-b border-purple-800/50">TOTAL IN WAREHOUSE</th>
                <th rowspan="2" class="p-3 text-center border-r border-slate-800 text-cyan-400 font-bold bg-cyan-950/20">TOTAL RENTED</th>
                <th rowspan="2" class="p-3 text-center border-r border-slate-800 text-purple-400 font-bold bg-purple-950/20">TOTAL WAREHOUSE</th>
                <th rowspan="2" class="p-3 text-center text-blue-400 font-bold bg-blue-950/20">TOTAL FLEET</th>
            </tr>`;

            let theadRow2 = `<tr class="bg-slate-900/90 border-b border-slate-800 text-slate-400 uppercase font-semibold text-[9px]">`;
            
            // Sub-columns for Rented Models
            vModels.forEach(m => {
                theadRow2 += `<th class="p-2 text-center text-cyan-400 font-medium">${escapeHtml(m)}</th>`;
            });
            // Sub-columns for Ready to Rent Models
            vModels.forEach(m => {
                theadRow2 += `<th class="p-2 text-center text-emerald-400 font-medium border-l border-slate-800/60">${escapeHtml(m)}</th>`;
            });
            // Sub-columns for Under Repair Models
            vModels.forEach(m => {
                theadRow2 += `<th class="p-2 text-center text-amber-400 font-medium border-l border-slate-800/60">${escapeHtml(m)}</th>`;
            });
            // Sub-columns for Total in Warehouse Models
            vModels.forEach(m => {
                theadRow2 += `<th class="p-2 text-center text-purple-400 font-medium border-l border-slate-800/60">${escapeHtml(m)}</th>`;
            });
            theadRow2 += `</tr>`;

            vThead.innerHTML = theadRow1 + theadRow2;

            if (vList.length === 0) {
                vTbody.innerHTML = `<tr><td colspan="${(vModels.length * 4) + 4}" class="p-4 text-center text-slate-500">No vehicle data available.</td></tr>`;
            } else {
                let tbodyHtml = '';
                vList.forEach(b => {
                    const isSelected = selectedBranch !== 'all' && (b.branch_id == selectedBranch);
                    const rowClass = isSelected
                        ? 'bg-blue-950/60 border-l-4 border-l-blue-500 border-b border-slate-800 font-bold ring-1 ring-blue-500/40'
                        : 'hover:bg-slate-900/40 border-b border-slate-800/40';

                    tbodyHtml += `<tr class="${rowClass}">
                        <td class="p-3 text-left font-bold text-slate-200 border-r border-slate-800">
                            ${escapeHtml(b.branch_name)}
                            <span class="ml-1.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-900 border border-slate-800 text-indigo-400 font-mono">${escapeHtml(b.abbreviation)}</span>
                            ${isSelected ? '<span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] bg-blue-500 text-white font-bold">SELECTED</span>' : ''}
                        </td>`;

                    // 1. Rented models columns
                    b.models.forEach(m => {
                        tbodyHtml += `<td class="p-2.5 text-center font-mono font-bold text-cyan-400">${m.rented}</td>`;
                    });
                    // 2. Ready to Rent models columns
                    b.models.forEach(m => {
                        tbodyHtml += `<td class="p-2.5 text-center font-mono text-emerald-400 border-l border-slate-800/60">${m.ready_to_rent}</td>`;
                    });
                    // 3. Under Repair models columns
                    b.models.forEach(m => {
                        tbodyHtml += `<td class="p-2.5 text-center font-mono text-amber-400 border-l border-slate-800/60">${m.under_repair}</td>`;
                    });
                    // 4. Total in Warehouse models columns
                    b.models.forEach(m => {
                        tbodyHtml += `<td class="p-2.5 text-center font-mono font-bold text-purple-400 border-l border-slate-800/60">${m.total_warehouse}</td>`;
                    });

                    // Summary totals columns
                    tbodyHtml += `
                        <td class="p-3 text-center border-l border-slate-800 font-mono font-bold text-cyan-400 bg-cyan-950/20">${b.totals.rented}</td>
                        <td class="p-3 text-center border-l border-slate-800 font-mono font-bold text-purple-400 bg-purple-950/20">${b.totals.total_warehouse}</td>
                        <td class="p-3 text-center border-l border-slate-800 font-mono font-bold text-blue-400 bg-blue-950/20">${b.totals.total_fleet}</td>
                    </tr>`;
                });
                vTbody.innerHTML = tbodyHtml;
            }
        }
    } catch (err) {
        showToast(err.message, 'error');
    }
}
window.fetchReportSummary = fetchReportSummary;

function renderFleetWidgetCards() {
    const grid = document.getElementById('report-fleet-widget-grid');
    if (!grid) return;

    const data = AppStore.reportData || {};
    const vModels = data.vehicle_models || ['DeSultan', 'ZUZU 72V', 'ZUZU', 'TEMPUR'];
    const vList = data.vehicle_breakdown || [];
    const filterSelect = document.getElementById('report-fleet-widget-status-filter');
    const selectedFilter = filterSelect ? filterSelect.value : 'all_fleet';

    const branchSelect = document.getElementById('report-branch-select');
    const selectedBranch = branchSelect ? branchSelect.value : 'all';

    // Update Subtitle indicator
    const subtitleEl = document.getElementById('report-fleet-widget-subtitle');
    if (subtitleEl) {
        let branchLabel = t('fleet_widget.all_branches', 'All Branches');
        if (selectedBranch !== 'all' && Array.isArray(AppStore.branches)) {
            const bObj = AppStore.branches.find(b => b.id == selectedBranch);
            if (bObj) branchLabel = bObj.name;
        }
        const subPrefix = t('fleet_widget.subtitle_prefix', 'Real-time aggregate count per model');
        subtitleEl.innerText = `${subPrefix} (${branchLabel}).`;
    }

    // Calculate aggregated counts per model across branches (or filtered by selected branch)
    const modelSums = {};
    vModels.forEach(m => {
        modelSums[m] = 0;
    });

    vList.forEach(b => {
        if (selectedBranch !== 'all' && b.branch_id != selectedBranch) {
            return;
        }

        if (Array.isArray(b.models)) {
            b.models.forEach(mObj => {
                const mName = mObj.model;
                if (modelSums.hasOwnProperty(mName)) {
                    if (selectedFilter === 'rented') {
                        modelSums[mName] += (mObj.rented || 0);
                    } else if (selectedFilter === 'ready_to_rent') {
                        modelSums[mName] += (mObj.ready_to_rent || 0);
                    } else if (selectedFilter === 'under_repair') {
                        modelSums[mName] += (mObj.under_repair || 0);
                    } else { // all_fleet / default
                        modelSums[mName] += (mObj.total_fleet || 0);
                    }
                }
            });
        }
    });

    // Theme & styling color maps per model (High-contrast text & badges)
    const modelStyles = {
        'DeSultan': { icon: 'fa-crown', border: 'border-purple-500/40 dark:border-purple-500/40', bg: 'bg-purple-500/15 dark:bg-purple-500/20', text: 'text-purple-600 dark:text-purple-300', badgeBg: 'bg-purple-100 dark:bg-purple-950/80', badgeText: 'text-purple-800 dark:text-purple-300' },
        'ZUZU 72V': { icon: 'fa-bolt', border: 'border-cyan-500/40 dark:border-cyan-500/40', bg: 'bg-cyan-500/15 dark:bg-cyan-500/20', text: 'text-cyan-600 dark:text-cyan-300', badgeBg: 'bg-cyan-100 dark:bg-cyan-950/80', badgeText: 'text-cyan-800 dark:text-cyan-300' },
        'ZUZU': { icon: 'fa-motorcycle', border: 'border-blue-500/40 dark:border-blue-500/40', bg: 'bg-blue-500/15 dark:bg-blue-500/20', text: 'text-blue-600 dark:text-blue-300', badgeBg: 'bg-blue-100 dark:bg-blue-950/80', badgeText: 'text-blue-800 dark:text-blue-300' },
        'TEMPUR': { icon: 'fa-shield-halved', border: 'border-emerald-500/40 dark:border-emerald-500/40', bg: 'bg-emerald-500/15 dark:bg-emerald-500/20', text: 'text-emerald-600 dark:text-emerald-300', badgeBg: 'bg-emerald-100 dark:bg-emerald-950/80', badgeText: 'text-emerald-800 dark:text-emerald-300' }
    };

    const filterLabels = {
        'all_fleet': t('fleet_widget.opt_all_fleet', 'All Fleet'),
        'rented': t('fleet_widget.opt_rented', 'Rented'),
        'ready_to_rent': t('fleet_widget.opt_ready_to_rent', 'Ready to Rent'),
        'under_repair': t('fleet_widget.opt_under_repair', 'Under Repair')
    };

    const unitsLabel = t('fleet_widget.units', 'Units');

    let gridHtml = '';
    vModels.forEach(m => {
        const style = modelStyles[m] || { icon: 'fa-motorcycle', border: 'border-slate-700', bg: 'bg-slate-800', text: 'text-slate-200', badgeBg: 'bg-slate-900', badgeText: 'text-slate-300' };
        const count = modelSums[m] || 0;
        const filterText = filterLabels[selectedFilter] || t('fleet_widget.opt_all_fleet', 'All Fleet');

        gridHtml += `
            <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-2 border ${style.border} transition duration-300 hover:scale-[1.01]">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-900 dark:text-slate-100 uppercase tracking-wider">${escapeHtml(m)}</span>
                    <div class="w-8 h-8 rounded-xl ${style.bg} border ${style.border} ${style.text} flex items-center justify-center font-bold text-sm shadow-sm">
                        <i class="fa-solid ${style.icon}"></i>
                    </div>
                </div>
                <h4 class="text-2xl font-extrabold ${style.text} font-sans tracking-tight">${count} <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 font-sans">${escapeHtml(unitsLabel)}</span></h4>
                <div class="flex items-center justify-between text-[11px] pt-1">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">${escapeHtml(filterText)}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${style.badgeBg} ${style.badgeText} border border-slate-300 dark:border-slate-700/50 uppercase">${escapeHtml(filterText)}</span>
                </div>
            </div>
        `;
    });

    grid.innerHTML = gridHtml;
}
window.renderFleetWidgetCards = renderFleetWidgetCards;

// -------------------------------------------------------------
// Global Modal Click-Outside & Escape Key Dismiss Behavior
// -------------------------------------------------------------
function closeModalElement(modal) {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
    if (modal.id === 'global-dynamic-modal') {
        modal.remove();
    }
}

function initGlobalModalDismissListeners() {
    document.addEventListener('click', function (e) {
        const openModals = document.querySelectorAll(
            'div[id$="-modal"]:not(.hidden), div[id*="-modal-"]:not(.hidden)'
        );

        openModals.forEach(modal => {
            const innerCard = modal.querySelector('.glass-panel, form, .modal-card') || modal.firstElementChild;
            if (innerCard && !innerCard.contains(e.target) && (modal.contains(e.target) || e.target === modal)) {
                closeModalElement(modal);
            }
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll(
                'div[id$="-modal"]:not(.hidden), div[id*="-modal-"]:not(.hidden)'
            );
            openModals.forEach(modal => closeModalElement(modal));
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGlobalModalDismissListeners);
} else {
    initGlobalModalDismissListeners();
}