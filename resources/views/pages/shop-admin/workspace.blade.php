<!-- ========================================== -->
<!-- MODULE: SHOP ADMIN WORKSPACE               -->
<!-- ========================================== -->
<section id="shop-admin-workspace" class="w-full hidden space-y-6 mt-6 pt-0">

    <!-- Sub-Tab 1: Live Branch Inventory Stock -->
    <div id="shop-panel-inventory" class="hidden space-y-4">

        <!-- Brief Inventory Dashboard Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Widget 1: Low Stock Spare Parts (< 5 units) -->
            <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-100 uppercase tracking-wider" data-i18n="inv.low_stock">Low Stock Parts</h4>
                    </div>
                    <select id="shop-dash-low-stock-period" onchange="renderShopInventoryDashboard()"
                        class="px-2.5 py-1 rounded-lg text-[11px] bg-slate-900 border border-slate-800 text-slate-300 font-semibold cursor-pointer">
                        <option value="daily" data-i18n="period.daily">Harian</option>
                        <option value="monthly" data-i18n="period.monthly" selected>Bulanan</option>
                        <option value="yearly" data-i18n="period.yearly">Tahunan</option>
                    </select>
                </div>
                <div id="shop-dash-low-stock-list" class="space-y-2 max-h-[180px] overflow-y-auto custom-scrollbar pr-1">
                    <!-- Injected JS -->
                </div>
            </div>

            <!-- Widget 2: 5 Most Available Stock Spare Parts -->
            <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-100 uppercase tracking-wider" data-i18n="inv.top_available">5 Stok Terbanyak</h4>
                    </div>
                    <select id="shop-dash-top-avail-period" onchange="renderShopInventoryDashboard()"
                        class="px-2.5 py-1 rounded-lg text-[11px] bg-slate-900 border border-slate-800 text-slate-300 font-semibold cursor-pointer">
                        <option value="daily" data-i18n="period.daily">Harian</option>
                        <option value="monthly" data-i18n="period.monthly" selected>Bulanan</option>
                        <option value="yearly" data-i18n="period.yearly">Tahunan</option>
                    </select>
                </div>
                <div id="shop-dash-top-avail-list" class="space-y-2 max-h-[180px] overflow-y-auto custom-scrollbar pr-1">
                    <!-- Injected JS -->
                </div>
            </div>

            <!-- Widget 3: 5 Most Spent / Used Spare Parts -->
            <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-100 uppercase tracking-wider" data-i18n="inv.top_spent">5 Stok Terpakai</h4>
                    </div>
                    <select id="shop-dash-top-spent-period" onchange="renderShopInventoryDashboard()"
                        class="px-2.5 py-1 rounded-lg text-[11px] bg-slate-900 border border-slate-800 text-slate-300 font-semibold cursor-pointer">
                        <option value="daily" data-i18n="period.daily">Harian</option>
                        <option value="monthly" data-i18n="period.monthly" selected>Bulanan</option>
                        <option value="yearly" data-i18n="period.yearly">Tahunan</option>
                    </select>
                </div>
                <div id="shop-dash-top-spent-list" class="space-y-2 max-h-[180px] overflow-y-auto custom-scrollbar pr-1">
                    <!-- Injected JS -->
                </div>
            </div>
        </div>

        <!-- Integrated Header & Controls Container -->
        <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
            <!-- Top Row: Title, Branch Badge & Action Button -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-blue-400"></i> <span data-i18n="inv.live_ledger">Live Branch Inventory Ledger</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5" data-i18n="inv.live_ledger_desc">Manage spare part stock levels and price tags for your assigned branch.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                    <div class="bg-slate-900 px-3.5 py-1.5 rounded-xl border border-slate-800 flex items-center justify-between sm:justify-start gap-2 w-full sm:w-auto">
                        <span class="text-xs text-slate-400 uppercase font-semibold" data-i18n="reports.branch">Branch:</span>
                        <span id="shop-admin-branch-badge" class="text-xs font-bold text-blue-400"></span>
                    </div>
                    <button onclick="openShopAdminAddInventoryModal()"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-plus"></i> <span data-i18n="inv.add_new">Add New Item</span> <span id="shop-admin-add-branch" class="ml-1 text-xs text-blue-300"></span>
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
                        placeholder="Cari Kode SKU, Nama Barang, atau Deskripsi..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                </div>

                <!-- Hidden / Contextual Branch Filter (Required for JS compatibility) -->
                <select id="shop-admin-branch-filter" onchange="filterShopAdminInventory()" class="hidden"></select>

                <!-- Column Filter Group -->
                <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                    <select id="shop-admin-inv-column-select" onchange="filterShopAdminInventory()"
                        class="w-full flex-1 min-w-[120px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                        <option value="" data-i18n="table.col_filter">Filter Kolom...</option>
                        <option value="sku" data-i18n="table.sku">Kode SKU</option>
                        <option value="part_name" data-i18n="table.part_name_only">Nama Barang</option>
                        <option value="description" data-i18n="table.description">Deskripsi</option>
                        <option value="category" data-i18n="table.category">Kategori</option>
                        <option value="available_qty" data-i18n="table.min_qty">Jumlah Stok Min</option>
                        <option value="price" data-i18n="table.min_price">Harga Min (IDR)</option>
                    </select>
                    <input type="text" id="shop-admin-inv-column-val" oninput="filterShopAdminInventory()"
                        placeholder="Nilai..."
                        class="w-full flex-1 min-w-[90px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                </div>

                <!-- Sort Control -->
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-arrow-down-short-wide text-blue-400"></i><span data-i18n="table.sort">Urutkan:</span>
                    </span>
                    <select id="shop-admin-inv-sort-select" onchange="filterShopAdminInventory()"
                        class="w-full flex-1 min-w-[130px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                        <option value="updated_at-desc" data-i18n="sort.updated_desc">Diperbarui (Terbaru Pertama)</option>
                        <option value="sku-asc" data-i18n="sort.sku_asc">SKU (A-Z)</option>
                        <option value="part_name-asc" data-i18n="sort.name_asc">Nama Barang (A-Z)</option>
                        <option value="category-asc" data-i18n="sort.cat_asc">Kategori (A-Z)</option>
                        <option value="available_qty-desc" data-i18n="sort.qty_desc">Jumlah Stok (Tertinggi)</option>
                        <option value="available_qty-asc" data-i18n="sort.qty_asc">Jumlah Stok (Terendah)</option>
                        <option value="price-desc" data-i18n="sort.price_desc">Harga (Tertinggi)</option>
                        <option value="price-asc" data-i18n="sort.price_asc">Harga (Terendah)</option>
                    </select>
                </div>

                <!-- Visible Columns Dropdown Toggle -->
                <div class="relative inline-block text-left flex-1 min-w-[130px] sm:flex-initial">
                    <button type="button" id="btn-shop-inv-column-toggle" onclick="toggleShopInvColumnMenu(event)"
                        class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold hover:bg-slate-800 transition flex items-center justify-between gap-2 shadow-sm">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-columns text-cyan-400"></i> <span data-i18n="table.columns">Kolom</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                    </button>

                    <!-- Dropdown Popover Menu -->
                    <div id="shop-admin-inv-column-menu"
                        class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-slate-950/95 backdrop-blur-xl border border-slate-800 shadow-2xl p-3 z-[100] text-xs space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 mb-1">
                            <span class="font-bold text-slate-200 uppercase tracking-wider text-[10px]" data-i18n="table.show_hide_columns">Tampilkan / Sembunyikan Kolom</span>
                            <button type="button" onclick="resetShopInvColumns()" class="text-[10px] text-cyan-400 hover:underline font-semibold" data-i18n="table.select_all">Pilih Semua</button>
                        </div>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto custom-scrollbar">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="sku" checked onchange="toggleShopInvColumn('sku', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.sku">Kode SKU</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="part_name" checked onchange="toggleShopInvColumn('part_name', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.part_name_only">Nama Barang</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="description" checked onchange="toggleShopInvColumn('description', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.description">Deskripsi</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="category" checked onchange="toggleShopInvColumn('category', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.category">Kategori</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="available_qty" checked onchange="toggleShopInvColumn('available_qty', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.qty">Jumlah Stok</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="price" checked onchange="toggleShopInvColumn('price', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.price">Harga (IDR)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="updated_at" checked onchange="toggleShopInvColumn('updated_at', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.last_updated">Terakhir Diperbarui</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-shop-col="actions" checked onchange="toggleShopInvColumn('actions', this.checked)" class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                <span data-i18n="table.actions">Aksi</span>
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
                                <span data-i18n="table.sku">Kode SKU</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortShopInventory('part_name')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[22%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.part_name_only">Nama Barang</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[25%] text-center" data-i18n="table.description">
                                Deskripsi
                            </th>
                            <th onclick="toggleSortShopInventory('category')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[12%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.category">Kategori</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortShopInventory('available_qty')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[9%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.qty">Jumlah Stok</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortShopInventory('price')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[11%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.price">Harga (IDR)</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortShopInventory('updated_at')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[8%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.last_updated">Terakhir Diperbarui</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[8%] text-center" data-i18n="table.actions">
                                Aksi
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
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i> <span data-i18n="inv.parts_history">Spare Part Usage History</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5" data-i18n="hist.desc">Audit log of all spare parts taken and used in maintenance repair jobs for your branch.</p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Mode Selector: List View vs Monthly View -->
                <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 w-full sm:w-auto">
                    <button onclick="setPartsHistoryMode('list')" id="btn-parts-hist-mode-list"
                        class="flex-1 sm:flex-none w-full sm:w-auto px-3.5 py-1.5 font-bold text-xs rounded-lg transition bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-list"></i> <span data-i18n="hist.list_view">List View</span>
                    </button>
                    <button onclick="setPartsHistoryMode('monthly')" id="btn-parts-hist-mode-monthly"
                        class="flex-1 sm:flex-none w-full sm:w-auto px-3.5 py-1.5 font-bold text-xs rounded-lg transition text-slate-400 hover:text-slate-200 hover:bg-slate-800 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-calendar-days"></i> <span data-i18n="hist.monthly_view">Monthly View</span>
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
                                <i class="fa-solid fa-filter text-cyan-400 mr-1"></i><span data-i18n="reports.from">Periode:</span>
                            </span>
                            <select id="parts-hist-period-select" onchange="handlePartsHistoryPeriodChange()"
                                class="px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold">
                                <option value="all" data-i18n="period.all">Semua Waktu</option>
                                <option value="today" data-i18n="period.today">Hari Ini</option>
                                <option value="yesterday" data-i18n="period.yesterday">Kemarin</option>
                                <option value="this_week" data-i18n="period.this_week">Minggu Ini</option>
                                <option value="custom_date" data-i18n="period.custom_date">Pilih Tanggal...</option>
                                <option value="custom_range" data-i18n="period.custom_range">Rentang Tanggal...</option>
                            </select>
                        </div>

                        <!-- Dynamic Date Pickers -->
                        <div id="parts-hist-date-single-wrap" class="hidden items-center gap-1.5">
                            <input type="text" id="parts-hist-single-date" onchange="filterPartsHistory()"
                                placeholder="dd/mm/yyyy"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 w-32 text-center font-mono">
                        </div>

                        <div id="parts-hist-date-range-wrap" class="hidden items-center gap-1.5">
                            <input type="text" id="parts-hist-range-start" onchange="filterPartsHistory()"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 w-32 text-center font-mono"
                                placeholder="dd/mm/yyyy">
                            <span class="text-slate-500 text-xs">s/d</span>
                            <input type="text" id="parts-hist-range-end" onchange="filterPartsHistory()"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 w-32 text-center font-mono"
                                placeholder="dd/mm/yyyy">
                        </div>
                    </div>

                    <!-- Right Group: Category Filter -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-semibold uppercase">
                            <i class="fa-solid fa-tags text-purple-400 mr-1"></i><span data-i18n="table.category">Category:</span>
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
                <div class="overflow-x-auto">
                    <table class="w-full text-center text-sm border-collapse min-w-[1050px] table-auto">
                        <thead>
                            <tr id="shop-admin-history-thead-tr" class="bg-slate-900 border-b border-slate-800 text-slate-300 text-center">
                                <th class="p-3.5 font-semibold text-xs whitespace-nowrap text-center" data-i18n="table.date">Date & Time</th>
                                <th class="p-3.5 font-semibold text-xs whitespace-nowrap text-center" data-i18n="shop.assigned_job_id">Job ID</th>
                                <th class="p-3.5 font-semibold text-xs text-center max-w-[220px]" data-i18n="shop.part_sku_name">Part Name & SKU</th>
                                <th class="p-3.5 font-semibold text-xs whitespace-nowrap text-center" data-i18n="table.qty_used">Qty Used</th>
                                <th class="p-3.5 font-semibold text-xs whitespace-nowrap text-center" data-i18n="maint.unit_price">Unit Price</th>
                                <th class="p-3.5 font-semibold text-xs whitespace-nowrap text-center" data-i18n="table.subtotal">Subtotal Cost</th>
                                <th class="p-3.5 font-semibold text-xs text-center max-w-[180px]" data-i18n="table.customer">Customer / Vehicle</th>
                                <th class="p-3.5 font-semibold text-xs whitespace-nowrap text-center" data-i18n="table.mechanic">Mechanic</th>
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
    <div id="shop-panel-intake" class="hidden space-y-6">

    <!-- Container 0 (Atas): Ketersediaan Mekanik (Mechanic Availability) -->
    <div class="glass-panel rounded-2xl p-5 shadow-xl border border-slate-800">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-emerald-400"></i> <span data-i18n="shop.mechanic_availability">Ketersediaan Mekanik</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5 w-full" data-i18n="shop.mechanic_avail_desc">Status ketersediaan mekanik spesialis di cabang saat ini</p>
            </div>
            <div class="flex items-center gap-2 text-[10px] font-bold flex-wrap">
                <span class="px-2 py-0.5 rounded-full bg-emerald-200/90 text-emerald-950 border border-emerald-300 font-bold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span> <span data-i18n="shop.status_available">Tersedia</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-pink-200/90 text-pink-950 border border-pink-300 font-bold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-pink-600"></span> <span data-i18n="shop.status_busy">Sedang Bekerja</span>
                </span>
                <span class="px-2 py-0.5 rounded-full bg-amber-200/90 text-amber-950 border border-amber-300 font-bold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-600"></span> <span data-i18n="shop.mechanic_status_rest">Istirahat / Dihentikan</span>
                </span>
            </div>
        </div>

        <!-- Grid Mechanic Availability Cards -->
        <div id="shop-mechanic-availability-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 max-h-[220px] overflow-y-auto custom-scrollbar p-1">
            <p class="text-xs text-slate-500 italic col-span-full" data-i18n="shop.loading_mechanics">Memuat data ketersediaan mekanik...</p>
        </div>
    </div>

    <!-- Container 1: Active Jobs (In Progress) + Tombol Add New Job -->
    <div class="glass-panel rounded-2xl p-5 shadow-xl border border-slate-800">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-wrench text-amber-400"></i> <span data-i18n="shop.active_jobs_list">Daftar Pengerjaan Aktif (Sedang Berjalan)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5 w-full" data-i18n="shop.active_jobs_desc">Pilih pekerjaan perbaikan aktif untuk mengedit detail, kelola suku cadang, atau cetak surat perintah kerja</p>
            </div> 

            <!-- Tombol Add New Job dipindahkan ke seberang title Active Jobs -->
            <button type="button" onclick="handleAddNewJobClick()" class="w-full sm:w-auto px-4 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md shrink-0">
                <i class="fa-solid fa-plus-circle"></i> <span data-i18n="shop.create_new_intake">Buat Lembar Servis Baru</span>
            </button>
        </div>
        
        <!-- Horizontal Scrollable Active Jobs -->
        <div id="shop-active-jobs-list" class="flex flex-row overflow-x-auto custom-scrollbar gap-3.5 pb-2.5 max-w-full p-1">
            <p class="text-xs text-slate-500 italic col-span-full" data-i18n="shop.no_active_jobs">Tidak ada pengerjaan aktif yang sedang berlangsung.</p>
        </div>
    </div>

    <!-- Container 2 (Bawah): Auto-Intake Repair Sheet Form -->
    <div class="glass-panel rounded-2xl p-6 shadow-2xl relative overflow-hidden">
        <form onsubmit="submitMechanicJobRecord(event)" class="space-y-6">
            
            <!-- Header Form Intake -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4 mb-6">
                <div class="w-full sm:flex-1">
                    <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-file-medical text-blue-400"></i> <span data-i18n="shop.auto_intake_sheet">Auto-Intake Repair Sheet</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5 w-full" data-i18n="shop.auto_intake_desc">Generate job numbers, search customer databases, assign mechanics, log issues, and track task durations.</p>
                </div>
                
                <div class="text-left sm:text-right shrink-0 flex items-center gap-3">
                    <div class="bg-emerald-500/10 border-2 border-emerald-500/40 px-3.5 py-1 rounded-xl text-center shadow-lg">
                        <span class="text-[9px] text-emerald-400 font-extrabold uppercase tracking-wider block" data-i18n="shop.daily_queue_no">No. Antrean Hari Ini</span>
                        <span id="mech-daily-queue-num" class="text-xl font-black font-sans text-emerald-300 leading-none tracking-tight">#--</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 uppercase font-bold block mb-0.5" data-i18n="shop.assigned_job_id">Assigned Job ID</span>
                        <span id="mech-job-id" class="inline-block whitespace-nowrap text-sm font-mono font-bold text-blue-400 bg-blue-100/10 border border-blue-800/40 px-3 py-1 rounded">JOB-PENDING</span>
                    </div>
                </div>
            </div>

                <!-- Step 1: Assign Mechanic Specialist (Inline Layout & Consistent Input Styling) -->
                <div class="bg-slate-900/40 border border-slate-800 p-4 rounded-xl mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <!-- Sisi Kiri: Label Title & Icon -->
                        <div class="flex items-center gap-2 shrink-0">
                            <h4 class="text-xs font-bold text-blue-300 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">1</span>
                                <span data-i18n="shop.step1_assign_mech">Assign Mechanic Specialist</span>
                            </h4>
                            <span class="text-[10px] text-slate-400 font-mono font-semibold hidden sm:inline-block">
                                (Optional - Can Assign Later)
                            </span>
                        </div>

                        <!-- Sisi Kanan: Input Dropdown Inline yang Seragam dengan Field Lain -->
                        <div class="flex items-center gap-2 w-full md:w-auto md:min-w-[320px]">
                            <div class="relative w-full">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-400 pointer-events-none">
                                    <i class="fa-solid fa-user-gear text-xs"></i>
                                </span>
                                <!-- Assigned Mechanic Specialist -->
                                <select id="mech-assign-mechanic" onchange="updateStartSaveButtonText()" 
                                    class="w-full pl-9 pr-8 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs font-semibold focus:border-blue-500 focus:outline-none transition duration-150">
                                    <option value="" data-i18n="shop.choose_mechanic_spec">-- Choose Mechanic Specialist --</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Step 2: Customer Type & File Verification -->
                <div class="bg-slate-900/40 border border-slate-800 p-4 rounded-xl mb-6 space-y-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <h4 class="w-full sm:w-auto text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">2</span>
                            <span data-i18n="shop.step2_cust_type">Customer Type & Verification</span>
                        </h4>
                        <div class="flex rounded-lg bg-slate-950 p-1 border border-slate-800 w-full sm:w-auto">
                            <button type="button" id="btn-cust-type-external" onclick="setMechanicCustomerType('external')" class="flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 text-slate-400 hover:text-slate-200 flex items-center justify-center">
                                <i class="fa-solid fa-user mr-1.5"></i><span data-i18n="shop.ext_cust">External Customer</span>
                            </button>
                            <button type="button" id="btn-cust-type-internal" onclick="setMechanicCustomerType('internal')" class="flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 bg-blue-600 text-white shadow-md flex items-center justify-center">
                                <i class="fa-solid fa-building-user mr-1.5"></i><span data-i18n="shop.int_cust">Internal Customer</span>
                            </button>
                        </div>
                    </div>
                    <div id="mech-lookup-section" class="pt-2 border-t border-slate-800/60">
                        <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-1.5" data-i18n="shop.search_cust_id_card">Search Customer ID or ID Card Number</label>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="relative w-full flex-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i class="fa-solid fa-id-card"></i></span>
                                <input type="text" id="mech-search-query" class="w-full pl-9 pr-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200" placeholder="e.g. 1 or 32012345...">
                            </div>
                            <button type="button" onclick="performMechanicCustomerLookup()" class="w-full sm:w-auto px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-blue-500/10 shrink-0 flex items-center justify-center" data-i18n="shop.verify_cust">Verify Customer</button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Vehicle & Customer Details (Customer Status Inline dengan Title) -->
                <div class="bg-slate-900/20 border border-slate-800/80 rounded-xl p-4 space-y-4">
                    
                    <!-- Header Step 3: Title Sejajar dengan Badge & Customer Status Dropdown -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3">
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">3</span>
                                <span data-i18n="shop.step3_veh_cust">Vehicle & Customer Details</span>
                            </h4>
                            <span id="mech-editable-mode-badge" class="hidden text-[10px] text-emerald-400 bg-emerald-100/10 border border-emerald-800/60 px-2.5 py-0.5 rounded-full font-semibold">
                                <i class="fa-solid fa-user-plus mr-1"></i>New Customer Creation Allowed
                            </span>
                        </div>

                        <!-- Customer Status Dropdown dibuat Inline di Seberang Judul -->
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <label class="text-[10px] text-blue-400 uppercase tracking-wider font-bold whitespace-nowrap">
                                <i class="fa-solid fa-user-tag mr-1"></i><span data-i18n="shop.cust_status">Status:</span>
                            </label>
                            <select id="mech-customer-status" onchange="onMechanicCustomerStatusChange()" 
                                class="w-full sm:w-40 px-3 py-1.5 rounded-lg bg-slate-950 border border-blue-500/30 text-blue-300 font-bold focus:border-blue-500 focus:outline-none text-xs">
                                <option value="Gomolis">Gomolis</option>
                                <option value="Operasional">Operasional</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" id="mech-selected-vehicle-id">
                    <input type="hidden" id="mech-customer-type" value="internal">

                    <!-- Grid Row 1: Informasi Pelanggan (4 Kolom Simetris) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.cust_name">Customer Name</label>
                            <input type="text" id="mech-display-name" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. John Doe">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.cust_phone">Phone Number</label>
                            <input type="text" id="mech-display-phone" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono focus:outline-none transition duration-150" placeholder="e.g. 08123456789">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.id_card_num">ID Card Number</label>
                            <input type="text" id="mech-display-idcard" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono focus:outline-none transition duration-150" placeholder="e.g. 320100000000">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.cust_address">Address</label>
                            <input type="text" id="mech-display-address" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. Jl. Sudirman No. 12">
                        </div>
                    </div>

                    <!-- Grid Row 2: Spesifikasi Kendaraan (4 Kolom Simetris) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.license_plate">License Plate</label>
                            <input type="text" id="mech-display-plate" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. B 1234 ABC">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.veh_type">Vehicle Type</label>
                            <input type="text" id="mech-display-type" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. Gesits G1">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.frame_num">Frame Number (VIN)</label>
                            <input type="text" id="mech-display-frame" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. MH123456789">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.controller_num">Controller Number</label>
                            <input type="text" id="mech-display-controller" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. CTRL-9876">
                            <input type="hidden" id="mech-display-activate-date">
                        </div>
                    </div>
                </div>

                <!-- Step 4: Diagnostic Checklists -->
                <div class="space-y-4 bg-slate-900/20 border border-slate-800/80 rounded-xl p-4">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">4</span>
                        <span data-i18n="shop.step4_repair_details">Issue Diagnostic Log</span>
                    </h4>
                    <div class="grid grid-cols-1 gap-4 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold" data-i18n="shop.km_reached">KM Reached (Odometer)</label>
                            <input type="number" id="mech-km-reached" oninput="filterMechanicPartOptions()" required class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 font-mono" placeholder="e.g. 15200">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold" data-i18n="shop.common_issues">Common Issues Checklist</label>
                            <div id="mech-common-issues-container" class="grid grid-cols-2 gap-2 text-xs text-slate-300"></div>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold" data-i18n="shop.required_tasks">Required Mechanic Checklist Tasks</label>
                            <div id="mech-form-items-container" class="grid grid-cols-2 gap-2 text-xs text-slate-300"></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-1.5 font-bold" data-i18n="shop.other_notes">Other Custom Issues & Service Details</label>
                        <textarea id="mech-other-issues" rows="2" class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800" placeholder="Describe other diagnostics or client concerns..."></textarea>
                    </div>
                </div>

                <!-- Step 5: Parts Checklist Accumulator -->
                <div class="space-y-4 bg-slate-900/20 border border-slate-800/80 rounded-xl p-4">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">5</span>
                        <span data-i18n="shop.parts_accumulator">Spare Parts Checklist Accumulator (Max 25)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
                        <div class="col-span-3 space-y-2">
                            <div>
                                <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1" data-i18n="shop.search_sparepart">Search Sparepart</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-500"><i class="fa-solid fa-magnifying-glass text-[10px]"></i></span>
                                    <input type="text" id="mech-part-search-input" oninput="filterMechanicPartOptions()" class="w-full pl-8 pr-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200 font-mono" placeholder="Type to search...">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1" data-i18n="shop.select_sparepart">Select Sparepart</label>
                                <select id="mech-part-select" onchange="onMechPartSelectChange(this)" class="w-full px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-300 font-mono"></select>
                            </div>
                        </div>
                        <div class="flex flex-col justify-end">
                            <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1" data-i18n="shop.qty_options">Qty & Options</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" id="mech-part-qty" value="1" min="1" class="w-16 px-2 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-300 text-center font-mono">
                                 <label id="mech-part-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-blue-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-part-charged-input" onchange="onMechPartChargedInputToggle(this.checked)" class="w-4 h-4 accent-blue-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span data-i18n="shop.charged">Charged</span>
                                </label>
                                 <button type="button" onclick="addPartToMechAccumulator()" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-950/60 rounded-lg p-3 border border-slate-900 text-xs max-h-40 overflow-auto custom-scrollbar">
                        <table class="w-full text-left">
                            <thead>
                                <tr id="mech-parts-accumulator-thead-tr" class="text-slate-500 border-b border-slate-800">
                                    <th class="pb-1 font-semibold text-[10px] uppercase" data-i18n="shop.part_sku_name">Part SKU & Name</th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-20 text-right" data-i18n="table.price">Price</th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right" data-i18n="table.qty">Qty</th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right" data-i18n="table.delete">Delete</th>
                                </tr>
                            </thead>
                            <tbody id="mech-parts-accumulator-tbody" class="divide-y divide-slate-800/40 text-slate-300 font-mono">
                                <tr><td colspan="4" class="py-3 text-center text-slate-600 text-[10px]" data-i18n="shop.no_parts_accumulated">No spareparts accumulated.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-[10px] text-slate-500 text-right mt-1"><span data-i18n="shop.total_items">Total Items:</span> <span id="mech-accumulated-total-count" class="font-bold text-slate-400 font-mono">0</span> / 25</div>
                </div>

                <!-- Step 6: Service Options & Other Services -->
                <div class="bg-slate-900/20 border border-slate-800/80 rounded-xl p-4 space-y-4">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">6</span>
                        <span data-i18n="shop.service_options">Service Options & Other Services</span>
                    </h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
                            <h5 class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-wrench"></i> <span data-i18n="mgt.tab_services">Service Options</span></h5>
                            <div class="flex gap-2 items-center">
                                <select id="mech-service-select" class="flex-1 px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200"><option value="">-- Choose Service Option --</option></select>
                                <label id="mech-service-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-cyan-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-service-charged-input" class="w-4 h-4 accent-cyan-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span data-i18n="shop.charged">Charged</span>
                                </label>
                                <button type="button" onclick="addServiceToMechAccumulator()" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                            <div class="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 max-h-32 overflow-y-auto custom-scrollbar">
                                <div id="mech-services-accumulator-list" class="space-y-1 text-xs text-slate-300 font-mono">
                                    <span class="text-slate-600 text-[10px] block text-center py-2" data-i18n="shop.no_services_added">No service options added.</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
                            <h5 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-hand-holding-dollar"></i> <span data-i18n="shop.other_services">Other Services</span></h5>
                            <div class="flex gap-2 items-center">
                                <select id="mech-other-service-select" class="flex-1 px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200"><option value="">-- Choose Other Service --</option></select>
                                <label id="mech-other-service-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-amber-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-other-service-charged-input" class="w-4 h-4 accent-amber-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span data-i18n="shop.charged">Charged</span>
                                </label>
                                <button type="button" onclick="addOtherServiceToMechAccumulator()" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                            <div class="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 max-h-32 overflow-y-auto custom-scrollbar">
                                <div id="mech-other-services-accumulator-list" class="space-y-1 text-xs text-slate-300 font-mono">
                                    <span class="text-slate-600 text-[10px] block text-center py-2" data-i18n="shop.no_other_services_added">No other services added.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 7: Estimated Grand Total Banner -->
                <div class="glass-panel rounded-xl p-4 border border-emerald-500/30 bg-slate-900 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold text-emerald-400 uppercase tracking-widest flex items-center gap-2"><i class="fa-solid fa-calculator"></i> <span data-i18n="shop.est_grand_total">Estimated Grand Total Summary</span></h5>
                        <div class="flex items-center gap-4 text-xs text-slate-400">
                            <span>Services: <strong id="summary-services-fee" class="text-cyan-400 font-mono">Rp 0</strong></span>
                            <span>•</span>
                            <span>Other Services: <strong id="summary-other-fee" class="text-amber-400 font-mono">Rp 0</strong></span>
                            <span>•</span>
                            <span>Spareparts: <strong id="summary-parts-fee" class="text-blue-400 font-mono">Rp 0</strong></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block" data-i18n="shop.est_grand_total">Estimated Grand Total</span>
                        <span id="summary-grand-total" class="text-2xl font-extrabold text-emerald-400 font-mono">Rp 0</span>
                    </div>
                </div>

                <!-- Step 8: Timer & Final Submission Controls (SUB TAB 3 Intake) -->
                <div class="glass-panel rounded-xl p-4 border border-slate-800 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-6 mt-6">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-6 w-full lg:w-auto">
                        <div class="bg-slate-950 border border-slate-800 rounded-xl px-5 py-2.5 text-center font-mono relative overflow-hidden w-full sm:w-auto">
                            <div class="absolute inset-0 bg-blue-500/5 pulse-bg z-0 pointer-events-none"></div>
                            <span class="text-[9px] uppercase tracking-widest text-slate-500 font-bold block mb-0.5" data-i18n="shop.repair_timer">Repair Timer</span>
                            <span id="mech-timer-display" class="text-2xl font-bold text-blue-400 z-10 relative font-mono">00:00:00</span>
                        </div>

                        <div class="flex flex-col text-[10px] text-slate-500 gap-1 font-semibold">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                <span>Start: <span id="mech-start-timestamp" class="text-slate-300 font-mono">-</span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                        <button type="button" id="btn-mech-print-order" onclick="printCurrentWorkOrderAndReset()" 
                            class="flex-1 lg:flex-none px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-print"></i> <span data-i18n="shop.print_work_order">Print Work Order</span>
                        </button>

                        <button type="button" id="btn-mech-start-and-save" onclick="startMechanicTimerClock()" 
                            class="flex-1 lg:flex-none px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-play-circle"></i> <span data-i18n="shop.start_fixing_save">Start Fixing & Save Data</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sub-Tab: Active Job Details (Edit Active Repair Sub-Tab) -->
    <div id="shop-panel-active-job" class="hidden space-y-6">
        <!-- Container Ketersediaan Mekanik (Mechanic Availability) -->
        <div class="glass-panel rounded-2xl p-5 shadow-xl border border-slate-800">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-emerald-400"></i> <span data-i18n="shop.mechanic_availability">Ketersediaan Mekanik</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5 w-full" data-i18n="shop.mechanic_avail_desc">Status ketersediaan mekanik spesialis di cabang saat ini</p>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-bold flex-wrap">
                    <span class="px-2 py-0.5 rounded-full bg-emerald-200/90 text-emerald-950 border border-emerald-300 font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span> <span data-i18n="shop.status_available">Tersedia</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-pink-200/90 text-pink-950 border border-pink-300 font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-pink-600"></span> <span data-i18n="shop.status_busy">Sedang Bekerja</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-amber-200/90 text-amber-950 border border-amber-300 font-bold flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span> <span data-i18n="shop.status_rest_stopped">Istirahat / Dihentikan</span>
                    </span>
                </div>
            </div>

            <!-- Grid Mechanic Availability Cards -->
            <div id="shop-mechanic-availability-list-tab" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 max-h-[220px] overflow-y-auto custom-scrollbar p-1">
                <p class="text-xs text-slate-500 italic col-span-full" data-i18n="shop.loading_mechanics">Memuat data ketersediaan mekanik...</p>
            </div>
        </div>

        <!-- Active Jobs Overview Container -->
        <div class="glass-panel rounded-2xl p-5 shadow-xl border border-slate-800">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-wrench text-amber-400"></i> <span data-i18n="shop.active_jobs_list">Daftar Pengerjaan Aktif (Sedang Berjalan)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5 w-full" data-i18n="shop.active_jobs_desc">Pilih pekerjaan perbaikan aktif untuk mengedit detail, kelola suku cadang, atau cetak surat perintah kerja</p>
                </div>
                <button type="button" onclick="setShopAdminTab('intake')" class="w-full sm:w-auto px-4 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md shrink-0">
                    <i class="fa-solid fa-plus-circle"></i> <span data-i18n="shop.create_new_intake">Buat Lembar Servis Baru</span>
                </button>
            </div>
            
            <div id="shop-active-jobs-list-tab" class="flex flex-row overflow-x-auto custom-scrollbar gap-3.5 pb-2.5 max-w-full p-1">
                <p class="text-xs text-slate-500 italic col-span-full" data-i18n="shop.no_active_jobs">Tidak ada pengerjaan aktif yang sedang berlangsung.</p>
            </div>
        </div>

        <!-- Banner Header -->
        <div class="glass-panel rounded-2xl p-5 shadow-xl border border-amber-500/30 bg-amber-950/10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-slate-800/80 pt-2 pb-2">
                <div>
                    <div class="flex items-center gap-2.5">
                        <h3 id="active-job-banner-title" class="text-lg font-bold text-slate-100 flex items-center gap-2" data-i18n="shop.active_job_details">Detail Pengerjaan Aktif</h3>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30" data-i18n="shop.active_repair">Perbaikan Aktif</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1" data-i18n="shop.active_job_banner_desc">Tinjau kemajuan perbaikan aktif, edit suku cadang & layanan, cetak surat perintah/faktur, simpan draf, atau kirim penyelesaian.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="setShopAdminTab('intake')" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs transition flex items-center gap-1.5 border border-slate-700">
                        <i class="fa-solid fa-arrow-left"></i> <span data-i18n="shop.back_to_intake">Kembali ke Lembar Penerimaan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 1. Quick Actions -->
        <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fa-solid fa-sliders text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider" data-i18n="shop.quick_actions">Aksi Cepat & Pencetakan</h4>
                    <p class="text-[10px] text-slate-400" data-i18n="shop.quick_actions_desc">Cetak surat perintah kerja, cetak faktur pelanggan, atau simpan draf kemajuan.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                <button type="button" id="btn-active-job-print-order" onclick="printCurrentWorkOrderAndReset()" class="flex-1 sm:flex-initial px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md">
                    <i class="fa-solid fa-print"></i> <span data-i18n="shop.print_work_order">Cetak Surat Perintah Kerja</span>
                </button>
                <button type="button" id="btn-active-job-print-invoice" onclick="printCurrentInvoice()" class="flex-1 sm:flex-initial px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md">
                    <i class="fa-solid fa-file-invoice-dollar"></i> <span data-i18n="shop.print_invoice">Cetak Faktur</span>
                </button>
                <button type="button" id="btn-active-job-send-wa" onclick="sendInvoiceWhatsApp()" class="flex-1 sm:flex-initial px-4 py-2 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md">
                    <i class="fa-brands fa-whatsapp text-sm"></i> <span>Kirim WA</span>
                </button>
                <button type="button" id="btn-active-job-save-draft" onclick="saveActiveJobDraft()" class="flex-1 sm:flex-initial px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 border border-slate-700">
                    <i class="fa-solid fa-floppy-disk"></i> <span data-i18n="shop.save_draft">Simpan Draf</span>
                </button>
                <button type="button" id="btn-active-job-cancel" onclick="cancelActiveJob()" class="flex-1 sm:flex-initial px-4 py-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-md">
                    <i class="fa-solid fa-trash-can"></i> <span data-i18n="shop.cancel_job">Batalkan Pengerjaan</span>
                </button>
            </div>
        </div>

        <!-- 2. Auto-Intake Repair Sheet Form (Identik dengan Sub-tab Intake) -->
        <div class="glass-panel rounded-2xl p-6 shadow-2xl relative overflow-hidden">
            <form onsubmit="submitMechanicJobRecord(event)" class="space-y-6">
                
                <!-- Header Form Intake -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4 mb-6">
                    <div class="w-full sm:flex-1">
                        <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-file-medical text-blue-400"></i> <span data-i18n="shop.auto_intake_sheet">Auto-Intake Repair Sheet</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5 w-full" data-i18n="shop.auto_intake_desc">Generate job numbers, search customer databases, assign mechanics, log issues, and track task durations.</p>
                    </div>
                    
                    <div class="text-left sm:text-right shrink-0 flex items-center gap-3">
                        <div class="bg-emerald-500/10 border-2 border-emerald-500/40 px-3.5 py-1 rounded-xl text-center shadow-lg">
                            <span class="text-[9px] text-emerald-400 font-extrabold uppercase tracking-wider block" data-i18n="shop.daily_queue_no">No. Antrean Hari Ini</span>
                            <span id="mech-daily-queue-num-tab" class="text-xl font-black font-sans text-emerald-300 leading-none tracking-tight">#--</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 uppercase font-bold block mb-0.5" data-i18n="shop.assigned_job_id">Assigned Job ID</span>
                            <span id="mech-job-id-tab" class="inline-block whitespace-nowrap text-sm font-mono font-bold text-blue-400 bg-blue-100/10 border border-blue-800/40 px-3 py-1 rounded">JOB-PENDING</span>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Assign Mechanic Specialist -->
                <div class="bg-slate-900/40 border border-slate-800 p-4 rounded-xl mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-2 shrink-0">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-slate-800 text-slate-400 inline-flex items-center justify-center font-bold text-[10px]">1</span>
                                <span data-i18n="shop.step1_assign_mech">Assign Mechanic Specialist</span>
                            </h4>
                        </div>
                        <div class="flex items-center gap-2 w-full md:w-auto md:min-w-[320px]">
                            <div class="relative w-full">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-blue-400 pointer-events-none">
                                    <i class="fa-solid fa-user-gear text-xs"></i>
                                </span>
                                <select id="mech-assign-mechanic" 
                                    class="w-full pl-9 pr-8 py-2 rounded-lg bg-slate-950 border border-slate-800 text-slate-200 text-xs font-semibold focus:border-blue-500 focus:outline-none transition duration-150">
                                    <option value="" data-i18n="shop.choose_mechanic_spec">-- Choose Mechanic Specialist --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Vehicle & Customer Details -->
                <div class="bg-slate-900/20 border border-slate-800/80 rounded-xl p-4 space-y-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3">
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">2</span>
                                <span data-i18n="shop.step3_veh_cust">Vehicle & Customer Details</span>
                            </h4>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold whitespace-nowrap">
                                <i class="fa-solid fa-user-tag mr-1"></i><span data-i18n="shop.cust_status">Status:</span>
                            </label>
                            <input type="text" id="active-job-customer-status" readonly 
                                class="w-full sm:w-40 px-3 py-1.5 rounded-lg bg-slate-950/80 border border-slate-800 text-slate-300 text-xs font-bold cursor-not-allowed" 
                                placeholder="Retail">
                        </div>
                    </div>

                    <input type="hidden" id="mech-selected-vehicle-id">
                    <input type="hidden" id="mech-customer-type" value="internal">

                    <!-- Grid Row 1: Customer Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.cust_name">Customer Name</label>
                            <input type="text" id="mech-display-name" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. John Doe">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.cust_phone">Phone Number</label>
                            <input type="text" id="mech-display-phone" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono focus:outline-none transition duration-150" placeholder="e.g. 08123456789">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.id_card_num">ID Card Number</label>
                            <input type="text" id="mech-display-idcard" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono focus:outline-none transition duration-150" placeholder="e.g. 320100000000">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.cust_address">Address</label>
                            <input type="text" id="mech-display-address" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. Jl. Sudirman No. 12">
                        </div>
                    </div>

                    <!-- Grid Row 2: Vehicle Specs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.license_plate">License Plate</label>
                            <input type="text" id="mech-display-plate" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. B 1234 ABC">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.veh_type">Vehicle Type</label>
                            <input type="text" id="mech-display-type" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150" placeholder="e.g. Gesits G1">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.frame_num">Frame Number (VIN)</label>
                            <input type="text" id="mech-display-frame" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. MH123456789">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1" data-i18n="shop.controller_num">Controller Number</label>
                            <input type="text" id="mech-display-controller" disabled class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150" placeholder="e.g. CTRL-9876">
                            <input type="hidden" id="mech-display-activate-date">
                        </div>
                    </div>
                </div>

                <!-- Step 4: Diagnostic Checklists -->
                <div class="space-y-4 bg-slate-900/20 border border-slate-800/80 rounded-xl p-4">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">4</span>
                        <span data-i18n="shop.step4_repair_details">Issue Diagnostic Log</span>
                    </h4>
                    <div class="grid grid-cols-1 gap-4 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold" data-i18n="shop.km_reached">KM Reached (Odometer)</label>
                            <input type="number" id="mech-km-reached" oninput="filterMechanicPartOptions()" required class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 font-mono" placeholder="e.g. 15200">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold" data-i18n="shop.common_issues">Common Issues Checklist</label>
                            <div id="mech-common-issues-container" class="grid grid-cols-2 gap-2 text-xs text-slate-300"></div>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold" data-i18n="shop.required_tasks">Required Mechanic Checklist Tasks</label>
                            <div id="mech-form-items-container" class="grid grid-cols-2 gap-2 text-xs text-slate-300"></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-1.5 font-bold" data-i18n="shop.other_notes">Other Custom Issues & Service Details</label>
                        <textarea id="mech-other-issues" rows="2" class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800" placeholder="Describe other diagnostics or client concerns..."></textarea>
                    </div>
                </div>

                <!-- Step 5: Spare Parts Checklist Accumulator -->
                <div class="space-y-4 bg-slate-900/20 border border-slate-800/80 rounded-xl p-4">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">5</span>
                        <span data-i18n="shop.parts_accumulator">Spare Parts Checklist Accumulator (Max 25)</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
                        <div class="col-span-3 space-y-2">
                            <div>
                                <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1" data-i18n="shop.search_sparepart">Search Sparepart</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-500"><i class="fa-solid fa-magnifying-glass text-[10px]"></i></span>
                                    <input type="text" id="mech-part-search-input" oninput="filterMechanicPartOptions()" class="w-full pl-8 pr-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200 font-mono" placeholder="Type to search...">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1" data-i18n="shop.select_sparepart">Select Sparepart</label>
                                <select id="mech-part-select" onchange="onMechPartSelectChange(this)" class="w-full px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-300 font-mono"></select>
                            </div>
                        </div>
                        <div class="flex flex-col justify-end">
                            <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1" data-i18n="shop.qty_options">Qty & Options</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" id="mech-part-qty" value="1" min="1" class="w-16 px-2 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-300 text-center font-mono">
                                 <label id="mech-part-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-blue-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-part-charged-input" onchange="onMechPartChargedInputToggle(this.checked)" class="w-4 h-4 accent-blue-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span data-i18n="shop.charged">Charged</span>
                                </label>
                                <button type="button" onclick="addPartToMechAccumulator()" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-950/60 rounded-lg p-3 border border-slate-900 text-xs max-h-40 overflow-auto custom-scrollbar">
                        <table class="w-full text-left">
                            <thead>
                                <tr id="mech-parts-accumulator-thead-tr" class="text-slate-500 border-b border-slate-800">
                                    <th class="pb-1 font-semibold text-[10px] uppercase" data-i18n="shop.part_sku_name">Part SKU & Name</th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-20 text-right" data-i18n="table.price">Price</th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right" data-i18n="table.qty">Qty</th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right" data-i18n="table.delete">Delete</th>
                                </tr>
                            </thead>
                            <tbody id="mech-parts-accumulator-tbody" class="divide-y divide-slate-800/40 text-slate-300 font-mono">
                                <tr><td colspan="4" class="py-3 text-center text-slate-600 text-[10px]" data-i18n="shop.no_parts_accumulated">No spareparts accumulated.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-[10px] text-slate-500 text-right mt-1"><span data-i18n="shop.total_items">Total Items:</span> <span id="mech-accumulated-total-count" class="font-bold text-slate-400 font-mono">0</span> / 25</div>
                </div>

                <!-- Step 6: Service Options & Other Services -->
                <div class="bg-slate-900/20 border border-slate-800/80 rounded-xl p-4 space-y-4">
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">6</span>
                        <span data-i18n="shop.service_options">Service Options & Other Services</span>
                    </h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
                            <h5 class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-wrench"></i> <span data-i18n="mgt.tab_services">Service Options</span></h5>
                            <div class="flex gap-2 items-center">
                                <select id="mech-service-select" class="flex-1 px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200"><option value="">-- Choose Service Option --</option></select>
                                <label id="mech-service-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-cyan-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-service-charged-input" class="w-4 h-4 accent-cyan-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span data-i18n="shop.charged">Charged</span>
                                </label>
                                <button type="button" onclick="addServiceToMechAccumulator()" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                            <div class="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 max-h-32 overflow-y-auto custom-scrollbar">
                                <div id="mech-services-accumulator-list" class="space-y-1 text-xs text-slate-300 font-mono">
                                    <span class="text-slate-600 text-[10px] block text-center py-2" data-i18n="shop.no_services_added">No service options added.</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
                            <h5 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-hand-holding-dollar"></i> <span data-i18n="shop.other_services">Other Services</span></h5>
                            <div class="flex gap-2 items-center">
                                <select id="mech-other-service-select" class="flex-1 px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200"><option value="">-- Choose Other Service --</option></select>
                                <label id="mech-other-service-charged-input-wrapper" class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-amber-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-other-service-charged-input" class="w-4 h-4 accent-amber-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span data-i18n="shop.charged">Charged</span>
                                </label>
                                <button type="button" onclick="addOtherServiceToMechAccumulator()" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                            <div class="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 max-h-32 overflow-y-auto custom-scrollbar">
                                <div id="mech-other-services-accumulator-list" class="space-y-1 text-xs text-slate-300 font-mono">
                                    <span class="text-slate-600 text-[10px] block text-center py-2" data-i18n="shop.no_other_services_added">No other services added.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 7: Estimated Grand Total Summary Banner -->
                <div class="glass-panel rounded-xl p-4 border border-emerald-500/30 bg-slate-900 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold text-emerald-400 uppercase tracking-widest flex items-center gap-2"><i class="fa-solid fa-calculator"></i> <span data-i18n="shop.est_grand_total">Estimated Grand Total Summary</span></h5>
                        <div class="flex items-center gap-4 text-xs text-slate-400">
                            <span>Services: <strong id="summary-services-fee" class="text-cyan-400 font-mono">Rp 0</strong></span>
                            <span>•</span>
                            <span>Other Services: <strong id="summary-other-fee" class="text-amber-400 font-mono">Rp 0</strong></span>
                            <span>•</span>
                            <span>Spareparts: <strong id="summary-parts-fee" class="text-blue-400 font-mono">Rp 0</strong></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block" data-i18n="shop.est_grand_total">Estimated Grand Total</span>
                        <span id="summary-grand-total" class="text-2xl font-extrabold text-emerald-400 font-mono">Rp 0</span>
                    </div>
                </div>

                <!-- Step 8: Timer Clock & Final Actions (Active Job Mode) -->
                <div class="glass-panel rounded-xl p-4 border border-slate-800 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-6 mt-6">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-6 w-full md:w-auto">
                        <div class="bg-slate-950 border border-slate-800 rounded-xl px-5 py-2.5 text-center font-mono relative overflow-hidden w-full sm:w-auto">
                            <div class="absolute inset-0 bg-blue-500/5 pulse-bg z-0 pointer-events-none"></div>
                            <span class="text-[9px] uppercase tracking-widest text-slate-500 font-bold block mb-0.5" data-i18n="shop.repair_timer">Repair Timer</span>
                            <span id="mech-timer-display" class="text-2xl font-bold text-blue-400 z-10 relative font-mono">00:00:00</span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <!-- Tombol Start Timer (Queue -> In Progress) -->
                            <button type="button" id="btn-mech-start-timer" onclick="startActiveJobTimer()" class="w-full sm:w-auto px-5 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-emerald-500/10">
                                <i class="fa-solid fa-play"></i> <span data-i18n="shop.start_timer">Start Timer</span>
                            </button>

                            <!-- Tombol Stop Timer (Aktif) -->
                            <button type="button" id="btn-mech-stop-timer" onclick="stopActiveJobTimer()" class="w-full sm:w-auto px-5 py-3 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/25 font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-rose-500/10">
                                <i class="fa-solid fa-stop"></i> <span data-i18n="shop.stop_timer">Stop Timer</span>
                            </button>
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
                    
                    <!-- Tombol Submit Record (Aktif) -->
                    <button type="submit" id="btn-mech-submit-record" class="w-full md:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> <span data-i18n="shop.submit_repair_record">Submit Repair Record</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sub-Tab 4: Customer History Logs (Table / Calendar Views) -->
    <div id="shop-panel-review" class="hidden space-y-6">
        <!-- Search panel -->
        <div class="flex flex-col md:flex-row items-stretch md:items-end gap-4 glass-panel rounded-2xl p-6">
            <div class="w-full flex-1">
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider" data-i18n="shop.search_cust_id_card">Search Maintenance History (Customer ID / ID Card Number)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="shop-history-query" class="w-full pl-9 pr-3 py-2.5 rounded-lg text-sm bg-slate-950 border border-slate-800 font-mono" placeholder="e.g. 1 or 32012345...">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <button onclick="fetchShopHistoryRecords()" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-blue-500/10 flex items-center justify-center" data-i18n="shop.verify_cust">
                    Search Records
                </button>

                <div class="hidden sm:block h-10 w-px bg-slate-800"></div>

                <!-- Toggle switcher -->
                <div class="flex w-full sm:w-auto bg-slate-950 p-1 rounded-lg border border-slate-800">
                    <button onclick="setShopHistoryViewMode('table')" id="btn-shop-history-view-table" class="flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 bg-slate-800 text-slate-200 flex items-center justify-center">
                        <i class="fa-solid fa-list mr-1"></i> <span data-i18n="hist.list_view">List Table</span>
                    </button>
                    <button onclick="setShopHistoryViewMode('calendar')" id="btn-shop-history-view-calendar" class="flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 text-slate-400 hover:text-slate-200 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days mr-1"></i> <span data-i18n="hist.monthly_view">Calendar Grid</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table View Interface (workspace.blade.php) -->
        <div id="shop-history-table-view" class="glass-panel rounded-2xl overflow-hidden shadow-2xl space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-300">
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-36" data-i18n="table.date">Date</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-44" data-i18n="table.customer">Customer Info</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs" data-i18n="maint.branch_mechanic">Branch Station</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs" data-i18n="shop.license_plate">Vehicle License</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs" data-i18n="maint.job_desc">Diagnostic Issues</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs" data-i18n="shop.km_reached">KM Reached</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-28 text-center" data-i18n="maint.parts_billed">Billed Parts</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-20" data-i18n="table.actions">Details</th>
                        </tr>
                    </thead>
                    <tbody id="shop-history-list-tbody" class="divide-y divide-slate-800/80 text-slate-300 text-xs font-mono">
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-500 font-sans">Provide Customer ID, ID Card, or License Plate above to search logs.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calendar View Interface -->
        <div id="shop-history-calendar-view" class="glass-panel rounded-2xl p-6 shadow-2xl hidden space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h4 class="text-md font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-blue-400"></i> <span data-i18n="hist.repair_monthly_calendar">Interactive Repair Monthly Calendar</span>
                </h4>
                <div class="flex items-center gap-2">
                    <button onclick="adjustCalendarMonth(-1)" class="w-8 h-8 rounded bg-slate-900 border border-slate-800 hover:bg-slate-800 transition text-slate-300"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                    <span id="shop-calendar-month-year-label" class="text-sm font-bold text-slate-200 w-36 text-center block">August 2026</span>
                    <button onclick="adjustCalendarMonth(1)" class="w-8 h-8 rounded bg-slate-900 border border-slate-800 hover:bg-slate-800 transition text-slate-300"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-2 text-center text-xs">
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider" data-i18n="day.sun">Sun</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider" data-i18n="day.mon">Mon</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider" data-i18n="day.tue">Tue</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider" data-i18n="day.wed">Wed</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider" data-i18n="day.thu">Thu</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider" data-i18n="day.fri">Fri</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider" data-i18n="day.sat">Sat</div>
            </div>
            <div id="shop-calendar-days-grid" class="grid grid-cols-7 gap-2 text-center text-xs">
                <!-- Days populated by JavaScript -->
            </div>

            <div class="text-[10px] text-slate-500 flex items-center gap-1.5 justify-end">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> <span data-i18n="hist.indicates_events">Indicates repair events logged on this date.</span>
            </div>
        </div>
    </div>

    <!-- Sub-Tab 5: Branch Maintenance Records -->
    <div id="shop-panel-maintenance" class="hidden space-y-4">
        <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-blue-400"></i> <span data-i18n="nav.branch_maintenance_records">Branch Maintenance Records</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5" data-i18n="maint.desc">View and print maintenance records for your assigned branch.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" onclick="openExportExcelModal('shop_admin')"
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-1.5"
                        title="Export Branch Maintenance Records to Excel">
                        <i class="fa-solid fa-file-excel"></i> <span data-i18n="maint.export_excel">Export Excel</span>
                    </button>
                </div>
            </div>

            <!-- Integrated Responsive Controls -->
            <div class="flex flex-wrap items-center gap-3 w-full">
                <!-- Search -->
                <div class="relative min-w-[200px] flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="shop-records-search" oninput="filterShopMaintenanceRecords()"
                        placeholder="Search records..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                </div>

                <!-- Status Filter -->
                <select id="shop-records-status-filter" onchange="filterShopMaintenanceRecords()"
                    class="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-bold">
                    <option value="" data-i18n="maint.all_statuses">All Statuses</option>
                    <option value="IN_PROGRESS" data-i18n="maint.status_in_progress">IN PROGRESS</option>
                    <option value="COMPLETED" data-i18n="maint.status_completed">COMPLETED</option>
                    <option value="CANCELLED" data-i18n="maint.status_cancelled">CANCELLED</option>
                </select>

                <!-- Sort Control -->
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-arrow-down-short-wide text-blue-400"></i><span data-i18n="table.sort">Sort:</span>
                    </span>
                    <select id="shop-records-sort-select" onchange="filterShopMaintenanceRecords()"
                        class="w-full flex-1 min-w-[130px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                        <option value="created_at-desc" data-i18n="sort.date_desc">Date (Newest First)</option>
                        <option value="created_at-asc" data-i18n="sort.date_asc">Date (Oldest First)</option>
                        <option value="customer_name-asc" data-i18n="sort.cust_asc">Customer Name (A-Z)</option>
                        <option value="status-asc" data-i18n="sort.status_asc">Status (A-Z)</option>
                        <option value="parts_cost-desc" data-i18n="sort.cost_desc">Parts Cost (Highest First)</option>
                        <option value="parts_cost-asc" data-i18n="sort.cost_asc">Parts Cost (Lowest First)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Top Pagination Toolbar -->
        <div id="shop-maintenance-pagination-top" class="my-4"></div>

        <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
            <div class="w-full min-w-0 max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full text-center text-sm border-collapse min-w-[900px]">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-300 text-center">
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-40 text-center" data-i18n="table.date">Date Logged</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="maint.branch_mechanic">Branch / Mechanic</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="table.customer">Customer Owner</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="maint.vehicle_info">Vehicle Info</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="maint.job_desc">Job Description</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-28 text-center" data-i18n="table.status">Job Status</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="table.subtotal">Total Billing</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-24 text-center" data-i18n="table.actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="shop-maintenance-tbody" class="divide-y divide-slate-800/80 text-slate-300 text-xs font-sans">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Pagination Toolbar -->
        <div id="shop-maintenance-pagination-bottom" class="my-4"></div>
    </div>
</section>