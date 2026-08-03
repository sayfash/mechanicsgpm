<!-- Modal: Add Common Issue -->
<div id="add-common-issue-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2">Add Common Issue</h3>
        <p class="text-xs text-slate-400 mb-4">Create a standard issue diagnosis option for customer checklists.</p>
        <form onsubmit="handleSubmitAddCommonIssue(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Issue Name</label>
                <input type="text" id="mgt-add-issue-name" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                    placeholder="e.g. Battery Damage">
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeAddCommonIssueModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Add
                    Issue</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Common Issue -->
<div id="edit-common-issue-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2">Edit Common Issue</h3>
        <p class="text-xs text-slate-400 mb-4">Rename this checklist option.</p>
        <form onsubmit="handleSubmitEditCommonIssue(event)" class="space-y-4">
            <input type="hidden" id="mgt-edit-issue-id">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Issue Name</label>
                <input type="text" id="mgt-edit-issue-name" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeEditCommonIssueModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Save
                    Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Mechanic Form Item -->
<div id="add-form-item-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2">Add Checklist Item</h3>
        <p class="text-xs text-slate-400 mb-4">Create a new mandatory checking task for mechanics.</p>
        <form onsubmit="handleSubmitAddFormItem(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Item Label</label>
                <input type="text" id="mgt-add-item-label" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                    placeholder="e.g. Tire Pressure Check">
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeAddMechanicFormItemModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Add
                    Item</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Mechanic Form Item -->
<div id="edit-form-item-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2">Edit Checklist Item</h3>
        <p class="text-xs text-slate-400 mb-4">Rename this checking task.</p>
        <form onsubmit="handleSubmitEditFormItem(event)" class="space-y-4">
            <input type="hidden" id="mgt-edit-item-id">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Item Label</label>
                <input type="text" id="mgt-edit-item-label" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeEditMechanicFormItemModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Save
                    Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Customers Batch Import (XLSX/CSV) -->
<div id="customers-csv-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-xl rounded-2xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-400"></i> Import Batch Customers (XLSX / CSV)
            </h3>
            <button onclick="closeCustomersCSVModal()" class="text-slate-400 hover:text-slate-200 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p class="text-xs text-slate-400 mb-4">Batch register or update customer profiles by uploading a
            spreadsheet.</p>

        <!-- Download Template Section -->
        <div class="bg-blue-950/40 border border-blue-800/60 rounded-xl p-4 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-600/20 text-blue-400 rounded-lg">
                    <i class="fa-solid fa-file-csv text-xl"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-200">Customer Template File (.xlsx)</h4>
                    <p class="text-[11px] text-slate-400">Formatted template with Customer Name, Phone, KTP, Status
                        & Branch.</p>
                </div>
            </div>
            <button onclick="downloadCustomersBatchTemplate()"
                class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow-lg shadow-blue-500/20 whitespace-nowrap">
                <i class="fa-solid fa-download"></i> Download XLSX Template
            </button>
        </div>

        <!-- Spreadsheet Instructions & Column Mapping -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 text-xs text-slate-300 space-y-2 mb-5">
            <div class="font-bold text-slate-200 flex items-center gap-1.5 text-xs">
                <i class="fa-solid fa-circle-info text-cyan-400"></i> Customer Column Structure:
            </div>
            <div
                class="bg-slate-950/80 border border-slate-800/80 rounded-lg p-2.5 text-[11px] font-mono text-cyan-300">
                Customer Name, Phone, ID Card Number, Address, Customer Status, Branch
            </div>
        </div>

        <!-- File Parsing Progress Indicator -->
        <div id="cust-import-progress-container"
            class="hidden bg-slate-900/90 border border-slate-800 rounded-xl p-4 mb-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span id="cust-progress-status-text" class="font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-spinner animate-spin text-cyan-400"></i> Reading & Parsing File...
                </span>
                <span id="cust-progress-percent" class="font-mono font-bold text-cyan-400">0%</span>
            </div>
            <div class="w-full h-2.5 bg-slate-950 rounded-full overflow-hidden border border-slate-800/80">
                <div id="cust-progress-bar"
                    class="h-full bg-gradient-to-r from-blue-500 via-cyan-400 to-emerald-400 w-0 transition-all duration-300 rounded-full shadow-lg shadow-cyan-500/20">
                </div>
            </div>
            <div id="cust-progress-details" class="text-[11px] text-slate-400 font-mono">
                Initializing parsing engine...
            </div>
        </div>

        <!-- Import Success / Completion Notification Banner -->
        <div id="cust-import-success-banner"
            class="hidden bg-emerald-950/40 border border-emerald-500/40 rounded-xl p-4 mb-4 space-y-2">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-lg shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 id="cust-success-title" class="text-xs font-bold text-emerald-300">Import Completed &
                        Database Updated!</h4>
                    <p id="cust-success-message" class="text-[11px] text-slate-300 mt-0.5">All customer profiles
                        have been committed.</p>
                </div>
            </div>
        </div>

        <!-- Embedded Preview Grid & Action inside Modal -->
        <div id="cust-import-preview-pane" class="hidden space-y-3 mt-4 border-t border-slate-800 pt-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass-chart text-amber-400"></i> Verification Preview
                </span>
                <button id="btn-cust-submit-import-batch" onclick="submitImportBatch()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-xs transition shadow-lg shadow-emerald-600/20">
                    Commit Batch Upload to Database
                </button>
            </div>
            <div class="overflow-x-auto max-h-[220px] custom-scrollbar border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead id="cust-import-preview-thead" class="bg-slate-900 text-slate-400 sticky top-0">
                        <!-- Dynamic Headers -->
                    </thead>
                    <tbody id="cust-import-preview-tbody" class="divide-y divide-slate-800/80 text-slate-300 font-mono">
                        <!-- Preview rows -->
                    </tbody>
                </table>
            </div>
        </div>

        <input type="file" id="csv-import-customers-file" accept=".xlsx,.xls,.csv" class="hidden"
            onchange="parseCSVImport('customers')">
        <button id="btn-select-customers-file"
            onclick="document.getElementById('csv-import-customers-file').click()"
            class="w-full py-3 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-400 border border-dashed border-emerald-500/30 hover:border-emerald-500 font-bold rounded-xl text-sm transition flex items-center justify-center gap-2 mt-4">
            <i class="fa-solid fa-file-arrow-up text-lg"></i> Select Customer Batch File (.xlsx, .xls, .csv)
        </button>

        <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
            <button type="button" onclick="closeCustomersCSVModal()"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Close</button>
        </div>
    </div>
