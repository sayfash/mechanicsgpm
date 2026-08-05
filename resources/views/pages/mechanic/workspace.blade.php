<!-- ========================================== -->
<!-- MODULE: MECHANIC WORKSPACE                 -->
<!-- ========================================== -->
<section id="mechanic-workspace" class="w-full hidden space-y-8 mt-0 pt-0">
    <!-- Branch Selection Check-In Banner -->
    <div id="mechanic-checkin-banner" class="hidden glass-panel rounded-xl p-6 border-l-4 border-amber-500">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-map-location-dot text-2xl text-amber-500"></i>
                <div>
                    <h3 class="font-bold text-slate-200">Branch Check-In Required</h3>
                    <p class="text-sm text-slate-400">You must check into an operational branch to intake cars
                        and record parts usage.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select id="mechanic-branch-select"
                    class="w-full md:w-60 px-3 py-2 rounded-lg text-sm"></select>
                <button onclick="handleMechanicCheckin()"
                    class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-sm transition">
                    Check In
                </button>
            </div>
        </div>
    </div>

    <!-- Container 1: Active Branch Station Header -->
    <div id="mechanic-active-banner"
        class="hidden flex items-center justify-between glass-panel rounded-xl px-6 py-4 mb-4">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl text-blue-400"></i>
            <div>
                <span class="text-xs text-slate-400 uppercase tracking-widest">Active Branch Station</span>
                <h4 id="mechanic-active-branch-name" class="font-bold text-slate-200">Downtown Main Office</h4>
            </div>
        </div>
        <button onclick="changeBranch()"
            class="px-3.5 py-1.5 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrows-rotate"></i> Switch Station
        </button>
    </div>

    <!-- TAB 1: Intake & Repair Sheet -->
    <!-- Intake Form moved to Shop Admin Workspace -->

    <!-- TAB 2: Review History Logs (Table / Calendar Views) -->
    <div id="mechanic-tab-review" class="hidden space-y-6">
        <!-- Search panel -->
        <div class="flex flex-col md:flex-row items-stretch md:items-end gap-4 glass-panel rounded-2xl p-6">
            <div class="w-full flex-1">
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Search
                    Maintenance History (Customer ID / ID Card Number)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i
                            class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="mech-history-query"
                        class="w-full pl-9 pr-3 py-2.5 rounded-lg text-sm bg-slate-950 border border-slate-800 font-mono"
                        placeholder="e.g. 1 or 32012345...">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <button onclick="fetchMechanicHistoryRecords()"
                    class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-blue-500/10 flex items-center justify-center">
                    Search Records
                </button>

                <div class="hidden sm:block h-10 w-px bg-slate-800"></div>

                <!-- Toggle switcher -->
                <div class="flex w-full sm:w-auto bg-slate-950 p-1 rounded-lg border border-slate-800">
                    <button onclick="setHistoryViewMode('table')" id="btn-history-view-table"
                        class="flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 bg-slate-800 text-slate-200 flex items-center justify-center">
                        <i class="fa-solid fa-list mr-1"></i> List Table
                    </button>
                    <button onclick="setHistoryViewMode('calendar')" id="btn-history-view-calendar"
                        class="flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 text-slate-400 hover:text-slate-200 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days mr-1"></i> Calendar Grid
                    </button>
                </div>
            </div>
        </div>

        <!-- Table View Interface -->
        <div id="mech-history-table-view" class="glass-panel rounded-2xl overflow-hidden shadow-2xl space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-300">
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-40">Date</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Branch Station</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Vehicle License</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Diagnostic Issues
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">KM Reached</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-28 text-center">
                                Billed Parts</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-20">Details</th>
                        </tr>
                    </thead>
                    <tbody id="mech-history-list-tbody"
                        class="divide-y divide-slate-800/80 text-slate-300 text-xs font-mono">
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500 font-sans">Provide Customer ID or ID
                                Card above to search logs.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calendar View Interface -->
        <div id="mech-history-calendar-view" class="glass-panel rounded-2xl p-6 shadow-2xl hidden space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h4 class="text-md font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-blue-400"></i> Interactive Repair Monthly Calendar
                </h4>
                <div class="flex items-center gap-2">
                    <button onclick="adjustCalendarMonth(-1)"
                        class="w-8 h-8 rounded bg-slate-900 border border-slate-800 hover:bg-slate-800 transition text-slate-300"><i
                            class="fa-solid fa-chevron-left text-xs"></i></button>
                    <span id="calendar-month-year-label"
                        class="text-sm font-bold text-slate-200 w-36 text-center block">July 2026</span>
                    <button onclick="adjustCalendarMonth(1)"
                        class="w-8 h-8 rounded bg-slate-900 border border-slate-800 hover:bg-slate-800 transition text-slate-300"><i
                            class="fa-solid fa-chevron-right text-xs"></i></button>
                </div>
            </div>

            <!-- Month Calendar Grid -->
            <div class="grid grid-cols-7 gap-2 text-center text-xs">
                <!-- Days Headers -->
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Sun</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Mon</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Tue</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Wed</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Thu</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Fri</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Sat</div>
            </div>
            <div id="calendar-days-grid" class="grid grid-cols-7 gap-2 text-center text-xs">
                <!-- Days populated by JavaScript -->
            </div>

            <div class="text-[10px] text-slate-500 flex items-center gap-1.5 justify-end">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> Indicates repair events
                logged on this date.
            </div>
        </div>
    </div>

    <!-- TAB 3: My Repair History -->
    <div id="mechanic-tab-my-history" class="hidden space-y-6">
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel rounded-2xl p-6">
            <div>
                <h3 class="text-xl font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-400"></i> My Completed Repair Jobs
                </h3>
                <p class="text-sm text-slate-400 mt-1">Review the list of repair tickets submitted by your
                    account.</p>
            </div>
        </div>

        <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-300">
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-40">Date & Time</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Customer Assigned
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">License Plate</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-32">Repair Time</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Job Notes & Checklist
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-32 text-right">Parts
                                Details</th>
                        </tr>
                    </thead>
                    <tbody id="mechanic-my-history-tbody" class="divide-y divide-slate-800/80 text-slate-300 font-mono">
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 font-semibold font-sans">No records
                                found. Submit jobs to see them here!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
