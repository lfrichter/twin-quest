import { IProduct } from "./product";
import { IPaginationLink } from "./paginationLink";
import { IPaginationMeta } from "./paginationMeta";

export interface IPaginatedProducts {
  data: IProduct[];
  links: IPaginationLink[]; // Simplified, Laravel's default pagination links structure
  meta: IPaginationMeta;
}
