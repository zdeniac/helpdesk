<template>
    <div>
        <h1>Conversations</h1>
        <div v-if="loading">Betöltés...</div>
        <div v-else>
            <div>
            </div>
            <div v-if="conversations.data?.length">
                <table border="1" cellpadding="5">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else>There are no conversations.</div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { API_BASE_URL } from "../../config";

export default {
    setup() {
        const conversations = ref([]);
        const loading = ref(true);

        const fetchConversations = async () => {
            const token = localStorage.getItem('token');
            if (!token) return;
            try {
                const response = await axios.get(`${API_BASE_URL}/api/agent/conversations`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                conversations.value = response.data;
            } catch (error) {
                console.error('Failed to fetch events', error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(fetchConversations);

        return {fetchConversations, conversations};
    }
}
</script>
