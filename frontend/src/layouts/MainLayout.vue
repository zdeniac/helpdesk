<template>
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
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
import LogoutButton from '../components/LogoutButton.vue'
</script>
