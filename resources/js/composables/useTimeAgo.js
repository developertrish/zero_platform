/**
 * Shared time-ago formatter used by PostCard, CommentItem, Posts/Show.
 * Returns a human-readable relative time string.
 */
export function useTimeAgo() {
    function timeAgo(dateStr) {
        const seconds = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);

        if (seconds < 5)   return 'just now';
        if (seconds < 60)  return `${seconds}s`;
        if (seconds < 3600) return `${Math.floor(seconds / 60)}m`;
        if (seconds < 86400) return `${Math.floor(seconds / 3600)}h`;
        if (seconds < 604800) return `${Math.floor(seconds / 86400)}d`;
        if (seconds < 2592000) return `${Math.floor(seconds / 604800)}w`;

        return new Date(dateStr).toLocaleDateString('en', {
            month: 'short',
            day:   'numeric',
            year:  new Date(dateStr).getFullYear() !== new Date().getFullYear() ? 'numeric' : undefined,
        });
    }

    function formatFull(dateStr) {
        return new Date(dateStr).toLocaleString('en', {
            dateStyle: 'long',
            timeStyle: 'short',
        });
    }

    function humanSize(bytes) {
        if (bytes < 1024)    return `${bytes} B`;
        if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`;
        return `${(bytes / 1048576).toFixed(1)} MB`;
    }

    return { timeAgo, formatFull, humanSize };
}
