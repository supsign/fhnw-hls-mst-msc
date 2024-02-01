import type { IModel } from './model.interface';

export interface IText extends IModel {
  content: string;
  name: string;
}
