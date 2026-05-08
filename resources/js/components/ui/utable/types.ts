import {DataSourceContext, DataSourceProps} from "@js/components/ui/udata/types.ts";

export interface Column {
    prop: string;
    label: string;
    headerComponent?: any;
    cellComponent?: any;
    headerClass?: string;
    cellClass?: string;
}

export interface TableContext extends DataSourceContext {
    addColumn: (column: Column) => void;
}

export interface TableProps extends DataSourceProps {

}

export interface TableColumnProps {
    prop?: string;
    label?: string;
    headerClass?: string;
    cellClass?: string;
}
