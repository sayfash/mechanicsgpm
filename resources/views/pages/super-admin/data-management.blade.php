<!-- SUB-TAB: Data Management CRUD Console -->
<div id="super-admin-tab-management" class="w-full min-w-0 max-w-full space-y-6 hidden mt-0 pt-0">
    <!-- Top Main Container Sub-Tabs Bar for Data Management -->
    <div class="w-full flex items-center gap-2 overflow-x-auto whitespace-nowrap glass-panel p-2.5 rounded-2xl border border-slate-800/80 shadow-lg custom-scrollbar">
        <button onclick="setManagementSubTab('branches')" id="btn-mgt-branches"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-sitemap text-indigo-400"></i><span data-i18n="mgt.tab_branches">Cabang Outlet</span>
            <span id="badge-mgt-branches-count" class="bg-slate-900 text-indigo-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('users')" id="btn-mgt-users"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-users text-blue-400"></i><span data-i18n="mgt.tab_users">Daftar Pengguna</span>
            <span id="badge-mgt-users-count" class="bg-slate-900 text-blue-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('common-issues')" id="btn-mgt-common-issues"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-triangle-exclamation text-amber-400"></i><span data-i18n="mgt.tab_issues">Kendala Umum</span>
            <span id="badge-mgt-common-issues-count" class="bg-slate-900 text-amber-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('form-items')" id="btn-mgt-form-items"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-list-check text-emerald-400"></i><span data-i18n="mgt.tab_checklist">Item Form</span>
            <span id="badge-mgt-form-items-count" class="bg-slate-900 text-emerald-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('categories')" id="btn-mgt-categories"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-tags text-purple-400"></i><span data-i18n="mgt.tab_categories">Kategori</span>
            <span id="badge-mgt-categories-count" class="bg-slate-900 text-purple-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('service-options')" id="btn-mgt-service-options"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-screwdriver-wrench text-cyan-400"></i><span data-i18n="mgt.tab_services">Opsi Jasa Servis</span>
            <span id="badge-mgt-service-options-count" class="bg-slate-900 text-cyan-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
        <button onclick="setManagementSubTab('other-services')" id="btn-mgt-other-services"
            class="px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 transition shrink-0 text-slate-400 hover:text-slate-200 hover:bg-slate-900/60">
            <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i><span data-i18n="mgt.tab_other_services">Layanan Lainnya</span>
            <span id="badge-mgt-other-services-count" class="bg-slate-900 text-amber-400 border border-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full font-mono">0</span>
        </button>
    </div>

    <!-- Sub-panel Display Area in Main Container -->
    <div class="w-full min-w-0 max-w-full space-y-6">
        <!-- Sub-panel: Service Options -->
        <div id="mgt-panel-service-options" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100" data-i18n="mgt.title_service_options">Kelola Opsi Jasa Servis</h4>
                    <p class="text-xs text-slate-400 w-full" data-i18n="mgt.desc_service_options">Atur jenis opsi servis mekanik, tarif standar, dan kode SKU.</p>
                </div>
                <button onclick="openAddServiceOptionModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-cyan-600/10">
                    <i class="fa-solid fa-plus"></i> <span data-i18n="mgt.add_service_option">Tambah Opsi Jasa Servis</span>
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[500px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="table.sku">SKU</th>
                            <th class="p-3 text-center font-sans" data-i18n="mgt.service_name">Nama Layanan / Jasa</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="mgt.fee">Biaya (IDR)</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="table.actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-service-options-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center font-mono">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Other Services -->
        <div id="mgt-panel-other-services" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100" data-i18n="mgt.title_other_services">Kelola Layanan Lainnya</h4>
                    <p class="text-xs text-slate-400 w-full" data-i18n="mgt.desc_other_services">Kelola item layanan tambahan dan tarif bengkel.</p>
                </div>
                <button onclick="openAddOtherServiceModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-amber-600/10">
                    <i class="fa-solid fa-plus"></i> <span data-i18n="mgt.add_other_service">Tambah Layanan Lainnya</span>
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[500px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="table.sku">SKU</th>
                            <th class="p-3 text-center font-sans" data-i18n="mgt.service_name">Nama Layanan / Jasa</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="mgt.fee">Biaya (IDR)</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="table.actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-other-services-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center font-mono">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Sparepart Categories -->
        <div id="mgt-panel-categories" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100" data-i18n="mgt.title_categories">Kelola Kategori Suku Cadang</h4>
                    <p class="text-xs text-slate-400 w-full" data-i18n="mgt.desc_categories">Sesuaikan kategori suku cadang yang tersedia di seluruh sistem.</p>
                </div>
                <button onclick="openAddCategoryModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-purple-600/10">
                    <i class="fa-solid fa-plus"></i> <span data-i18n="mgt.add_category">Tambah Kategori</span>
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[350px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 text-center font-sans" data-i18n="mgt.category_name">Nama Kategori</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="table.actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-categories-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Branches -->
        <div id="mgt-panel-branches" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100" data-i18n="mgt.title_branches">Kelola Cabang Outlet</h4>
                    <p class="text-xs text-slate-400 w-full" data-i18n="mgt.desc_branches">Buat cabang baru atau ubah rincian cabang yang ada.</p>
                </div>
                <button onclick="openAddBranchModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-plus"></i> <span data-i18n="mgt.add_branch">Tambah Cabang</span>
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[350px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 text-center font-sans" data-i18n="mgt.branch_name">Branch Name</th>
                            <th class="p-3 w-36 text-center font-sans" data-i18n="mgt.branch_abbreviation">Branch Abbreviation</th>
                            <th class="p-3 w-28 text-center font-sans" data-i18n="table.actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-branches-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Users -->
        <div id="mgt-panel-users" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100" data-i18n="mgt.title_users">Kelola Pengguna Sistem</h4>
                    <p class="text-xs text-slate-400 w-full" data-i18n="mgt.desc_users">Kelola peran, ikatan cabang, nama tampilan, dan atur ulang kata sandi.</p>
                </div>
                <button onclick="openUserRegistrationModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-user-plus"></i> <span data-i18n="mgt.register_user">Daftarkan Pengguna</span>
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[600px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-16 text-center">ID</th>
                            <th class="p-3 w-36 text-center font-sans" data-i18n="mgt.username">Nama Pengguna</th>
                            <th class="p-3 w-40 text-center font-sans" data-i18n="mgt.display_name">Nama Tampilan</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="mgt.designated_role">Peran Ditugaskan</th>
                            <th class="p-3 min-w-[180px] text-center font-sans" data-i18n="mgt.assigned_branch">Cabang Outlet Ditugaskan</th>
                            <th class="p-3 w-28 text-center font-sans" data-i18n="table.actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-users-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 text-center font-mono">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Customers -->
        <div id="mgt-panel-customers" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100" data-i18n="mgt.title_customers">Kelola Pelanggan</h4>
                    <p class="text-xs text-slate-400 w-full" data-i18n="mgt.desc_customers">Buat profil, perbarui kontak pelanggan, atau tinjau daftar kepemilikan.</p>
                </div>
                <button onclick="openAddCustomerModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-user-tag"></i> <span data-i18n="mgt.add_customer_profile">Tambah Profil Pelanggan</span>
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[700px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 w-44 text-center font-sans" data-i18n="shop.cust_name">Nama Pelanggan</th>
                            <th class="p-3 w-32 text-center font-sans" data-i18n="shop.cust_phone">Nomor Telepon</th>
                            <th class="p-3 w-36 text-center font-sans" data-i18n="shop.id_card_num">Nomor KTP</th>
                            <th class="p-3 min-w-[200px] text-center font-sans" data-i18n="shop.cust_address">Alamat Rumah</th>
                            <th class="p-3 w-24 text-center font-sans" data-i18n="cust.vehicles">Jumlah Kendaraan</th>
                            <th class="p-3 w-28 text-center font-sans" data-i18n="table.actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-customers-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center font-mono">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Common Issues -->
        <div id="mgt-panel-common-issues" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100" data-i18n="mgt.title_issues">Kelola Diagnosa Kendala Umum</h4>
                    <p class="text-xs text-slate-400 w-full" data-i18n="mgt.desc_issues">Tambah, ubah nama, atau hapus opsi standar daftar periksa penerimaan servis.</p>
                </div>
                <button onclick="openAddCommonIssueModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-plus"></i> <span data-i18n="mgt.add_common_issue">Tambah Kendala Umum</span>
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[400px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 min-w-[250px] text-center font-sans" data-i18n="mgt.issue_name">Nama Diagnosa Kendala</th>
                            <th class="p-3 w-28 text-center font-sans" data-i18n="table.actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-common-issues-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sub-panel: Mechanic Form Items -->
        <div id="mgt-panel-form-items" class="w-full min-w-0 max-w-full space-y-4 hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="w-full flex-1">
                    <h4 class="text-md font-bold text-slate-100" data-i18n="mgt.title_checklist">Kelola Item Lembar Servis Mekanik</h4>
                    <p class="text-xs text-slate-400 w-full" data-i18n="mgt.desc_checklist">Tambah, ubah nama, atau hapus item periksa wajib mekanik saat pengerjaan.</p>
                </div>
                <button onclick="openAddMechanicFormItemModal()"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-lg shadow-blue-500/10">
                    <i class="fa-solid fa-plus"></i> <span data-i18n="mgt.add_form_item">Tambah Item Form</span>
                </button>
            </div>
            <div class="glass-panel w-full max-w-full min-w-0 overflow-x-auto rounded-xl border border-slate-800/80">
                <table class="w-full min-w-[400px] text-center text-xs border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-900 text-slate-400 border-b border-slate-800 font-semibold uppercase tracking-wider text-[10px] text-center">
                            <th class="p-3 w-20 text-center">ID</th>
                            <th class="p-3 min-w-[250px] text-center font-sans" data-i18n="mgt.form_item_label">Label Item Lembar Servis</th>
                            <th class="p-3 w-28 text-center font-sans" data-i18n="table.actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mgt-form-items-tbody"
                        class="divide-y divide-slate-800/60 text-slate-300 font-medium text-center">
                        <!-- Injected JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
