// -------------------------------------------------------------
// MODULE: STORE & CORE UTILITIES
// -------------------------------------------------------------

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
window.escapeHtml = escapeHtml;

function formatIDR(val) {
    if (val === null || val === undefined || isNaN(val)) return 'Rp. 0';
    return 'Rp. ' + parseFloat(val).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}
window.formatIDR = formatIDR;

function formatJobCode(id, dateStr = null) {
    if (!id) return 'N/A';
    let datePart = '';
    if (dateStr) {
        const d = new Date(dateStr);
        if (!isNaN(d.getTime())) {
            const pad = (n) => String(n).padStart(2, '0');
            datePart = `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}`;
        }
    }
    if (!datePart) {
        const d = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        datePart = `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}`;
    }
    const paddedId = String(id).padStart(4, '0');
    return `JOB-${datePart}-${paddedId}`;
}
window.formatJobCode = formatJobCode;

var defaultInvCols = {
    branch: true,
    sku: true,
    part_name: true,
    description: true,
    category: true,
    connected_service: true,
    available_qty: true,
    price: true,
    updated_at: true,
    actions: true
};

var defaultVehCols = {
    id: true,
    license_plate: true,
    brand_model: true,
    color: true,
    vin: true,
    engine: true,
    controller: true,
    owner: true,
    actions: true
};
window.defaultInvCols = defaultInvCols;
window.defaultVehCols = defaultVehCols;

// System Application State Store
const AppStore = {
    user: null,
    branches: [],
    sparepartCategories: [],
    inventory: [],
    activeJobs: [],
    completedJobs: [],
    auditLogs: [],
    stats: null,
    activeTimersInterval: null,
    selectedIntakeVehicle: null,
    selectedStopRecordId: null,
    partsUsedAccumulator: [],
    mechanicInProgress: false,
    superAdminActiveTab: 'stats',
    chartInstance: null,
    invColumnVisibility: { ...defaultInvCols },
    vehColumnVisibility: { ...defaultVehCols },
    pagination: {
        shopInventory: { page: 1, limit: 25, custom: '' },
        globalInventory: { page: 1, limit: 25, custom: '' },
        auditLogs: { page: 1, limit: 25, custom: '' },
        maintenanceRecords: { page: 1, limit: 25, custom: '' },
        vehicles: { page: 1, limit: 25, custom: '' }
    }
};
window.AppStore = AppStore;

function getPageSize(key) {
    const state = AppStore.pagination[key];
    if (!state) return 25;
    if (state.limit === 'custom') {
        const parsed = parseInt(state.custom);
        return (!isNaN(parsed) && parsed > 0) ? parsed : 25;
    }
    return parseInt(state.limit) || 25;
}
window.getPageSize = getPageSize;

