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

function formatIndonesianDate(dateInput, includeTime = true) {
    if (!dateInput) return 'N/A';
    const d = parseTimestampToDate ? parseTimestampToDate(dateInput) : new Date(dateInput);
    if (!d || isNaN(d.getTime())) return 'N/A';

    const lang = (typeof getAppLanguage === 'function' ? getAppLanguage() : 'id');
    const locale = lang === 'en' ? 'en-US' : 'id-ID';
    const tzSuffix = lang === 'en' ? ' WIB' : ' WIB';

    if (includeTime) {
        return d.toLocaleString(locale, {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }) + tzSuffix;
    }
    return d.toLocaleDateString(locale, {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
}
window.formatIndonesianDate = formatIndonesianDate;

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

function parseTimestampToDate(ts) {
    if (!ts) return null;
    if (ts instanceof Date) return ts;
    const str = String(ts).trim();
    if (!str || str === 'null' || str === 'undefined') return null;

    if (str.includes('Z') || (str.includes('T') && (str.includes('+') || (str.lastIndexOf('-') > 10)))) {
        const d = new Date(str);
        if (!isNaN(d.getTime())) return d;
    }

    const cleaned = str.replace('T', ' ').replace(/\.\d+/, '');
    const parts = cleaned.split(/[- :]/);
    if (parts.length >= 6) {
        return new Date(
            parseInt(parts[0]),
            parseInt(parts[1]) - 1,
            parseInt(parts[2]),
            parseInt(parts[3]),
            parseInt(parts[4]),
            parseInt(parts[5])
        );
    } else if (parts.length === 3) {
        const now = new Date();
        return new Date(
            now.getFullYear(),
            now.getMonth(),
            now.getDate(),
            parseInt(parts[0]),
            parseInt(parts[1]),
            parseInt(parts[2])
        );
    }

    const d = new Date(str);
    return isNaN(d.getTime()) ? null : d;
}
window.parseTimestampToDate = parseTimestampToDate;

var defaultInvCols = {
    branch: true,
    sku: true,
    part_name: true,
    description: true,
    category: true,
    warranty_category: true,
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
    activate_date: true,
    branch: true,
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
    mechAccumulatedParts: [],
    mechAccumulatedServices: [],
    mechAccumulatedOtherServices: [],
    mechCustomerType: 'internal',
    mechanicInProgress: false,
    superAdminActiveTab: 'stats',
    chartInstance: null,
    invColumnVisibility: { ...defaultInvCols },
    vehColumnVisibility: { ...defaultVehCols },
    pagination: {
        partsHistoryMonthlyPage: 1,
        shopInventory: { page: 1, limit: 25, custom: '' },
        globalInventory: { page: 1, limit: 25, custom: '' },
        auditLogs: { page: 1, limit: 25, custom: '' },
        maintenanceRecords: { page: 1, limit: 25, custom: '' },
        shopMaintenanceRecords: { page: 1, limit: 25, custom: '' },
        vehicles: { page: 1, limit: 25, custom: '' },
        customers_table: { page: 1, limit: 25, custom: '' }
    }
};
window.AppStore = AppStore;

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
        'rebind_vehicles': { url: '/api/vehicles/rebind', method: 'POST' },
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
window.request = request;

async function requestFormData(formData) {
    const action = formData.get('action');
    const legacyRoutes = {
        'update_profile': { url: '/api/update-profile' }
    };
    const route = legacyRoutes[action];
    const url = route ? route.url : '/api/legacy';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const options = {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken || '',
            'Accept': 'application/json'
        }
    };
    return fetch(url, options).then(r => handleFetchResponse(r));
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

function getNavRoutes() {
    return [
        {
            group: t('nav.main_menu', 'Reports'),
            items: [
                { id: 'stats', label: t('nav.dashboard_metrics', 'Dashboard Metrics'), icon: 'fa-chart-line', permission: 'view_stats' },
                { id: 'reports', label: t('nav.reports_module', 'Reports & Analytics'), icon: 'fa-chart-pie', permission: 'view_reports' }
            ]
        },
        {
            group: t('nav.mechanic_workstation', 'Mechanic Workstation'),
            items: [
                { id: 'mechanic-review', label: t('nav.checklist_verification', 'Checklist Verification'), icon: 'fa-clipboard-check', permission: 'view_mechanic_workstation' },
                { id: 'mechanic-history', label: t('nav.my_repair_history', 'My Repair History'), icon: 'fa-clock-rotate-left', permission: 'view_mechanic_workstation' }
            ]
        },
        {
            group: t('nav.shop_admin', 'Shop Administration'),
            items: [
                { id: 'shop-intake', label: t('nav.shop_intake', 'Intake & Repair Sheet'), icon: 'fa-file-signature', permission: 'view_shop_intake' },
                { id: 'shop-active-job', label: t('nav.active_job_details', 'Active Job Details'), icon: 'fa-wrench', permission: 'view_shop_intake' },
                { id: 'shop-maintenance', label: t('nav.branch_maintenance_records', 'Branch Maintenance Records'), icon: 'fa-clipboard-list', permission: 'view_shop_records' },
                { id: 'shop-review', label: t('nav.customer_history_logs', 'Customer History Logs'), icon: 'fa-clipboard-check', permission: 'view_shop_records' },
                { id: 'shop-inventory', label: t('nav.live_inventory_stock', 'Live Inventory Stock'), icon: 'fa-boxes-stacked', permission: 'view_shop_inventory' },
                { id: 'shop-history', label: t('nav.spare_part_usage_history', 'Spare Part Usage History'), icon: 'fa-clock-rotate-left', permission: 'view_parts_history' }
            ]
        },
        {
            group: t('nav.global_management', 'Global Management'),
            items: [
                { id: 'maintenance', label: t('nav.maintenance_records', 'Maintenance Records'), icon: 'fa-clipboard-list', permission: 'view_maintenance' },
                { id: 'inventory', label: t('nav.global_inventory_master', 'Global Inventory Master'), icon: 'fa-boxes-stacked', permission: 'view_global_inventory' },
                { id: 'vehicles', label: t('nav.vehicles_directory', 'Vehicles Directory'), icon: 'fa-motorcycle', permission: 'view_vehicles' },
                { id: 'customers', label: t('nav.customers_directory', 'Customers Directory'), icon: 'fa-users', permission: 'view_customers' },
                { id: 'management', label: t('nav.data_management', 'Data Management'), icon: 'fa-sliders', permission: 'view_management' }
            ]
        },
        {
            group: t('nav.compliance', 'Compliance'),
            items: [
                { id: 'logs', label: t('nav.audit_logs', 'Audit Logs'), icon: 'fa-shield-halved', permission: 'view_logs' }
            ]
        }
    ];
}
window.getNavRoutes = getNavRoutes;
window.APP_NAV_ROUTES = getNavRoutes();

function hasPermission(permission) {
    if (!AppStore.user) return false;
    const userRole = AppStore.user.role || 'guest';

    if (Array.isArray(AppStore.user.permissions) && AppStore.user.permissions.length > 0) {
        return AppStore.user.permissions.includes(permission) || AppStore.user.permissions.includes('*');
    }

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
            'view_mechanic_workstation', 'edit_job'
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
    if (AppStore.user.role === 'inventory_admin') return 'shop-inventory';
    if (AppStore.user.role === 'shop_admin') return 'shop-intake';
    if (AppStore.user.role === 'mechanic') return 'mechanic-review';
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

let themeLabelTimeout = null;

function syncThemeUI(isDark, animate = true) {
    const pill = document.getElementById('dark-mode-toggle-pill');
    const circle = document.getElementById('dark-mode-toggle-circle');
    const icon = document.getElementById('theme-toggle-icon');
    const label = document.getElementById('theme-toggle-label');

    const mPill = document.getElementById('mobile-dark-mode-toggle-pill');
    const mCircle = document.getElementById('mobile-dark-mode-toggle-circle');
    const mIcon = document.getElementById('mobile-theme-toggle-icon');
    const mLabel = document.getElementById('mobile-theme-toggle-label');

    // 1. Immediately slide the toggle knobs & change pill track colors
    if (circle) {
        circle.style.transform = isDark ? 'translateX(20px)' : 'translateX(0px)';
    }
    if (pill) {
        pill.className = isDark
            ? 'w-11 h-6 bg-blue-600 rounded-full p-1 transition-colors duration-300 relative flex items-center pointer-events-none'
            : 'w-11 h-6 bg-slate-300 rounded-full p-1 transition-colors duration-300 relative flex items-center pointer-events-none';
    }

    if (mCircle) {
        mCircle.style.transform = isDark ? 'translateX(20px)' : 'translateX(0px)';
    }
    if (mPill) {
        mPill.className = isDark
            ? 'w-11 h-6 bg-blue-600 rounded-full p-1 transition-colors duration-300 relative flex items-center pointer-events-none'
            : 'w-11 h-6 bg-slate-700 rounded-full p-1 transition-colors duration-300 relative flex items-center pointer-events-none';
    }

    // 2. Only change the label and icon AFTER sliding (duration: 300ms)
    const updateLabels = () => {
        const iconClass = isDark ? 'fa-solid fa-moon text-blue-400 w-4 text-center mr-0' : 'fa-solid fa-sun text-amber-500 w-4 text-center mr-0';
        const mIconClass = isDark ? 'fa-solid fa-moon text-blue-400 text-xs w-4 text-center shrink-0 mr-0' : 'fa-solid fa-sun text-amber-500 text-xs w-4 text-center shrink-0 mr-0';
        const text = isDark ? 'Dark Mode' : 'Light Mode';

        if (icon) icon.className = iconClass;
        if (label) {
            label.innerText = text;
            label.className = isDark ? 'text-slate-200' : 'text-slate-700';
        }

        if (mIcon) mIcon.className = mIconClass;
        if (mLabel) {
            mLabel.innerText = text;
            mLabel.className = isDark ? 'text-xs text-left text-slate-200' : 'text-xs text-left text-slate-700';
        }
    };

    if (themeLabelTimeout) clearTimeout(themeLabelTimeout);

    if (animate) {
        themeLabelTimeout = setTimeout(updateLabels, 300);
    } else {
        updateLabels();
    }
}

function initTheme() {
    const savedTheme = localStorage.getItem('theme') || localStorage.getItem('sgpm_theme');
    let theme = savedTheme;
    if (!theme) {
        theme = 'light';
    }
    const isDark = theme === 'dark';

    if (!isDark) {
        document.body.classList.add('light-mode');
        document.documentElement.classList.remove('dark');
    } else {
        document.body.classList.remove('light-mode');
        document.documentElement.classList.add('dark');
    }
    syncThemeUI(isDark, false);
}
window.initTheme = initTheme;

function toggleThemeMode(e) {
    if (e) {
        if (e.stopPropagation) e.stopPropagation();
        if (e.preventDefault) e.preventDefault();
    }
    const isLight = document.body.classList.toggle('light-mode');
    const isDark = !isLight;

    if (isDark) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        localStorage.setItem('sgpm_theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
        localStorage.setItem('sgpm_theme', 'light');
    }

    syncThemeUI(isDark, true);
    if (typeof window.updateChartsThemeColors === 'function') {
        window.updateChartsThemeColors();
    }
    showToast(`Switched to ${isDark ? 'Dark' : 'Light'} Mode`, 'info');
}
window.toggleThemeMode = toggleThemeMode;

function getAppLanguage() {
    return localStorage.getItem('sgpm_lang') || 'en';
}
window.getAppLanguage = getAppLanguage;

function applyDOMTranslations() {
    if (typeof t !== 'function') return;
    const lang = (typeof getAppLanguage === 'function' ? getAppLanguage() : 'en');

    // 1. Explicit data-i18n attributes
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (key) {
            const translation = t(key, el.innerText.trim());
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                el.placeholder = translation;
            } else {
                el.innerText = translation;
            }
        }
    });

    // 2. Scan all UI headings, buttons, table headers, labels, and option text
    const selectors = 'h1, h2, h3, h4, h5, button, th, label, span.sidebar-label, span.sidebar-section-title, option';
    document.querySelectorAll(selectors).forEach(el => {
        // Skip user dynamic data containers or inputs with user content
        if (el.closest('#shop-admin-inventory-tbody') || 
            el.closest('#super-admin-inventory-tbody') || 
            el.closest('#parts-history-list-tbody') || 
            el.closest('#global-parts-history-list-tbody') || 
            el.closest('#super-admin-logs-tbody') ||
            el.closest('#customers-tbody') ||
            el.closest('#vehicles-tbody') ||
            el.closest('#maintenance-tbody')) {
            return;
        }

        const rawText = el.innerText ? el.innerText.trim() : '';
        if (!rawText) return;

        if (lang === 'id' && window.staticUiMap && window.staticUiMap[rawText]) {
            el.innerText = window.staticUiMap[rawText];
        } else if (lang === 'en' && window.staticUiMap) {
            for (const enKey in window.staticUiMap) {
                if (window.staticUiMap[enKey] === rawText) {
                    el.innerText = enKey;
                    break;
                }
            }
        }
    });

    // 3. Translate search input placeholders
    document.querySelectorAll('input[type="text"]').forEach(el => {
        const p = el.placeholder ? el.placeholder.trim() : '';
        if (!p) return;

        if (lang === 'id' && window.staticUiMap && window.staticUiMap[p]) {
            el.placeholder = window.staticUiMap[p];
        } else if (lang === 'en' && window.staticUiMap) {
            for (const enKey in window.staticUiMap) {
                if (window.staticUiMap[enKey] === p) {
                    el.placeholder = enKey;
                    break;
                }
            }
        }
    });
}
window.applyDOMTranslations = applyDOMTranslations;

