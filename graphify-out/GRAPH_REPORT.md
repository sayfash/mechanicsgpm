# Graph Report - sgpm-mechanic  (2026-08-13)

## Corpus Check
- 144 files · ~131,395 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 935 nodes · 1706 edges · 135 communities (115 shown, 20 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 94 edges (avg confidence: 0.79)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `bfdc8f50`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- showToast
- assets/js/app.js
- composer.json
- Illuminate\Http\Request
- scripts
- setSuperAdminTab
- store.js
- Illuminate\Database\Eloquent\Factories\HasFactory
- AuditLog
- devDependencies
- What You Must Do When Invoked
- MaintenanceRecord
- navigation.js
- Inventory
- filterVehiclesAndCustomers
- Vehicle
- getActiveEl
- auth.js
- What You Must Do When Invoked
- Controller
- app.blade.php
- Customer
- loadMechanicDashboard
- super-admin/workspace.blade.php
- filterGlobalPartsHistory
- AppServiceProvider
- graphify reference: extra exports and benchmark
- graphify reference: extra exports and benchmark
- README.md
- loadSuperAdminStats
- Pest.php
- fetchMechanicHistoryRecords
- loadActiveJobsList
- handleProfileUpdate
- index.blade.php
- welcome.blade.php
- graphify reference: query, path, explain
- Antigravity Workspace Rules & Persona
- BranchMaintenanceExport
- graphify reference: query, path, explain
- EnsureUserRole.php
- SimplePdfBuilder
- request
- graphify reference: add a URL and watch a folder
- graphify reference: commit hook and native CLAUDE.md integration
- graphify reference: incremental update and cluster-only
- graphify reference: add a URL and watch a folder
- graphify reference: commit hook and native CLAUDE.md integration
- graphify reference: incremental update and cluster-only
- i18n.js
- graphify reference: GitHub clone and cross-repo merge
- graphify reference: transcribe video and audio
- graphify reference: GitHub clone and cross-repo merge
- graphify reference: transcribe video and audio
- rules/graphify.md
- .agents/skills/graphify/references/extraction-spec.md
- laravel-migration/SKILL.md
- laravel-tutor/SKILL.md
- workflows/graphify.md
- CLAUDE.md
- .claude/CLAUDE.md
- .claude/skills/graphify/references/extraction-spec.md
- GEMINI.md
- fetchVehiclesAndCustomers
- fetchDynamicChecklists
- fetchGlobalInventory
- fetchShopInventory

## God Nodes (most connected - your core abstractions)
1. `showToast()` - 104 edges
2. `request()` - 88 edges
3. `AuditLog` - 24 edges
4. `MaintenanceRecord` - 23 edges
5. `getActiveEl()` - 22 edges
6. `Controller` - 21 edges
7. `Vehicle` - 21 edges
8. `DashboardController` - 20 edges
9. `Inventory` - 17 edges
10. `openGlobalModal()` - 16 edges

## Surprising Connections (you probably didn't know these)
- `CustomerController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/API/CustomerController.php → app/Http/Controllers/Controller.php
- `DashboardController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/API/DashboardController.php → app/Http/Controllers/Controller.php
- `InventoryController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/API/InventoryController.php → app/Http/Controllers/Controller.php
- `JobController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/API/JobController.php → app/Http/Controllers/Controller.php
- `UserController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/API/UserController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (135 total, 20 thin omitted)

### Community 0 - "showToast"
Cohesion: 0.11
Nodes (21): cancelActiveJob(), cancelActiveJobFromList(), closeExportExcelModal(), dateStrFilename(), downloadCustomersBatchTemplate(), downloadInventoryBatchTemplate(), downloadVehiclesBatchTemplate(), exportLogsCSV() (+13 more)

### Community 1 - "assets/js/app.js"
Cohesion: 0.05
Nodes (37): attachMechanicDraftListeners(), clearImportPreview(), closeCustomersCSVModal(), closeModalElement(), closeVehiclesCSVModal(), defaultCustCols, defaultRecordsCols, defaultShopInvCols (+29 more)

### Community 2 - "composer.json"
Cohesion: 0.05
Nodes (43): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+35 more)

### Community 3 - "Illuminate\Http\Request"
Cohesion: 0.11
Nodes (4): DashboardController, AuthController, LegacyApiController, Illuminate\Http\Request

### Community 4 - "scripts"
Cohesion: 0.08
Nodes (27): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+19 more)

### Community 5 - "setSuperAdminTab"
Cohesion: 0.12
Nodes (22): canUserAccessRoute(), closeMobileDrawer(), fetchBranches(), fetchMe(), fetchMyRepairHistory(), fetchReportSummary(), fetchSparepartCategories(), fillLogin() (+14 more)

### Community 6 - "store.js"
Cohesion: 0.13
Nodes (23): applyDOMTranslations(), AppStore, canUserAccessRoute(), escapeHtml(), fetchSparepartCategories(), formatIndonesianDate(), getAppLanguage(), getCategoryOptionsHtml() (+15 more)

### Community 7 - "Illuminate\Database\Eloquent\Factories\HasFactory"
Cohesion: 0.28
Nodes (4): CommonIssue, MechanicFormItem, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Eloquent\Model

### Community 8 - "AuditLog"
Cohesion: 0.14
Nodes (8): UserController, AuditLog, User, DatabaseSeeder, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable, Laravel\Sanctum\HasApiTokens

### Community 9 - "devDependencies"
Cohesion: 0.11
Nodes (17): concurrently, laravel-vite-plugin, devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite (+9 more)

### Community 10 - "What You Must Do When Invoked"
Cohesion: 0.07
Nodes (26): For /graphify add and --watch, For /graphify query, For the commit hook and native CLAUDE.md integration, For --update and --cluster-only, /graphify, Honesty Rules, Interpreter guard for subcommands, Part A - Structural extraction for code files (+18 more)

### Community 11 - "MaintenanceRecord"
Cohesion: 0.12
Nodes (4): JobController, MechanicController, MaintenanceRecord, RecordPartUsed

### Community 12 - "navigation.js"
Cohesion: 0.20
Nodes (13): applyPermissionGuardsOnUI(), closeMobileDrawer(), onNavRouteClick(), openMobileDrawer(), renderDashboardLayout(), renderRoleBasedNavigation(), setActiveNavRoute(), showSection() (+5 more)

### Community 14 - "filterVehiclesAndCustomers"
Cohesion: 0.10
Nodes (36): applyPermissionGuardsOnUI(), calculateDurationString(), closeAddVehicleModal(), determineVehicleWarrantyCategory(), fetchAuditLogs(), filterCustomersTable(), filterGlobalInventory(), filterMaintenanceRecords() (+28 more)

### Community 15 - "Vehicle"
Cohesion: 0.13
Nodes (5): VehicleController, Vehicle, UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 16 - "getActiveEl"
Cohesion: 0.22
Nodes (22): addOtherServiceToMechAccumulator(), addPartToMechAccumulator(), addServiceToMechAccumulator(), getActiveCustomerStatus(), getActiveEl(), isChargedOptionStatus(), isEditableCustomerStatus(), removeMechAccumulatedOtherService() (+14 more)

### Community 17 - "auth.js"
Cohesion: 0.21
Nodes (7): closeProfileModal(), fillLogin(), handleForgotPasswordSubmit(), handleLogin(), handleProfileUpdate(), toggleAuthView(), updateProfilePictureUI()

### Community 18 - "What You Must Do When Invoked"
Cohesion: 0.08
Nodes (24): For /graphify add and --watch, For /graphify query, For the commit hook and native CLAUDE.md integration, For --update and --cluster-only, /graphify, Honesty Rules, Interpreter guard for subcommands, Part A - Structural extraction for code files (+16 more)

### Community 19 - "Controller"
Cohesion: 0.14
Nodes (5): AuditLogController, ReportController, BranchController, Controller, Branch

### Community 20 - "app.blade.php"
Cohesion: 0.20
Nodes (9): modals.common-modals, modals.inventory-modals, modals.management-modals, modals.profile, modals.user-registration, partials.header-mobile, partials.header-topbar, partials.mobile-drawer (+1 more)

### Community 22 - "loadMechanicDashboard"
Cohesion: 0.15
Nodes (14): changeBranch(), fetchMechanicBranchParts(), filterMechanicPartOptions(), formatCategoryLastRepair(), formatDaysSince(), getActiveVehicleWarrantyState(), handleBeforeUnload(), handleMechanicCheckin() (+6 more)

### Community 23 - "super-admin/workspace.blade.php"
Cohesion: 0.22
Nodes (8): pages.super-admin.audit-logs, pages.super-admin.customers, pages.super-admin.dashboard, pages.super-admin.data-management, pages.super-admin.inventory, pages.super-admin.maintenance, pages.super-admin.reports, pages.super-admin.vehicles

### Community 24 - "filterGlobalPartsHistory"
Cohesion: 0.12
Nodes (17): fetchGlobalSparePartsHistory(), filterGlobalPartsHistory(), filterPartsHistory(), handleGlobalPartsHistoryPeriodChange(), handlePartsHistoryPeriodChange(), initFlatpickrDateInputs(), navigateGlobalMonthlyPartsHistory(), navigateMonthlyPartsHistory() (+9 more)

### Community 26 - "graphify reference: extra exports and benchmark"
Cohesion: 0.22
Nodes (8): graphify reference: extra exports and benchmark, Step 6b - Wiki (only if --wiki flag), Step 7 - Neo4j export (only if --neo4j or --neo4j-push flag), Step 7a - FalkorDB export (only if --falkordb or --falkordb-push flag), Step 7b - SVG export (only if --svg flag), Step 7c - GraphML export (only if --graphml flag), Step 7d - MCP server (only if --mcp flag), Step 8 - Token reduction benchmark (only if total_words > 5000)

### Community 27 - "graphify reference: extra exports and benchmark"
Cohesion: 0.22
Nodes (8): graphify reference: extra exports and benchmark, Step 6b - Wiki (only if --wiki flag), Step 7 - Neo4j export (only if --neo4j or --neo4j-push flag), Step 7a - FalkorDB export (only if --falkordb or --falkordb-push flag), Step 7b - SVG export (only if --svg flag), Step 7c - GraphML export (only if --graphml flag), Step 7d - MCP server (only if --mcp flag), Step 8 - Token reduction benchmark (only if total_words > 5000)

### Community 28 - "README.md"
Cohesion: 0.25
Nodes (7): About Laravel, Agentic Development, Code of Conduct, Contributing, Learning Laravel, License, Security Vulnerabilities

### Community 29 - "loadSuperAdminStats"
Cohesion: 0.33
Nodes (10): getChartGridColor(), getChartTextColor(), loadSuperAdminStats(), renderMechanicScoreboard(), renderMechanicTimeChart(), renderSparePartsVsDatesChart(), renderStatsChart(), updateChartsThemeColors() (+2 more)

### Community 31 - "fetchMechanicHistoryRecords"
Cohesion: 0.22
Nodes (9): adjustCalendarMonth(), fetchMechanicHistoryRecords(), fetchShopHistoryRecords(), renderMechanicHistoryCalendar(), renderMechanicHistoryTable(), renderShopHistoryCalendar(), renderShopHistoryTable(), setHistoryViewMode() (+1 more)

### Community 32 - "loadActiveJobsList"
Cohesion: 0.20
Nodes (14): fetchShopMaintenanceRecords(), fetchSparePartsHistory(), getLocalSQLTimestamp(), loadActiveJobsList(), loadShopAdminIntake(), renderMechanicAvailabilityCards(), saveActiveJobDraft(), setShopAdminTab() (+6 more)

### Community 33 - "handleProfileUpdate"
Cohesion: 0.40
Nodes (5): closeProfileModal(), handleFetchResponse(), handleProfileUpdate(), requestFormData(), updateProfilePictureUI()

### Community 34 - "index.blade.php"
Cohesion: 0.40
Nodes (4): pages.auth.gate, pages.mechanic.workspace, pages.shop-admin.workspace, pages.super-admin.workspace

### Community 35 - "welcome.blade.php"
Cohesion: 0.40
Nodes (4): pages.auth.gate, pages.mechanic.workspace, pages.shop-admin.workspace, pages.super-admin.workspace

### Community 36 - "graphify reference: query, path, explain"
Cohesion: 0.33
Nodes (5): For /graphify explain, For /graphify path, graphify reference: query, path, explain, Step 0 — Constrained query expansion (REQUIRED before traversal), Step 1 — Traversal

### Community 37 - "Antigravity Workspace Rules & Persona"
Cohesion: 0.33
Nodes (5): 1. Agent Persona & Teaching Style, 2. Code Generation & Style, 3. Token & File Context Restrictions, 4. Post-Task Explanation, Antigravity Workspace Rules & Persona

### Community 89 - "graphify reference: query, path, explain"
Cohesion: 0.33
Nodes (5): For /graphify explain, For /graphify path, graphify reference: query, path, explain, Step 0 — Constrained query expansion (REQUIRED before traversal), Step 1 — Traversal

### Community 93 - "EnsureUserRole.php"
Cohesion: 0.60
Nodes (3): EnsureUserRole, Closure, Symfony\Component\HttpFoundation\Response

### Community 95 - "request"
Cohesion: 0.14
Nodes (28): closeConfirmDeleteModal(), closeGlobalModal(), deleteCategory(), deleteMaintenanceRecord(), deleteUserAccount(), deleteVehicle(), fetchMaintenanceRecords(), fetchManagementBranches() (+20 more)

### Community 96 - "graphify reference: add a URL and watch a folder"
Cohesion: 0.50
Nodes (3): For /graphify add, For --watch, graphify reference: add a URL and watch a folder

### Community 97 - "graphify reference: commit hook and native CLAUDE.md integration"
Cohesion: 0.50
Nodes (3): For git commit hook, For native CLAUDE.md integration, graphify reference: commit hook and native CLAUDE.md integration

### Community 98 - "graphify reference: incremental update and cluster-only"
Cohesion: 0.50
Nodes (3): For --cluster-only, For --update (incremental re-extraction), graphify reference: incremental update and cluster-only

### Community 99 - "graphify reference: add a URL and watch a folder"
Cohesion: 0.50
Nodes (3): For /graphify add, For --watch, graphify reference: add a URL and watch a folder

### Community 100 - "graphify reference: commit hook and native CLAUDE.md integration"
Cohesion: 0.50
Nodes (3): For git commit hook, For native CLAUDE.md integration, graphify reference: commit hook and native CLAUDE.md integration

### Community 101 - "graphify reference: incremental update and cluster-only"
Cohesion: 0.50
Nodes (3): For --cluster-only, For --update (incremental re-extraction), graphify reference: incremental update and cluster-only

### Community 131 - "fetchVehiclesAndCustomers"
Cohesion: 0.22
Nodes (11): bindVehicleOwner(), closeAddCustomerModal(), closeEditCustomerModal(), closeEditVehicleModal(), deleteCustomer(), fetchManagementCustomers(), fetchVehiclesAndCustomers(), handleEditCustomerSubmit() (+3 more)

### Community 132 - "fetchDynamicChecklists"
Cohesion: 0.20
Nodes (14): deleteOtherService(), deleteServiceOption(), fetchCommonIssuesMgt(), fetchDynamicChecklists(), fetchFormItemsMgt(), fetchManagementOtherServices(), fetchManagementServiceOptions(), renderManagementOtherServicesTable() (+6 more)

### Community 134 - "fetchGlobalInventory"
Cohesion: 0.22
Nodes (10): closeSuperAdminAddInventoryModal(), deleteAllCurrentBranchInventory(), deleteInventoryItem(), deleteSelectedInventoryItems(), fetchGlobalInventory(), handleSubmitSuperAdminAddInventory(), submitImportBatch(), toggleSelectAllInventory() (+2 more)

### Community 137 - "fetchShopInventory"
Cohesion: 0.33
Nodes (6): closeShopAdminAddInventoryModal(), closeShopAdminEditInventoryModal(), fetchShopInventory(), handleSubmitShopAdminAddInventory(), handleSubmitShopAdminEditInventory(), loadShopAdminDashboard()

## Knowledge Gaps
- **192 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+187 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **20 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `MaintenanceRecord` connect `MaintenanceRecord` to `Illuminate\Database\Eloquent\Factories\HasFactory`, `Vehicle`, `Controller`, `Customer`, `BranchMaintenanceExport`?**
  _High betweenness centrality (0.005) - this node is a cross-community bridge._
- **Why does `showToast()` connect `showToast` to `loadActiveJobsList`, `assets/js/app.js`, `handleProfileUpdate`, `fetchVehiclesAndCustomers`, `fetchDynamicChecklists`, `setSuperAdminTab`, `fetchGlobalInventory`, `fetchShopInventory`, `filterVehiclesAndCustomers`, `getActiveEl`, `loadMechanicDashboard`, `filterGlobalPartsHistory`, `fetchMechanicHistoryRecords`, `loadSuperAdminStats`, `request`?**
  _High betweenness centrality (0.005) - this node is a cross-community bridge._
- **Why does `Vehicle` connect `Vehicle` to `MaintenanceRecord`, `Illuminate\Http\Request`, `Customer`, `Illuminate\Database\Eloquent\Factories\HasFactory`?**
  _High betweenness centrality (0.004) - this node is a cross-community bridge._
- **Are the 20 inferred relationships involving `AuditLog` (e.g. with `.index()` and `.destroy()`) actually correct?**
  _`AuditLog` has 20 INFERRED edges - model-reasoned connections that need verification._
- **Are the 14 inferred relationships involving `MaintenanceRecord` (e.g. with `.query()` and `.cancelActiveJob()`) actually correct?**
  _`MaintenanceRecord` has 14 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _192 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `showToast` be split into smaller, more focused modules?**
  _Cohesion score 0.11428571428571428 - nodes in this community are weakly interconnected._