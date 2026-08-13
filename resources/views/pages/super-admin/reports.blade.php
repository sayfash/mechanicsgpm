<!-- SUB-TAB: Reports & Analytics Module -->
<div id="super-admin-tab-reports" class="space-y-6 hidden mt-0 pt-0">
    <!-- Streamlined Integrated Header & Controls Container -->
    <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
        <!-- Top Row: Title, Description & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-blue-400"></i> <span data-i18n="reports.title">Laporan & Analisis Kinerja Cabang</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5" data-i18n="reports.desc">Generasi laporan pendapatan, statistik servis, dan konsumsi suku cadang per cabang maupun agregat seluruh cabang.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                <button onclick="openExportExcelModal('super_admin')"
                    class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-1.5"
                    title="Export Report to Excel">
                    <i class="fa-solid fa-file-excel"></i> <span data-i18n="reports.export_excel">Export Excel</span>
                </button>
            </div>
        </div>

        <!-- Bottom Row: Filter Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full">
            <!-- Branch Filter -->
            <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i class="fa-solid fa-sitemap text-blue-400"></i><span data-i18n="reports.branch">Cabang:</span></span>
                <select id="report-branch-select" onchange="fetchReportSummary()"
                    class="w-full flex-1 min-w-[140px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                    <option value="all" data-i18n="table.all_branches">Semua Cabang (Agregat)</option>
                </select>
            </div>

            <!-- Start Date -->
            <div class="flex items-center gap-2 flex-1 min-w-[180px]">
                <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i class="fa-solid fa-calendar text-blue-400"></i><span data-i18n="reports.from">Dari:</span></span>
                <input type="date" id="report-start-date" onchange="fetchReportSummary()"
                    class="w-full flex-1 px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
            </div>

            <!-- End Date -->
            <div class="flex items-center gap-2 flex-1 min-w-[180px]">
                <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i class="fa-solid fa-calendar text-blue-400"></i><span data-i18n="reports.to">Sampai:</span></span>
                <input type="date" id="report-end-date" onchange="fetchReportSummary()"
                    class="w-full flex-1 px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
            </div>
        </div>
    </div>

    <!-- Summary KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Revenue -->
        <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider" data-i18n="reports.total_revenue">Total Pendapatan</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
            <h4 id="report-kpi-revenue" class="text-2xl font-extrabold text-emerald-400 font-sans tracking-tight">Rp 0</h4>
            <p class="text-[11px] text-slate-500 flex items-center justify-between">
                <span>Jasa: <strong id="report-kpi-labor" class="text-slate-300">Rp 0</strong></span>
                <span>Part: <strong id="report-kpi-parts" class="text-slate-300">Rp 0</strong></span>
            </p>
        </div>

        <!-- Card 2: Total Jobs -->
        <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider" data-i18n="reports.total_jobs">Total Pengerjaan</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-wrench"></i>
                </div>
            </div>
            <h4 id="report-kpi-jobs" class="text-2xl font-extrabold text-blue-400 font-sans tracking-tight">0</h4>
            <p class="text-[11px] text-slate-500 flex items-center justify-between">
                <span>Selesai: <strong id="report-kpi-completed" class="text-emerald-400">0</strong></span>
                <span>Proses: <strong id="report-kpi-inprogress" class="text-amber-400">0</strong></span>
            </p>
        </div>

        <!-- Card 3: Spare Parts Used Qty -->
        <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider" data-i18n="reports.parts_used">Total Suku Cadang Terpakai</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <h4 id="report-kpi-parts-qty" class="text-2xl font-extrabold text-purple-400 font-sans tracking-tight">0 Unit</h4>
            <p class="text-[11px] text-slate-500" data-i18n="reports.parts_used_desc">Kuantitas fisik sparepart yang terpasang.</p>
        </div>

        <!-- Card 4: Completion Rate -->
        <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider" data-i18n="reports.completion_rate">Tingkat Penyelesaian</span>
                <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <h4 id="report-kpi-rate" class="text-2xl font-extrabold text-cyan-400 font-sans tracking-tight">0%</h4>
            <p class="text-[11px] text-slate-500" data-i18n="reports.completion_desc">Rasio pekerjaan selesai vs total masukan.</p>
        </div>
    </div>

    <!-- Vehicle Fleet & Rental Status Summary Card -->
    <div id="report-vehicle-fleet-widget-card" class="glass-panel rounded-2xl p-5 shadow-2xl space-y-4">
        <!-- Top Bar: Title & Filter Dropdown -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
            <div>
                <h4 class="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-motorcycle text-cyan-400"></i> <span data-i18n="fleet_widget.title">Vehicle Fleet & Rental Status</span>
                </h4>
                <p id="report-fleet-widget-subtitle" class="text-xs text-slate-400 mt-0.5" data-i18n="fleet_widget.subtitle_prefix">Real-time aggregate count per model across selected fleet status.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i class="fa-solid fa-filter text-cyan-400"></i><span data-i18n="fleet_widget.filter_label">Filter:</span></span>
                <select id="report-fleet-widget-status-filter" onchange="renderFleetWidgetCards()"
                    class="px-3.5 py-2 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold focus:outline-none focus:border-cyan-500">
                    <option value="all_fleet" data-i18n="fleet_widget.opt_all_fleet">All Fleet</option>
                    <option value="rented" data-i18n="fleet_widget.opt_rented">Rented</option>
                    <option value="ready_to_rent" data-i18n="fleet_widget.opt_ready_to_rent">Ready to Rent</option>
                    <option value="under_repair" data-i18n="fleet_widget.opt_under_repair">Under Repair</option>
                </select>
            </div>
        </div>

        <!-- 4 Column Grid for Vehicle Models -->
        <div id="report-fleet-widget-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Dynamic 4 Columns Injected via JS -->
        </div>
    </div>

    <!-- Branch Performance Comparison Table -->
    <div id="report-branch-comparison-card" class="glass-panel rounded-2xl p-5 shadow-2xl space-y-4">
        <h4 class="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-sitemap text-indigo-400"></i> <span data-i18n="reports.branch_comparison">Perbandingan Kinerja Antar Cabang</span>
        </h4>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-center text-xs border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase font-semibold text-[10px]">
                        <th class="p-3 text-center">ID</th>
                        <th class="p-3 text-center font-sans" data-i18n="mgt.branch_name">Nama Cabang</th>
                        <th class="p-3 text-center font-sans" data-i18n="mgt.branch_abbreviation">Singkatan</th>
                        <th class="p-3 text-center font-sans" data-i18n="reports.col_total_jobs">Total Servis</th>
                        <th class="p-3 text-center font-sans" data-i18n="reports.col_completed">Selesai</th>
                        <th class="p-3 text-center font-sans" data-i18n="reports.col_labor_fee">Jasa Servis</th>
                        <th class="p-3 text-center font-sans" data-i18n="reports.col_parts_billed">Penjualan Part</th>
                        <th class="p-3 text-center font-sans" data-i18n="reports.col_total_revenue">Total Omset</th>
                    </tr>
                </thead>
                <tbody id="report-branch-table-tbody" class="divide-y divide-slate-800/80 text-slate-300 font-sans">
                    <!-- Injected JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Vehicle Fleet & Rental Status Breakdown per Branch & Model -->
    <div id="report-vehicle-fleet-card" class="glass-panel rounded-2xl p-5 shadow-2xl space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-motorcycle text-cyan-400"></i> <span data-i18n="reports.fleet_breakdown_title">Perbandingan Unit Kendaraan & Status Sewa Antar Cabang</span>
            </h4>
            <span class="text-xs text-slate-400 italic" data-i18n="reports.fleet_breakdown_desc">Dipisahkan berdasarkan Model Kendaraan & Cabang</span>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-center text-xs border-collapse min-w-[900px]">
                <thead id="report-vehicle-table-thead">
                    <!-- Dynamic Headers for Vehicle Models Injected via JS -->
                </thead>
                <tbody id="report-vehicle-table-tbody" class="divide-y divide-slate-800/80 text-slate-300 font-sans">
                    <!-- Dynamic Rows Injected via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>
