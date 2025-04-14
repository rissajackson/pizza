import axios from 'axios';
import CustomEcho from './CustomEcho.js';

window.axios = axios;

// Ensure Axios sends the `X-Requested-With` header with all requests
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


// Set up Laravel Echo with Reverb as the broadcaster
window.Echo = new CustomEcho({
    broadcaster: 'reverb', // Specify Reverb as the broadcasting driver
    key: import.meta.env.VITE_REVERB_APP_KEY || 'local', // Application key defined in your .env
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname, // WebSocket host, defaults to current hostname
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080, // WebSocket port, fallback to 8080
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https', // TLS flag based on scheme
    disableStats: true, // Disables Pusher stats collection, not applicable to Reverb
});
