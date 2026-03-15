import { createRouter, createWebHistory } from 'vue-router';

import MainLayout from '../layouts/MainLayout.vue';

import Login from '../features/auth/Login.vue';
import Events from '../features/auth/Events.vue';
import Conversations from '../features/auth/Conversations.vue';
import Helpdesk from '../features/auth/Helpdesk.vue';

const routes = [
	{
		path: '/login',
		component: Login
	},
	{
		path: '/',
		redirect: '/login',
		component: MainLayout,
		children: [
			{
				path: 'events',
				component: Events
			},
			{
				path: 'conversations',
				component: Conversations
			},
			{
				path: 'helpdesk/:id?',
				component: Helpdesk,
				props: true
			}
		],
	}
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;