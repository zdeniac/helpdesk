<template>
    <div class="container-fluid pt-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Helpdesk</h3>
            </div>

            <!-- chat messages -->
            <div class="card-body" style="height: 400px; overflow-y: auto;" ref="chatBody">
                <div v-for="msg in messages" :key="msg.id" class="mb-2 d-flex"
                        :class="{'justify-content-end': msg.senderType === 'user','justify-content-start': msg.senderType === 'bot'}">
                <div :class="['p-2 rounded', msg.senderType === 'user' ? 'bg-primary text-white' : 'bg-light text-dark']" style="max-width: 70%;">
                    <div>{{ msg.message }}</div>
                        <small :class="[msg.senderType === 'user' ? 'text-white' : 'text-muted']">
                            {{ formatDate(msg.createdAt) }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- input -->
            <div class="card-footer">
                <form @submit.prevent="sendMessage" class="d-flex">
                    <input v-model="newMessage" type="text" class="form-control me-2" placeholder="Üzenet...">
                    <button class="btn btn-primary" type="submit">Küldés</button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import { API_BASE_URL } from '../../config';

export default {
    setup() {
        const messages = ref([]);
        const newMessage = ref('');
        const chatBody = ref(null);

    const fetchConversation = async () => {
        const token = localStorage.getItem('token');
        if (!token) return;

        try {
            const res = await axios.get(`${API_BASE_URL}/helpdesk`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            messages.value = res.data.messages;
            scrollToBottom();
        } catch (err) {
            console.error(err);
        }
    };

    const sendMessage = async () => {
        if (!newMessage.value.trim()) return;

        const token = localStorage.getItem('token');

        if (!token) return;
        
        try {
            const res = await axios.post(`${API_BASE_URL}/helpdesk`, {
            message: newMessage.value
            }, {
            headers: { Authorization: `Bearer ${token}` }
            });

            messages.value = res.data.messages;
            newMessage.value = '';
            scrollToBottom();
        } catch (err) {
            console.error(err);
        }
    };

    const scrollToBottom = () => {
        nextTick(() => {
            if (chatBody.value) {
                chatBody.value.scrollTop = chatBody.value.scrollHeight;
            }
      });
    };

    const formatDate = (d) => {
        return new Date(d).toLocaleString('hu-HU', { hour: '2-digit', minute: '2-digit' });
    };

    onMounted(fetchConversation);

    return { messages, newMessage, chatBody, formatDate, sendMessage };
  }
};
</script>
<style scoped>
.card-body::-webkit-scrollbar {
  width: 6px;
}
.card-body::-webkit-scrollbar-thumb {
  background-color: rgba(0,0,0,0.2);
  border-radius: 3px;
}
</style>