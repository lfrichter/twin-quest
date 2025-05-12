import { ICategory } from "./category";

export interface IProduct {
  id: number;
  name: string;
  category: ICategory;
  status: string;
  price: number;
  created_at: string;
}

