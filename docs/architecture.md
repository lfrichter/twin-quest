# Architectural Decisions

This document outlines the key architectural decisions made for this project, based on the technical requirements and best practices.

## 1. Technical Stack

The technology stack was defined based on the project’s technical requirements, with some upgrades to leverage the latest stable releases and improve code maintainability:

* **Backend Framework:** Laravel 12
  *While the technical requirement specified Laravel 10, I opted for the latest stable version, Laravel 12, to benefit from enhanced features, long-term support, and the most up-to-date framework improvements for backend development.*

* **Frontend Framework:** Vue.js 3 (Composition API with TypeScript)
  *The requirements indicated familiarity with Vue.js but did not mandate the use of the Composition API. I adopted the Composition API to achieve more modular, readable, and maintainable code. In addition, TypeScript was integrated into the stack to provide static typing, which increases code robustness and reduces compile-time errors.*

* **Backend-Frontend Integration:** Inertia.js 1.0
  *Inertia.js 1.0 was used, according to project guidelines, to enable a server-driven single-page application architecture, combining the strengths of Laravel on the backend and Vue.js on the frontend, while simplifying API interactions.*

* **Database:** PostgreSQL 16
  *Although PostgreSQL 15 was required, I chose to use the more recent PostgreSQL 16 to benefit from the latest performance improvements and features, ensuring even greater reliability for the data layer.*

* **Frontend State Management:** Pinia 2
  *Pinia 2, the official state management library for Vue.js, was selected in line with the project's technical direction, providing a simple API, seamless integration with the Composition API, and full support for Vue DevTools.*

* **PHP Testing Framework:** Pest 2
  *Pest 2 was used as required, offering an expressive and developer-friendly testing framework for Laravel.*

In summary, the selected stack was guided by the project’s technical requirements, with strategic upgrades and enhancements—such as Laravel 12, PostgreSQL 16, the Vue Composition API and Typescript — to ensure a modern, maintainable, and robust development environment.


Claro! Aqui está a estrutura revisada com a inclusão dos testes Vitest em `tests/Js/` e dos arquivos de documentação:


## 2. Recommended Directory Structure

The directory structure follows established Laravel, Vue.js, and Inertia.js best practices:

### **Laravel**
*   `app/Http/Controllers/`: Controllers to handle HTTP requests.
*   `app/Http/Requests/`: Form Requests for input data validation.
*   `app/Http/Resources/`: API Resources to transform Eloquent models/collections into JSON.
*   `app/Models/`: Eloquent models for database interactions.
*   `app/Providers/`: Service Providers for registering application services.
*   `app/Services/`: Service classes that encapsulate business logic.
*   `app/Repositories/`: (Optional, if Repository pattern is applied) Classes to abstract data access logic.

*   `database/migrations/`: Database migration files.
*   `database/seeders/`: Seeders for populating tables with test or initial data.

*   `routes/api.php`: API route definitions.
*   `routes/web.php`: Web route definitions (used together with Inertia.js).

### **Frontend (`resources/js/`)**
*   `Pages/`: Vue.js page components rendered by Inertia.
*   `Components/`: Reusable Vue.js components.
*   `Layouts/`: Vue.js layouts to wrap pages.
*   `Store/`: Pinia stores for global state management.
*   `Composables/`: Reusable Composition API logic (custom hooks).

### **Testing**
*   `tests/Feature/`: Feature and integration tests (using Pest).
*   `tests/Unit/`: Unit tests (using Pest).
*   `tests/Js/`: Frontend unit and integration tests using Vitest
    *   Example:
        *   `tests/Js/Components/Products/ProductFilters.spec.ts`
        *   `tests/Js/Pages/Products/Index.spec.ts`

### **Documentation**
*   `docs/architecture.md`: Project architecture documentation.
*   `docs/api.md`: API documentation and reference.

**Note:**
- The structure within `resources/js/` is organized according to best practices for Inertia.js and Vue.js projects to promote maintainability and reusability.
- The use of the `app/Repositories/` directory is optional and depends on the application of the Repository pattern.
- All documentation is centrally located in the `docs/` directory for easy access.


## 3. Design Patterns to be Implemented