function renderPaginationUI(key, totalItems, topTargetId, bottomTargetId, onPageChangeCallback) {
    const state = AppStore.pagination[key] || { page: 1, limit: 25, custom: '' };
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
window.renderPaginationUI = renderPaginationUI;

window.handlePageSizeChange = function (key, val) {
    AppStore.pagination[key].limit = val;
    AppStore.pagination[key].page = 1;
    triggerTableRefresh(key);
};

window.handleCustomPageSizeChange = function (key, val) {
    AppStore.pagination[key].custom = val;
    AppStore.pagination[key].page = 1;
    triggerTableRefresh(key);
};

window.handlePageNav = function (key, pageNum) {
    AppStore.pagination[key].page = pageNum;
    triggerTableRefresh(key);
};

function triggerTableRefresh(key) {
    if (key === 'shopInventory' && typeof filterShopAdminInventory === 'function') filterShopAdminInventory();
    else if (key === 'globalInventory' && typeof filterGlobalInventory === 'function') filterGlobalInventory();
    else if (key === 'maintenanceRecords' && typeof filterMaintenanceRecords === 'function') filterMaintenanceRecords();
    else if (key === 'auditLogs' && typeof fetchAuditLogs === 'function') fetchAuditLogs();
    else if (key === 'vehicles' && typeof filterVehiclesAndCustomers === 'function') filterVehiclesAndCustomers();
}
window.triggerTableRefresh = triggerTableRefresh;

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
        'edit_audit_log': { url: '/api/audit-logs', method: 'PUT' }
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
window.request = request;

async function requestFormData(formData) {
    const options = {
        method: 'POST',
        body: formData
    };
    return fetch('/api/legacy', options).then(r => handleFetchResponse(r));
}
window.requestFormData = requestFormData;

async function handleFetchResponse(response) {
    let data;
    try {
        data = await response.json();
    } catch (e) {
        throw new Error('Server returned an unreadable response.');
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

async function fetchSparepartCategories() {
    try {
        const data = await request('get_sparepart_categories', {}, 'GET');
        AppStore.sparepartCategories = data || [];
    } catch (err) {
        console.error("Failed to fetch sparepart categories:", err);
    }
}
window.fetchSparepartCategories = fetchSparepartCategories;

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
window.getCategoryOptionsHtml = getCategoryOptionsHtml;

// -------------------------------------------------------------
// Database-Driven Role & Permission Security Engine (RBAC)
// -------------------------------------------------------------

const APP_NAV_ROUTES = [
    {
        group: 'Main Menu',
        items: [
            { id: 'stats', label: 'Dashboard Metrics', icon: 'fa-chart-line', permission: 'view_stats' }
        ]
    },
    {
        group: 'Mechanic Workstation',
        items: [
            { id: 'mechanic-form', label: 'Intake & Repair Sheet', icon: 'fa-file-signature', permission: 'view_mechanic_workstation' },
            { id: 'mechanic-review', label: 'Checklist Verification', icon: 'fa-clipboard-check', permission: 'view_mechanic_workstation' },
            { id: 'mechanic-history', label: 'My Repair History', icon: 'fa-clock-rotate-left', permission: 'view_mechanic_workstation' }
        ]
    },
    {
        group: 'Shop Administration',
        items: [
            { id: 'shop-inventory', label: 'Live Inventory Stock', icon: 'fa-boxes-stacked', permission: 'view_shop_inventory' },
            { id: 'shop-history', label: 'Spare Part Usage History', icon: 'fa-clock-rotate-left', permission: 'view_shop_history' }
        ]
    },
    {
        group: 'Global Management',
        items: [
            { id: 'inventory', label: 'Global Inventory Master', icon: 'fa-boxes-stacked', permission: 'view_global_inventory' },
            { id: 'maintenance', label: 'Maintenance Records', icon: 'fa-clipboard-list', permission: 'view_maintenance' },
            { id: 'vehicles', label: 'Vehicles Directory', icon: 'fa-motorcycle', permission: 'view_vehicles' },
            { id: 'customers', label: 'Customers Directory', icon: 'fa-users', permission: 'view_customers' },
            { id: 'management', label: 'Data Management', icon: 'fa-sliders', permission: 'view_management' }
        ]
    },
    {
        group: 'Compliance',
        items: [
            { id: 'logs', label: 'Audit Logs', icon: 'fa-shield-halved', permission: 'view_logs' }
        ]
    }
];
window.APP_NAV_ROUTES = APP_NAV_ROUTES;

function hasPermission(permission) {
    if (!AppStore.user) return false;
    const userRole = AppStore.user.role || 'guest';

    if (Array.isArray(AppStore.user.permissions) && AppStore.user.permissions.length > 0) {
        return AppStore.user.permissions.includes(permission) || AppStore.user.permissions.includes('*');
    }

    const ROLE_PERMISSIONS = {
        'super_admin': [
            'view_stats', 'view_global_inventory', 'edit_inventory', 'delete_inventory',
            'view_maintenance', 'edit_maintenance', 'delete_maintenance',
            'view_vehicles', 'edit_vehicles', 'delete_vehicles',
            'view_customers', 'edit_customers', 'delete_customers',
            'view_management', 'edit_management', 'view_logs', 'export_data'
        ],
        'shop_admin': [
            'view_shop_inventory', 'view_shop_history', 'edit_inventory'
        ],
        'mechanic': [
            'view_mechanic_workstation', 'create_job', 'edit_job'
        ]
    };

    const perms = ROLE_PERMISSIONS[userRole] || [];
    return perms.includes('*') || perms.includes(permission);
}
window.hasPermission = hasPermission;

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
window.canUserAccessRoute = canUserAccessRoute;

function getDefaultUserRoute() {
    if (!AppStore.user) return 'auth-gate';
    if (AppStore.user.role === 'shop_admin') return 'shop-inventory';
    if (AppStore.user.role === 'mechanic') return 'mechanic-form';
    if (hasPermission('view_stats')) return 'stats';
    return 'stats';
}
window.getDefaultUserRoute = getDefaultUserRoute;

// -------------------------------------------------------------
// Toast UI Message Notifications
// -------------------------------------------------------------

function showToast(message, type = 'success', duration = 4000) {
    const container = document.getElementById('toast-container');
    if (!container) return;
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

    setTimeout(() => {
        toast.classList.remove('translate-x-20', 'opacity-0');
    }, 10);

    setTimeout(() => {
        toast.classList.add('translate-x-20', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, duration);
}
window.showToast = showToast;

function initTheme() {
    const savedTheme = localStorage.getItem('theme') || localStorage.getItem('sgpm_theme');
    let theme = savedTheme;
    if (!theme) {
        theme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) ? 'light' : 'dark';
    }
    const switchEl = document.getElementById('theme-toggle-switch');
    const icon = document.getElementById('theme-toggle-icon');

    if (theme === 'light') {
        document.body.classList.add('light-mode');
        if (switchEl) switchEl.checked = false;
        if (icon) {
            icon.className = 'fa-solid fa-sun text-amber-500';
        }
    } else {
        document.body.classList.remove('light-mode');
        if (switchEl) switchEl.checked = true;
        if (icon) {
            icon.className = 'fa-solid fa-moon text-blue-400';
        }
    }
}
window.initTheme = initTheme;

function toggleThemeMode(e) {
    if (e) e.stopPropagation();
    const isLight = document.body.classList.toggle('light-mode');
    const switchEl = document.getElementById('theme-toggle-switch');
    const icon = document.getElementById('theme-toggle-icon');

    if (isLight) {
        localStorage.setItem('theme', 'light');
        if (switchEl) switchEl.checked = false;
        if (icon) {
            icon.className = 'fa-solid fa-sun text-amber-500';
        }
        showToast('Light mode activated', 'info');
    } else {
        localStorage.setItem('theme', 'dark');
        if (switchEl) switchEl.checked = true;
        if (icon) {
            icon.className = 'fa-solid fa-moon text-blue-400';
        }
        showToast('Dark mode activated', 'info');
    }
}
window.toggleThemeMode = toggleThemeMode;

function showToast(message, type = 'success', duration = 4000) {
    const toastEl = document.getElementById('toast');
    // Guard against missing element
    if (!toastEl) return;   // <-- new line

    toastEl.className = `toast toast-${type}`;
    toastEl.innerText = message;
    toastEl.style.display = 'block';
    setTimeout(() => toastEl.style.display = 'none', duration);
}
