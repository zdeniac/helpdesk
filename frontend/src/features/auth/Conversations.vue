<template>
  <div class="container-fluid pt-3">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <h3 class="card-title mb-0">Conversations</h3>
      </div>

      <div class="card-body">
        <div v-if="loading" class="text-center">
          Betöltés...
        </div>

        <div v-else>
          <div v-if="conversations.length">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="conversation in conversations" :key="conversation.id">
                  <td>{{ conversation.id }}</td>
                  <td>{{ conversation.user.name ?? conversation.userId }}</td>
                  <td>{{ conversation.status }}</td>
                  <td>
                    <button
                      class="btn btn-sm btn-info me-1"
                      @click="openConversation(conversation.id)"
                    >
                      Megnyitás
                    </button>
                    <button
                      class="btn btn-sm btn-danger"
                      @click="closeConversation(conversation.id)"
                    >
                      Lezárás
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-else class="text-center text-muted">
            Nincsenek beszélgetések.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useRouter } from "vue-router";
import { API_BASE_URL } from '../../config';

export default {
	setup() {
		const conversations = ref([]);
		const loading = ref(true);
		const router = useRouter();

		const fetchConversations = async () => {
			const token = localStorage.getItem('token');
			if (!token) return;

			try {
				const response = await axios.get(`${API_BASE_URL}/agent/conversations`, {
					headers: { Authorization: `Bearer ${token}` }
				});
				conversations.value = response.data;
				console.log(conversations.value);
			} catch (err) {
				console.error('Failed to fetch conversations', err);
			} finally {
				loading.value = false;
			}	
		};

		const openConversation = (conversationId) => {
			router.push(`/helpdesk/${conversationId}`);
		};

    	const closeConversation = async (conversationId) => {
      		const token = localStorage.getItem('token');
			if (!token) return;

			if (!confirm('Biztosan lezárod ezt a beszélgetést?')) return;

			try {
				await axios.post(`${API_BASE_URL}/agent/conversations/${conversationId}/close`, null, {
					headers: { Authorization: `Bearer ${token}` },
				});

				conversations.value = conversations.value.filter(c => c.id !== conversationId);
			} catch (err) {
				console.error('Failed to close conversation', err);
			}
		};

		onMounted(fetchConversations);

    return {
        conversations,
		    loading,
			fetchConversations,
			openConversation,
			closeConversation,
		};
  	},
};
</script>

<style scoped>
.table td, .table th {
  vertical-align: middle;
}
</style>