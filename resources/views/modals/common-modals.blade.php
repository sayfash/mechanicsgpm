<!-- Start New Job Modal -->
<div id="new-job-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-4">Start New Repair Job</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Search Intake Vehicle
                    (License Plate)</label>
                <div class="relative">
                    <input type="text" id="job-search-query" oninput="debounceLookup()"
                        class="w-full px-3 py-2 rounded-lg text-sm font-mono uppercase" placeholder="Search plates...">
                    <div id="lookup-dropdown-results"
                        class="absolute left-0 right-0 mt-1 max-h-40 overflow-y-auto bg-slate-900 border border-slate-800 rounded-lg shadow-2xl z-50 hidden text-sm divide-y divide-slate-800">
                    </div>
                </div>
            </div>

            <div id="selected-vehicle-display"
                class="hidden bg-slate-900/80 p-3 rounded-lg border border-slate-800 text-xs">
                <span class="text-slate-400 font-bold block">Selected Vehicle:</span>
                <span id="selected-vehicle-info" class="text-slate-200 font-mono"></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Job Description</label>
                <textarea id="job-description" rows="3" required class="w-full px-3 py-2 rounded-lg text-sm"
                    placeholder="Details of repair task..."></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
            <button onclick="closeNewJobModal()"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
            <button onclick="submitNewJob()"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Create
                Job</button>
        </div>
    </div>
</div>

<!-- Modal: Maintenance Record Full Details -->
<div id="maintenance-record-details-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden p-4">
    <div class="glass-panel w-full max-w-3xl rounded-2xl p-6 shadow-2xl relative max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-blue-600/10 border border-blue-500/20 flex items-center justify-center text-blue-400 font-bold">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
                <div>
                    <h3 id="record-detail-title" class="text-lg font-bold text-slate-100">Maintenance Record Details
                    </h3>
                    <p id="record-detail-subtitle" class="text-xs text-slate-400 font-mono">JOB-000</p>
                </div>
            </div>
            <button onclick="closeRecordDetailsModal()"
                class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-slate-200 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Scrollable Content Body -->
        <div id="record-detail-body" class="overflow-y-auto space-y-6 pr-2 custom-scrollbar flex-1 text-xs">
            <!-- Dynamically populated JS -->
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 border-t border-slate-800/80 pt-4 mt-4">
            <button onclick="closeRecordDetailsModal()"
                class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-lg text-xs transition">Close</button>
        </div>
    </div>
</div>

<!-- Modal: Custom Confirm Action / Delete Modal -->
<div id="custom-confirm-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[9990] items-center justify-center hidden p-4">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative border border-rose-500/30">
        <div class="flex items-center gap-3 border-b border-slate-800 pb-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 font-bold text-lg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 id="confirm-modal-title" class="text-base font-bold text-slate-100">Confirm Deletion</h3>
                <p class="text-[11px] text-slate-400">This action cannot be undone.</p>
            </div>
        </div>

        <p id="confirm-modal-message" class="text-xs text-slate-300 mb-6 leading-relaxed">
            Are you sure you want to delete this record?
        </p>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <button type="button" onclick="closeConfirmDeleteModal()"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">
                Cancel
            </button>
            <button type="button" id="confirm-modal-action-btn"
                class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-rose-600/20 flex items-center gap-1.5">
                <i class="fa-solid fa-trash"></i> Delete Permanently
            </button>
        </div>
    </div>
</div>
