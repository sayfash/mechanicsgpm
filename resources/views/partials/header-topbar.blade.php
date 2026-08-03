<!-- Top App Bar Header Navigation (Full Width Above App Shell & Sidebar) -->
<header id="top-app-bar" class="w-full hidden flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 px-6 py-3 bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 backdrop-blur-md sticky top-0 z-40">
    <!-- Left: Logo & Branding -->
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2.5">
            <div class="bg-blue-600 text-white p-2 rounded-xl shadow-lg shadow-blue-500/20 shrink-0 flex items-center justify-center">
                <i class="fa-solid fa-infinity text-base"></i>
            </div>
            <div class="truncate">
                <h1 class="text-sm font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">SGPM MECHANIC</h1>
                <p class="text-[9px] text-slate-500 dark:text-slate-400 tracking-widest uppercase font-semibold">Compliance Desk</p>
            </div>
        </div>
        <nav class="hidden md:flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 ml-4 border-l border-slate-200 dark:border-slate-800 pl-4">
            <i class="fa-solid fa-house text-slate-400 dark:text-slate-500"></i>
            <span>/</span>
            <span id="breadcrumb-section-label" class="text-slate-800 dark:text-slate-200 font-bold">Portofolio domain</span>
            <span>/</span>
            <span id="breadcrumb-tab-label" class="text-slate-500 dark:text-slate-400 font-bold">Transfer</span>
        </nav>
    </div>

    <!-- Right Action Items (Profile Avatar Trigger & Dropdown Menu) -->
    <div class="flex items-center justify-end gap-2.5 relative">

        <!-- Profile Avatar Circle Button -->
        <button type="button" id="btn-top-profile-trigger" onclick="toggleTopProfileDropdown(event)" aria-label="User Profile"
            class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-slate-300 dark:border-slate-700 font-bold flex items-center justify-center shadow-sm hover:border-blue-500 transition relative overflow-hidden focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
            <i id="profile-picture-icon" class="fa-solid fa-user text-xs m-0 p-0 mr-0 pointer-events-none"></i>
            <img id="profile-picture-img" src="" class="w-full h-full object-cover hidden pointer-events-none" alt="Profile">
        </button>

        <!-- Floating Dropdown Profile Menu -->
        <div id="top-profile-dropdown" class="hidden absolute right-0 top-full mt-2 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-[99999] p-3 text-xs space-y-1 text-left">
            <!-- User Header Info -->
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-2.5 mb-1 px-3 pt-1 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-blue-600 dark:text-blue-400 overflow-hidden shrink-0">
                    <i id="dropdown-profile-icon" class="fa-solid fa-user text-xs m-0 p-0 mr-0 pointer-events-none"></i>
                    <img id="dropdown-profile-img" src="" class="w-full h-full object-cover hidden pointer-events-none" alt="Profile">
                </div>
                <div class="truncate flex-1 min-w-0 text-left">
                    <div id="dropdown-user-name" class="font-bold text-slate-900 dark:text-slate-100 text-xs truncate">Santomo Group</div>
                    <div id="dropdown-user-email" class="text-slate-400 dark:text-slate-400 text-[10px] font-mono mt-0.5 truncate">it.santomo@outlook.com</div>
                </div>
            </div>

            <!-- Edit Profile (Left Aligned) -->
            <button type="button" onclick="openProfileModal(event); toggleTopProfileDropdown(event);"
                class="w-full text-left px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold transition flex items-center justify-start gap-2.5 cursor-pointer">
                <i class="fa-solid fa-user-pen text-blue-500 dark:text-blue-400 w-4 text-center mr-0 pointer-events-none"></i>
                <span class="text-left flex-1">Edit Profile</span>
            </button>

            <!-- Dark Mode Switch Row -->
            <div class="theme-toggle-row px-3 py-2 rounded-xl flex items-center justify-between cursor-pointer font-semibold" onclick="toggleThemeMode(event)">
                <span class="flex items-center gap-2.5 text-left">
                    <i id="theme-toggle-icon" class="fa-solid fa-moon text-blue-500 dark:text-blue-400 w-4 text-center mr-0 pointer-events-none"></i>
                    <span>Dark Mode</span>
                </span>
                <input type="checkbox" id="theme-toggle-switch" class="sr-only peer" onchange="toggleThemeMode(event)">
                <div id="dark-mode-toggle-pill" class="w-9 h-5 bg-slate-300 dark:bg-slate-700 rounded-full p-0.5 transition-colors relative pointer-events-none">
                    <div id="dark-mode-toggle-circle" class="w-4 h-4 bg-white rounded-full shadow-md transition-transform transform translate-x-0"></div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-800 my-1 pt-1"></div>

            <!-- Sign Out Button (Left Aligned) -->
            <button type="button" onclick="logout()"
                class="w-full text-left px-3 py-2 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold transition flex items-center justify-start gap-2.5 cursor-pointer">
                <i class="fa-solid fa-right-from-bracket w-4 text-center mr-0 pointer-events-none"></i>
                <span class="text-left flex-1">Sign Out</span>
            </button>
        </div>
    </div>
</header>
