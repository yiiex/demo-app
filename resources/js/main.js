import axios from "axios";

window.axios = axios;
const csrfToken = document.querySelector('meta[name="csrf"]').getAttribute('content');
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

import './inertia.js';

