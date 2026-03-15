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

    	const logout = async () => {
		const token = localStorage.getItem('token');

		localStorage.removeItem('token');

    	try {
			if (token) {
				await axios.post(`${API_BASE_URL}/logout`, null, {
					headers: { Authorization: `Bearer ${token}` }
				});
			}
		} catch (err) {
			console.warn('Logout request failed, redirecting anyway', err);
		} finally {
			router.push('/login');
		}
    };
	
	return { logout };
  } 
}
</script>