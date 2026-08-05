<!-- ========================================== -->
<!-- MODULE: SHOP ADMIN WORKSPACE               -->
<!-- ========================================== -->
<section id="shop-admin-workspace" class="w-full hidden space-y-6 mt-6 pt-0">

    <!-- Sub-Tab 1: Live Branch Inventory Stock -->
    <div id="shop-panel-inventory" class="hidden space-y-4">
        <!-- Integrated Header & Controls Container -->
        <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
            <!-- Top Row: Title, Branch Badge & Action Button -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-blue-400"></i> Live Branch Inventory Ledger
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Manage spare part stock levels and price tags for your assigned branch.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                    <div class="bg-slate-900 px-3.5 py-1.5 rounded-xl border border-slate-800 flex items-center justify-between sm:justify-start gap-2 w-full sm:w-auto">
                        <span class="text-xs text-slate-400 uppercase font-semibold">Branch:</span>
                        <span id="shop-admin-branch-badge" class="text-xs font-bold text-blue-400"></span>
                    </div>
                    <button onclick="openShopAdminAddInventoryModal()"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-plus"></i> Add New Item <span id="shop-admin-add-branch" class="ml-1 text-xs text-blue-300"></span>
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
                    <input type="text" id="shop-admin-inventory-search" oninput="filterShopAdminInventory()"
                        placeholder="Search SKU, Name, or Description..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                </div>

                <!-- Hidden / Contextual Branch Filter (Required for JS compatibility) -->
                <select id="shop-admin-branch-filter" onchange="filterShopAdminInventory()" class="hidden"></select>

                <!-- Column Filter Group -->
                <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                    <select id="shop-admin-inv-column-select" onchange="filterShopAdminInventory()"
                        class="w-full flex-1 min-w-[120px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                        <option value="">Column Filter...</option>
                        <option value="sku">SKU Code</option>
                        <option value="part_name">Part Name</option>
                        <option value="description">Description</option>
                        <option value="category">Category</option>
                        <option value="available_qty">Min Qty</option>
                        <option value="price">Min Price (IDR)</option>
                    </select>
                    <input type="text" id="shop-admin-inv-column-val" oninput="filterShopAdminInventory()"
                        placeholder="Value..."
                        class="w-full flex-1 min-w-[90px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                </div>

                <!-- Sort Control -->
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-arrow-down-short-wide text-blue-400"></i>Sort:
                    </span>
                    <select id="shop-admin-inv-sort-select" onchange="filterShopAdminInventory()"
                        class="w-full flex-1 min-w-[130px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                        <option value="updated_at-desc">Updated (Newest First)</option>
                        <option value="sku-asc">SKU (A-Z)</option>
                        <option value="part_name-asc">Part Name (A-Z)</option>
                        <option value="category-asc">Category (A-Z)</option>
                        <option value="available_qty-desc">Qty (Highest First)</option>
                        <option value="available_qty-asc">Qty (Lowest First)</option>
                        <option value="price-desc">Price (Highest First)</option>
                        <option value="price-asc">Price (Lowest First)</option>
                    </select>
                </div>

                <!-- Visible Columns Dropdown Toggle -->
                <div class="relative inline-block text-left flex-1 min-w-[130px] sm:flex-initial">
                    <button type="button" id="btn-shop-inv-column-toggle" onclick="toggleShopInvColumnMenu(event)"
                        class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold hover:bg-slate-800 transition flex items-center justify-between gap-2 shadow-sm">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-columns text-cyan-400"></i> Columns
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                    </button>

                    <!-- Dropdown Popover Menu -->
                    <div id="shop-admin-inv-column-menu"
                        class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-slate-950/95 backdrop-blur-xl border border-slate-800 shadow-2xl p-3 z-[100] text-xs space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 mb-1">
                            <span class="font-bold text-slate-200 uppercase tracking-wider text-[10px]">Show / Hide Columns</span>
                            <button type="button" onclick="resetShopInvColumns()" class="text-[10px] text-cyan-400 hover:underline font-semibold">Select All</button>
                        </div>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto custom-scrollbar">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="sku" checked onchange="toggleShopInvColumn('sku', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>SKU Code</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="part_name" checked onchange="toggleShopInvColumn('part_name', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Part Name</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="description" checked onchange="toggleShopInvColumn('description', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Description</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="category" checked onchange="toggleShopInvColumn('category', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Category</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="available_qty" checked onchange="toggleShopInvColumn('available_qty', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Available Qty</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="price" checked onchange="toggleShopInvColumn('price', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Price (IDR)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="updated_at" checked onchange="toggleShopInvColumn('updated_at', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Last Updated</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="actions" checked onchange="toggleShopInvColumn('actions', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Actions</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Pagination Toolbar -->
        <div id="shop-admin-inventory-pagination-top" class="my-4"></div>

        <!-- Inventory Spreadsheet Table -->
        <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-center text-sm border-collapse table-auto">
                    <thead>
                        <tr id="shop-admin-inventory-thead-tr" class="bg-slate-900 border-b border-slate-800 text-slate-300 text-center">
                            <th onclick="toggleSortShopInventory('sku')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[13%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                SKU <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortShopInventory('part_name')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[22%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                Part Name <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[25%] text-center">
                                Description
                            </th>
                            <th onclick="toggleSortShopInventory('category')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[12%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                Category <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortShopInventory('available_qty')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[9%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                Available Qty <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortShopInventory('price')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[11%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                Price (IDR) <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortShopInventory('updated_at')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[8%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                Last Updated <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[8%] text-center">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="shop-admin-inventory-tbody" class="divide-y divide-slate-800/80 text-slate-300 text-xs font-sans">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Pagination Toolbar -->
        <div id="shop-admin-inventory-pagination-bottom"></div>
    </div>

    <!-- Sub-Tab 2: Spare Part Usage History -->
    <div id="shop-panel-history" class="hidden space-y-6">
        <!-- Title Container & Mode Selector -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 glass-panel rounded-2xl p-6 shadow-xl">
            <div class="w-full flex-1">
                <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i> Spare Part Usage History
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Audit log of all spare parts taken and used in maintenance repair jobs for your branch.</p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Mode Selector: List View vs Monthly View -->
                <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 w-full sm:w-auto">
                    <button onclick="setPartsHistoryMode('list')" id="btn-parts-hist-mode-list"
                        class="flex-1 sm:flex-none w-full sm:w-auto px-3.5 py-1.5 font-bold text-xs rounded-lg transition bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-list"></i> List View
                    </button>
                    <button onclick="setPartsHistoryMode('monthly')" id="btn-parts-hist-mode-monthly"
                        class="flex-1 sm:flex-none w-full sm:w-auto px-3.5 py-1.5 font-bold text-xs rounded-lg transition text-slate-400 hover:text-slate-200 hover:bg-slate-800 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-calendar-days"></i> Monthly View
                    </button>
                </div>
            </div>
        </div>

        <!-- LIST VIEW MODE CONTAINER -->
        <div id="parts-history-list-container" class="space-y-6">
            <!-- Controls & Date Filter Toolbar -->
            <div class="glass-panel rounded-2xl p-5 shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <!-- Left Group: Quick Time Filter & Custom Date Range -->
                    <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[320px]">
                        <!-- Search Box -->
                        <div class="relative min-w-[180px] flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </span>
                            <input type="text" id="parts-hist-search" oninput="filterPartsHistory()"
                                placeholder="Search Part, SKU, Plate, Customer, Mechanic..."
                                class="w-full pl-9 pr-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                        </div>

                        <!-- Time Filter Select (Day, Week, Range) -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-slate-400 font-semibold uppercase">
                                <i class="fa-solid fa-filter text-cyan-400 mr-1"></i>Period:
                            </span>
                            <select id="parts-hist-period-select" onchange="handlePartsHistoryPeriodChange()"
                                class="px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold">
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="this_week">This Week</option>
                                <option value="custom_date">Filter by Date...</option>
                                <option value="custom_range">Custom Date Range...</option>
                            </select>
                        </div>

                        <!-- Dynamic Date Pickers -->
                        <div id="parts-hist-date-single-wrap" class="hidden items-center gap-1.5">
                            <input type="date" id="parts-hist-single-date" onchange="filterPartsHistory()"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200">
                        </div>

                        <div id="parts-hist-date-range-wrap" class="hidden items-center gap-1.5">
                            <input type="date" id="parts-hist-range-start" onchange="filterPartsHistory()"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200"
                                placeholder="From">
                            <span class="text-slate-500 text-xs">to</span>
                            <input type="date" id="parts-hist-range-end" onchange="filterPartsHistory()"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200"
                                placeholder="To">
                        </div>
                    </div>

                    <!-- Right Group: Category Filter -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-semibold uppercase">
                            <i class="fa-solid fa-tags text-purple-400 mr-1"></i>Category:
                        </span>
                        <select id="parts-hist-category-select" onchange="filterPartsHistory()"
                            class="px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold">
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto lg:overflow-x-hidden">
                    <table class="w-full text-center text-sm border-collapse table-auto lg:table-fixed">
                        <thead>
                            <tr id="shop-admin-history-thead-tr" class="bg-slate-900 border-b border-slate-800 text-slate-300 text-center">
                                <th class="p-4 font-semibold text-xs whitespace-nowrap lg:w-[13%]">Date & Time</th>
                                <th class="p-4 font-semibold text-xs whitespace-nowrap lg:w-[10%]">Job ID</th>
                                <th class="p-4 font-semibold text-xs whitespace-nowrap lg:w-[22%]">Part Name & SKU</th>
                                <th class="p-4 font-semibold text-xs whitespace-nowrap lg:w-[8%]">Qty Used</th>
                                <th class="p-4 font-semibold text-xs whitespace-nowrap lg:w-[12%]">Unit Price</th>
                                <th class="p-4 font-semibold text-xs whitespace-nowrap lg:w-[13%]">Subtotal Cost</th>
                                <th class="p-4 font-semibold text-xs whitespace-nowrap lg:w-[12%]">Customer / Vehicle</th>
                                <th class="p-4 font-semibold text-xs whitespace-nowrap lg:w-[10%]">Mechanic</th>
                            </tr>
                        </thead>
                        <tbody id="parts-history-list-tbody" class="divide-y divide-slate-800/80 text-slate-300 text-xs font-sans">
                            <!-- Injected JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Pagination -->
            <div id="shop-admin-history-pagination-bottom" class="my-4"></div>
        </div>

        <!-- MONTHLY VIEW MODE CONTAINER -->
        <div id="parts-history-monthly-container" class="hidden space-y-6">
            <div id="parts-history-monthly-cards" class="space-y-6">
                <!-- Injected JS monthly summary cards & breakdown tables -->
            </div>
        </div>
    </div>

    <!-- Sub-Tab 3: Intake & Repair Sheet -->
    <div id="shop-panel-intake" class="space-y-6">
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <!-- Left Side: Active Jobs List (1 column) -->
            <div class="xl:col-span-1 space-y-4">
                <div class="glass-panel rounded-2xl p-5 shadow-xl border border-slate-800">
                    <h3 class="text-sm font-bold text-slate-200 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-blue-400"></i> Active Jobs (In Progress)
                    </h3>
                    <div id="shop-active-jobs-list" class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar">
                        <p class="text-xs text-slate-500 italic">No active jobs in progress.</p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Intake Form (3 columns) -->
            <div class="xl:col-span-3">
                <div class="glass-panel rounded-2xl p-6 shadow-2xl relative overflow-hidden">
                    <form onsubmit="submitMechanicJobRecord(event)" class="space-y-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4 mb-6">
                        <div class="w-full sm:flex-1">
                            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-file-medical text-blue-400"></i> Auto-Intake Repair Sheet
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5 w-full">Generate job numbers, search customer databases, assign mechanics, log issues, and track task durations.</p>
                        </div>
                        <div class="w-full sm:w-auto text-left sm:text-right shrink-0">
                            <span class="text-[10px] text-slate-500 uppercase font-bold block mb-0.5">Assigned Job ID</span>
                            <span id="mech-job-id" class="inline-block whitespace-nowrap text-sm font-mono font-bold text-blue-400 bg-blue-100/10 border border-blue-800/40 px-3 py-1 rounded">JOB-PENDING</span>
                        </div>
                    </div>

                    <!-- Step 1: Assign Mechanic Specialist -->
                    <div class="bg-slate-900/40 border border-slate-800 p-4 rounded-xl mb-6 space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <h4 class="text-xs font-bold text-blue-300 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">1</span>
                                Assign Mechanic Specialist
                            </h4>
                            <span class="text-[10px] text-blue-400/80 font-mono font-semibold"><i class="fa-solid fa-asterisk text-[8px] mr-1"></i>Required for Dispatch</span>
                        </div>
                        <div class="max-w-md">
                            <label class="block text-[10px] text-blue-400 uppercase tracking-wider mb-1 font-bold"><i class="fa-solid fa-user-gear mr-1"></i>Select Mechanic</label>
                            <select id="mech-assign-mechanic" required class="w-full px-3.5 py-2.5 rounded-lg bg-slate-950 border border-blue-500/40 text-blue-300 font-bold focus:border-blue-500 focus:outline-none text-sm">
                                <option value="">-- Choose Mechanic --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Step 2: Customer Type & File Verification -->
                    <div class="bg-slate-900/40 border border-slate-800 p-4 rounded-xl mb-6 space-y-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <h4 class="w-full sm:w-auto text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">2</span>
                                Customer Type & Verification
                            </h4>
                            <div class="flex rounded-lg bg-slate-950 p-1 border border-slate-800 w-full sm:w-auto">
                                <button type="button" id="btn-cust-type-external" onclick="setMechanicCustomerType('external')" class="flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 bg-blue-600 text-white shadow-md flex items-center justify-center">
                                    <i class="fa-solid fa-user mr-1.5"></i>External Customer
                                </button>
                                <button type="button" id="btn-cust-type-internal" onclick="setMechanicCustomerType('internal')" class="flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 text-slate-400 hover:text-slate-200 flex items-center justify-center">
                                    <i class="fa-solid fa-building-user mr-1.5"></i>Internal Customer
                                </button>
                            </div>
                        </div>
                        <div id="mech-lookup-section" class="pt-2 border-t border-slate-800/60 hidden">
                            <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-1.5">Search Customer ID or ID Card Number</label>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <div class="relative w-full flex-1">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i class="fa-solid fa-id-card"></i></span>
                                    <input type="text" id="mech-search-query" class="w-full pl-9 pr-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200" placeholder="e.g. 1 or 32012345...">
                                </div>
                                <button type="button" onclick="performMechanicCustomerLookup()" class="w-full sm:w-auto px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-blue-500/10 shrink-0 flex items-center justify-center">Verify Customer</button>
                            </div>
                        </div>
                    </div>

                    <!-- Client Intake Form Layout -->
                    <!-- Step 3: Vehicle & Customer Details -->
                        <div class="bg-slate-900/20 border border-slate-800/80 rounded-xl p-4 space-y-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                                <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">3</span>
                                    Vehicle & Customer Details
                                </h4>
                                <span id="mech-editable-mode-badge" class="hidden text-[10px] text-emerald-400 bg-emerald-100/10 border border-emerald-800/60 px-2.5 py-0.5 rounded-full font-semibold">
                                    <i class="fa-solid fa-user-plus mr-1"></i>New Customer Creation Allowed
                                </span>
                            </div>
                            <input type="hidden" id="mech-selected-vehicle-id">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5 text-xs">
                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Customer Name</label>
                                    <input type="text" id="mech-display-name" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. John Doe">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Phone Number</label>
                                    <input type="text" id="mech-display-phone" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono focus:outline-none transition duration-150" placeholder="e.g. 08123456789">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">ID Card Number</label>
                                    <input type="text" id="mech-display-idcard" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono focus:outline-none transition duration-150" placeholder="e.g. 320100000000">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Address</label>
                                    <input type="text" id="mech-display-address" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. Jl. Sudirman No. 12">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-blue-400 uppercase tracking-wider mb-1 font-bold"><i class="fa-solid fa-user-tag mr-1"></i>Customer Status</label>
                                    <select id="mech-customer-status" onchange="onMechanicCustomerStatusChange()" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-blue-500/30 text-blue-300 font-bold focus:border-blue-500 focus:outline-none">
                                        <option value="Retail">Retail</option>
                                        <option value="Gomolis">Gomolis</option>
                                        <option value="Cocoride">Cocoride</option>
                                        <option value="Sales">Sales</option>
                                        <option value="Operasional">Operasional</option>
                                        <option value="TPI">TPI</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 text-xs">
                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">License Plate</label>
                                    <input type="text" id="mech-display-plate" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. B 1234 ABC">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Vehicle Type</label>
                                    <input type="text" id="mech-display-type" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. Gesits G1">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Frame Number (VIN)</label>
                                    <input type="text" id="mech-display-frame" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. MH123456789">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Controller Number</label>
                                    <input type="text" id="mech-display-controller" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. CTRL-9876">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Diagnostic Checklists -->
                        <div class="space-y-4 bg-slate-900/20 border border-slate-800/80 rounded-xl p-4">
                            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">3</span>
                                Issue Diagnostic Log
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div>
                                    <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold">KM Reached (Odometer)</label>
                                    <input type="number" id="mech-km-reached" required class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 font-mono" placeholder="e.g. 15200">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold">Repair Category</label>
                                    <select id="mech-repair-category" class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                                        <option value="Repair">Repair</option>
                                        <option value="Claim">Claim</option>
                                        <option value="Stock">Stock</option>
                                        <option value="Waiting">Waiting</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div>
                                    <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold">Common Issues Checklist</label>
                                    <div id="mech-common-issues-container" class="grid grid-cols-2 gap-2 text-xs text-slate-300"></div>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold">Required Mechanic Checklist Tasks</label>
                                    <div id="mech-form-items-container" class="grid grid-cols-2 gap-2 text-xs text-slate-300"></div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Other Custom Issues & Service Details</label>
                                <textarea id="mech-other-issues" rows="2" class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800" placeholder="Describe other diagnostics or client concerns..."></textarea>
                            </div>
                        </div>

                        <!-- Step 4: Parts Checklist Accumulator -->
                        <div class="space-y-4 bg-slate-900/20 border border-slate-800/80 rounded-xl p-4">
                            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">4</span>
                                Spare Parts Checklist Accumulator (Max 25)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
                                <div class="col-span-3 space-y-2">
                                    <div>
                                        <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1">Search Sparepart</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-500"><i class="fa-solid fa-magnifying-glass text-[10px]"></i></span>
                                            <input type="text" id="mech-part-search-input" oninput="filterMechanicPartOptions()" class="w-full pl-8 pr-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200 font-mono" placeholder="Type to search...">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1">Select Sparepart</label>
                                        <select id="mech-part-select" class="w-full px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-300 font-mono"></select>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-end">
                                    <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1">Qty & Options</label>
                                    <div class="flex gap-2 items-center">
                                        <input type="number" id="mech-part-qty" value="1" min="1" class="w-16 px-2 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-300 text-center font-mono">
                                        <label id="mech-part-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-blue-400 font-bold cursor-pointer select-none shrink-0">
                                            <input type="checkbox" id="mech-part-charged-input" class="w-4 h-4 accent-blue-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                            <span>Charged</span>
                                        </label>
                                        <button type="button" onclick="addPartToMechAccumulator()" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shrink-0">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-950/60 rounded-lg p-3 border border-slate-900 text-xs max-h-40 overflow-auto custom-scrollbar">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr id="mech-parts-accumulator-thead-tr" class="text-slate-500 border-b border-slate-800">
                                            <th class="pb-1 font-semibold text-[10px] uppercase">Part SKU & Name</th>
                                            <th class="pb-1 font-semibold text-[10px] uppercase w-20 text-right">Price</th>
                                            <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right">Qty</th>
                                            <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mech-parts-accumulator-tbody" class="divide-y divide-slate-800/40 text-slate-300 font-mono">
                                        <tr><td colspan="4" class="py-3 text-center text-slate-600 text-[10px]">No spareparts accumulated.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-[10px] text-slate-500 text-right mt-1">Total Items: <span id="mech-accumulated-total-count" class="font-bold text-slate-400 font-mono">0</span> / 25</div>
                        </div>

                        <!-- Step 5: Service Options & Other Services -->
                        <div class="bg-slate-900/20 border border-slate-800/80 rounded-xl p-4 space-y-4">
                            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">5</span>
                                Service Options & Other Services
                            </h4>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
                                    <h5 class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-wrench"></i> Service Options</h5>
                                    <div class="flex gap-2 items-center">
                                        <select id="mech-service-select" class="flex-1 px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200"><option value="">-- Choose Service Option --</option></select>
                                        <label id="mech-service-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-cyan-400 font-bold cursor-pointer select-none shrink-0">
                                            <input type="checkbox" id="mech-service-charged-input" class="w-4 h-4 accent-cyan-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                            <span>Charged</span>
                                        </label>
                                        <button type="button" onclick="addServiceToMechAccumulator()" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-xs shrink-0">+</button>
                                    </div>
                                    <div class="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 max-h-32 overflow-y-auto custom-scrollbar">
                                        <div id="mech-services-accumulator-list" class="space-y-1 text-xs text-slate-300 font-mono">
                                            <span class="text-slate-600 text-[10px] block text-center py-2">No service options added.</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
                                    <h5 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-hand-holding-dollar"></i> Other Services</h5>
                                    <div class="flex gap-2 items-center">
                                        <select id="mech-other-service-select" class="flex-1 px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200"><option value="">-- Choose Other Service --</option></select>
                                        <label id="mech-other-service-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-amber-400 font-bold cursor-pointer select-none shrink-0">
                                            <input type="checkbox" id="mech-other-service-charged-input" class="w-4 h-4 accent-amber-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                            <span>Charged</span>
                                        </label>
                                        <button type="button" onclick="addOtherServiceToMechAccumulator()" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-xs shrink-0">+</button>
                                    </div>
                                    <div class="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 max-h-32 overflow-y-auto custom-scrollbar">
                                        <div id="mech-other-services-accumulator-list" class="space-y-1 text-xs text-slate-300 font-mono">
                                            <span class="text-slate-600 text-[10px] block text-center py-2">No other services added.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 6: Estimated Grand Total Banner -->
                        <div class="glass-panel rounded-xl p-4 border border-emerald-500/30 bg-slate-900 flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="space-y-1">
                                <h5 class="text-xs font-bold text-emerald-400 uppercase tracking-widest flex items-center gap-2"><i class="fa-solid fa-calculator"></i> Estimated Grand Total Summary</h5>
                                <div class="flex items-center gap-4 text-xs text-slate-400">
                                    <span>Services: <strong id="summary-services-fee" class="text-cyan-400 font-mono">Rp 0</strong></span>
                                    <span>•</span>
                                    <span>Other Services: <strong id="summary-other-fee" class="text-amber-400 font-mono">Rp 0</strong></span>
                                    <span>•</span>
                                    <span>Spareparts: <strong id="summary-parts-fee" class="text-blue-400 font-mono">Rp 0</strong></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Estimated Grand Total</span>
                                <span id="summary-grand-total" class="text-2xl font-extrabold text-emerald-400 font-mono">Rp 0</span>
                            </div>
                        </div>

                        <!-- Step 7: Timer & Final Submission Controls -->
                        <div class="glass-panel rounded-xl p-4 border border-slate-800 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-6 mt-6">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-6 w-full md:w-auto">
                                <div class="bg-slate-950 border border-slate-800 rounded-xl px-5 py-2.5 text-center font-mono relative overflow-hidden w-full sm:w-auto">
                                    <div class="absolute inset-0 bg-blue-500/5 pulse-bg z-0 pointer-events-none"></div>
                                    <span class="text-[9px] uppercase tracking-widest text-slate-500 font-bold block mb-0.5">Repair Timer</span>
                                    <span id="mech-timer-display" class="text-2xl font-bold text-blue-400 z-10 relative font-mono">00:00:00</span>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                                    <button type="button" id="btn-mech-start-timer" onclick="startMechanicTimerClock()" class="w-full sm:w-auto px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10"><i class="fa-solid fa-play"></i> Start Fixing</button>
                                    <button type="button" id="btn-mech-stop-timer" onclick="stopMechanicTimerClock()" disabled class="w-full sm:w-auto px-5 py-3 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/25 font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 disabled:opacity-30 disabled:cursor-not-allowed"><i class="fa-solid fa-stop"></i> Stop Timer</button>
                                </div>
                                <div class="flex flex-col text-[10px] text-slate-500 gap-1 ml-0 sm:ml-2 font-semibold">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        <span>Start: <span id="mech-start-timestamp" class="text-slate-300 font-mono">-</span></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Stop: <span id="mech-stop-timestamp" class="text-slate-300 font-mono">-</span></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btn-mech-submit-record" disabled class="w-full md:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-emerald-600/20 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-check"></i> Submit Repair Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>