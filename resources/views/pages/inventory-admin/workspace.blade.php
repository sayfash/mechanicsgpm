<!-- ========================================== -->
<!-- MODULE: INVENTORY ADMIN WORKSPACE          -->
<!-- ========================================== -->
<section id="inventory-admin-workspace" class="w-full hidden space-y-6 mt-6 pt-0">

    <!-- Sub-Tab 1: Live Branch Inventory Stock -->
    <div id="inv-admin-panel-inventory" class="hidden space-y-4">
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
                        <span id="inv-admin-branch-badge" class="text-xs font-bold text-blue-400"></span>
                    </div>
                    <button onclick="openShopAdminAddInventoryModal()"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-plus"></i> Add New Item <span id="inv-admin-add-branch" class="ml-1 text-xs text-blue-300"></span>
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
                    <input type="text" id="inv-admin-inventory-search" oninput="filterShopAdminInventory()"
                        placeholder="Search SKU, Name, or Description..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                </div>

                <!-- Hidden / Contextual Branch Filter (Required for JS compatibility) -->
                <select id="inv-admin-branch-filter" onchange="filterShopAdminInventory()" class="hidden"></select>

                <!-- Column Filter Group -->
                <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                    <select id="inv-admin-inv-column-select" onchange="filterShopAdminInventory()"
                        class="w-full flex-1 min-w-[120px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                        <option value="">Column Filter...</option>
                        <option value="sku">SKU Code</option>
                        <option value="part_name">Part Name</option>
                        <option value="description">Description</option>
                        <option value="category">Category</option>
                        <option value="available_qty">Min Qty</option>
                        <option value="price">Min Price (IDR)</option>
                    </select>
                    <input type="text" id="inv-admin-inv-column-val" oninput="filterShopAdminInventory()"
                        placeholder="Value..."
                        class="w-full flex-1 min-w-[90px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                </div>

                <!-- Sort Control -->
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-arrow-down-short-wide text-blue-400"></i>Sort:
                    </span>
                    <select id="inv-admin-inv-sort-select" onchange="filterShopAdminInventory()"
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
                    <button type="button" id="btn-inv-admin-inv-column-toggle" onclick="toggleShopInvColumnMenu(event)"
                        class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold hover:bg-slate-800 transition flex items-center justify-between gap-2 shadow-sm">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-columns text-cyan-400"></i> Columns
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                    </button>

                    <!-- Dropdown Popover Menu -->
                    <div id="inv-admin-inv-column-menu"
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
                                <input type="checkbox" data-shop-col="connected_service" checked onchange="toggleShopInvColumn('connected_service', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Connected Service</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="available_qty" checked onchange="toggleShopInvColumn('available_qty', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Available Qty</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="price" checked onchange="toggleShopInvColumn('price', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span>Price Tag (IDR)</span>
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

        <!-- Inventory Table -->
        <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-2xl p-4 shadow-xl relative z-10">
            <div id="inv-admin-inventory-pagination-top" class="mb-3"></div>
            <table class="w-full text-left text-xs border-collapse">
                <thead id="inv-admin-inventory-thead">
                    <!-- Dynamic Headers render in JS -->
                </thead>
                <tbody id="inv-admin-inventory-tbody" class="divide-y divide-slate-800/60 text-slate-300">
                    <!-- Injected JS -->
                </tbody>
            </table>
            <div id="inv-admin-inventory-pagination-bottom" class="mt-3"></div>
        </div>
    </div>

    <!-- Sub-Tab 2: Spare Part Usage History -->
    <div id="inv-admin-panel-history" class="hidden space-y-4">
        <!-- Header & Search Controls Container -->
        <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-400"></i> Spare Part Usage Audit Trail
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Complete record of all spare parts consumed during maintenance operations.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="exportShopAdminHistory()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs transition flex items-center gap-1.5 border border-slate-700 shadow-sm">
                        <i class="fa-solid fa-file-excel text-emerald-400"></i> Export XLS
                    </button>
                </div>
            </div>

            <!-- Integrated Controls Row -->
            <div class="flex flex-wrap items-center gap-3 w-full">
                <!-- Search Box -->
                <div class="relative min-w-[200px] flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="inv-admin-history-search" oninput="filterShopAdminHistory()"
                        placeholder="Search Job #, Mechanic, Vehicle, SKU, Part Name..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                </div>

                <!-- Sort Control -->
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-arrow-down-short-wide text-blue-400"></i>Sort:
                    </span>
                    <select id="inv-admin-history-sort-select" onchange="filterShopAdminHistory()"
                        class="w-full flex-1 min-w-[130px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                        <option value="date-desc">Used Date (Newest First)</option>
                        <option value="date-asc">Used Date (Oldest First)</option>
                        <option value="job-asc">Job Code (A-Z)</option>
                        <option value="qty-desc">Quantity (Highest First)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-2xl p-4 shadow-xl relative z-10">
            <div id="inv-admin-history-pagination-top" class="mb-3"></div>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="p-3 text-center">Job Code</th>
                        <th class="p-3 text-center">Used Date</th>
                        <th class="p-3">Mechanic</th>
                        <th class="p-3">Vehicle</th>
                        <th class="p-3 font-mono">SKU</th>
                        <th class="p-3">Sparepart Name</th>
                        <th class="p-3 text-center">Qty Used</th>
                        <th class="p-3 text-right">Unit Price</th>
                        <th class="p-3 text-right">Total Price</th>
                    </tr>
                </thead>
                <tbody id="inv-admin-history-tbody" class="divide-y divide-slate-800/60 text-slate-300">
                    <!-- Injected JS -->
                </tbody>
            </table>
            <div id="inv-admin-history-pagination-bottom" class="mt-3"></div>
        </div>
    </div>

</section>
