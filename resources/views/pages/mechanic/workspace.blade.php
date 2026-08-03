<!-- ========================================== -->
<!-- MODULE: MECHANIC WORKSPACE                 -->
<!-- ========================================== -->
<section id="mechanic-workspace" class="w-full hidden space-y-8">
    <!-- Branch Selection Check-In Banner -->
    <div id="mechanic-checkin-banner" class="hidden glass-panel rounded-xl p-6 border-l-4 border-amber-500">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-map-location-dot text-2xl text-amber-500"></i>
                <div>
                    <h3 class="font-bold text-slate-200">Branch Check-In Required</h3>
                    <p class="text-sm text-slate-400">You must check into an operational branch to intake cars
                        and record parts usage.</p>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select id="mechanic-branch-select"
                    class="w-full md:w-60 px-3 py-2 rounded-lg text-sm"></select>
                <button onclick="handleMechanicCheckin()"
                    class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-sm transition">
                    Check In
                </button>
            </div>
        </div>
    </div>

    <!-- Container 1: Active Branch Station Header -->
    <div id="mechanic-active-banner"
        class="hidden flex items-center justify-between glass-panel rounded-xl px-6 py-4 mb-4">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl text-blue-400"></i>
            <div>
                <span class="text-xs text-slate-400 uppercase tracking-widest">Active Branch Station</span>
                <h4 id="mechanic-active-branch-name" class="font-bold text-slate-200">Downtown Main Office</h4>
            </div>
        </div>
        <button onclick="changeBranch()"
            class="px-3.5 py-1.5 bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrows-rotate"></i> Switch Station
        </button>
    </div>

    <!-- TAB 1: Intake & Repair Sheet -->
    <div id="mechanic-tab-form" class="hidden space-y-6">
        <div class="glass-panel rounded-2xl p-6 shadow-2xl relative overflow-hidden">
            

            <div
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4 mb-6">
                <div class="w-full sm:flex-1">
                    <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-file-medical text-blue-400"></i> Auto-Intake Repair Sheet
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5 w-full">Generate job numbers, search customer databases,
                        log issues, and track task durations.</p>
                </div>
                <div class="w-full sm:w-auto text-left sm:text-right shrink-0">
                    <span class="text-[10px] text-slate-500 uppercase font-bold block mb-0.5">Assigned Job
                        ID</span>
                    <span id="mech-job-id"
                        class="inline-block whitespace-nowrap text-sm font-mono font-bold text-blue-400 bg-blue-950/40 border border-blue-800/40 px-3 py-1 rounded">JOB-PENDING</span>
                </div>
            </div>

            <!-- Step 1: Customer Type & File Verification -->
            <div class="bg-slate-900/40 border border-slate-800 p-4 rounded-xl mb-6 space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <h4
                        class="w-full sm:w-auto text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                        <span
                            class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">1</span>
                        Customer Type & Verification
                    </h4>
                    <!-- Customer Type Selector Buttons -->
                    <div
                        class="flex rounded-lg bg-slate-950 p-1 border border-slate-800 w-full sm:w-auto">
                        <button type="button" id="btn-cust-type-external"
                            onclick="setMechanicCustomerType('external')"
                            class="flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 bg-blue-600 text-white shadow-md flex items-center justify-center">
                            <i class="fa-solid fa-user mr-1.5"></i>External Customer
                        </button>
                        <button type="button" id="btn-cust-type-internal"
                            onclick="setMechanicCustomerType('internal')"
                            class="flex-1 sm:flex-none w-full sm:w-auto px-4 py-1.5 rounded-md text-xs font-bold transition-all duration-200 text-slate-400 hover:text-slate-200 flex items-center justify-center">
                            <i class="fa-solid fa-building-user mr-1.5"></i>Internal Customer
                        </button>
                    </div>
                </div>

                <!-- Verification Search Container (Shown for Internal Customers) -->
                <div id="mech-lookup-section" class="pt-2 border-t border-slate-800/60 hidden">
                    <label class="block text-[10px] text-slate-400 uppercase tracking-wider mb-1.5">Search
                        Customer ID or ID Card Number</label>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div class="relative w-full flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i
                                    class="fa-solid fa-id-card"></i></span>
                            <input type="text" id="mech-search-query"
                                class="w-full pl-9 pr-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                                placeholder="e.g. 1 or 32012345...">
                        </div>
                        <button type="button" onclick="performMechanicCustomerLookup()"
                            class="w-full sm:w-auto px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-blue-500/10 shrink-0 flex items-center justify-center">
                            Verify Customer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Client Intake Form Layout -->
            <form onsubmit="submitMechanicJobRecord(event)" class="space-y-6">
                <!-- Step 2: Auto-Populated Generated Fields (Customer Status Editable Control) -->
                <div class="bg-slate-900/20 border border-slate-800/80 rounded-xl p-4 space-y-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                        <h4
                            class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                            <span
                                class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">2</span>
                            Vehicle & Customer Details
                        </h4>
                        <span id="mech-editable-mode-badge"
                            class="hidden text-[10px] text-emerald-400 bg-emerald-950/60 border border-emerald-800/60 px-2.5 py-0.5 rounded-full font-semibold">
                            <i class="fa-solid fa-user-plus mr-1"></i>New Customer Creation Allowed
                        </span>
                    </div>

                    <!-- Hidden link holder -->
                    <input type="hidden" id="mech-selected-vehicle-id">

                    <!-- Customer Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5 text-xs">
                        <div>
                            <label
                                class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Customer
                                Name</label>
                            <input type="text" id="mech-display-name" disabled
                                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150"
                                placeholder="e.g. John Doe">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Phone
                                Number</label>
                            <input type="text" id="mech-display-phone" disabled
                                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono focus:outline-none transition duration-150"
                                placeholder="e.g. 08123456789">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">ID
                                Card Number</label>
                            <input type="text" id="mech-display-idcard" disabled
                                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono focus:outline-none transition duration-150"
                                placeholder="e.g. 320100000000">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Address</label>
                            <input type="text" id="mech-display-address" disabled
                                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150"
                                placeholder="e.g. Jl. Sudirman No. 12">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] text-blue-400 uppercase tracking-wider mb-1 font-bold"><i
                                    class="fa-solid fa-user-tag mr-1"></i>Customer Status</label>
                            <select id="mech-customer-status" onchange="onMechanicCustomerStatusChange()"
                                class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-blue-500/30 text-blue-300 font-bold focus:border-blue-500 focus:outline-none">
                                <option value="Retail">Retail</option>
                                <option value="Gomolis">Gomolis</option>
                                <option value="Gomolis-RF">Gomolis-RF</option>
                                <option value="Gomolis-B">Gomolis-B</option>
                                <option value="Cocoride">Cocoride</option>
                                <option value="Cocoride-B">Cocoride-B</option>
                                <option value="Sales">Sales</option>
                                <option value="Operasional">Operasional</option>
                                <option value="TPI">TPI</option>
                            </select>
                        </div>
                    </div>

                    <!-- Vehicle Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 text-xs">
                        <div>
                            <label
                                class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">License
                                Plate</label>
                            <input type="text" id="mech-display-plate" disabled
                                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150"
                                placeholder="e.g. B 1234 ABC">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Vehicle
                                Type</label>
                            <input type="text" id="mech-display-type" disabled
                                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 focus:outline-none transition duration-150"
                                placeholder="e.g. Gesits G1">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Frame
                                Number (VIN)</label>
                            <input type="text" id="mech-display-frame" disabled
                                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150"
                                placeholder="e.g. MH123456789">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] text-slate-500 uppercase tracking-wider mb-1">Controller
                                Number</label>
                            <input type="text" id="mech-display-controller" disabled
                                class="w-full px-3 py-2 rounded-lg bg-slate-900/60 border border-slate-800 text-slate-400 font-mono uppercase focus:outline-none transition duration-150"
                                placeholder="e.g. CTRL-9876">
                        </div>
                    </div>
                </div>

                <!-- Step 3: Diagnostic Checklists -->
                <div class="space-y-4 bg-slate-900/20 border border-slate-800/80 rounded-xl p-4">
                    <h4
                        class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                        <span
                            class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">3</span>
                        Issue Diagnostic Log
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold">KM
                                Reached (Odometer)</label>
                            <input type="number" id="mech-km-reached" required
                                class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 font-mono"
                                placeholder="e.g. 15200">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold">Repair
                                Category</label>
                            <select id="mech-repair-category"
                                class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                                <option value="Repair">Repair</option>
                                <option value="Claim">Claim</option>
                                <option value="Stock">Stock</option>
                                <option value="Waiting">Waiting</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold">Common
                                Issues Checklist</label>
                            <div id="mech-common-issues-container"
                                class="grid grid-cols-2 gap-2 text-xs text-slate-300">
                                <!-- Injected dynamically -->
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-bold">Required
                                Mechanic Checklist Tasks</label>
                            <div id="mech-form-items-container"
                                class="grid grid-cols-2 gap-2 text-xs text-slate-300">
                                <!-- Injected dynamically -->
                            </div>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] text-slate-400 uppercase tracking-wider mb-1.5 font-bold">Other
                            Custom Issues & Service Details</label>
                        <textarea id="mech-other-issues" rows="2"
                            class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800"
                            placeholder="Describe other diagnostics or client concerns..."></textarea>
                    </div>
                </div>

                <!-- Step 4: Parts Checklist Accumulator (Max 25) -->
                <div class="space-y-4 bg-slate-900/20 border border-slate-800/80 rounded-xl p-4">
                    <h4
                        class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                        <span
                            class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">4</span>
                        Spare Parts Checklist Accumulator (Max 25)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-3">
                        <div class="col-span-3 space-y-2">
                            <div>
                                <label
                                    class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1">Search
                                    Sparepart (SKU, Name, or Description)</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-500">
                                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                    </span>
                                    <input type="text" id="mech-part-search-input"
                                        oninput="filterMechanicPartOptions()"
                                        class="w-full pl-8 pr-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200 font-mono"
                                        placeholder="Type to search SKU, Name, or Description...">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1">Select
                                    Sparepart</label>
                                <select id="mech-part-select"
                                    class="w-full px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-300 font-mono"></select>
                            </div>
                        </div>
                        <div class="flex flex-col justify-end">
                            <label class="block text-[9px] uppercase tracking-wider text-slate-500 mb-1">Qty &
                                Options</label>
                            <div class="flex gap-2 items-center">
                                <input type="number" id="mech-part-qty" value="1" min="1"
                                    class="w-16 px-2 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-300 text-center font-mono">
                                <label id="mech-part-charged-input-wrapper"
                                    class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-blue-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-part-charged-input"
                                        class="w-4 h-4 accent-blue-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span>Charged</span>
                                </label>
                                <button type="button" onclick="addPartToMechAccumulator()"
                                    class="px-4 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Accumulated Parts Grid -->
                    <div
                        class="bg-slate-950/60 rounded-lg p-3 border border-slate-900 text-xs max-h-40 overflow-x-auto overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left">
                            <thead>
                                <tr id="mech-parts-accumulator-thead-tr"
                                    class="text-slate-500 border-b border-slate-800">
                                    <th class="pb-1 font-semibold text-[10px] uppercase">Part SKU & Name</th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-20 text-right">Price
                                    </th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right">Qty
                                    </th>
                                    <th class="pb-1 font-semibold text-[10px] uppercase w-12 text-right">Delete
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="mech-parts-accumulator-tbody"
                                class="divide-y divide-slate-800/40 text-slate-300 font-mono">
                                <tr>
                                    <td colspan="4" class="py-3 text-center text-slate-600 text-[10px]">No
                                        spareparts accumulated.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-[10px] text-slate-500 text-right mt-1">
                        Total Items: <span id="mech-accumulated-total-count"
                            class="font-bold text-slate-400 font-mono">0</span> / 25
                    </div>
                </div>

                <!-- Step 5: Service Options & Other Services Section (After Spare Parts) -->
                <div class="bg-slate-900/20 border border-slate-800/80 rounded-xl p-4 space-y-4">
                    <h4
                        class="text-xs font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1.5">
                        <span
                            class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-400 inline-flex items-center justify-center font-bold text-[10px]">5</span>
                        Service Options & Other Services
                    </h4>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Service Options -->
                        <div class="space-y-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
                            <h5
                                class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-wrench"></i> Service Options
                            </h5>
                            <div class="flex gap-2 items-center">
                                <select id="mech-service-select"
                                    class="flex-1 px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200">
                                    <option value="">-- Choose Service Option --</option>
                                </select>
                                <label id="mech-service-charged-input-wrapper"
                                    class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-cyan-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-service-charged-input"
                                        class="w-4 h-4 accent-cyan-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span>Charged</span>
                                </label>
                                <button type="button" onclick="addServiceToMechAccumulator()"
                                    class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                            <div
                                class="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 max-h-32 overflow-y-auto custom-scrollbar">
                                <div id="mech-services-accumulator-list"
                                    class="space-y-1 text-xs text-slate-300 font-mono">
                                    <span class="text-slate-600 text-[10px] block text-center py-2">No service
                                        options added.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Other Services -->
                        <div class="space-y-3 bg-slate-950/60 p-3.5 rounded-xl border border-slate-800">
                            <h5
                                class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-hand-holding-dollar"></i> Other Services
                            </h5>
                            <div class="flex gap-2 items-center">
                                <select id="mech-other-service-select"
                                    class="flex-1 px-3 py-1.5 rounded bg-slate-950 border border-slate-800 text-xs text-slate-200">
                                    <option value="">-- Choose Other Service --</option>
                                </select>
                                <label id="mech-other-service-charged-input-wrapper"
                                    class="hidden inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-950 rounded-lg text-xs text-amber-400 font-bold cursor-pointer select-none shrink-0">
                                    <input type="checkbox" id="mech-other-service-charged-input"
                                        class="w-4 h-4 accent-amber-500 rounded border-slate-700 bg-slate-900 cursor-pointer">
                                    <span>Charged</span>
                                </label>
                                <button type="button" onclick="addOtherServiceToMechAccumulator()"
                                    class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-xs shrink-0">+</button>
                            </div>
                            <div
                                class="bg-slate-900/80 rounded-lg p-2.5 border border-slate-800 max-h-32 overflow-y-auto custom-scrollbar">
                                <div id="mech-other-services-accumulator-list"
                                    class="space-y-1 text-xs text-slate-300 font-mono">
                                    <span class="text-slate-600 text-[10px] block text-center py-2">No other
                                        services added.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Estimated Grand Total Banner (After Spare Parts) -->
                <div
                    class="glass-panel rounded-xl p-4 border border-emerald-500/30 bg-slate-900 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h5
                            class="text-xs font-bold text-emerald-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-calculator"></i> Estimated Grand Total Summary
                        </h5>
                        <div class="flex items-center gap-4 text-xs text-slate-400">
                            <span>Services: <strong id="summary-services-fee" class="text-cyan-400 font-mono">Rp
                                    0</strong></span>
                            <span>•</span>
                            <span>Other Services: <strong id="summary-other-fee" class="text-amber-400 font-mono">Rp
                                    0</strong></span>
                            <span>•</span>
                            <span>Spareparts: <strong id="summary-parts-fee" class="text-blue-400 font-mono">Rp
                                    0</strong></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span
                            class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Estimated
                            Grand Total</span>
                        <span id="summary-grand-total"
                            class="text-2xl font-extrabold text-emerald-400 font-mono">Rp 0</span>
                    </div>
                </div>

                <!-- Step 4: Timer & Final Submission Controls -->
                <div
                    class="glass-panel rounded-xl p-4 border border-slate-800 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-6 mt-6">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-6 w-full md:w-auto">
                        <!-- Clock widget -->
                        <div
                            class="bg-slate-950 border border-slate-800 rounded-xl px-5 py-2.5 text-center font-mono relative overflow-hidden w-full sm:w-auto">
                            <div class="absolute inset-0 bg-blue-500/5 pulse-bg z-0 pointer-events-none"></div>
                            <span
                                class="text-[9px] uppercase tracking-widest text-slate-500 font-bold block mb-0.5">Repair
                                Timer</span>
                            <span id="mech-timer-display"
                                class="text-2xl font-bold text-blue-400 z-10 relative font-mono">00:00:00</span>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <button type="button" id="btn-mech-start-timer" onclick="startMechanicTimerClock()"
                                class="w-full sm:w-auto px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                                <i class="fa-solid fa-play"></i> Start Fixing
                            </button>
                            <button type="button" id="btn-mech-stop-timer" onclick="stopMechanicTimerClock()"
                                disabled
                                class="w-full sm:w-auto px-5 py-3 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/25 font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 disabled:opacity-30 disabled:cursor-not-allowed">
                                <i class="fa-solid fa-stop"></i> Stop Timer
                            </button>
                        </div>

                        <div class="flex flex-col text-[10px] text-slate-500 gap-1 ml-0 sm:ml-2 font-semibold">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                <span>Start: <span id="mech-start-timestamp"
                                        class="text-slate-300 font-mono">-</span></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>Stop: <span id="mech-stop-timestamp"
                                        class="text-slate-300 font-mono">-</span></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="btn-mech-submit-record" disabled
                        class="w-full md:w-auto px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-emerald-600/20 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> Submit Repair Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 2: Review History Logs (Table / Calendar Views) -->
    <div id="mechanic-tab-review" class="hidden space-y-6">
        <!-- Search panel -->
        <div class="flex flex-col md:flex-row items-stretch md:items-end gap-4 glass-panel rounded-2xl p-6">
            <div class="w-full flex-1">
                <label class="block text-xs font-semibold text-slate-400 mb-1.5 uppercase tracking-wider">Search
                    Maintenance History (Customer ID / ID Card Number)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500"><i
                            class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="mech-history-query"
                        class="w-full pl-9 pr-3 py-2.5 rounded-lg text-sm bg-slate-950 border border-slate-800 font-mono"
                        placeholder="e.g. 1 or 32012345...">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <button onclick="fetchMechanicHistoryRecords()"
                    class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-blue-500/10 flex items-center justify-center">
                    Search Records
                </button>

                <div class="hidden sm:block h-10 w-px bg-slate-800"></div>

                <!-- Toggle switcher -->
                <div class="flex w-full sm:w-auto bg-slate-950 p-1 rounded-lg border border-slate-800">
                    <button onclick="setHistoryViewMode('table')" id="btn-history-view-table"
                        class="flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 bg-slate-800 text-slate-200 flex items-center justify-center">
                        <i class="fa-solid fa-list mr-1"></i> List Table
                    </button>
                    <button onclick="setHistoryViewMode('calendar')" id="btn-history-view-calendar"
                        class="flex-1 sm:flex-none px-3 py-1.5 rounded font-bold text-xs transition duration-150 text-slate-400 hover:text-slate-200 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days mr-1"></i> Calendar Grid
                    </button>
                </div>
            </div>
        </div>

        <!-- Table View Interface -->
        <div id="mech-history-table-view" class="glass-panel rounded-2xl overflow-hidden shadow-2xl space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-300">
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-40">Date</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Branch Station</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Vehicle License</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Diagnostic Issues
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">KM Reached</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-28 text-center">
                                Billed Parts</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-20">Details</th>
                        </tr>
                    </thead>
                    <tbody id="mech-history-list-tbody"
                        class="divide-y divide-slate-800/80 text-slate-300 text-xs font-mono">
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500 font-sans">Provide Customer ID or ID
                                Card above to search logs.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Calendar View Interface -->
        <div id="mech-history-calendar-view" class="glass-panel rounded-2xl p-6 shadow-2xl hidden space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h4 class="text-md font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-blue-400"></i> Interactive Repair Monthly Calendar
                </h4>
                <div class="flex items-center gap-2">
                    <button onclick="adjustCalendarMonth(-1)"
                        class="w-8 h-8 rounded bg-slate-900 border border-slate-800 hover:bg-slate-800 transition text-slate-300"><i
                            class="fa-solid fa-chevron-left text-xs"></i></button>
                    <span id="calendar-month-year-label"
                        class="text-sm font-bold text-slate-200 w-36 text-center block">July 2026</span>
                    <button onclick="adjustCalendarMonth(1)"
                        class="w-8 h-8 rounded bg-slate-900 border border-slate-800 hover:bg-slate-800 transition text-slate-300"><i
                            class="fa-solid fa-chevron-right text-xs"></i></button>
                </div>
            </div>

            <!-- Month Calendar Grid -->
            <div class="grid grid-cols-7 gap-2 text-center text-xs">
                <!-- Days Headers -->
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Sun</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Mon</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Tue</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Wed</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Thu</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Fri</div>
                <div class="py-2 text-slate-500 uppercase font-bold tracking-wider">Sat</div>
            </div>
            <div id="calendar-days-grid" class="grid grid-cols-7 gap-2 text-center text-xs">
                <!-- Days populated by JavaScript -->
            </div>

            <div class="text-[10px] text-slate-500 flex items-center gap-1.5 justify-end">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> Indicates repair events
                logged on this date.
            </div>
        </div>
    </div>

    <!-- TAB 3: My Repair History -->
    <div id="mechanic-tab-my-history" class="hidden space-y-6">
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel rounded-2xl p-6">
            <div>
                <h3 class="text-xl font-bold text-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-400"></i> My Completed Repair Jobs
                </h3>
                <p class="text-sm text-slate-400 mt-1">Review the list of repair tickets submitted by your
                    account.</p>
            </div>
        </div>

        <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-300">
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-40">Date & Time</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Customer Assigned
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">License Plate</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-32">Repair Time</th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs">Job Notes & Checklist
                            </th>
                            <th class="p-4 font-semibold uppercase tracking-wider text-xs w-32 text-right">Parts
                                Details</th>
                        </tr>
                    </thead>
                    <tbody id="mechanic-my-history-tbody" class="divide-y divide-slate-800/80 text-slate-300 font-mono">
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 font-semibold font-sans">No records
                                found. Submit jobs to see them here!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
