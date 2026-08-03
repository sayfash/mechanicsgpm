<!-- Edit Profile Modal -->
<div id="profile-modal"
    class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 items-center justify-center hidden">
    <div class="glass-panel w-full max-w-md rounded-2xl p-6 shadow-2xl relative">
        <h3 class="text-lg font-bold text-slate-100 mb-2">Edit Profile Settings</h3>
        <p class="text-xs text-slate-400 mb-4">Modify display name (username) and change security keys.</p>

        <!-- Current Profile Preview Badge -->
        <div class="flex items-center gap-4 mb-4 p-3 bg-slate-900/40 rounded-xl border border-slate-800/60">
            <div
                class="w-14 h-14 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-blue-400 overflow-hidden shrink-0 shadow-inner">
                <i id="modal-profile-picture-icon" class="fa-solid fa-user-gear text-2xl"></i>
                <img id="modal-profile-picture-img" src="" class="w-full h-full object-cover hidden"
                    alt="Avatar Preview">
            </div>
            <div>
                <div id="modal-profile-username-display" class="text-sm font-bold text-slate-200">sysadmin</div>
                <div class="text-xs text-slate-400">Allowed: JPG, PNG, GIF, WEBP (Max 1MB)</div>
            </div>
        </div>

        <form onsubmit="handleProfileUpdate(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Display Name</label>
                <input type="text" id="profile-new-display-name" required
                    class="w-full px-3 py-2 rounded-lg text-sm" placeholder="Display Name">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1 uppercase">Upload New Picture</label>
                <input type="file" id="profile-new-picture" accept="image/*"
                    onchange="previewSelectedProfilePicture(event)"
                    class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600/10 file:text-blue-400 hover:file:bg-blue-600/20">
            </div>

            <div class="border-t border-slate-800/60 pt-4">
                <span class="text-xs font-bold text-slate-400 block mb-2 uppercase">Change Password
                    (Optional)</span>
                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-0.5">Old Password</label>
                        <input type="password" id="profile-old-password" class="w-full px-3 py-2 rounded-lg text-sm"
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-0.5">New Password</label>
                        <input type="password" id="profile-new-password" class="w-full px-3 py-2 rounded-lg text-sm"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 border-t border-slate-800/80 pt-4">
                <button type="button" onclick="closeProfileModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm font-semibold rounded-lg">Cancel</button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-lg">Save
                    Changes</button>
            </div>
        </form>
    </div>
</div>
