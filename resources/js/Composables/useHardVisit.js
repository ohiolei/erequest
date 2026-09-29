/**
 * Full-document POST + replace navigation so Login As lands on a fresh
 * document under the new session (history entry replaced, auth meta refreshed).
 */
export async function hardPost(action, data = {}) {
    const xsrf = decodeURIComponent(
        document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/)?.[1] ?? ''
    );
    const csrfMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const body = new FormData();
    if (csrfMeta) {
        body.append('_token', csrfMeta);
    }
    Object.entries(data).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
            body.append(key, String(value));
        }
    });

    const headers = {
        Accept: 'text/html, application/xhtml+xml',
        'X-Requested-With': 'XMLHttpRequest',
    };
    if (xsrf) {
        headers['X-XSRF-TOKEN'] = xsrf;
    }

    const response = await fetch(action, {
        method: 'POST',
        credentials: 'same-origin',
        headers,
        body,
        redirect: 'follow',
    });

    const target = response.url || window.location.href;

    // replace() starts a fresh history stack for the impersonated session
    // so Back/Forward move between that account's pages, not the previous one.
    window.location.replace(target);
}
