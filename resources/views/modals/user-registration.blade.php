<!-- Modal: User Registration popup -->
<div id="user-registration-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-user-shield text-blue-400"></i> Register New User Account
        </h3>
        <p class="text-xs text-slate-400 mb-4">Create authorized profiles with designated system access roles.</p>

        <form onsubmit="handleUserRegistration(event)" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Username</label>
                    <input type="text" id="reg-username" required
                        class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                        placeholder="username">
                </div>
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Display Name</label>
                    <input type="text" id="reg-display-name"
                        class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                        placeholder="John Doe">
                </div>
            </div>
            <div>
                <label
                    class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Password</label>
                <input type="password" id="reg-password" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200"
                    placeholder="••••••••">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Role</label>
                <select id="reg-role" required
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                    <option value="mechanic">Mechanic</option>
                    <option value="shop_admin">Shop Admin (Inventory Scoped)</option>
                    <option value="inventory_admin">Inventory Admin (Branch Inventory Scoped)</option>
                    <option value="manager">Manager (Read-Only Global View)</option>
                    <option value="super_admin">Super Admin (Full Governance)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase tracking-wider">Assign
                    Branch</label>
                <select id="reg-branch"
                    class="w-full px-3 py-2 rounded-lg text-sm bg-slate-950 border border-slate-800 text-slate-200">
                    <option value="">No Branch Association (Super Admin Only)</option>
                    <!-- Injected branches -->
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeUserRegistrationModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Register
                    User</button>
            </div>
        </form>
    </div>
</div>
