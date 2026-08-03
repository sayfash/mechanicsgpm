<!-- ========================================== -->
<!-- MODULE: SHOP ADMIN WORKSPACE               -->
<!-- ========================================== -->
<section id="shop-admin-workspace" class="w-full hidden space-y-6">

    <!-- Top Tab Bar: Live Branch Stock vs Spare Part Usage History -->
    <div class="flex items-center justify-between glass-panel rounded-2xl p-2.5 shadow-lg border border-slate-800/80">
        <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none max-w-full">
            <button onclick="setShopAdminTab('inventory')" id="btn-shop-tab-inventory"
                class="px-5 py-2.5 font-bold text-xs rounded-xl transition duration-200 bg-blue-600/10 text-blue-400 border border-blue-500/20 shadow-md flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-sm"></i> Live Branch Stock
            </button>
            <button onclick="setShopAdminTab('history')" id="btn-shop-tab-history"
                class="px-5 py-2.5 font-bold text-xs rounded-xl transition duration-200 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-sm"></i> Spare Part Usage History
            </button>
        </div>
    </div>

    <!-- Sub-Tab 1: Live Inventory Stock -->
    <div id="shop-panel-inventory" class="space-y-4">
        <!-- Streamlined Integrated Header & Controls Container -->
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
                        <span id="shop-admin-branch-badge" class="text-xs font-bold text-blue-400">Downtown Main Office</span>
                    </div>
                    <button onclick="openShopAdminAddInventoryModal()"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-plus"></i> Add New Item
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
                    <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i
                            class="fa-solid fa-arrow-down-short-wide text-blue-400"></i>Sort:</span>
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
                    <div id="shop-admin-inv-column-menu" class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-slate-950/95 backdrop-blur-xl border border-slate-800 shadow-2xl p-3 z-[100] text-xs space-y-2">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 mb-1">
                                <span class="font-bold text-slate-200 uppercase tracking-wider text-[10px]">Show / Hide Columns</span>
                                <button type="button" onclick="resetShopInvColumns()" class="text-[10px] text-cyan-400 hover:underline font-semibold">Select All</button>
                            </div>
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

        <!-- Top Pagination Toolbar -->
        <div id="shop-admin-inventory-pagination-top"></div>

        <!-- Inventory Spreadsheet Table -->
        <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto lg:overflow-x-hidden">
                <table class="w-full text-center text-sm border-collapse table-auto lg:table-fixed">
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
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="shop-admin-inventory-tbody"
                        class="divide-y divide-slate-800/80 text-slate-300 text-xs font-sans">
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
        <div
            class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 glass-panel rounded-2xl p-6 shadow-xl">
            <div class="w-full flex-1">
                <h2 class="text-2xl font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i> Spare Part Usage History
                </h2>
                <p class="text-sm text-slate-400 mt-1 w-full">Audit log of all spare parts taken and used in
                    maintenance repair jobs.</p>
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
                            <span class="text-xs text-slate-400 font-semibold uppercase"><i
                                    class="fa-solid fa-filter text-cyan-400 mr-1"></i>Period:</span>
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
                        <span class="text-xs text-slate-400 font-semibold uppercase"><i
                                class="fa-solid fa-tags text-purple-400 mr-1"></i>Category:</span>
                        <select id="parts-hist-category-select" onchange="filterPartsHistory()"
                            class="px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold">
                            <!-- Populated dynamically -->
                        </select>
                    </div>
                </div>
            </div>

            <!-- Usage Records Table -->
            <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-center text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-900 border-b border-slate-800 text-slate-300 text-center">
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs w-44 text-center">
                                    Date & Time</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center">Job
                                    ID</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center">Spare
                                    Part & Category</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs w-28 text-center">
                                    Qty Taken</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs w-36 text-center">
                                    Unit Price</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs w-40 text-center">
                                    Total Billed</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center">
                                    Vehicle & Customer</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center">
                                    Assigned Mechanic</th>
                            </tr>
                        </thead>
                        <tbody id="parts-history-list-tbody"
                            class="divide-y divide-slate-800/80 text-slate-300 text-xs font-sans">
                            <!-- Injected JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MONTHLY VIEW MODE CONTAINER -->
        <div id="parts-history-monthly-container" class="hidden space-y-6">
            <div id="parts-history-monthly-cards" class="space-y-6">
                <!-- Injected JS monthly summary cards & breakdown tables -->
            </div>
        </div>
    </div>
</section>
