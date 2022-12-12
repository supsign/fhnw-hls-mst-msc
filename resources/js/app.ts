import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './index.css';

import 'sweetalert2/src/sweetalert2.scss';

const env = import.meta.env;

const app = createApp(App);
app.use(router);
app.mount('#app');
