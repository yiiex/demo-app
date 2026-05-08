import {Action} from "@js/components/ui/uaction/types.ts";
import {Form} from "@js/components/ui/uform/types.ts";

export interface PaginationMeta {
    total: number;
    page: number;
    perPage: number;
    count: number;
}

export interface DataProvider<T = any> {
    data: T[];
    meta: PaginationMeta;
}

export interface CrudDataProvider<T = any> extends DataProvider<T> {
    actions?: Action[][];
    filter?: Form;
}

export interface PaginationProps {
    meta: PaginationMeta;
}

export interface DataSourceProps {
    data?: CrudDataProvider;
    url?: string;
    mode?: 'default' | 'lazy';
}

export interface DataListProps extends DataSourceProps {
    listClass?: string;
    containerClass?: string;
    paginationClass?: string;
    paginationWrapperClass?: string;
}

export interface DataSourceContext {
    refresh: () => Promise<void>;
}
