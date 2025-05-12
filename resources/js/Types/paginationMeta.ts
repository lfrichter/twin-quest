
import { IPaginationLink } from "./paginationLink";
import { IFilterState } from "./filterState";

export interface IPaginationMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  links: IPaginationLink[];
  path: string;
  per_page: number;
  to: number | null;
  total: number;
  filters?: IFilterState; // Active filters returned from backend
}
