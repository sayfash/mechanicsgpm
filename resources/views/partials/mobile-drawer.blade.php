<!-- Mobile Navigation Drawer Backdrop Overlay -->
<div id="mobile-drawer-overlay" onclick="closeMobileDrawer()" class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0 pointer-events-none md:hidden"></div>

<!-- Mobile Navigation Drawer (< 768px) -->
<aside id="mobile-nav-drawer" role="navigation" aria-label="Mobile Navigation" class="fixed inset-y-0 left-0 z-[70] w-72 bg-slate-950 border-r border-slate-800 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col md:hidden">
    <!-- Top Section -->
    <div class="p-4 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 text-white p-2 rounded-lg shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-infinity text-lg"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-100">SGPM SERVICE CENTER</h2>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest">Compliance Desk</p>
            </div>
        </div>
        <button onclick="closeMobileDrawer()" aria-label="Close Navigation Menu" class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Middle Section (Dynamic Scrollable Links Rendered via RBAC) -->
    <div id="mobile-nav-container" class="flex-1 overflow-y-auto px-3 py-4 space-y-6 custom-scrollbar"></div>
</aside>
