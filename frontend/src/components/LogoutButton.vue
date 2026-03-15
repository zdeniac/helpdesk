<template>
    <button @click="logout">Logout</button>
</template>

<script>
import axios from 'axios';
import { useRouter } from "vue-router";
import { API_BASE_URL } from "../config";

export default {
    setup() {
        const router = useRouter();
        const token = localStorage.getItem('token');

        const logout = async () => {
            const response = await axios.post(`${API_BASE_URL}/api/logout`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            localStorage.removeItem('token');
            router.push('/login');
        };

        return { logout };
  }
}
</script>