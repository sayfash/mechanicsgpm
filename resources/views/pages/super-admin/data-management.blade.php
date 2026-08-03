<!-- SUB-TAB: Data Management CRUD Console -->
<div id="super-admin-tab-management" class="w-full min-w-0 max-w-full space-y-6 hidden">
    <!-- Top Main Container Sub-Tabs Bar for Data Management -->
    <div class="w-full flex items-center gap-2 overflow-x-auto whitespace-nowrap glass-panel p-2.5 rounded-2xl border border-slate-800/80 shadow-lg custom-scrollbar">
        <button onclick="setManagementSubTab('branches')" id="btn-mgt-branches"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-sitemap text-indigo-400"></i><span>Branches</span>
            <span id="badge-mgt-branches-count" class="bg-slate-900 text-indigo-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('users')" id="btn-mgt-users"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-users text-blue-400"></i><span>Users List</span>
            <span id="badge-mgt-users-count" class="bg-slate-900 text-blue-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('common-issues')" id="btn-mgt-common-issues"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-triangle-exclamation text-amber-400"></i><span>Common Issues</span>
            <span id="badge-mgt-common-issues-count" class="bg-slate-900 text-amber-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('form-items')" id="btn-mgt-form-items"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-list-check text-emerald-400"></i><span>Form Items</span>
            <span id="badge-mgt-form-items-count" class="bg-slate-900 text-emerald-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('categories')" id="btn-mgt-categories"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-tags text-purple-400"></i><span>Categories</span>
            <span id="badge-mgt-categories-count" class="bg-slate-900 text-purple-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('service-options')" id="btn-mgt-service-options"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-screwdriver-wrench text-cyan-400"></i><span>Service Options</span>
            <span id="badge-mgt-service-options-count" class="bg-slate-900 text-cyan-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('other-services')" id="btn-mgt-other-services"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i><span>Other Services</span>
            <span id="badge-mgt-other-services-count" class="bg-slate-900 text-amber-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
    </div>

    <!-- Sub-panel Display Area in Main Container -->
    <div class="w-full min-w-0 max-w-full space-y-6">
        <!-- Sub-panel: Service Options -->
        <div id="mgt-panel-service-options" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100">Service Options</h4>
                    <p class="text-xs text-slate-400 w-full">Manage mechanic service option types, default
                        fees, and SKUs.</p>
                </div>
                <button onclick="openAddServiceOptionModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-cyan-600/10">
                    <i class="fa-solid fa-plus"></i> Add Service Option
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[500px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 w-32 text-center font-mono">SKU</th>
                            <th class="p-3 text-center">Service Name</th>
                            <th class="p-3 w-32 text-center font-mono">Fee (IDR)</th>
                            <th class="p-3 w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-service-options-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center font-mono">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Other Services -->
        <div id="mgt-panel-other-services" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100">Other Services</h4>
                    <p class="text-xs text-slate-400 w-full">Manage additional shop service items and fees.</p>
                </div>
                <button onclick="openAddOtherServiceModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-amber-600/10">
                    <i class="fa-solid fa-plus"></i> Add Other Service
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[500px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 w-32 text-center font-mono">SKU</th>
                            <th class="p-3 text-center">Service Name</th>
                            <th class="p-3 w-32 text-center font-mono">Fee (IDR)</th>
                            <th class="p-3 w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-other-services-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center font-mono">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Sparepart Categories -->
        <div id="mgt-panel-categories" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100">Spare Part Categories</h4>
                    <p class="text-xs text-slate-400 w-full">Customize spare part categories available across
                        the system.</p>
                </div>
                <button onclick="openAddCategoryModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-purple-600/10">
                    <i class="fa-solid fa-plus"></i> Add Category
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[350px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 text-center">Category Name</th>
                            <th class="p-3 w-32 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-categories-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Branches -->
        <div id="mgt-panel-branches" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100">Manage Branches</h4>
                    <p class="text-xs text-slate-400 w-full">Create new service outlets or rename existing
                        ones.</p>
                </div>
                <button onclick="openAddBranchModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-plus"></i> Add Branch
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[350px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 text-center">Branch Name</th>
                            <th class="p-3 w-28 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-branches-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Users -->
        <div id="mgt-panel-users" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100">Manage System Users</h4>
                    <p class="text-xs text-slate-400 w-full">Manage roles, branch associations, display names,
                        and password resets.</p>
                </div>
                <button onclick="openUserRegistrationModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-user-plus"></i> Register User
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[600px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-16 text-center">ID</th>
                            <th class="p-3 w-40 text-center font-mono">Username</th>
                            <th class="p-3 w-36 text-center">Designated Role</th>
                            <th class="p-3 min-w-[180px] text-center">Assigned Branch Outlet</th>
                            <th class="p-3 w-28 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-users-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center font-mono">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Customers -->
        <div id="mgt-panel-customers" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100">Manage Customers</h4>
                    <p class="text-xs text-slate-400 w-full">Create profiles, update contact credentials, or
                        review ownership lists.</p>
                </div>
                <button onclick="openAddCustomerModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-user-tag"></i> Add Customer Profile
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[700px] text-center text-xs border-collapse">
                    <thead>
                        <tr
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
                    <tbody id="mgt-customers-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center font-mono">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Common Issues -->
        <div id="mgt-panel-common-issues" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100">Manage Common Issue Diagnostics</h4>
                    <p class="text-xs text-slate-400 w-full">Add, rename, or delete standard options for
                        customer intake checklists.</p>
                </div>
                <button onclick="openAddCommonIssueModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-plus"></i> Add Common Issue
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[400px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 min-w-[250px] text-center">Issue Diagnosis Name</th>
                            <th class="p-3 w-28 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-common-issues-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Mechanic Form Items -->
        <div id="mgt-panel-form-items" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100">Manage Mechanic Form Checklist Items
                    </h4>
                    <p class="text-xs text-slate-400 w-full">Add, rename, or delete items that mechanics must
                        check off during service.</p>
                </div>
                <button onclick="openAddMechanicFormItemModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-plus"></i> Add Form Item
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[400px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 min-w-[250px] text-center">Form Item Checklist Label</th>
                            <th class="p-3 w-28 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-form-items-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
