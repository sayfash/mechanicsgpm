<!-- Mobile & Tablet Header (< 768px) -->
<header id="mobile-nav-header" class="hidden flex md:hidden sticky top-0 z-40 bg-slate-950/90 border-b border-slate-800 px-4 py-3 items-center justify-between backdrop-blur-md">
    <div class="flex items-center gap-3">
        <button id="mobile-hamburger-btn" onclick="toggleMobileDrawer()" aria-label="Toggle Navigation Menu" aria-expanded="false" aria-controls="mobile-nav-drawer" class="p-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 text-white p-1.5 rounded-lg shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-infinity text-sm"></i>
            </div>
            <span class="font-bold text-base tracking-wider bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-400">SGPM</span>
        </div>
    </div>
    
    <!-- Mobile/Tablet User Profile Menu (Top Right Header Navigation) -->
    <div class="relative">
        <button id="mobile-profile-btn" onclick="toggleMobileProfileDropdown(event)" aria-label="User Profile Menu" class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-blue-400 hover:border-blue-500 transition cursor-pointer overflow-hidden focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i id="mobile-profile-icon" class="fa-solid fa-user text-sm m-0 p-0 mr-0"></i>
            <img id="mobile-profile-img" src="" class="w-full h-full object-cover hidden" alt="Profile">
        </button>

        <!-- Mobile/Tablet Profile Dropdown Menu -->
        <div id="mobile-profile-dropdown" class="absolute right-0 mt-2 w-60 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl z-50 hidden p-[5px] text-sm glass-panel divide-y divide-slate-800/80 text-left">
            <!-- User Details Header -->
            <div class="p-[5px] flex items-center gap-2.5 text-left">
                <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-blue-400 overflow-hidden shrink-0">
                    <i id="mobile-dropdown-profile-icon" class="fa-solid fa-user text-xs m-0 p-0 mr-0"></i>
                    <img id="mobile-dropdown-profile-img" src="" class="w-full h-full object-cover hidden" alt="Profile">
                </div>
                <div class="truncate flex-1 min-w-0 text-left">
                    <div id="mobile-user-name" class="text-xs font-semibold text-slate-100 truncate text-left">Loading...</div>
                    <div id="mobile-user-role" class="text-[10px] font-semibold text-blue-400 truncate text-left">Role</div>
                </div>
            </div>

            <!-- Menu Actions -->
            <div class="py-[5px] space-y-[2px]">
                <button onclick="openProfileModal(event); closeMobileProfileDropdown();" class="w-full text-left p-[5px] rounded-lg hover:bg-slate-800 text-slate-300 hover:text-white transition flex items-center gap-2.5">
                    <i class="fa-solid fa-user-pen text-blue-400 text-xs w-4 text-center shrink-0 mr-0"></i>
                    <span class="flex-1 text-xs text-left">Edit Profile</span>
                </button>
                <button onclick="toggleThemeMode(event)" class="w-full text-left p-[5px] rounded-lg hover:bg-slate-800 text-slate-300 transition flex items-center justify-between cursor-pointer">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-moon text-blue-400 text-xs w-4 text-center shrink-0 mr-0"></i>
                        <span class="text-xs text-left">Appearance</span>
                    </div>
                    <span class="text-[9px] font-medium bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded border border-slate-700">Theme</span>
                </button>
            </div>

            <!-- Sign Out -->
            <div class="pt-[5px]">
                <button onclick="logout()" class="w-full text-left p-[5px] rounded-lg hover:bg-slate-800 text-rose-400 transition flex items-center gap-2.5">
                    <i class="fa-solid fa-right-from-bracket text-rose-400 text-xs w-4 text-center shrink-0 mr-0"></i>
                    <span class="flex-1 text-xs text-left">Sign Out</span>
                </button>
            </div>
        </div>
    </div>
</header>
