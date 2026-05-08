interface DebugTrace {
    class: string;
    type: string;
    function: string;
    file?: string;
    line?: number;
}

interface DebugQuery {
    sql: string;
    sql_formatted?: string;
    duration: number;
    connection: string;
    params?: Record<string, unknown>;
    trace?: DebugTrace[];
}

interface DebugRoute {
    name: string;
    pattern: string;
    uri: string;
    matchTime: number;
    middlewares?: string[];
}

interface DebugApp {
    memoryUsage: number;
    memoryPeakUsage: number;
    requestProcessingTime: number;
    applicationEmit: number;
}

interface DebugRequest {
    requestMethod: string;
    requestPath: string;
    responseStatusCode: number;
    userIp: string;
    requestIsAjax: boolean;
    requestRaw?: string;
}

interface DebugLog {
    id: string;
    app?: DebugApp;
    queries?: DebugQuery[];
    currentRoute?: DebugRoute;
    request?: DebugRequest;
}
