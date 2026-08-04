<!-- SUB-TAB: Compliance Audit Logs -->
<div id="super-admin-tab-logs" class="space-y-4 hidden mt-0 pt-0">
    <!-- Streamlined Integrated Header & Controls Container -->
    <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-slate-800/60">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-amber-400"></i> Compliance Audit Logs
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Immutable system activity ledger & database change trail across all operations.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="exportLogsCSV()"
                    class="px-3.5 py-2 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-file-csv"></i> Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Top Pagination Toolbar -->
    <div id="super-admin-logs-pagination-top" class="my-4"></div>

    <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
        <div class="w-full min-w-0 max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-center text-xs border-collapse min-w-[850px]">
                <thead>
                    <tr class="bg-slate-900 border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px] font-semibold text-center">
                        <th class="p-4 w-40 text-center">Timestamp</th>
                        <th class="p-4 text-center">User</th>
                        <th class="p-4 w-28 text-center">Action</th>
                        <th class="p-4 text-center">Target Table</th>
                        <th class="p-4 w-24 text-center">Record ID</th>
                        <th class="p-4 text-center">Old State</th>
                        <th class="p-4 text-center">New State</th>
                    </tr>
                </thead>
                <tbody id="super-admin-logs-tbody" class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center font-sans">
                    <!-- Injected JS audit log rows -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Pagination Toolbar -->
    <div id="super-admin-logs-pagination-bottom" class="my-4"></div>
</div>
</div>
