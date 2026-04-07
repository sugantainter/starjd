/**
 * Parses axios errors into professional, user-friendly messages.
 * Prevents raw SQL or technical details from leaking to the end user.
 */
export function getErrorMessage(error) {
    // 1. If it's a string, return it directly
    if (typeof error === 'string') return error;

    // 2. If it's not an axios error object, return fallback
    if (!error || typeof error !== 'object') {
        return 'An unexpected error occurred. Please try again.';
    }

    // 3. Handle network/connection errors
    if (!error.response) {
        if (error.code === 'ECONNABORTED') return 'The request timed out. Please check your internet connection.';
        return error.message || 'No response from server. Please check your connection.';
    }

    const { status, data } = error.response;

    // 4. Handle Laravel Validation Errors (422)
    if (status === 422) {
        if (data.errors) {
            // Get the first error message from any field
            const allErrors = Object.values(data.errors).flat();
            if (allErrors.length > 0) return allErrors[0];
        }
        return data.message || 'The provided data is invalid.';
    }

    // 5. Handle Payload Too Large (413) - common with image uploads
    if (status === 413) {
        return 'The uploaded file is too large. Please use a smaller image.';
    }

    // 6. Handle technical leakage in generic messages
    if (data.message && typeof data.message === 'string') {
        const technicalTerms = ['SQLSTATE', 'Integrity constraint', 'QueryException', 'Unknown column', 'Syntax error', 'Connection refused', 'Stack trace'];
        const isTechnical = technicalTerms.some(term => data.message.includes(term));
        
        if (isTechnical) {
            // Check for specific constraint types to give slightly better context without raw code
            if (data.message.includes('Duplicate entry')) {
                return 'This item already exists in our system. Please check for duplicates.';
            }
            if (data.message.includes('foreign key constraint')) {
                return 'This item cannot be deleted or modified because it is being used elsewhere.';
            }
            return 'A system error occurred while processing your request. Our technical team has been notified.';
        }
        
        return data.message;
    }

    // 7. Fallback based on HTTP status codes
    switch (status) {
        case 401: return 'Your session has expired. Please log in again.';
        case 403: return 'You do not have permission to perform this action.';
        case 404: return 'The requested resource could not be found.';
        case 419: return 'Page expired. Please refresh and try again.';
        case 500: return 'Internal server error. Our team is looking into it.';
        case 503: return 'Service is temporarily unavailable. Please try again in a few minutes.';
        default: return `An error occurred (Status: ${status}). Please try again later.`;
    }
}
