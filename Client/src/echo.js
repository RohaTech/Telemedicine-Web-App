import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make Pusher available globally
window.Pusher = Pusher;

// Initialize Laravel Echo with dynamic auth headers
const createEcho = () => {
  return new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: 'http://localhost:8000/broadcasting/auth',
    auth: {
      headers: {
        get Authorization() {
          const token = localStorage.getItem('token');
          return token ? `Bearer ${token}` : '';
        },
      },
    },
  });
};

// Initialize Echo
window.Echo = createEcho();

// Export a function to reinitialize Echo if needed
window.reinitializeEcho = () => {
  if (window.Echo) {
    window.Echo.disconnect();
  }
  window.Echo = createEcho();
};

export default window.Echo;
