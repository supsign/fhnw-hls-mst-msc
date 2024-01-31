import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      children: [
        {
          component: () => import('../views/Home.vue'),
          name: 'Home',
          path: ''
        },
        {
          component: () => import('../views/Config.vue'),
          path: 'admin/config'
        },
        {
          component: () => import('../views/Privacy.vue'),
          name: 'Privacy',
          path: 'privacy'
        },
        {
          component: () => import('../views/Imprint.vue'),
          name: 'Imprint',
          path: 'imprint'
        }
      ],
      component: () => import('../layouts/Main.vue'),
      path: '/'
    }
  ]
});

export default router;
