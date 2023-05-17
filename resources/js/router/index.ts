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
          name: 'Home',
          component: () => import('../views/Home.vue')
        },
        {
          path: 'admin/config',
          component: () => import('../views/Config.vue')
        },
        {
          path: 'privacy',
          name: 'Privacy',
          component: () => import('../views/Privacy.vue')
        },
        {
          path: 'imprint',
          name: 'Imprint',
          component: () => import('../views/Imprint.vue')
        }
      ]
    }
  ]
});

export default router;
