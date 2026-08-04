<!-- SUB-TAB: Customers Directory -->
<div id="super-admin-tab-customers" class="space-y-4 hidden mt-0 pt-0">
    <!-- Streamlined Integrated Header & Controls Container -->
    <div class="glass-panel rounded-2xl p-4 sm:p-5 shadow-xl relative z-20 overflow-visible space-y-4">
        <!-- Top Row: Title, Description & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/60 pb-3.5">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-users text-emerald-400"></i> Registered Customers Directory
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Directory of all client profiles, contact numbers, KTP numbers, and vehicle counts.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                <button onclick="openCustomersCSVModal()"
                    class="px-3.5 py-2 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-file-excel text-emerald-400"></i> Upload Batch Customers
                </button>
                <button onclick="openAddCustomerModal()"
                    class="px-3.5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-emerald-500/20 shrink-0">
                    <i class="fa-solid fa-user-plus"></i> Add New Customer
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
                <input type="text" id="super-admin-customers-search" oninput="filterCustomersTable()"
                    placeholder="Search customers by name, phone, KTP, address..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
            </div>

            <!-- Branch Filter -->
            <select id="super-admin-customers-branch-filter" onchange="filterCustomersTable()"
                class="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                <option value="">All Branches</option>
            </select>

            <!-- Status Filter -->
            <!--<select id="super-admin-customers-status-filter" onchange="filterCustomersTable()"
                class="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-bold">
                <option value="">All Customer Statuses</option>
                <option value="Retail">Retail</option>
                <option value="Fleet">Fleet</option>
                <option value="Gomolis">Gomolis</option>
                <option value="Gomolis-RF">Gomolis-RF</option>
                <option value="VIP">VIP</option>
            </select> -->

            <!-- Custom Column Filter Group -->
            <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                <select id="super-admin-customers-column-select" onchange="filterCustomersTable()"
                    class="w-full flex-1 min-w-[120px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200">
                    <option value="">Column Filter...</option>
                    <option value="name">Customer Name</option>
                    <option value="phone">Phone Number</option>
                    <option value="id_card_number">ID Card (KTP)</option>
                    <option value="address">Home Address</option>
                    <option value="customer_status">Status</option>
                </select>
                <input type="text" id="super-admin-customers-column-val" oninput="filterCustomersTable()"
                    placeholder="Value..."
                    class="w-full flex-1 min-w-[90px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-mono">
            </div>

            <!-- Sort Control -->
            <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                <span class="text-xs text-slate-400 font-semibold uppercase flex items-center gap-1.5 shrink-0"><i
                        class="fa-solid fa-arrow-down-short-wide text-emerald-400"></i>Sort:</span>
                <select id="super-admin-customers-sort-select" onchange="filterCustomersTable()"
                    class="w-full flex-1 min-w-[130px] px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-medium">
                    <option value="id-desc">ID (Newest First)</option>
                    <option value="id-asc">ID (Oldest First)</option>
                    <option value="name-asc">Name (A-Z)</option>
                    <option value="customer_status-asc">Status (A-Z)</option>
                    <option value="phone-asc">Phone Number (A-Z)</option>
                </select>
            </div>

            <!-- Visible Columns Dropdown Toggle -->
            <div class="relative inline-block text-left flex-1 min-w-[130px] sm:flex-initial">
                <button type="button" id="btn-cust-column-toggle" onclick="toggleCustColumnMenu(event)"
                    class="w-full px-3.5 py-2.5 rounded-xl text-xs bg-slate-900 border border-slate-800 text-slate-200 font-semibold hover:bg-slate-800 transition flex items-center justify-between gap-2 shadow-sm">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-columns text-emerald-400"></i> Columns
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                </button>
                <div id="cust-column-menu" class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-slate-950/95 backdrop-blur-xl border border-slate-800 shadow-2xl p-3 z-[100] text-xs space-y-2">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2 mb-1">
                            <span class="font-bold text-slate-200 uppercase tracking-wider text-[10px]">Show / Hide Columns</span>
                            <button type="button" onclick="resetCustColumns()" class="text-[10px] text-emerald-400 hover:underline font-semibold">Select All</button>
                        </div>
                        <div class="space-y-1.5 max-h-60 overflow-y-auto custom-scrollbar">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-cust-col="id" checked onchange="toggleCustColumn('id', this.checked)" class="w-3.5 h-3.5 accent-emerald-500 rounded bg-slate-900 border-slate-700">
                                <span>Cust ID</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-cust-col="name" checked onchange="toggleCustColumn('name', this.checked)" class="w-3.5 h-3.5 accent-emerald-500 rounded bg-slate-900 border-slate-700">
                                <span>Customer Name</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-cust-col="phone" checked onchange="toggleCustColumn('phone', this.checked)" class="w-3.5 h-3.5 accent-emerald-500 rounded bg-slate-900 border-slate-700">
                                <span>Phone Number</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-cust-col="id_card" checked onchange="toggleCustColumn('id_card', this.checked)" class="w-3.5 h-3.5 accent-emerald-500 rounded bg-slate-900 border-slate-700">
                                <span>ID Card Number</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-cust-col="address" checked onchange="toggleCustColumn('address', this.checked)" class="w-3.5 h-3.5 accent-emerald-500 rounded bg-slate-900 border-slate-700">
                                <span>Home Address</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-cust-col="vehicles" checked onchange="toggleCustColumn('vehicles', this.checked)" class="w-3.5 h-3.5 accent-emerald-500 rounded bg-slate-900 border-slate-700">
                                <span>Vehicles</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white select-none py-0.5">
                                <input type="checkbox" data-cust-col="actions" checked onchange="toggleCustColumn('actions', this.checked)" class="w-3.5 h-3.5 accent-emerald-500 rounded bg-slate-900 border-slate-700">
                                <span>Actions</span>
                            </label>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <!-- Top Pagination -->
    <div id="super-admin-customers-pagination-top" class="my-4"></div>

    <!-- Customers Table -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
        <div class="w-full min-w-0 max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full text-center text-xs border-collapse min-w-[800px]">
                <thead>
                    <tr id="super-admin-customers-thead-tr"
                        class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                        <th class="p-3 w-20 text-center">Cust ID</th>
                        <th class="p-3 w-44 text-center">Customer Name</th>
                        <th class="p-3 w-32 text-center">Phone Number</th>
                        <th class="p-3 w-36 text-center">ID Card Number</th>
                        <th class="p-3 min-w-[200px] text-center">Home Address</th>
                        <th class="p-3 w-24 text-center">Vehicles</th>
                        <th class="p-3 w-28 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="super-admin-customers-tbody"
                    class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center font-sans">
                    <!-- Injected JS customer rows -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Pagination -->
    <div id="super-admin-customers-pagination-bottom" class="my-4"></div>
</div>


