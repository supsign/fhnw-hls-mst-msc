import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: () => import('../layouts/Main.vue'),
            children: [
                {
                    path: '',
                    component: () => import('../views/Home.vue'),
                },
                {
                    path: 'admin/config',
                    component: () => import('../views/Config.vue'),
                },
            ],
        },
    ],
});

export default router;
