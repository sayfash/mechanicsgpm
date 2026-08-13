<!-- Desktop Persistent Vertical Sidebar (≥ 768px) -->
<aside id="app-sidebar" role="navigation" aria-label="Desktop Main Navigation" class="hidden md:flex flex-col w-64 border-r border-slate-800 bg-slate-950/80 backdrop-blur-md sticky top-[61px] h-[calc(100vh-61px)] transition-all duration-300 shrink-0 z-30">
    <!-- Top Section (Sidebar Toggle Button) -->
        <!-- Floating Chevron Button Outside Navigation Bar -->
        <button id="sidebar-toggle-btn" onclick="toggleDesktopSidebar()" title="Collapse / Expand Sidebar" aria-label="Toggle Sidebar" class="absolute -right-3.5 top-2.5 w-7 h-7 rounded-full bg-slate-900 border border-slate-700 text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center justify-center shadow-none focus:outline-none focus:ring-2 focus:ring-blue-500 z-50">
            <i id="sidebar-toggle-icon" class="fa-solid fa-angles-left text-xs"></i>
        </button>

    <!-- Middle Section (Dynamic Scrollable Grouped Navigation Rendered via RBAC) -->
    <div id="desktop-nav-container" class="flex-1 overflow-y-auto px-3 py-4 space-y-6 custom-scrollbar"></div>
</aside>
