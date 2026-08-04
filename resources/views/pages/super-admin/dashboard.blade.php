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
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block">Total Branches</span>
            <div id="stat-total-branches" class="text-3xl font-bold mt-2 text-blue-400 font-mono">0</div>
        </div>
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block">Registered Users</span>
            <div id="stat-total-users" class="text-3xl font-bold mt-2 text-cyan-400 font-mono">0</div>
        </div>
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block">Active Jobs</span>
            <div id="stat-active-jobs" class="text-3xl font-bold mt-2 text-amber-400 font-mono">0</div>
        </div>
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block">Completed Jobs</span>
            <div id="stat-completed-jobs" class="text-3xl font-bold mt-2 text-emerald-400 font-mono">0</div>
        </div>
        <div class="glass-panel rounded-xl p-5 shadow-lg relative overflow-hidden">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest block">Parts Revenue</span>
            <div id="stat-parts-revenue" class="text-3xl font-bold mt-2 text-violet-400 font-mono">Rp. 0</div>
        </div>
    </div>

    <!-- Graphs Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Branch Analytics Bar Graph -->
        <div class="lg:col-span-2 glass-panel rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-100 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-blue-400"></i> Branch Performance Chart
            </h3>
            <div class="h-80">
                <canvas id="branchPerformanceChart"></canvas>
            </div>
        </div>
        <!-- Warning / Alert Dashboard Pane -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-100 mb-4 flex items-center gap-2 text-amber-500">
                    <i class="fa-solid fa-triangle-exclamation"></i> Low Inventory Warnings
                </h3>
                <div id="low-stock-panel-list"
                    class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar text-sm">
                    <!-- Injected JS warnings -->
                </div>
            </div>
            <div class="pt-4 border-t border-slate-800/80 mt-4 text-xs text-slate-400 text-center">
                Checks stock items where <code>available_qty &lt; 5</code>
            </div>
        </div>
    </div>

    <!-- New Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-6">
        <!-- Spare Parts vs Dates Line Chart -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-100 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-emerald-400"></i> Spare Parts Used vs Dates
            </h3>
            <div class="h-80">
                <canvas id="sparePartsVsDatesChart"></canvas>
            </div>
        </div>
        <!-- Mechanic Record Time Bar Chart -->
        <div class="glass-panel rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-blue-400"></i> Mechanics Repair Duration
                </h3>
                <div>
                    <select id="mechanic-chart-filter" onchange="updateMechanicDurationChart()"
                        class="bg-slate-900 border border-slate-800 rounded px-2.5 py-1 text-xs text-slate-200">
                        <option value="all">All Mechanics</option>
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
            <i class="fa-solid fa-trophy text-amber-400 animate-pulse"></i> Best Mechanic Scoreboard
        </h3>
        <p class="text-xs text-slate-400 mb-4">Scoring formula:
            <code>(Jobs * 100) + (Parts * 10) - (Avg Duration * 2)</code>. Higher is better.</p>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr
                        class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="p-3 w-16 text-center">Rank</th>
                        <th class="p-3">Mechanic</th>
                        <th class="p-3 text-center">Jobs Completed</th>
                        <th class="p-3 text-center">Parts Used</th>
                        <th class="p-3 text-center">Avg Duration (min)</th>
                        <th class="p-3 text-right pr-6">Overall Score</th>
                    </tr>
                </thead>
                <tbody id="mechanic-scoreboard-tbody" class="divide-y divide-slate-800/60 text-slate-300 font-sans">
                    <!-- Injected JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>
