<!-- SUB-TAB: Vehicles Inventory Directory -->
<div id="super-admin-tab-vehicles" class="space-y-4 hidden mt-0 pt-0">
    <!-- Streamlined Integrated Header & Controls Container -->
    <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
        <!-- Top Row: Title, Description & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-motorcycle text-cyan-400"></i> Vehicles Directory & Inventory
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Directory of all client vehicles, specifications, VIN numbers, and bound customer owners.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                <button onclick="openVehiclesCSVModal()"
                    class="px-3.5 py-2 bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 border border-cyan-500/30 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-file-excel text-cyan-400"></i> Upload Batch Vehicles
                </button>
                <button onclick="openAddVehicleModal()"
                    class="px-3.5 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-cyan-500/20 shrink-0">
                    <i class="fa-solid fa-motorcycle"></i> Add New Vehicle
                </button>
            </div>
        </div>

        <!-- Bottom Row: Integrated Responsive Controls -->
        <div class="flex flex-wrap items-center gap-3 w-full">
            <!-- Search Box -->
            <div class="relative min-w-[200px] flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" id="super-admin-vehicles-search"
                    oninput="filterVehiclesAndCustomers()"
                    placeholder="Search license plate, brand/model, VIN, customer..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
            </div>

            <!-- Branch Filter -->
            <select id="super-admin-vehicles-branch-filter" onchange="filterVehiclesAndCustomers()"
                class="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                <option value="">All Branches</option>
            </select>

            <!-- Custom Column Filter Group -->
            <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                <select id="super-admin-vehicles-column-select" onchange="filterVehiclesAndCustomers()"
                    class="w-full flex-1 min-w-[120px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                    <option value="">Column Filter...</option>
                    <option value="license_plate">License Plate</option>
                    <option value="make">Brand / Model</option>
                    <option value="color">Color</option>
                    <option value="vin">VIN (Frame No)</option>
                    <option value="engine_number">Engine No</option>
                    <option value="controller_number">Controller Code</option>
                    <option value="customer_name">Owner Name</option>
                </select>
                <input type="text" id="super-admin-vehicles-column-val" oninput="filterVehiclesAndCustomers()"
                    placeholder="Value..."
                    class="w-full flex-1 min-w-[90px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
            </div>

            <!-- Sort Control -->
            <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i
                        class="fa-solid fa-arrow-down-short-wide text-cyan-400"></i>Sort:</span>
                <select id="super-admin-vehicles-sort-select" onchange="filterVehiclesAndCustomers()"
                    class="w-full flex-1 min-w-[130px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                    <option value="id-desc">ID (Newest First)</option>
                    <option value="id-asc">ID (Oldest First)</option>
                    <option value="license_plate-asc">License Plate (A-Z)</option>
                    <option value="make-asc">Brand / Model (A-Z)</option>
                    <option value="customer_name-asc">Owner Name (A-Z)</option>
                </select>
            </div>

            <!-- Visible Columns Dropdown Toggle -->
            <div class="relative inline-block text-left flex-1 min-w-[130px] sm:flex-initial">
                <button type="button" id="btn-veh-column-toggle" onclick="toggleVehColumnMenu(event)"
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold hover:bg-slate-800 transition flex items-center justify-between gap-2 shadow-sm">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-columns text-cyan-400"></i> Columns
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                </button>
                <div id="veh-column-menu" class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-slate-950/95 backdrop-blur-xl border border-slate-800 shadow-2xl p-3 z-[100] text-xs space-y-2">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 mb-1">
                        <span class="font-bold text-slate-200 uppercase tracking-wider text-[10px]">Show / Hide Columns</span>
                        <button type="button" onclick="resetVehColumns()" class="text-[10px] text-cyan-400 hover:underline font-semibold">Select All</button>
                    </div>
                    <div class="space-y-1.5 max-h-60 overflow-y-auto custom-scrollbar">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="id" checked onchange="toggleVehColumn('id', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>Veh ID</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="license_plate" checked onchange="toggleVehColumn('license_plate', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>License Plate</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="brand_model" checked onchange="toggleVehColumn('brand_model', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>Brand / Model</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="color" checked onchange="toggleVehColumn('color', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>Color</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="vin" checked onchange="toggleVehColumn('vin', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>VIN (Frame No)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="engine" checked onchange="toggleVehColumn('engine', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>Engine No</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="controller" checked onchange="toggleVehColumn('controller', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>Controller Code</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="owner" checked onchange="toggleVehColumn('owner', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>Assigned Owner</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                            <input type="checkbox" data-veh-col="actions" checked onchange="toggleVehColumn('actions', this.checked)" class="w-3.5 h-3.5 accent-cyan-500 rounded bg-slate-900 border-slate-700">
                            <span>Actions</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Pagination -->
    <div id="super-admin-vehicles-pagination-top" class="my-4"></div>

    <!-- Vehicles Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
        <div class="w-full min-w-0 max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-center text-xs border-collapse min-w-[850px]">
                <thead>
                    <tr id="super-admin-vehicles-thead-tr"
                        class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                        <th class="p-3 w-16 text-center">ID</th>
                        <th class="p-3 w-28 text-center">License Plate</th>
                        <th class="p-3 w-36 text-center">Brand / Model</th>
                        <th class="p-3 w-24 text-center">Color</th>
                        <th class="p-3 w-40 text-center">VIN (Frame No)</th>
                        <th class="p-3 w-36 text-center">Engine No</th>
                        <th class="p-3 w-36 text-center">Controller Code</th>
                        <th class="p-3 min-w-[180px] text-center">Assigned Owner</th>
                        <th class="p-3 w-28 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="super-admin-vehicles-tbody"
                    class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center font-sans">
                    <!-- Injected JS vehicle rows -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Pagination -->
    <div id="super-admin-vehicles-pagination-bottom" class="my-4"></div>
</div>

