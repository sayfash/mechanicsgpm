<!-- Mobile & Tablet Header (< 768px) -->
<header id="mobile-nav-header" class="hidden flex md:hidden sticky top-0 z-40 bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 px-4 py-3 items-center justify-between backdrop-blur-md">
    <div class="flex items-center gap-3">
        <button id="mobile-hamburger-btn" onclick="toggleMobileDrawer()" aria-label="Toggle Navigation Menu" aria-expanded="false" aria-controls="mobile-nav-drawer" class="p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <div class="flex items-center gap-2">
            <div class="bg-blue-600 text-white p-1.5 rounded-lg shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-infinity text-sm"></i>
            </div>
            <span class="font-bold text-base tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 dark:from-blue-400 dark:to-cyan-400">SGPM</span>
        </div>
    </div>
    
    <!-- Mobile/Tablet User Profile Menu (Top Right Header Navigation) -->
    <div class="relative">
        <button id="mobile-profile-btn" onclick="toggleMobileProfileDropdown(event)" aria-label="User Profile Menu" class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-slate-300 dark:border-slate-700 font-bold flex items-center justify-center shadow-sm hover:border-blue-500 transition cursor-pointer overflow-hidden focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i id="mobile-profile-icon" class="fa-solid fa-user text-xs m-0 p-0 mr-0 pointer-events-none"></i>
            <img id="mobile-profile-img" src="" class="w-full h-full object-cover hidden pointer-events-none" alt="Profile">
        </button>

        <!-- Mobile/Tablet Profile Dropdown Menu (Identical to Topbar) -->
        <div id="mobile-profile-dropdown" class="hidden absolute right-0 top-full mt-2 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-[99999] p-3 text-xs space-y-1 text-left">
            <!-- User Header Info -->
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-2.5 mb-1 px-3 pt-1 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-400 overflow-hidden shrink-0">
                    <i id="mobile-dropdown-profile-icon" class="fa-solid fa-user text-xs m-0 p-0 mr-0 pointer-events-none"></i>
                    <img id="mobile-dropdown-profile-img" src="" class="w-full h-full object-cover hidden pointer-events-none" alt="Profile">
                </div>
                <div class="truncate flex-1 min-w-0 text-left">
                    <div id="mobile-dropdown-user-name" class="font-bold text-slate-900 dark:text-slate-100 text-xs truncate">Santomo Group</div>
                    <div class="mt-0.5"><span id="mobile-dropdown-user-role" class="px-1.5 py-[1px] text-[8px] tracking-wider font-extrabold rounded-full inline-block bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400 border border-blue-200 dark:border-blue-800/40 uppercase">Role</span></div>
                </div>
            </div>

            <!-- Edit Profile -->
            <button type="button" onclick="openProfileModal(event); closeMobileProfileDropdown();"
                class="w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold transition flex items-center justify-start gap-2.5 cursor-pointer">
                <i class="fa-solid fa-user text-blue-500 dark:text-blue-400 w-4 text-center mr-0 pointer-events-none"></i>
                <span class="text-left flex-1" data-i18n="nav.edit_profile">Edit Profile</span>
            </button>

            <!-- Dark Mode Switch Row -->
            <div class="theme-toggle-row px-3 py-2 rounded-xl flex items-center justify-between cursor-pointer font-semibold select-none" onclick="toggleThemeMode(event)">
                <span class="flex items-center gap-2.5 text-left">
                    <i id="mobile-theme-toggle-icon" class="fa-solid fa-moon text-blue-500 dark:text-blue-400 w-4 text-center mr-0 pointer-events-none"></i>
                    <span id="mobile-theme-toggle-label" class="text-slate-700 dark:text-slate-200" data-i18n="nav.dark_mode">Dark Mode</span>
                </span>
                <div id="mobile-dark-mode-toggle-pill" class="w-11 h-6 bg-blue-600 rounded-full p-1 transition-colors duration-300 relative pointer-events-none flex items-center">
                    <div id="mobile-dark-mode-toggle-circle" class="w-4 h-4 bg-white rounded-full shadow-md transition-transform duration-300 ease-in-out transform translate-x-5"></div>
                </div>
            </div>

            <!-- Language Switcher Row -->
            <div class="lang-switch-row px-3 py-2 rounded-xl flex items-center justify-between font-semibold select-none hover:bg-slate-100 dark:hover:bg-slate-800">
                <span class="flex items-center gap-2.5 text-left text-slate-700 dark:text-slate-200">
                    <i class="fa-solid fa-globe text-cyan-500 dark:text-cyan-400 w-4 text-center mr-0 pointer-events-none"></i>
                    <span id="mobile-lang-switch-label" class="text-slate-700 dark:text-slate-200" data-i18n="nav.language">Language</span>
                </span>
                <select id="app-language-select" onchange="setAppLanguage(this.value)" class="px-2 py-0.5 rounded-lg text-[11px] bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-bold cursor-pointer focus:outline-none">
                    <option value="en">EN 🇺🇸</option>
                    <option value="id">ID 🇮🇩</option>
                </select>
            </div>

            <div class="border-t border-slate-200 dark:border-slate-800 my-1 pt-1"></div>

            <!-- Sign Out Button -->
            <button type="button" onclick="logout()"
                class="w-full text-left px-3 py-2 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold transition flex items-center justify-start gap-2.5 cursor-pointer">
                <i class="fa-solid fa-right-from-bracket w-4 text-center mr-0 pointer-events-none"></i>
                <span class="text-left flex-1" data-i18n="nav.logout">Sign Out</span>
            </button>
        </div>
    </div>
</header>
