// Import test utilities from Vitest (or Jest) and Vue Test Utils
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { createPinia, setActivePinia } from 'pinia';

import { defineComponent, h } from 'vue'; // Added PropType for more specific prop typing
import type { PropType } from 'vue';

// Import the component to be tested
import IndexPage from '@/Pages/Products/Index.vue';
// Add this import if not already present, adjust path if necessary
import { ICategory, IPaginatedProducts, IPaginationLink, IFilterState, IProduct } from '@/Types'; // Assuming IProduct might be used or part of IPaginatedProducts

// Mock Inertia and Ziggy
// It's crucial to mock dependencies used by IndexPage or its children
vi.mock('@inertiajs/vue3', async (importOriginal) => {
  const actual = await importOriginal<typeof import('@inertiajs/vue3')>();
  return {
    ...actual,
    usePage: vi.fn(() => ({
      props: { // Provide minimal necessary props that IndexPage or its layout might expect
        auth: { user: { name: 'Test User' } }, // Example
        errors: {},
        flash: {},
        // Add other props if your component/layout depends on them
      },
      component: 'Products/Index',
      url: '/products',
      version: 'test-version',
    })),
    router: {
      get: vi.fn(),
      post: vi.fn(),
      put: vi.fn(),
      delete: vi.fn(),
      patch: vi.fn(),
      reload: vi.fn(),
      remember: vi.fn(),
      restore: vi.fn(),
      replace: vi.fn(),
      visit: vi.fn(),
    },
    Link: defineComponent({ // Simple stub for Link
      name: 'Link',
      props: ['href'],
      render() { return h('a', { href: this.href }, this.$slots.default ? this.$slots.default() : []); }
    }),
    Head: defineComponent({ // Simple stub for Head
        name: 'Head',
        render() { return h('div', {}, this.$slots.default ? this.$slots.default() : []); } // Render slot in a div
    }),
  };
});

vi.mock('ziggy-js', () => ({
  default: vi.fn((name?: string) => `mocked.route.${name || 'unnamed'}`),
  route: vi.fn((name?: string) => `mocked.route.${name || 'unnamed'}`),
}));


