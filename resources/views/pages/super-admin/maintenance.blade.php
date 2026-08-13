<!-- SUB-TAB: Detailed Maintenance Records -->
<div id="super-admin-tab-maintenance" class="space-y-4 hidden mt-0 pt-0">
    <!-- Streamlined Integrated Header & Controls Container -->
    <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
        <!-- Top Row: Title, Description & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-blue-400"></i> <span data-i18n="maint.title">Catatan Perbaikan & Servis Global</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5" data-i18n="maint.desc">Tinjau riwayat perbaikan, foto dokumentasi, penggunaan suku cadang, dan durasi servis lintas cabang.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0">
                <input type="file" id="excel-33-import-file" accept=".xlsx, .xls" class="hidden"
                    onchange="importFromExcel33(event)">
                <button type="button" onclick="document.getElementById('excel-33-import-file').click()" data-permission="edit_maintenance"
                    class="px-3.5 py-2 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-400 border border-emerald-500/20 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm"
                    title="Import Office Excel">
                    <i class="fa-solid fa-file-import"></i> <span data-i18n="maint.import_excel">Impor Excel</span>
                </button>
                <button type="button" onclick="openExportExcelModal('super_admin')"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-1.5"
                    title="Export Maintenance Records to Excel">
                    <i class="fa-solid fa-file-excel"></i> <span data-i18n="maint.export_excel">Export Excel</span>
                </button>
            </div>
        </div>

        <!-- Bottom Row: Integrated Responsive Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full">
            <!-- Global Search -->
            <div class="relative min-w-[200px] flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="super-admin-records-search" oninput="filterMaintenanceRecords()"
                    data-i18n-placeholder="maint.search_placeholder"
                    placeholder="Cari catatan perbaikan..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
            </div>

            <!-- Branch Filter -->
            <select id="super-admin-records-branch-filter" onchange="filterMaintenanceRecords()"
                class="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                <option value="" data-i18n="table.all_branches">Semua Cabang</option>
            </select>

            <!-- Status Filter -->
            <select id="super-admin-records-status-filter" onchange="filterMaintenanceRecords()"
                class="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-bold">
                <option value="" data-i18n="maint.all_statuses">Semua Status</option>
                <option value="IN_PROGRESS" data-i18n="maint.status_in_progress">SEDANG DIKERJAKAN</option>
                <option value="COMPLETED" data-i18n="maint.status_completed">SELESAI</option>
                <option value="CANCELLED" data-i18n="maint.status_cancelled">DIBATALKAN</option>
            </select>

            <!-- Custom Column Filter Group -->
            <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                <select id="super-admin-records-column-select" onchange="filterMaintenanceRecords()"
                    class="w-full flex-1 min-w-[120px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                    <option value="" data-i18n="table.col_filter">Filter Kolom...</option>
                    <option value="customer_name" data-i18n="shop.cust_name">Nama Pelanggan</option>
                    <option value="license_plate" data-i18n="shop.license_plate">Plat Nomor</option>
                    <option value="mechanic_name" data-i18n="maint.col_mechanic">Mekanik</option>
                    <option value="description" data-i18n="maint.job_desc">Deskripsi Pengerjaan</option>
                    <option value="parts_cost" data-i18n="maint.col_min_cost">Biaya Suku Cadang Min</option>
                </select>
                <input type="text" id="super-admin-records-column-val"
                    oninput="filterMaintenanceRecords()"
                    data-i18n-placeholder="table.val_placeholder"
                    placeholder="Nilai..."
                    class="w-full flex-1 min-w-[90px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
            </div>

            <!-- Sort Control -->
            <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i
                        class="fa-solid fa-arrow-down-short-wide text-blue-400"></i><span data-i18n="table.sort">Urutkan:</span></span>
                <select id="super-admin-records-sort-select" onchange="filterMaintenanceRecords()"
                    class="w-full flex-1 min-w-[130px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                    <option value="created_at-desc" data-i18n="sort.date_desc">Tanggal (Terbaru)</option>
                    <option value="created_at-asc" data-i18n="sort.date_asc">Tanggal (Terlama)</option>
                    <option value="customer_name-asc" data-i18n="sort.cust_asc">Nama Pelanggan (A-Z)</option>
                    <option value="branch_name-asc" data-i18n="sort.branch_asc">Cabang (A-Z)</option>
                    <option value="status-asc" data-i18n="sort.status_asc">Status (A-Z)</option>
                    <option value="parts_cost-desc" data-i18n="sort.cost_desc">Biaya Suku Cadang (Tertinggi)</option>
                    <option value="parts_cost-asc" data-i18n="sort.cost_asc">Biaya Suku Cadang (Terendah)</option>
                </select>
            </div>

            <!-- Column Visibility Toggle Dropdown -->
            <div class="relative inline-block text-left flex-1 min-w-[130px] sm:flex-initial">
                <button type="button" id="btn-records-column-toggle"
                    onclick="toggleRecordsColumnMenu(event)"
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold hover:bg-slate-800 transition flex items-center justify-between gap-2 shadow-sm">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-columns text-blue-400"></i>
                        <span data-i18n="table.columns">Kolom</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                </button>
                <div id="records-column-menu"
                    class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-slate-950/95 backdrop-blur-xl border border-slate-800 shadow-2xl p-3 z-[100] text-xs space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 mb-1">
                            <span class="font-bold text-slate-200 uppercase tracking-wider text-[10px]" data-i18n="table.show_hide_columns">Tampilkan / Sembunyikan Kolom</span>
                            <button type="button" onclick="resetRecordsColumns()"
                                class="text-[10px] text-blue-400 hover:underline font-semibold" data-i18n="table.select_all">Pilih Semua</button>
                        </div>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto custom-scrollbar">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="date" checked onchange="toggleRecordsColumn('date', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.date">Tanggal</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="branch" checked onchange="toggleRecordsColumn('branch', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="mgt.assigned_branch">Cabang Outlet</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="vehicle" checked onchange="toggleRecordsColumn('vehicle', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="maint.vehicle_info">Info Kendaraan</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="customer" checked onchange="toggleRecordsColumn('customer', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="shop.cust_name">Nama Pelanggan</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="mechanic" checked onchange="toggleRecordsColumn('mechanic', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="maint.col_mechanic">Mekanik</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="status" checked onchange="toggleRecordsColumn('status', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="shop.cust_status">Status Servis</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="notes" checked onchange="toggleRecordsColumn('notes', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="maint.job_desc">Deskripsi Pengerjaan</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="billing" checked onchange="toggleRecordsColumn('billing', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="maint.parts_billed">Rincian & Biaya</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-rec-col="actions" checked onchange="toggleRecordsColumn('actions', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.actions">Aksi</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Top Pagination Toolbar -->
    <div id="super-admin-maintenance-pagination-top" class="my-4"></div>

    <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
        <div class="w-full min-w-0 max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-center text-sm border-collapse min-w-[900px]">
                <thead>
                    <tr id="super-admin-maintenance-thead-tr" class="bg-slate-900 border-b border-slate-800 text-slate-300 text-center">
                        <th onclick="toggleSortRecords('created_at')"
                            class="p-4 font-semibold uppercase tracking-wider text-xs w-40 cursor-pointer select-none hover:text-blue-400 transition text-center">
                            <span data-i18n="table.date">Tanggal</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                        </th>
                        <th onclick="toggleSortRecords('branch_name')"
                            class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center font-sans">
                            <span data-i18n="maint.branch_mechanic">Cabang / Mekanik</span> <i
                                class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                        </th>
                        <th onclick="toggleSortRecords('customer_name')"
                            class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center font-sans">
                            <span data-i18n="shop.cust_name">Nama Pelanggan</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                        </th>
                        <th onclick="toggleSortRecords('license_plate')"
                            class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center font-sans">
                            <span data-i18n="maint.vehicle_info">Info Kendaraan</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                        </th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center font-sans" data-i18n="maint.job_desc">Deskripsi Pengerjaan</th>
                        <th onclick="toggleSortRecords('status')"
                            class="p-4 font-semibold uppercase tracking-wider text-xs w-28 cursor-pointer select-none hover:text-blue-400 transition text-center font-sans">
                            <span data-i18n="shop.cust_status">Status</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                        </th>
                        <th onclick="toggleSortRecords('parts_cost')"
                            class="p-4 font-semibold uppercase tracking-wider text-xs cursor-pointer select-none hover:text-blue-400 transition text-center font-sans">
                            <span data-i18n="maint.parts_billed">Rincian & Biaya</span> <i
                                class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                        </th>
                        <th class="p-4 font-semibold uppercase tracking-wider text-xs w-24 text-center font-sans" data-i18n="table.actions">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody id="super-admin-maintenance-tbody"
                    class="divide-y divide-slate-800/80 text-slate-300 text-xs font-sans">
                    <!-- Injected JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Pagination Toolbar -->
    <div id="super-admin-maintenance-pagination-bottom" class="my-4"></div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.printMaintenanceRecord = function(recordId) {
            if (!recordId) {
                const titleEl = document.getElementById('record-detail-title');
                recordId = titleEl ? titleEl.dataset.id : null;
            }
            if (!recordId) {
                showToast('Record ID not found – cannot print.', 'error');
                return;
            }
            const url = `/api/print-maintenance-record?record_id=${encodeURIComponent(recordId)}`;
            window.open(url, '_blank');
        };
    });
</script>
