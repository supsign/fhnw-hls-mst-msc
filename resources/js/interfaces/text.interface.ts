import type { IModel } from './model.interface';

export interface IText extends IModel {
    name: string;
    content: string;
}