// Define the test suite for ProductFilters.vue
describe('Pages/Products/Index.vue', () => {
  let pinia: any; // Declare pinia instance

  beforeEach(() => { // Setup Pinia before each test
    pinia = createPinia();
    setActivePinia(pinia);
    vi.clearAllMocks(); // Clear mocks
  });

  // Helper function to create default mock props
  // Function to create mock props with proper typing and defaults
  const createMockProps = (overrides: any = {}): { products: IPaginatedProducts, filters: IFilterState, categories: ICategory[] } => {
    const defaultState = {
      products: {
        data: [] as IProduct[],
        links: [{ url: '', label: 'Previous', active: false }, { url: '', label: 'Next', active: false }] as IPaginationLink[], // Default pagination links
        meta: { // Provide default meta structure
          current_page: 1,
          from: 0,
          last_page: 1,
          links: [] as IPaginationLink[], // Numeric page links inside meta
          path: '/products',
          per_page: 15,
          to: 0,
          total: 0,
          filters: { // Default filters in meta, if applicable
            name: '',
            category_id: undefined,
            status: '',
          } as IFilterState
        },
      } as IPaginatedProducts,
      filters: { // Top-level filters prop
        name: '',
        category_id: undefined,
        status: '',
      } as IFilterState,
      categories: [] as ICategory[],
    };

    // Create a deep copy of defaultState to avoid unintended mutations
    const mergedProps = JSON.parse(JSON.stringify(defaultState));

    // Merge top-level overrides (filters, categories)
    if (overrides.filters) {
        mergedProps.filters = { ...mergedProps.filters, ...overrides.filters };
    }
    if (overrides.categories) {
        mergedProps.categories = overrides.categories;
    }

    // Deep merge for products if provided in overrides
    if (overrides.products) {
      const defaultProd = defaultState.products; // Use original default for products base
      const overrideProd = overrides.products;

      mergedProps.products = {
        // Ensure 'data' is an array, taken from override if present, else default
        data: overrideProd.data !== undefined ? overrideProd.data : defaultProd.data,
        // Ensure 'links' is an array, taken from override if present, else default
        links: overrideProd.links !== undefined ? overrideProd.links : defaultProd.links,
        // Deep merge 'meta'
        meta: {
          ...defaultProd.meta,
          ...(overrideProd.meta || {}),
        },
      };
    }
    return mergedProps;
  };

  // Test case: Check if the component renders correctly with initial products and filters
  it('renders correctly with initial products and filters', () => {
    const props = createMockProps({
      products: { // Override products data for this test
        data: [
          // Adjusted mock to better match IProduct and ICategory interfaces
          { id: 1, name: 'Product 1', category: { id: 1, name: 'Category 1' }, price: 10.00, status: "active", created_at: '2024-01-01T00:00:00.000Z'} as IProduct,
          { id: 2, name: 'Product 2', category: { id: 1, name: 'Category 1' }, price: 20.00, status: "active", created_at: '2024-01-01T00:00:00.000Z'} as IProduct,
        ],
        // 'links' for IPaginatedProducts (top-level)
        links: [
            { url: '/products?page=1', label: '&laquo; Previous', active: false },
            { url: '/products?page=2', label: 'Next &raquo;', active: true }
        ] as IPaginationLink[],
        meta: { // Ensure meta reflects the data
            total: 2,
            from: 1,
            to: 2,
            current_page: 1,
            last_page: 1,
            per_page: 15,
            // 'links' inside meta (for numbered pages)
            links: [
                { url: '/products?page=1', label: '1', active: true }
            ] as IPaginationLink[],
            path: '/products',
            filters: { // Filters that were applied for this result set
              name: '',
              category_id: undefined,
              status: '',
            } as IFilterState
        }
      } as IPaginatedProducts // Ensure the entire override matches IPaginatedProducts
    });
    const wrapper = mount(IndexPage, {
      props,
      global: {
        plugins: [pinia], // Add Pinia plugin
        stubs: {
          ProductFilters: true, // Stub child components for focused unit tests
          Pagination: true,
          ProductList: defineComponent({ // More robust stub definition for ProductList
            name: 'ProductListStub',
            props: {
              products: {
                type: Array as PropType<IProduct[]>, // Changed: Expect IProduct[] directly
                required: true
              }
            },
            template: `
              <div data-testid="product-list-stub">
                <ul v-if="products && products.length > 0"> <!-- Changed: Check products.length -->
                  <li v-for="product in products" :key="product.id">{{ product.name }}</li> <!-- Changed: Iterate over products -->
                </ul>
                <p v-else data-testid="stub-no-products-message">No products found (from stub)</p>
              </div>
            `,
          }),
        }
      }
    });

    // Assert that the component exists
    expect(wrapper.exists()).toBe(true);
    // Assert that product name is rendered by the ProductList stub
    const productListStub = wrapper.find('[data-testid="product-list-stub"]');
    expect(productListStub.exists()).toBe(true); // Check if the stub itself rendered
    expect(productListStub.text()).toContain('Product 1'); // Check for product name
    expect(productListStub.text()).toContain('Product 2');
    expect(productListStub.text()).not.toContain('No products found (from stub)');
  });

  // Test case: Check if "No products found" message is shown when there are no products
  it('displays "No products found" message when there are no products', () => {
    const props = createMockProps({
      products: { // Override products
        data: [], // with empty data
        meta: { total: 0, from: 0, to: 0 } // and total 0 to be more explicit
      }
    });
    const wrapper = mount(IndexPage, {
        props,
        global: { // Ensure Pinia and stubs are also provided here
            plugins: [pinia],
            stubs: {
                ProductFilters: true,
                Pagination: true,
                ProductList: defineComponent({ // Use the same robust stub definition
                  name: 'ProductListStub',
                  props: {
                    products: { type: Object as PropType<IPaginatedProducts>, required: true }
                  },
                  // This template is for the stub, which we expect NOT to be rendered by IndexPage
                  template: `
                    <div data-testid="product-list-stub">
                      <ul v-if="products && products.data && products.data.length > 0">
                        <li v-for="product in products.data" :key="product.id">{{ product.name }}</li>
                      </ul>
                      <p v-else data-testid="stub-no-products-message">No products found (from stub)</p>
                    </div>
                  `
                })
            }
        }
    });

    // Assert that the ProductList stub is NOT rendered by IndexPage
    // (because IndexPage should have a v-if to hide it when products.data is empty)
    expect(wrapper.find('[data-testid="product-list-stub"]').exists()).toBe(false);

    // Assert that IndexPage itself displays a "no products" message.
    // This assumes your IndexPage.vue has a message like:
    // <p data-testid="index-no-products-message">No products found matching your criteria.</p>
    // If using a generic text search:
    expect(wrapper.text()).toContain('No products found');
    // If you have a specific data-testid in IndexPage.vue for this message, use:
    // const noProductsMessage = wrapper.find('[data-testid="index-no-products-message"]');
    // expect(noProductsMessage.exists()).toBe(true);
    // expect(noProductsMessage.text()).toContain('No products found matching your criteria.'); // Or the exact text
  });
});
