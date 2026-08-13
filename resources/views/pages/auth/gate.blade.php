<!-- MODULE: AUTHENTICATION GATE -->
<section id="auth-gate" class="w-full max-w-md mx-auto py-8 hidden">
    <div class="glass-panel rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden border border-slate-800 bg-slate-950/90 backdrop-blur-2xl">

        <!-- LOGIN WRAPPER -->
        <div id="login-form-wrapper">
            <!-- Brand Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center bg-blue-600 text-white w-14 h-14 rounded-2xl shadow-lg shadow-blue-600/20 mb-4">
                    <i class="fa-solid fa-infinity text-2xl"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight">SGPM SERVICE CENTER</h2>
                <p class="text-xs font-semibold text-blue-400 uppercase tracking-widest mt-1">Compliance & Workshop Desk</p>
            </div>

            <form onsubmit="handleLogin(event)" class="space-y-5">
                <!-- Username Field -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Nama Pengguna</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 w-11 flex items-center justify-center text-slate-500 group-focus-within:text-blue-400 transition pointer-events-none">
                            <i class="fa-solid fa-user text-sm"></i>
                        </span>
                        <input type="text" id="login-username" required
                            class="w-full pl-11 pr-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200" placeholder="Masukkan nama pengguna">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Kata Sandi</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 w-11 flex items-center justify-center text-slate-500 group-focus-within:text-blue-400 transition pointer-events-none">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="login-password" required
                            class="w-full pl-11 pr-11 py-3 rounded-xl bg-slate-900 border border-slate-800 text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-200" placeholder="••••••••">
                        <button type="button" onclick="togglePasswordVisibility('login-password', 'toggle-pass-icon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition focus:outline-none" title="Tampilkan/sembunyikan kata sandi">
                            <i id="toggle-pass-icon" class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl transition duration-200 shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2 group cursor-pointer active:scale-[0.99] mt-2">
                    <span>Masuk Ke Sistem</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>
        </div>
    </div>
</section>