*   **PSR-12:** PHP code must adhere to the PSR-12 coding style guide.
*   **MVC (Model-View-Controller):** In the Laravel backend, to separate responsibilities. The "View" in this context is the Vue.js component rendered via Inertia.
*   **Repository Pattern:** I chose not to use this pattern as I didn't find it necessary for the current complexity, and here, I think that just using Service Layer Patter is enough.
*   **Service Layer Pattern:** Implemented to encapsulate complex business logic, keeping controllers lean and improving code organization. For example, `ProductService` handles the logic for fetching, filtering, and caching products and categories, abstracting these operations from the `ProductController`. This promotes reusability and testability.
*   **Form Request Classes (Laravel):** To encapsulate HTTP request validation logic in the backend.
*   **API Resources (Laravel):** To format API responses consistently and decouple the model structure from the API representation.
*   **Single Responsibility Principle (Vue Components):** Vue components should have a single, clear responsibility.
*   **Composition API (Vue.js):** To organize component logic by feature and promote reusability through `composables`.

## 4. Data Flow Between Components and Application Layers

1.  **Browser Request:** The user interacts with a Vue.js component.
2.  **Inertia.js:** An action (e.g., link click, form submission) triggers an Inertia request to the Laravel backend.
3.  **Laravel Routing:** The corresponding Laravel route is triggered, directing to a Controller.
4.  **Laravel Controller:**
    *   Validates the request (using Form Requests, if applicable).
    *   Calls Services or Repositories to execute business logic and interact with the database (via Eloquent Models).
    *   Prepares the response data.
5.  **Inertia Response:** The Controller returns an Inertia response, passing data (props) to a specific Vue.js page component. Laravel API Resources can be used here to format the data.
6.  **Vue.js Rendering:** Inertia.js on the client-side receives the data and dynamically updates the relevant Vue.js component or loads a new page/component without a full page reload.
7.  **State Management (Pinia):** Global application state (e.g., user data, active filters) is managed by Pinia stores, which can be accessed and modified by any Vue.js component. Changes in Pinia state can reactively update the UI.
8.  **Component Interaction:** Vue.js components can have local state (using `ref` or `reactive`) and communicate with parent/child components via `props` and `emits`.

## 5. Caching, Performance, and Security Strategies

*   **Caching Strategy:**
    *   **Objective:** To improve application performance, reduce database load, and meet the API response time target of <500ms.
    *   **Implementation:** Laravel's `Cache` facade (`Illuminate\Support\Facades\Cache`) is utilized with the `Cache::remember()` method for an efficient cache-aside pattern.
    *   **Product List Caching:**
        *   **Scope:** Applied to the paginated and filtered list of products in `ProductService::getFilteredAndPaginatedProducts`.
        *   **Cache Key:** Dynamically generated based on all query parameters (`$request->query()`), including filters (`name`, `category_id`, `status`), pagination (`page`), and items per page (`per_page`). The `ksort()` function ensures consistent key order, and `md5(http_build_query())` creates a unique hash. Example key format: `products_list_` + hash.
        *   **Duration:** 10 minutes (600 seconds). This duration balances data freshness with performance gains.
    *   **Category List Caching:**
        *   **Scope:** Applied to the list of categories used for filter dropdowns in `ProductService::getCategoriesForFilter`.
        *   **Cache Key:** A static key, e.g., `categories_dropdown_list`, as this data changes less frequently.
        *   **Duration:** 60 minutes (3600 seconds).
*   **Performance:**
    *   **Eager Loading (Laravel):** Use eager loading to avoid the N+1 problem in Eloquent queries, especially when loading related models in STORY-001.
    *   **Query Optimization:** Analyze and optimize complex SQL queries. Use appropriate database indexes.
    *   **Asset Minification:** Vite (Laravel's default) should be configured to minify JS and CSS files in production.
    *   **Code Splitting:** Inertia.js, in conjunction with Vue Router (used internally by Inertia for some functionalities), generally handles page-level code splitting well.
    *   **Reusable Vue.js Components:** Promote the use of small, reusable components to optimize rendering and maintenance.
    *   **API Response Time < 500ms:** As per non-functional requirement.
*   **Security:**
    *   **Input Validation:** Validate all input data on both the frontend (for quick user feedback) and the backend (as the final authority).
    *   **CSRF Protection:** Laravel provides CSRF protection by default, which must be maintained.
    *   **XSS Protection:** Vue.js and Blade (used by Inertia for the initial layout) escape data by default, but care must be taken when using `v-html` or unescaped output.
    *   **SQL Injection:** The Eloquent ORM uses prepared statements, which protect against SQL injection. Avoid using raw SQL queries whenever possible.
    *   **Authorization:** Although not explicitly in the scope of the stories, in a real application, implement Laravel Gates/Policies for access control.
    *   **HTTPS:** The application must be served over HTTPS in production.
    *   **Dependency Updates:** Keep dependencies (Laravel, Vue, NPM/Composer packages) updated to fix known vulnerabilities.
