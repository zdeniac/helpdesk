<template>
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            <ul class="navbar-nav">
                <li class="nav-item" v-if="isUser">
                    <router-link to="/events" class="nav-link">
                        Eseményeim
                    </router-link>
                </li>
                <li class="nav-item" v-if="isUser">
                    <router-link to="/helpdesk" class="nav-link">
                        Helpdesk
                    </router-link>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <LogoutButton />
                </li>
            </ul>

        </nav>

        <!-- Content -->
        <div class="content-wrapper">
            <router-view />
        </div>
    </div>
</template>

<script setup>
import LogoutButton from '../components/LogoutButton.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import {API_BASE_URL } from '../config';

const isUser = ref(false);

onMounted(async () => {
    const token = localStorage.getItem('token');
    if (!token) return;

    try {
        const response = await axios.get(`${API_BASE_URL}/me`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        isUser.value = response.data.role === 'user';
    } catch (e) {
        console.error('Nem sikerült lekérni a felhasználót', e);
    }
})
</script>
