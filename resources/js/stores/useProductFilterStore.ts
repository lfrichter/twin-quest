import { defineStore } from 'pinia';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { IProductFilterStoreState, IFilterState } from '@/Types';

export const useProductFilterStore = defineStore('productFilter', {
  state: (): IProductFilterStoreState => ({
    filters: {
      name: '',
      category_id: undefined,
      status: '',
    },
    isLoading: false,
  }),
  actions: {
    /**
     * Initializes the filters in the store.
     * Typically called on component mount or when props.filters change.
     * Only updates the store's filters if the new values are actually different.
     * @param {IFilterState} initialFilters - The initial filter values.
     */
    initializeFilters(initialFilters: IFilterState) {
      // console.log('Store: initializeFilters called with:', initialFilters);
      const current = this.filters;

      // Prepare a normalized version of initialFilters for comparison and potential assignment.
      // Ensure that undefined or null props are consistently handled as empty strings or undefined for category_id.
      const normalizedFilters = {
        name: initialFilters?.name || '',
        category_id: initialFilters?.category_id, // Retains undefined if that's the value
        status: initialFilters?.status || '',
      };

      const { name, category_id, status } = this.filters;

      if (
        name !== normalizedFilters.name ||
        category_id !== normalizedFilters.category_id ||
        status !== normalizedFilters.status
      ) {
        this.filters = normalizedFilters;
        // console.log('Store: Filters updated by initializeFilters to:', this.filters);
      } else {
        // console.log('Store: initializeFilters called, but filter values are already synchronized.');
      }
    },

    /**
     * Updates filters and schedules a debounced product fetch.
     * @param {Partial<IFilterState>} newFilters - The new filter values to apply.
     */
    updateAndFetchFilters(newFilters: Partial<IFilterState>) {
      this.filters = { ...this.filters, ...newFilters };
      // console.log('Store: Filters updated by updateAndFetchFilters:', this.filters);
      this.scheduleFetchProducts({ preserveScroll: true });
    },

    /**
     * Resets filters to their default values and fetches products immediately.
     */
    resetFilters() {
      this.filters = {
        name: '',
        category_id: undefined,
        status: '',
      };
      // console.log('Store: Filters reset:', this.filters);
      // Fetch products immediately after reset, preserving scroll if content is above
      this.fetchProducts({ preserveScroll: true });
    },

    /**
     * Sets the loading state.
     * @param {boolean} loading - The new loading state.
     */
    setLoading(loading: boolean) {
      this.isLoading = loading;
    },

    _debounceTimerId: undefined as ReturnType<typeof setTimeout> | undefined,
    /**
     * Schedules a debounced call to fetchProducts.
     * @param {{ preserveScroll?: boolean; page?: number }} options - Options for fetching products.
     */
    scheduleFetchProducts(options: { preserveScroll?: boolean; page?: number } = {}) {
        if (this._debounceTimerId) {
            clearTimeout(this._debounceTimerId);
        }
        this._debounceTimerId = setTimeout(() => {
            this.fetchProducts(options);
        }, 500); // 500ms debounce delay
    },

    /**
     * Fetches products based on the current filter state and page.
     * This action makes the Inertia GET request.
     * @param {{ preserveScroll?: boolean; page?: number }} options - Navigation options.
     */
    fetchProducts(options: { preserveScroll?: boolean; page?: number } = {}) {
      this.setLoading(true);

      try {
        const url = route('products.index');
        router.get(
          url,
          { ...this.filters, page: options.page },
          {
            preserveState: true,
            preserveScroll: options.preserveScroll || false,
            replace: true, // Replace history state to avoid multiple entries for filter changes
            only: ['products', 'filters'],
            onSuccess: () => {
              // console.log('Store: Fetch successful, products prop will be updated by Inertia.');
            },
            onError: (errors) => {
              console.error('Store: Error fetching products:', errors);
            },
            onFinish: () => {
              this.setLoading(false);
              // console.log('Store: Fetch completed, isLoading:', this.isLoading);
            },
          }
        );
      } catch (e) {
        console.error("Store: Error using route() function:", e);
        this.setLoading(false);
      }
    },

    /**
     * Handles navigation to a new page via pagination links.
     * @param {string | null} url - The URL for the new page.
     */
    handlePageChange(url: string | null) {
      if (!url) return;
      this.setLoading(true);
      // console.log('Store: Navigating to pagination URL:', url);
      router.get(
        url,
        { ...this.filters }, // Pass current filters to maintain context during pagination
        {
          preserveState: true, // Important to keep filter state when paginating
          preserveScroll: false, // Usually, scroll to top for new page content
          replace: false, // Add to browser history for back/forward
          only: ['products', 'filters'],
          onSuccess: () => {
            // console.log('Store: Pagination successful, products prop will be updated.');
          },
          onError: (errors) => {
            console.error('Store: Error during pagination:', errors);
          },
          onFinish: () => {
            this.setLoading(false);
            // console.log('Store: Pagination fetch completed');
          },
        }
      );
    }
  },
  getters: {
    /**
     * Gets the current filter state.
     * @param {IProductFilterStoreState} state - The store state.
     * @returns {IFilterState} The current filters.
     */
    currentFilters(state): IFilterState {
      return state.filters;
    },
    /**
     * Gets the current loading status.
     * @param {IProductFilterStoreState} state - The store state.
     * @returns {boolean} The loading status.
     */
    isLoadingStatus(state): boolean {
      return state.isLoading;
    }
  },
});
