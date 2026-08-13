<!-- ========================================== -->
<!-- MODULE: SUPER ADMIN WORKSPACE              -->
<!-- MODULE: SUPER ADMIN WORKSPACE (ROLE ZERO TOP GAP UPDATED) -->
<section id="super-admin-workspace" class="w-full hidden space-y-6 mt-0 pt-0">

    <!-- Sub-Tab: Dashboard Metrics -->
    @include('pages.super-admin.dashboard')

    <!-- Sub-Tab: Reports & Analytics Module -->
    @include('pages.super-admin.reports')

    <!-- Sub-Tab: Global Inventory Master Ledger -->
    @include('pages.super-admin.inventory')

    <!-- Sub-Tab: Detailed Maintenance Records -->
    @include('pages.super-admin.maintenance')

    <!-- Sub-Tab: Customers Directory -->
    @include('pages.super-admin.customers')

    <!-- Sub-Tab: Vehicles Inventory Directory -->
    @include('pages.super-admin.vehicles')

    <!-- Sub-Tab: Data Management Console -->
    @include('pages.super-admin.data-management')

    <!-- Sub-Tab: Audit & Security Logs -->
    @include('pages.super-admin.audit-logs')
</section>
