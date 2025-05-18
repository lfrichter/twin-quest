# Architectural Decisions

This document outlines the key architectural decisions made for this project, based on the technical requirements and best practices.

## 1. Technical Stack

The chosen technology stack is:

*   **Backend Framework:** Laravel 12
    *   *Justification:* A robust PHP framework with a mature ecosystem, facilitating rapid development of RESTful APIs and server-side logic.
*   **Frontend Framework:** Vue.js 3 (Composition API with TypeScript)
    *   *Justification:* Selected for its reactivity, flexibility, and the modern Composition API, which allows for more cohesive and reusable logical organization. TypeScript is included to add static typing, improving code robustness, facilitating maintenance, and reducing compile-time errors.
*   **Backend-Frontend Integration:** Inertia.js 1.0
    *   *Justification:* Used to create modern, server-driven single-page applications (SPAs), combining Laravel's power on the backend with Vue.js's reactivity on the frontend without the complexity of building and consuming separate APIs for main navigation.
*   **Database:** PostgreSQL 15
    *   *Justification:* A powerful, open-source object-relational database management system known for its reliability and robustness.
*   **Frontend State Management:** Pinia 2
    *   *Justification:* The official state management library for Vue.js, chosen for its simplicity, integration with Vue DevTools, and full support for the Composition API.
*   **PHP Testing Framework:** Pest 2
    *   *Justification:* An elegant, developer-focused PHP testing framework that provides an expressive and enjoyable testing experience for Laravel.

This technology stack was chosen to assess proficiency in integrating modern and efficient technologies for full-stack development, focusing on productivity, performance, and maintainability.

## 2. Recommended Directory Structure

The directory structure will follow standard Laravel and Vue.js conventions:

*   **Laravel:**
    *   `app/Http/Controllers/`: Controllers for handling HTTP requests.
    *   `app/Http/Requests/`: Form Requests for input data validation.
    *   `app/Http/Resources/`: API Resources for transforming Eloquent models and collections into JSON.
    *   `app/Models/`: Eloquent models for database interaction.
    *   `app/Providers/`: Service Providers for registering services.
    *   `app/Services/`: Service classes for encapsulating business logic.
    *   `app/Repositories/`: (Optional, if Repository pattern is adopted) Classes for abstracting data access logic.
    *   `database/migrations/`: Database migrations.
    *   `database/seeders/`: Seeders for populating the database.
    *   `routes/api.php`: API route definitions.
    *   `routes/web.php`: Web route definitions (managed by Inertia.js).
    *   `resources/js/Pages/`: Vue.js page components (rendered by Inertia).
    *   `resources/js/Components/`: Reusable Vue.js components.
    *   `resources/js/Layouts/`: Vue.js layouts for pages.
    *   `resources/js/Store/`: Pinia stores for global state management.
    *   `resources/js/Composables/`: Reusable Composition API functions.
    *   `tests/Feature/`: Feature tests (Pest).
    *   `tests/Unit/`: Unit tests (Pest).
*   **Vue.js (within `resources/js/`):**
    *   The structure will be organized into `Pages`, `Components`, `Layouts`, `Store` (for Pinia), and `Composables`, following best practices for Inertia.js applications.

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
