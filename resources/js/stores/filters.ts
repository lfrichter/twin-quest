// stores/filters.ts
import { defineStore } from 'pinia';

interface IFilterState {
  name?: string;
  category_id?: number | null;
  status?: string;
}

export const useFiltersStore = defineStore('filters', {
  state: () => ({
    filters: {
      name: '',
      category_id: null,
      status: '',
    } as IFilterState,
  }),
  actions: {
    updateFilters(newFilters: IFilterState) {
      this.filters = { ...this.filters, ...newFilters };
    },
  },
});
