import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Create Echo instance with Pusher configuration
const echo = new Echo({
    broadcaster: 'pusher',
    key: '881a64e93fcc6b50919d', // Your Pusher app key
    cluster: 'eu', // Your Pusher cluster
    forceTLS: true,
    encrypted: true,
    enabledTransports: ['ws', 'wss'],
});

export default echo;
