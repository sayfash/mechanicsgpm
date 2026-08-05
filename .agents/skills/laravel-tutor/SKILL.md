---
name: laravel-tutor
description: Use when building or modifying Laravel features (Routes, Controllers, Models, Blade Views, or Form Requests).
---

# Laravel Development & Teaching Guidelines

When implementing or editing Laravel code:

1. **Architectural Pipeline:**
   - Follow the standard Laravel request flow: `Route -> Controller -> Model/Eloquent -> View/Response`.
   - Before outputting code, provide a 2-sentence summary explaining which files are being modified and why.

2. **Clean Code & Best Practices:**
   - Use Eloquent ORM relationships and query builders instead of raw SQL queries wherever possible.
   - Use Form Requests for complex validation logic instead of bloating Controller methods.
   - Add educational inline comments (`// ...`) on non-obvious Laravel methods (e.g., Dependency Injection, Middleware, Model Observers).

3. **Context Optimization:**
   - Keep file changes localized strictly to the requested feature.
   - Do not refactor unrelated classes or scan external dependencies.