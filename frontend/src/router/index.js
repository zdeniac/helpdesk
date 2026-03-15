import { createRouter, createWebHistory } from 'vue-router';

import MainLayout from '../layouts/MainLayout.vue';

import Login from '../features/auth/Login.vue';
import Events from '../features/auth/Events.vue';
import Conversations from '../features/auth/Conversations.vue';
import Helpdesk from '../features/auth/Helpdesk.vue';
import ForgotPassword from '../features/auth/ForgotPassword.vue';
import ResetPassword from '../features/auth/ResetPassword.vue';

const routes = [
  // Public routes
  {
    path: '/login',
    component: Login
  },
  {
    path: '/forgot-password',
    component: ForgotPassword
  },
  {
    path: '/password-reset/:token?',
    component: ResetPassword,
	props: true
  },

  // Protected routes (MainLayout + children)
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', redirect: '/events' }, // ha valaki simán '/'-ra megy, menjen events-re
      { path: 'events', component: Events },
      { path: 'conversations', component: Conversations },
      { path: 'helpdesk/:id?', component: Helpdesk, props: true }
    ]
  },

  // Catch-all redirect
  { path: '/:catchAll(.*)', redirect: '/login' }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;