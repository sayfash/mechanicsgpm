<!-- Modal: Shop Admin Add Inventory Item -->
<div id="shop-admin-add-inventory-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-boxes-packing text-blue-400"></i> Add New Inventory Item
        </h3>
        <p class="text-xs text-slate-400 mb-4">Register a new spare part in your branch inventory ledger.</p>
        <form onsubmit="handleSubmitShopAdminAddInventory(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">SKU Code</label>
                <input type="text" id="shop-add-inv-sku" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200 font-mono"
                    placeholder="e.g. BRK-001">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Part Name</label>
                <input type="text" id="shop-add-inv-name" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                    placeholder="e.g. Front Brake Pad Set">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Spare Part Category</label>
                <select id="shop-add-inv-category" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                    <!-- Populated JS -->
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-amber-400 mb-1 uppercase">Warranty Category</label>
                <select id="shop-add-inv-warranty-category" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-amber-900/50 text-amber-300 font-bold">
                    <option value="Warranty A">Warranty A (under 6 months / 10k KM)</option>
                    <option value="Warranty B">Warranty B (under 1 year / 10k KM)</option>
                    <option value="Warranty C">Warranty C (under 2 years / 20k KM)</option>
                    <option value="Unclaimable / No Warranty" selected>Unclaimable / No Warranty</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Connected Service
                    (Optional)</label>
                <select id="shop-add-inv-connected-service"
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                    <!-- Populated JS -->
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Available Qty</label>
                    <input type="number" id="shop-add-inv-qty" min="0" required value="0"
                        class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Price (IDR)</label>
                    <input type="number" id="shop-add-inv-price" min="0" step="1000" required value="0"
                        class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200 font-mono">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeShopAdminAddInventoryModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Save
                    Item</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Shop Admin Edit Inventory Item -->
<div id="shop-admin-edit-inventory-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-blue-400"></i> Update Spare Part Stock & Price
        </h3>
        <p id="shop-edit-inv-subtitle" class="text-xs text-slate-400 mb-4">Editing inventory part item</p>
        <form onsubmit="handleSubmitShopAdminEditInventory(event)" class="space-y-4">
            <input type="hidden" id="shop-edit-inv-id">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Spare Part Category</label>
                <select id="shop-edit-inv-category" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                    <!-- Populated JS -->
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-amber-400 mb-1 uppercase">Warranty Category</label>
                <select id="shop-edit-inv-warranty-category" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-amber-900/50 text-amber-300 font-bold">
                    <option value="Warranty A">Warranty A (under 6 months / 10k KM)</option>
                    <option value="Warranty B">Warranty B (under 1 year / 10k KM)</option>
                    <option value="Warranty C">Warranty C (under 2 years / 20k KM)</option>
                    <option value="Unclaimable / No Warranty">Unclaimable / No Warranty</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Connected Service
                    (Optional)</label>
                <select id="shop-edit-inv-connected-service"
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                    <!-- Populated JS -->
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Spare Parts Description
                    (Optional)</label>
                <textarea id="shop-edit-inv-description" rows="2"
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                    placeholder="e.g. Compatible with Honda Beat 110cc 2018-2022"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Available Qty</label>
                    <input type="number" id="shop-edit-inv-qty" min="0" required
                        class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Price (IDR)</label>
                    <input type="number" id="shop-edit-inv-price" min="0" step="1000" required
                        class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200 font-mono">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeShopAdminEditInventoryModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Update
                    Stock</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Super Admin Add Inventory Item -->
<div id="super-admin-add-inventory-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-blue-400"></i> Add New Global Inventory Item
        </h3>
        <p class="text-xs text-slate-400 mb-4">Register a new spare part in the master inventory ledger.</p>
        <form onsubmit="handleSubmitSuperAdminAddInventory(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Target Branch</label>
                <select id="super-add-inv-branch" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                    <!-- Populated JS -->
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">SKU Code</label>
                <input type="text" id="super-add-inv-sku" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200 font-mono"
                    placeholder="e.g. BRK-001">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Part Name</label>
                <input type="text" id="super-add-inv-name" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                    placeholder="e.g. Front Brake Pad Set">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Spare Part Category</label>
                <select id="super-add-inv-category" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                    <!-- Populated JS -->
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Spare Parts Description
                    (Optional)</label>
                <textarea id="super-add-inv-description" rows="2"
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                    placeholder="e.g. Compatible with Honda Beat 110cc 2018-2022"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Available Qty</label>
                    <input type="number" id="super-add-inv-qty" min="0" required value="0"
                        class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Price (IDR)</label>
                    <input type="number" id="super-add-inv-price" min="0" step="1000" required value="0"
                        class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200 font-mono">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeSuperAdminAddInventoryModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Save
                    Item</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Global Inventory Batch Import (XLSX/CSV) -->
