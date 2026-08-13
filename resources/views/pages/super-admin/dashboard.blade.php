<!-- SUB-TAB: Dashboard Metrics -->
<div id="super-admin-tab-stats" class="space-y-6 hidden pt-6">
    <!-- Title Banner
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel rounded-2xl p-6 shadow-xl">
        <div>
            <h3 class="text-xl font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-blue-400"></i> Executive Performance Metrics
            </h3>
            <p class="text-sm text-slate-400 mt-1">High-level analytics, active repairs, branch metrics, and low-inventory telemetry.</p>
        </div>
    </div>  -->

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Cabang -->
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block" data-i18n="stat.total_branches">Total Cabang</span>
            <div id="stat-total-branches" class="text-3xl font-extrabold mt-2 text-blue-400 font-sans tracking-tight">0</div>
        </div>
        <!-- 2. Servis Aktif -->
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block" data-i18n="stat.active_jobs">Servis Aktif</span>
            <div id="stat-active-jobs" class="text-3xl font-extrabold mt-2 text-amber-400 font-sans tracking-tight">0</div>
        </div>
        <!-- 3. Servis Selesai -->
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block" data-i18n="stat.completed_jobs">Servis Selesai</span>
            <div id="stat-completed-jobs" class="text-3xl font-extrabold mt-2 text-emerald-400 font-sans tracking-tight">0</div>
        </div>
        <!-- 4. Pendapatan Servis -->
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block" data-i18n="stat.service_revenue">Pendapatan Servis</span>
            <div id="stat-service-revenue" class="text-3xl font-extrabold mt-2 text-cyan-400 font-sans tracking-tight">Rp. 0</div>
        </div>
        <!-- 5. Pendapatan Suku Cadang -->
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block" data-i18n="stat.parts_revenue">Pendapatan Suku Cadang</span>
            <div id="stat-parts-revenue" class="text-3xl font-extrabold mt-2 text-violet-400 font-sans tracking-tight">Rp. 0</div>
        </div>
    </div>

    <!-- Graphs Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Branch Analytics Bar Graph -->
        <div class="lg:col-span-2 glass-panel rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-100 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-blue-400"></i> <span data-i18n="dash.branch_performance">Grafik Performa Cabang</span>
            </h3>
            <div class="h-80">
                <canvas id="branchPerformanceChart"></canvas>
            </div>
        </div>
        <!-- Warning / Alert Dashboard Pane -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-100 mb-4 flex items-center gap-2 text-amber-500">
                    <i class="fa-solid fa-triangle-exclamation"></i> <span data-i18n="dash.low_stock_warning">Peringatan Stok Rendah</span>
                </h3>
                <div id="low-stock-panel-list"
                    class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar text-sm">
                    <!-- Injected JS warnings -->
                </div>
            </div>
            <div class="pt-4 border-t border-slate-800/80 mt-4 text-xs text-slate-400 text-center">
                Mengecek item stok dengan <code>available_qty &lt; 5</code>
            </div>
        </div>
    </div>

    <!-- New Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-6">
        <!-- Spare Parts vs Dates Line Chart -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-emerald-400"></i> <span data-i18n="dash.parts_vs_date">Penggunaan Suku Cadang</span>
                </h3>
                <div>
                    <select id="spare-parts-period-filter" onchange="updateSparePartsChartPeriod()"
                        class="bg-slate-900 border border-slate-800 rounded px-2.5 py-1 text-xs text-slate-200">
                        <option value="daily">Daily (Current Week)</option>
                        <option value="weekly">Weekly (Current Month)</option>
                        <option value="monthly">Monthly (Current Year)</option>
                    </select>
                </div>
            </div>
            <div class="h-80">
                <canvas id="sparePartsVsDatesChart"></canvas>
            </div>
        </div>
        <!-- Mechanic Record Time Bar Chart -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-blue-400"></i> <span data-i18n="dash.mechanic_duration">Durasi Perbaikan Mekanik</span>
                </h3>
                <div>
                    <select id="mechanic-chart-filter" onchange="updateMechanicDurationChart()"
                        class="bg-slate-900 border border-slate-800 rounded px-2.5 py-1 text-xs text-slate-200">
                        <option value="all">Semua Mekanik</option>
                        <!-- Dynamic options -->
                    </select>
                </div>
            </div>
            <div class="h-80">
                <canvas id="mechanicRecordTimeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Mechanic Scoreboard Section -->
    <div class="glass-panel rounded-2xl p-6 shadow-xl mt-6">
        <h3 class="text-lg font-bold text-slate-100 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-trophy text-amber-400 animate-pulse"></i> <span data-i18n="dash.scoreboard">Papan Skor Mekanik Terbaik</span>
        </h3>
        <p class="text-xs text-slate-400 mb-4">Formula Penilaian:
            <code>(Jumlah Pengerjaan * 100) + (Suku Cadang * 10) - (Rata-rata Durasi * 2)</code>. Nilai lebih tinggi lebih baik.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr
                        class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="p-3 w-16 text-center">Peringkat</th>
                        <th class="p-3">Mekanik</th>
                        <th class="p-3 text-center">Pengerjaan Selesai</th>
                        <th class="p-3 text-center">Suku Cadang Terpakai</th>
                        <th class="p-3 text-center">Rata-rata Durasi (menit)</th>
                        <th class="p-3 text-right pr-6">Skor Akhir</th>
                    </tr>
                </thead>
                <tbody id="mechanic-scoreboard-tbody" class="divide-y divide-slate-800/60 text-slate-300 font-sans">
                    <!-- Injected JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>
