---
name: laravel-migration
description: Use when creating, modifying, or auditing database schemas, tables, migrations, or seeders in Laravel.
---

# Database Migration Standards

When generating database migrations:

1. **Schema Safety:**
   - Always create a **new** migration file when altering existing tables (never edit previously executed migrations).
   - Ensure foreign key constraints include proper deletion handling (`onDelete('cascade')` or `nullOnDelete()`).

2. **Data Types & Conventions:**
   - Use standard Laravel Blueprint methods (`string`, `text`, `integer`, `boolean`, `timestamp`, `foreignId`).
   - Include `$table->timestamps()` unless explicitly instructed otherwise.

3. **Verification Step:**
   - Prompt the user to run `php artisan migrate` after creating the migration file.