<div id="global-inventory-csv-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-xl rounded-2xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-400"></i> Import Batch Master Ledger (XLSX / CSV)
            </h3>
            <button onclick="closeGlobalInventoryCSVModal()" class="text-slate-400 hover:text-slate-200 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <p class="text-xs text-slate-400 mb-4">Batch register or adjust spare part stock levels and price governance
            across branches by uploading a spreadsheet.</p>

        <!-- Download Template Section -->
        <div class="bg-blue-950/40 border border-blue-800/60 rounded-xl p-4 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-600/20 text-blue-400 rounded-lg">
                    <i class="fa-solid fa-file-csv text-xl"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-200">Official XLSX Template</h4>
                    <p class="text-[11px] text-slate-400">Download formatted spreadsheet with all inventory columns.
                    </p>
                </div>
            </div>
            <button onclick="downloadInventoryBatchTemplate()"
                class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow-lg shadow-blue-500/20 whitespace-nowrap">
                <i class="fa-solid fa-download"></i> Download XLSX Template
            </button>
        </div>

        <!-- Spreadsheet Instructions & Column Mapping -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 text-xs text-slate-300 space-y-2 mb-5">
            <div class="font-bold text-slate-200 flex items-center gap-1.5 text-xs">
                <i class="fa-solid fa-circle-info text-cyan-400"></i> Import Instructions & Column Structure:
            </div>
            <ol class="list-decimal list-inside text-[11px] text-slate-400 space-y-1">
                <li>Download and fill out the official template above or use a compatible file
                    (<strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong>).</li>
                <li>The system will read the file and automatically map the following headers to the inventory
                    database:</li>
            </ol>
            <div
                class="bg-slate-950/80 border border-slate-800/80 rounded-lg p-2.5 text-[11px] font-mono text-cyan-300">
                SKU, Sparepart Name, Description, Category, Available Qty, Price, Branch
            </div>
        </div>

        <!-- File Parsing Progress Indicator -->
        <div id="batch-import-progress-container"
            class="hidden bg-slate-900/90 border border-slate-800 rounded-xl p-4 mb-4 space-y-3">
            <div class="flex items-center justify-between text-xs">
                <span id="batch-progress-status-text" class="font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-spinner animate-spin text-cyan-400"></i> Reading & Parsing File...
                </span>
                <span id="batch-progress-percent" class="font-mono font-bold text-cyan-400">0%</span>
            </div>
            <div class="w-full h-2.5 bg-slate-950 rounded-full overflow-hidden border border-slate-800/80">
                <div id="batch-progress-bar"
                    class="h-full bg-gradient-to-r from-blue-500 via-cyan-400 to-emerald-400 w-0 transition-all duration-300 rounded-full shadow-lg shadow-cyan-500/20">
                </div>
            </div>
            <div id="batch-progress-details" class="text-[11px] text-slate-400 font-mono">
                Initializing parsing engine...
            </div>
        </div>

        <!-- Import Success / Completion Notification Banner -->
        <div id="batch-import-success-banner"
            class="hidden bg-emerald-950/40 border border-emerald-500/40 rounded-xl p-4 mb-4 space-y-2">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-lg shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 id="batch-success-title" class="text-xs font-bold text-emerald-300">Import Completed &
                        Database Updated!</h4>
                    <p id="batch-success-message" class="text-[11px] text-slate-300 mt-0.5">All valid rows have been
                        mapped and committed to the database ledger.</p>
                </div>
            </div>
        </div>

        <!-- Embedded Preview Grid & Action inside Modal -->
        <div id="modal-import-preview-pane" class="hidden space-y-3 mt-4 border-t border-slate-800 pt-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass-chart text-amber-400"></i> Verification Preview
                </span>
                <button id="btn-modal-submit-import-batch" onclick="submitImportBatch()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-xs transition shadow-lg shadow-emerald-600/20">
                    Commit Batch Upload to Database
                </button>
            </div>
            <div class="overflow-x-auto max-h-[220px] custom-scrollbar border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead id="modal-import-preview-thead" class="bg-slate-900 text-slate-400 sticky top-0">
                        <!-- Dynamic Headers -->
                    </thead>
                    <tbody id="modal-import-preview-tbody" class="divide-y divide-slate-800/80 text-slate-300 font-mono">
                        <!-- Preview rows -->
                    </tbody>
                </table>
            </div>
        </div>

        <input type="file" id="csv-import-inventory-file" accept=".xlsx,.xls,.csv" class="hidden"
            onchange="parseCSVImport('inventory')">
        <button id="btn-select-inventory-file"
            onclick="document.getElementById('csv-import-inventory-file').click()"
            class="w-full py-3 bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-400 border border-dashed border-emerald-500/30 hover:border-emerald-500 font-bold rounded-xl text-sm transition flex items-center justify-center gap-2 mt-4">
            <i class="fa-solid fa-file-arrow-up text-lg"></i> Select Batch Spreadsheet File (.xlsx, .xls, .csv)
        </button>

        <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
            <button type="button" onclick="closeGlobalInventoryCSVModal()"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Close</button>
        </div>
    </div>
</div>