</div>

<!-- Modal: Vehicles Batch Import (XLSX/CSV) -->
<div id="vehicles-csv-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-xl rounded-2xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-motorcycle text-cyan-400"></i> Import Batch Vehicles (XLSX / CSV)
            </h3>
            <button onclick="closeVehiclesCSVModal()" class="text-slate-400 hover:text-slate-200 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p class="text-xs text-slate-400 mb-4">Batch register or update vehicle specifications and optional customer
            ownership links by uploading a spreadsheet.</p>

        <!-- Download Template Section -->
        <div class="bg-blue-950/40 border border-blue-800/60 rounded-xl p-4 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-cyan-600/20 text-cyan-400 rounded-lg">
                    <i class="fa-solid fa-file-csv text-xl"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-200">Vehicle Template File (.xlsx)</h4>
                    <p class="text-[11px] text-slate-400">Formatted template with Plate, Type, Make, Model, VIN,
                        Controller & Branch.</p>
                </div>
            </div>
            <button onclick="downloadVehiclesBatchTemplate()"
                class="px-3.5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow-lg shadow-cyan-500/20 whitespace-nowrap">
                <i class="fa-solid fa-download"></i> Download XLSX Template
            </button>
        </div>

        <!-- Spreadsheet Instructions & Column Mapping -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 text-xs text-slate-300 space-y-2 mb-5">
            <div class="font-bold text-slate-200 flex items-center gap-1.5 text-xs">
                <i class="fa-solid fa-circle-info text-cyan-400"></i> Vehicle Column Structure:
            </div>
            <div
                class="bg-slate-950/80 border border-slate-800/80 rounded-lg p-2.5 text-[11px] font-mono text-cyan-300">
                License Plate, Vehicle Type, Make, Model, Year, Frame Number / VIN, Engine Number, Controller
                Number, Customer Name, Branch
            </div>
        </div>

        <!-- File Parsing Progress Indicator -->
        <div id="veh-import-progress-container"
            class="hidden bg-slate-900/90 border border-slate-800 rounded-xl p-4 mb-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span id="veh-progress-status-text" class="font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-spinner animate-spin text-cyan-400"></i> Reading & Parsing File...
                </span>
                <span id="veh-progress-percent" class="font-mono font-bold text-cyan-400">0%</span>
            </div>
            <div class="w-full h-2.5 bg-slate-950 rounded-full overflow-hidden border border-slate-800/80">
                <div id="veh-progress-bar"
                    class="h-full bg-gradient-to-r from-blue-500 via-cyan-400 to-emerald-400 w-0 transition-all duration-300 rounded-full shadow-lg shadow-cyan-500/20">
                </div>
            </div>
            <div id="veh-progress-details" class="text-[11px] text-slate-400 font-mono">
                Initializing parsing engine...
            </div>
        </div>

        <!-- Import Success / Completion Notification Banner -->
        <div id="veh-import-success-banner"
            class="hidden bg-emerald-950/40 border border-emerald-500/40 rounded-xl p-4 mb-4 space-y-2">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-lg shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 id="veh-success-title" class="text-xs font-bold text-emerald-300">Import Completed &
                        Database Updated!</h4>
                    <p id="veh-success-message" class="text-[11px] text-slate-300 mt-0.5">All vehicle records have
                        been committed.</p>
                </div>
            </div>
        </div>

        <!-- Embedded Preview Grid & Action inside Modal -->
        <div id="veh-import-preview-pane" class="hidden space-y-3 mt-4 border-t border-slate-800 pt-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass-chart text-amber-400"></i> Verification Preview
                </span>
                <button id="btn-veh-submit-import-batch" onclick="submitImportBatch()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-xs transition shadow-lg shadow-emerald-600/20">
                    Commit Batch Upload to Database
                </button>
            </div>
            <div class="overflow-x-auto max-h-[220px] custom-scrollbar border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead id="veh-import-preview-thead" class="bg-slate-900 text-slate-400 sticky top-0">
                        <!-- Dynamic Headers -->
                    </thead>
                    <tbody id="veh-import-preview-tbody" class="divide-y divide-slate-800/80 text-slate-300 font-mono">
                        <!-- Preview rows -->
                    </tbody>
                </table>
            </div>
        </div>

        <input type="file" id="csv-import-vehicles-file" accept=".xlsx,.xls,.csv" class="hidden"
            onchange="parseCSVImport('vehicles')">
        <button id="btn-select-vehicles-file" onclick="document.getElementById('csv-import-vehicles-file').click()"
            class="w-full py-3 bg-cyan-600/10 hover:bg-cyan-600/20 text-cyan-400 border border-dashed border-cyan-500/30 hover:border-cyan-500 font-bold rounded-xl text-sm transition flex items-center justify-center gap-2 mt-4">
            <i class="fa-solid fa-file-arrow-up text-lg"></i> Select Vehicle Batch File (.xlsx, .xls, .csv)
        </button>

        <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
            <button type="button" onclick="closeVehiclesCSVModal()"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Close</button>
        </div>
    </div>
