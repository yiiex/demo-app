import {ComputedRef, Ref} from "vue";

export interface SelectListItem {
    key: string | number,
    label: string,
}

export interface SelectProps {
    placeholder?: string,
    multiple?: boolean,
    list: SelectListItem[],
}

type ErrorMap = Record<string, string[]>;
type LabelMap = Record<string, string>;

export interface Form {
    model: any;
    attributeLabels: LabelMap;
    safeAttributes: string[];
    errors: ErrorMap;
}

export interface UFormProps {
    url: string
    errors?: Record<string, string[]> | null
    method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
    model?: Record<string, any> | null
    modelPath?: string | null
    safeAttributes?: string[]
    attributeLabels?: Record<string, string>
}

export interface UFormEmits {
    (e: 'submit'): void

    (e: 'onResponse', data: any): void

    (e: 'onResponseError', error: any): void

    (e: 'onResponseFinal'): void
}

export interface FormContext {
    model: ComputedRef<Record<string, any>>
    safeAttributes: string[]
    attributeLabels: Record<string, string>
    errors: Ref<Record<string, string[]>>
    setErrors: (errors: Record<string, string[]> | null) => void
    clearErrors: () => void
}

export interface FormResponse {
    success: boolean
    message?: string
    errors?: Record<string, string[]>
    redirectUrl?: string
    data?: any
}

export interface UFormButtonProps {
    name?: string
    color?: string
    type?: 'button' | 'submit' | 'reset'
    size?: 'default' | 'sm' | 'lg' | 'icon'
    loading?: boolean
    disabled?: boolean
    plain?: boolean
    href?: string | null
}

export interface UFormButtonEmits {
    (e: 'click', event: MouseEvent): void
}

export interface UFormItemProps {
    label?: string | null
    name?: string | null
    errors?: string[] | null
    showErrorText?: boolean
    inline?: boolean
    labelPosition?: 'before' | 'after'
    withoutLabel?: boolean
}
