export const formatBytes = (bytes: number | null | undefined): string => {
    if (!bytes) return '0 B';
    const sizes: string[] = ['B', 'KB', 'MB', 'GB'];
    const i: number = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
};

export const formatMs = (seconds: number | null | undefined): string => {
    if (!seconds) return '0 ms';
    return (seconds * 1000).toFixed(2) + ' ms';
};

export const highlightSql = (sql: string | undefined): string => {
    if (!sql) return '';
    const keywords = [
        'SHOW', 'SUM',
        'SELECT', 'FROM', 'WHERE', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'OUTER JOIN',
        'ORDER BY', 'GROUP BY', 'LIMIT', 'OFFSET', 'INSERT', 'UPDATE', 'DELETE', 'SET',
        'VALUES', 'AND', 'OR', 'AS', 'ON', 'IN', 'NOT', 'NULL', 'IS', 'LIKE',
        'BETWEEN', 'EXISTS', 'HAVING', 'DISTINCT', 'COUNT', 'SUM', 'AVG', 'MAX', 'MIN',
        'CREATE', 'ALTER', 'DROP', 'TABLE', 'INDEX',
        'CASE', 'WHEN', 'THEN', 'ELSE', 'END', 'UNION', 'ALL', 'ASC', 'DESC',
    ];
    const unique = [...new Set(keywords)].sort((a, b) => b.length - a.length);
    let highlighted = sql;
    unique.forEach(keyword => {
        const regex = new RegExp(`\\b${keyword.replace(/\s+/g, '\\s+')}\\b`, 'gi');
        highlighted = highlighted.replace(regex, match =>
            `<span class="text-rose-600 dark:text-amber-400 font-semibold">${match}</span>`
        );
    });
    return highlighted;
};