</div>

<!-- Modal: Edit Customer Profile -->
<div id="super-admin-edit-customer-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-lg rounded-2xl p-6 shadow-2xl relative border border-slate-800">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-400"></i> Edit Customer Profile
            </h3>
            <button onclick="closeEditCustomerModal()" class="text-slate-400 hover:text-slate-200 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form onsubmit="handleEditCustomerSubmit(event)" class="space-y-4">
            <input type="hidden" id="edit-cust-id">

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Customer Name <span
                        class="text-rose-400">*</span></label>
                <input type="text" id="edit-cust-name" required
                    class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Phone Number</label>
                    <input type="text" id="edit-cust-phone"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">ID Card Number (KTP)</label>
                    <input type="text" id="edit-cust-idcard"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Customer Status Category</label>
                    <select id="edit-cust-status"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none font-bold">
                        <option value="Retail">Retail</option>
                        <option value="Fleet">Fleet</option>
                        <option value="Gomolis">Gomolis</option>
                        <option value="Gomolis-RF">Gomolis-RF</option>
                        <option value="VIP">VIP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Branch Outlet</label>
                    <select id="edit-cust-branch"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none">
                        <option value="">Global / All Branches</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Address</label>
                <textarea id="edit-cust-address" rows="2"
                    class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeEditCustomerModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-blue-500/20">Save
                    Customer Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Vehicle Specifications -->
<div id="super-admin-edit-vehicle-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-lg rounded-2xl p-6 shadow-2xl relative border border-slate-800">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-motorcycle text-cyan-400"></i> Edit Vehicle Specifications
            </h3>
            <button onclick="closeEditVehicleModal()" class="text-slate-400 hover:text-slate-200 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form onsubmit="handleEditVehicleSubmit(event)" class="space-y-4">
            <input type="hidden" id="edit-veh-id">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">License Plate <span
                            class="text-rose-400">*</span></label>
                    <input type="text" id="edit-veh-plate" required
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-blue-500 focus:outline-none uppercase">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Vehicle Type</label>
                    <input type="text" id="edit-veh-type"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Brand</label>
                    <input type="text" id="edit-veh-make"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Model</label>
                    <input type="text" id="edit-veh-model"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Color</label>
                    <input type="text" id="edit-veh-color"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none"
                        placeholder="e.g. Red Matte">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Year</label>
                    <input type="number" id="edit-veh-year"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Chassis / Frame (VIN)</label>
                    <input type="text" id="edit-veh-vin"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Engine Number</label>
                    <input type="text" id="edit-veh-engine"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Controller Code</label>
                    <input type="text" id="edit-veh-controller"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Branch Outlet</label>
                    <select id="edit-veh-branch"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none">
                        <option value="">Global / All Branches</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Bound Customer Owner</label>
                <select id="edit-veh-owner"
                    class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-blue-500 focus:outline-none">
                    <option value="">-- No Customer Bound --</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeEditVehicleModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-cyan-500/20">Save
                    Vehicle Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add New Customer Profile -->
