import { createApp } from 'vue';

import App from './App.vue';
import router from './router';

import 'admin-lte/dist/css/adminlte.min.css';
import 'admin-lte/dist/js/adminlte.min.js';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

const app = createApp(App);

// Globális interceptor minden axios hívásra a token érvényesség ellenőrzéséhez
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    if (to.meta.requiresAuth && !token) {
        return next('/login');
    }
    next();
});

app.use(router)
    .mount('#app');