function setAppLanguage(lang) {
    localStorage.setItem('sgpm_lang', lang);
    const htmlEl = document.documentElement;
    if (lang === 'en') {
        htmlEl.setAttribute('lang', 'en-US');
    } else {
        htmlEl.setAttribute('lang', 'id-ID');
    }

    // Sync dropdown selects
    const selects = document.querySelectorAll('#app-language-select');
    selects.forEach(s => s.value = lang);

    applyDOMTranslations();

    // Re-render active views and sidebar navigation
    window.APP_NAV_ROUTES = (typeof getNavRoutes === 'function') ? getNavRoutes() : window.APP_NAV_ROUTES;
    if (typeof renderRoleBasedNavigation === 'function') renderRoleBasedNavigation();
    if (typeof filterPartsHistory === 'function') filterPartsHistory();
    if (typeof filterGlobalPartsHistory === 'function') filterGlobalPartsHistory();
    if (typeof filterShopAdminInventory === 'function') filterShopAdminInventory();
    if (typeof filterGlobalInventory === 'function') filterGlobalInventory();
    const currentRoute = localStorage.getItem('sgpm_last_route') || (typeof getDefaultUserRoute === 'function' ? getDefaultUserRoute() : '');
    if (currentRoute && typeof setActiveNavRoute === 'function') setActiveNavRoute(currentRoute);

    if (typeof loadSuperAdminStats === 'function') loadSuperAdminStats();

    const langName = lang === 'en' ? 'English (US)' : 'Bahasa Indonesia';
    showToast(`Bahasa diubah ke ${langName}`, 'info');
}
window.setAppLanguage = setAppLanguage;

function initLanguage() {
    const lang = getAppLanguage();
    const htmlEl = document.documentElement;
    if (lang === 'en') {
        htmlEl.setAttribute('lang', 'en-US');
    } else {
        htmlEl.setAttribute('lang', 'id-ID');
    }

    const selects = document.querySelectorAll('#app-language-select');
    selects.forEach(s => s.value = lang);
    applyDOMTranslations();
}
window.initLanguage = initLanguage;
