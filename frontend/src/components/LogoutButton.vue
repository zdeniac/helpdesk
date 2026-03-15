<template>
  <button @click="logout" class="btn btn-danger btn-sm">
    <i class="fas fa-sign-out-alt me-1"></i> Logout
  </button>
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
            try {
                const token = localStorage.getItem('token');
                if (!token) return;

                await axios.post(`${API_BASE_URL}/logout`, null, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                localStorage.removeItem('token');
                router.push('/login');
            } catch (err) {
                console.error('Logout failed', err);
            }
        };
        
        return { logout };
  }
}
</script>