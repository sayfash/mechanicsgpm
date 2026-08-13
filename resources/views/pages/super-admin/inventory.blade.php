<!-- SUB-TAB: Global Inventory Master Ledger -->
<div id="super-admin-tab-inventory" class="space-y-6 hidden mt-0 pt-0">
    <!-- Top Tab Bar: Live Global Stock vs Global Spare Part Usage History -->
    <div
        class="flex items-center justify-between glass-panel rounded-2xl p-2.5 shadow-lg border border-slate-800/80">
        <div class="flex items-center gap-2 overflow-x-auto whitespace-nowrap scrollbar-none max-w-full">
            <button onclick="setGlobalInventoryTab('stock')" id="btn-global-tab-stock"
                class="px-5 py-2.5 font-bold text-xs rounded-xl transition duration-200 bg-blue-600/10 text-blue-400 border border-blue-500/20 shadow-md flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-sm"></i> <span data-i18n="tab.live_global_stock">Stok Global Lintas Cabang</span>
            </button>
            <button onclick="setGlobalInventoryTab('history')" id="btn-global-tab-history"
                class="px-5 py-2.5 font-bold text-xs rounded-xl transition duration-200 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-sm"></i> <span data-i18n="tab.global_parts_history">Riwayat Pemakaian Suku Cadang Global</span>
            </button>
        </div>
    </div>

    <!-- Sub-Panel 1: Live Global Stock -->
    <div id="global-inv-panel-stock" class="space-y-4">

        <!-- Brief Global Inventory Dashboard Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Widget 1: Low Stock Spare Parts (< 5 units) -->
            <div class="glass-panel p-5 rounded-2xl shadow-xl space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-100 uppercase tracking-wider">Low Stock Parts</h4>
                    </div>
                    <select id="global-dash-low-stock-period" onchange="renderGlobalInventoryDashboard()"
                        class="px-2.5 py-1 rounded-lg text-[11px] bg-slate-900 border border-slate-800 text-slate-300 font-semibold cursor-pointer">
                        <option value="daily">Harian</option>
                        <option value="monthly" selected>Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
                <div id="global-dash-low-stock-list" class="space-y-2 max-h-[180px] overflow-y-auto custom-scrollbar pr-1">
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
                        <h4 class="text-xs font-bold text-slate-100 uppercase tracking-wider">5 Stok Terbanyak</h4>
                    </div>
                    <select id="global-dash-top-avail-period" onchange="renderGlobalInventoryDashboard()"
                        class="px-2.5 py-1 rounded-lg text-[11px] bg-slate-900 border border-slate-800 text-slate-300 font-semibold cursor-pointer">
                        <option value="daily">Harian</option>
                        <option value="monthly" selected>Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
                <div id="global-dash-top-avail-list" class="space-y-2 max-h-[180px] overflow-y-auto custom-scrollbar pr-1">
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
                        <h4 class="text-xs font-bold text-slate-100 uppercase tracking-wider">5 Stok Terpakai</h4>
                    </div>
                    <select id="global-dash-top-spent-period" onchange="renderGlobalInventoryDashboard()"
                        class="px-2.5 py-1 rounded-lg text-[11px] bg-slate-900 border border-slate-800 text-slate-300 font-semibold cursor-pointer">
                        <option value="daily">Harian</option>
                        <option value="monthly" selected>Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
                <div id="global-dash-top-spent-list" class="space-y-2 max-h-[180px] overflow-y-auto custom-scrollbar pr-1">
                    <!-- Injected JS -->
                </div>
            </div>
        </div>

        <!-- Streamlined Integrated Header & Controls Container -->
        <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
            <!-- Top Row: Title, Description & Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-blue-400"></i> <span data-i18n="inv.global_ledger">Global Inventory Master Ledger</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5" data-i18n="inv.global_ledger_desc">Cross-branch physical stock counts and price governance ledger across all shops.</p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                    <button onclick="openGlobalInventoryCSVModal()" data-permission="edit_inventory"
                        class="px-3.5 py-2 bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 border border-cyan-500/30 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-file-excel text-cyan-400"></i> <span data-i18n="inv.batch_upload">Upload Batch Inventory</span>
                    </button>
                    <button onclick="openSuperAdminAddInventoryModal()" data-permission="edit_inventory"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-plus"></i> <span data-i18n="inv.add_new">Add New Item</span>
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
                    <input type="text" id="super-admin-inventory-search"
                        oninput="filterGlobalInventory()"
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono"
                        data-i18n-placeholder="table.search_placeholder"
                        placeholder="Search SKU, Name, or Description...">
                </div>

                <!-- Branch Filter -->
                <select id="super-admin-branch-filter" onchange="fetchGlobalInventory()"
                    class="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200"></select>

                <!-- Column Filter Group -->
                <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                    <select id="super-admin-inv-column-select" onchange="filterGlobalInventory()"
                        class="w-full flex-1 min-w-[120px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                        <option value="" data-i18n="table.col_filter">Filter Kolom...</option>
                        <option value="sku" data-i18n="table.sku">Kode SKU</option>
                        <option value="part_name" data-i18n="table.part_name">Nama Barang</option>
                        <option value="description" data-i18n="table.description">Deskripsi</option>
                        <option value="category" data-i18n="table.category">Kategori</option>
                        <option value="warranty_category">Kategori Garansi</option>
                        <option value="available_qty" data-i18n="table.min_qty">Jumlah Min</option>
                        <option value="price" data-i18n="table.min_price">Harga Min (IDR)</option>
                    </select>
                    <input type="text" id="super-admin-inv-column-val" oninput="filterGlobalInventory()"
                        data-i18n-placeholder="table.val_placeholder"
                        placeholder="Nilai..."
                        class="w-full flex-1 min-w-[90px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                </div>

                <!-- Sort Control -->
                <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                    <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i
                            class="fa-solid fa-arrow-down-short-wide text-blue-400"></i><span data-i18n="table.sort">Urutkan:</span></span>
                    <select id="super-admin-inv-sort-select" onchange="filterGlobalInventory()"
                        class="w-full flex-1 min-w-[130px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                        <option value="updated_at-desc" data-i18n="sort.updated_desc">Terbaru Diperbarui</option>
                        <option value="sku-asc" data-i18n="sort.sku_asc">SKU (A-Z)</option>
                        <option value="part_name-asc" data-i18n="sort.name_asc">Nama Barang (A-Z)</option>
                        <option value="category-asc" data-i18n="sort.cat_asc">Kategori (A-Z)</option>
                        <option value="warranty_category-asc">Kategori Garansi (A-Z)</option>
                        <option value="branch_name-asc" data-i18n="sort.branch_asc">Cabang (A-Z)</option>
                        <option value="available_qty-desc" data-i18n="sort.qty_desc">Stok (Tertinggi)</option>
                        <option value="available_qty-asc" data-i18n="sort.qty_asc">Stok (Terendah)</option>
                        <option value="price-desc" data-i18n="sort.price_desc">Harga (Tertinggi)</option>
                        <option value="price-asc" data-i18n="sort.price_asc">Harga (Terendah)</option>
                    </select>
                </div>

                <!-- Column Visibility Toggle Dropdown -->
                <div class="relative inline-block text-left flex-1 min-w-[130px] sm:flex-initial">
                    <button type="button" id="btn-inv-column-toggle"
                        onclick="toggleInvColumnMenu(event)"
                        class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold hover:bg-slate-800 transition flex items-center justify-between gap-2 shadow-sm">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-columns text-blue-400"></i>
                            <span data-i18n="table.columns">Kolom</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                    </button>

                    <!-- Dropdown Popover Menu -->
                    <div id="inv-column-menu"
                        class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-slate-950/95 backdrop-blur-xl border border-slate-800 shadow-2xl p-3 z-[100] text-xs space-y-2">
                            <div
                                class="flex items-center justify-between border-b border-slate-800/80 pb-2 mb-1">
                                <span
                                    class="font-bold text-slate-200 uppercase tracking-wider text-[10px]" data-i18n="table.show_hide_columns">Tampilkan / Sembunyikan Kolom</span>
                                <button type="button" onclick="resetInvColumns()"
                                    class="text-[10px] text-blue-400 hover:underline font-semibold" data-i18n="table.select_all">Pilih Semua</button>
                            </div>
                            <div class="space-y-1.5 max-h-60 overflow-y-auto custom-scrollbar">
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="branch" checked
                                        onchange="toggleInvColumn('branch', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.branch">Cabang</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="sku" checked
                                        onchange="toggleInvColumn('sku', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.sku">Kode SKU</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="part_name" checked
                                        onchange="toggleInvColumn('part_name', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.part_name">Nama Barang</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="description" checked
                                        onchange="toggleInvColumn('description', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.description">Deskripsi</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="category" checked
                                        onchange="toggleInvColumn('category', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.category">Kategori</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="warranty_category" checked
                                        onchange="toggleInvColumn('warranty_category', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.warranty_category">Kategori Garansi</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="connected_service" checked
                                        onchange="toggleInvColumn('connected_service', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.connected_service">Jasa Terhubung</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="available_qty" checked
                                        onchange="toggleInvColumn('available_qty', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.available_qty">Stok Tersedia</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="price" checked
                                        onchange="toggleInvColumn('price', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.price">Harga (IDR)</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="updated_at" checked
                                        onchange="toggleInvColumn('updated_at', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.last_updated">Terakhir Diperbarui</span>
                                </label>
                                <label
                                    class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                    <input type="checkbox" data-col="actions" checked
                                        onchange="toggleInvColumn('actions', this.checked)"
                                        class="w-3.5 h-3.5 accent-blue-500 rounded bg-slate-900 border-slate-700">
                                    <span data-i18n="table.actions">Aksi</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Bulk Selection Actions Toolbar (Hidden by default, shown when items are selected) -->
        <div id="inv-bulk-action-toolbar" class="hidden glass-panel rounded-2xl p-4 shadow-xl border border-rose-500/30 bg-rose-950/20 my-4" data-permission="delete_inventory">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-semibold text-rose-300">
                    <i class="fa-solid fa-check-double text-rose-400"></i>
                    <span>Bulk Actions (<span id="inv-selected-count">0</span> items selected)</span>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <button onclick="deleteSelectedInventoryItems()" data-permission="delete_inventory"
                        class="w-full sm:w-auto px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-rose-600/20">
                        <i class="fa-solid fa-trash-can"></i> Delete Selected (<span id="inv-selected-count-btn">0</span>)
                    </button>
                    <button onclick="deleteAllCurrentBranchInventory()" data-permission="delete_inventory"
                        class="w-full sm:w-auto px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-amber-600/20"
                        title="Delete all items belonging to the selected branch filter">
                        <i class="fa-solid fa-dumpster-fire"></i> Clear Selected Branch Stock
                    </button>
                </div>
            </div>
        </div>

        <!-- Top Pagination Toolbar -->
        <div id="super-admin-inventory-pagination-top" class="my-4"></div>

        <!-- Inventory Spreadsheet Table -->
        <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto lg:overflow-x-hidden">
                <table class="w-full text-center text-sm border-collapse table-auto lg:table-fixed">
                    <thead>
                        <tr id="super-admin-inventory-thead-tr"
                            class="bg-slate-900 border-b border-slate-800 text-slate-300 text-center">
                            <th onclick="toggleSortGlobalInventory('branch_name')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[12%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.branch">Branch</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortGlobalInventory('sku')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[12%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.sku">SKU</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortGlobalInventory('part_name')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[18%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.part_name_only">Part Name</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[22%] text-center" data-i18n="table.description">
                                Description
                            </th>
                            <th onclick="toggleSortGlobalInventory('category')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[11%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.category">Category</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortGlobalInventory('warranty_category')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[11%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.warranty_category">Warranty Category</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortGlobalInventory('available_qty')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[7%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.qty">Qty</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortGlobalInventory('price')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[10%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.price">Price (IDR)</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th onclick="toggleSortGlobalInventory('updated_at')"
                                class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[8%] cursor-pointer select-none hover:text-blue-400 transition text-center">
                                <span data-i18n="table.last_updated">Updated</span> <i class="fa-solid fa-sort text-[10px] ml-1 text-slate-500"></i>
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs whitespace-nowrap lg:w-[10%] text-center" data-i18n="table.actions">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody id="super-admin-inventory-tbody"
                        class="divide-y divide-slate-800/80 text-slate-300 text-xs font-sans">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bottom Pagination Toolbar -->
        <div id="super-admin-inventory-pagination-bottom" class="my-4"></div>
    </div>

    <!-- Sub-Panel 2: Global Spare Part Usage History -->
    <div id="global-inv-panel-history" class="hidden space-y-6">
        <!-- Title Container & Mode Selector -->
        <div
            class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 glass-panel rounded-2xl p-6 shadow-xl">
            <div>
                <h3 class="text-xl font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400"></i> <span data-i18n="hist.title">Riwayat Pemakaian Suku Cadang Global</span>
                </h3>
                <p class="text-sm text-slate-400 mt-1" data-i18n="hist.desc">Log audit lintas cabang dari seluruh suku cadang yang diambil dan digunakan dalam pekerjaan perbaikan.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800">
                    <button onclick="setGlobalPartsHistoryMode('list')" id="btn-global-parts-hist-mode-list"
                        class="px-3.5 py-1.5 font-bold text-xs rounded-lg transition bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 flex items-center gap-1.5">
                        <i class="fa-solid fa-list"></i> <span data-i18n="hist.list_view">Tampilan Daftar</span>
                    </button>
                    <button onclick="setGlobalPartsHistoryMode('monthly')"
                        id="btn-global-parts-hist-mode-monthly"
                        class="px-3.5 py-1.5 font-bold text-xs rounded-lg transition text-slate-400 hover:text-slate-200 hover:bg-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-days"></i> <span data-i18n="hist.monthly_view">Tampilan Bulanan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- LIST VIEW MODE CONTAINER -->
        <div id="global-parts-history-list-container" class="space-y-6">
            <!-- Toolbar Filters & Controls -->
            <div class="glass-panel rounded-2xl p-5 shadow-xl">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[320px]">
                        <!-- Search Box -->
                        <div class="relative min-w-[180px] flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            </span>
                            <input type="text" id="global-parts-hist-search"
                                oninput="filterGlobalPartsHistory()"
                                placeholder="Search Part, SKU, Branch, Plate, Customer, Mechanic..."
                                class="w-full pl-9 pr-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
                        </div>

                        <!-- Branch Filter -->
                        <select id="global-parts-hist-branch-select" onchange="filterGlobalPartsHistory()"
                            class="px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold">
                            <option value="">All Branches</option>
                        </select>

                        <!-- Period Filter -->
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs text-slate-400 font-semibold uppercase"><i
                                     class="fa-solid fa-filter text-cyan-400 mr-1"></i>Periode:</span>
                            <select id="global-parts-hist-period-select"
                                onchange="handleGlobalPartsHistoryPeriodChange()"
                                class="px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold">
                                <option value="all">Semua Waktu</option>
                                <option value="today">Hari Ini</option>
                                <option value="yesterday">Kemarin</option>
                                <option value="this_week">Minggu Ini</option>
                                <option value="custom_date">Pilih Tanggal...</option>
                                <option value="custom_range">Rentang Tanggal...</option>
                            </select>
                        </div>

                        <!-- Dynamic Date Pickers -->
                        <div id="global-parts-hist-date-single-wrap" class="hidden items-center gap-1.5">
                            <input type="text" id="global-parts-hist-single-date"
                                onchange="filterGlobalPartsHistory()"
                                placeholder="dd/mm/yyyy"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 w-32 text-center font-mono">
                        </div>

                        <div id="global-parts-hist-date-range-wrap" class="hidden items-center gap-1.5">
                            <input type="text" id="global-parts-hist-range-start"
                                onchange="filterGlobalPartsHistory()"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 w-32 text-center font-mono"
                                placeholder="dd/mm/yyyy">
                            <span class="text-slate-500 text-xs">s/d</span>
                            <input type="text" id="global-parts-hist-range-end"
                                onchange="filterGlobalPartsHistory()"
                                class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 w-32 text-center font-mono"
                                placeholder="dd/mm/yyyy">
                        </div>
                    </div>

                    <!-- Right Group: Category & Sort Filter -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 font-semibold uppercase"><i
                                    class="fa-solid fa-tags text-purple-400 mr-1"></i>Category:</span>
                            <select id="global-parts-hist-category-select"
                                onchange="filterGlobalPartsHistory()"
                                class="px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold">
                                <!-- Dynamic options -->
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 font-semibold uppercase"><i
                                    class="fa-solid fa-arrow-down-short-wide text-blue-400 mr-1"></i>Sort:</span>
                            <select id="global-parts-hist-sort-select" onchange="filterGlobalPartsHistory()"
                                class="px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold">
                                <option value="used_at-desc">Date (Newest First)</option>
                                <option value="used_at-asc">Date (Oldest First)</option>
                                <option value="total_billed-desc">Total Cost (Highest First)</option>
                                <option value="total_billed-asc">Total Cost (Lowest First)</option>
                                <option value="quantity_used-desc">Qty (Highest First)</option>
                                <option value="quantity_used-asc">Qty (Lowest First)</option>
                                <option value="part_name-asc">Part Name (A-Z)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Records Table -->
            <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-center text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-900 border-b border-slate-800 text-slate-300 text-center">
                                <th
                                    class="p-4 font-semibold uppercase tracking-wider text-xs w-44 text-center" data-i18n="table.date">
                                    Date & Time</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="shop.assigned_job_id">
                                    Job ID</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="table.branch">
                                    Branch Outlet</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="maint.spare_part_cat">
                                    Spare Part & Category</th>
                                <th
                                    class="p-4 font-semibold uppercase tracking-wider text-xs w-28 text-center" data-i18n="table.qty_used">
                                    Qty Taken</th>
                                <th
                                    class="p-4 font-semibold uppercase tracking-wider text-xs w-36 text-center" data-i18n="maint.unit_price">
                                    Unit Price</th>
                                <th
                                    class="p-4 font-semibold uppercase tracking-wider text-xs w-40 text-center" data-i18n="table.subtotal">
                                    Total Billed</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="table.customer">
                                    Vehicle & Customer</th>
                                <th class="p-4 font-semibold uppercase tracking-wider text-xs text-center" data-i18n="table.mechanic">
                                    Assigned Mechanic</th>
                            </tr>
                        </thead>
                        <tbody id="global-parts-history-list-tbody"
                            class="divide-y divide-slate-800/80 text-slate-300 text-xs font-sans">
                            <!-- Injected JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MONTHLY VIEW MODE CONTAINER -->
        <div id="global-parts-history-monthly-container" class="hidden space-y-6">
            <div id="global-parts-history-monthly-cards" class="space-y-6">
                <!-- Injected JS monthly summary cards & breakdown tables -->
            </div>
        </div>
    </div>
</div>
