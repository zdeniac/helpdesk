import { createRouter, createWebHistory } from 'vue-router';
import Login from '../features/auth/Login.vue';
import Events from '../features/auth/Events.vue';
import Conversations from '../features/auth/Conversations.vue';
import MainLayout from '../layouts/MainLayout.vue';

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
				path: '/events',
				component: Events
			},
			{
				path: '/conversations',
				component: Conversations
			}
		],
	}
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;