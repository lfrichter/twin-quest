import { IFilterState } from "./filterState";

export interface IProductFilterStoreState {
  filters: IFilterState;
  isLoading: boolean;
}
