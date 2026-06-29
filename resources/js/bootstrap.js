import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// WebSocket (Reverb) dinonaktifkan.
// Notifikasi berjalan via polling otomatis setiap 15 detik — kompatibel dengan Shared Hosting.