<div id="super-admin-add-customer-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-lg rounded-2xl p-6 shadow-2xl relative border border-slate-800">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-emerald-400"></i> Register New Customer Profile
            </h3>
            <button onclick="closeAddCustomerModal()" class="text-slate-400 hover:text-slate-200 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form onsubmit="handleSubmitAddCustomer(event)" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Customer ID (Optional)</label>
                    <input type="text" id="add-cust-id"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-emerald-500 focus:outline-none uppercase font-mono"
                        placeholder="Leave blank to auto-generate">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Customer Name <span
                            class="text-rose-400">*</span></label>
                    <input type="text" id="add-cust-name" required
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-emerald-500 focus:outline-none"
                        placeholder="e.g. John Doe">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Phone Number</label>
                    <input type="text" id="add-cust-phone"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-emerald-500 focus:outline-none font-mono"
                        placeholder="e.g. 08123456789">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">ID Card Number (KTP)</label>
                    <input type="text" id="add-cust-idcard"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-emerald-500 focus:outline-none font-mono"
                        placeholder="e.g. 3201234567890001">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Customer Status Category</label>
                    <select id="add-cust-status"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-emerald-500 focus:outline-none font-bold">
                        <option value="Retail">Retail</option>
                        <option value="Fleet">Fleet</option>
                        <option value="Gomolis">Gomolis</option>
                        <option value="Gomolis-RF">Gomolis-RF</option>
                        <option value="VIP">VIP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Branch Outlet</label>
                    <select id="add-cust-branch"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-emerald-500 focus:outline-none">
                        <option value="">Global / All Branches</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Address</label>
                <textarea id="add-cust-address" rows="2"
                    class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-emerald-500 focus:outline-none"
                    placeholder="Street Address, City"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeAddCustomerModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-emerald-500/20">Register
                    Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add New Vehicle -->
<div id="super-admin-add-vehicle-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-lg rounded-2xl p-6 shadow-2xl relative border border-slate-800">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-motorcycle text-cyan-400"></i> Register New Vehicle
            </h3>
            <button onclick="closeAddVehicleModal()" class="text-slate-400 hover:text-slate-200 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form onsubmit="handleSubmitAddVehicle(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Vehicle ID (Optional)</label>
                <input type="text" id="add-veh-id"
                    class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-cyan-500 focus:outline-none uppercase"
                    placeholder="Leave blank to auto-generate">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">License Plate <span
                            class="text-rose-400">*</span></label>
                    <input type="text" id="add-veh-plate" required
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-cyan-500 focus:outline-none uppercase"
                        placeholder="e.g. B 1234 ABC">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Vehicle Type</label>
                    <input type="text" id="add-veh-type"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-cyan-500 focus:outline-none"
                        placeholder="e.g. EV Scooter Gesits">
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Brand</label>
                    <input type="text" id="add-veh-make"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-cyan-500 focus:outline-none"
                        placeholder="e.g. Gesits">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Model</label>
                    <input type="text" id="add-veh-model"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-cyan-500 focus:outline-none"
                        placeholder="e.g. G1">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Color</label>
                    <input type="text" id="add-veh-color"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-cyan-500 focus:outline-none"
                        placeholder="e.g. Red Matte">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Year</label>
                    <input type="number" id="add-veh-year"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-cyan-500 focus:outline-none"
                        placeholder="2024">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Chassis / Frame (VIN)</label>
                    <input type="text" id="add-veh-vin"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-cyan-500 focus:outline-none"
                        placeholder="e.g. MH123456789">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Engine Number</label>
                    <input type="text" id="add-veh-engine"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-cyan-500 focus:outline-none"
                        placeholder="e.g. ENG-9988">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Controller Code</label>
                    <input type="text" id="add-veh-controller"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 font-mono focus:border-cyan-500 focus:outline-none"
                        placeholder="e.g. CTL-4455">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Branch Outlet</label>
                    <select id="add-veh-branch"
                        class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-cyan-500 focus:outline-none">
                        <option value="">Global / All Branches</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-1">Bound Customer Owner</label>
                <select id="add-veh-owner"
                    class="w-full px-3 py-2 rounded-lg text-xs bg-slate-900 border border-slate-800 text-slate-100 focus:border-cyan-500 focus:outline-none">
                    <option value="">-- No Customer Bound --</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeAddVehicleModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-cyan-500/20">Register
                    Vehicle</button>
            </div>
        </form>
    </div>
</div>
