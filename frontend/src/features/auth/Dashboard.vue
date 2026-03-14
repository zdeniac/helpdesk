<template>
    <div>
        <h1>Dashboard</h1>
        <div v-if="loading">Betöltés...</div>
        <div v-else>
            <div>
                <button @click="showForm = !showForm">
                    {{ showForm ? 'Mégse' : 'Új esemény' }}
                </button>
                <div v-if="showForm" class="event-form">
                    <form @submit.prevent="submitEvent">
                        <div>
                            <label for="title">Cím:</label>
                            <input type="text" id="title" v-model="newEvent.title" required>
                        </div>

                        <div>
                            <label for="occurrence">Dátum:</label>
                            <input type="datetime-local" id="occurrence" v-model="newEvent.occurrence">
                        </div>

                        <div>
                            <label for="description">Leírás:</label>
                            <textarea id="description" v-model="newEvent.description"></textarea>
                        </div>

                        <button type="submit">Mentés</button>
                    </form>
                </div>
            </div>
            <div v-if="events.data?.length">
                <table border="1" cellpadding="5">
                    <thead>
                        <tr>
                            <th>Cím</th>
                            <th>Dátum</th>
                            <th>Leírás</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="event in events.data" :key="event.id">
                            <td>{{ event.title }}</td>
                            <td>{{ new Date(event.occurrence).toLocaleString() }}</td>
                            <td>{{ event.description }}</td>
                            <td>
                                <button @click="editEvent(event)">Szerkesztés</button>
                                <button @click="deleteEvent(event.id)">Törlés</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else>Nincsenek eseményeid.</div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { API_BASE_URL } from "../../config";

export default {
    setup() {
        const events = ref([]);
        const loading = ref(true);
        const showForm = ref(false);
        const editingEvent = ref(null);
        const newEvent = ref({
            title: '',
            occurrence: '',
            description: ''
        });

        const fetchEvents = async () => {
            const token = localStorage.getItem('token');
            if (!token) return;
            try {
                const response = await axios.get(`${API_BASE_URL}/api/events`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                events.value = response.data;
            } catch (error) {
                console.error('Failed to fetch events', error);
            } finally {
                loading.value = false;
            }
        };

        const openForm = () => {
            showForm.value = true;
            editingEvent.value = null;
            newEvent.value = { title: '', occurrence: '', description: '' };
        };

        const cancelForm = () => {
            showForm.value = false;
            editingEvent.value = null;
            newEvent.value = { title: '', occurrence: '', description: '' };
        };

        const submitEvent = async () => {
            const token = localStorage.getItem('token');
            if (!token) return;

            try {
                if (editingEvent.value) {
                    // Update
                    const response = await axios.put(`${API_BASE_URL}/api/events/${editingEvent.value.id}`, newEvent.value, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    // updating the dable
                    const index = events.value.data.findIndex(e => e.id === editingEvent.value.id);
                    if (index !== -1) events.value.data[index] = response.data.data;
                } else {
                    // Create
                    console.log(newEvent.value);
                    const response = await axios.post(`${API_BASE_URL}/api/events`, newEvent.value, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    events.value.data.push(response.data.data);
                }
                cancelForm();
            } catch (error) {
                console.error('Failed to save event', error);
            }
        };

        const editEvent = (event) => {
            showForm.value = true;
            editingEvent.value = event;
            newEvent.value = {
                title: event.title,
                occurrence: event.occurrence,
                description: event.description
            };
        };

        const deleteEvent = async (id) => {
            const token = localStorage.getItem('token');
            if (!token) return;

            if (!confirm('Biztosan törlöd ezt az eseményt?')) return;

            try {
                await axios.delete(`${API_BASE_URL}/api/events/${id}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                events.value.data = events.value.data.filter(e => e.id !== id);
            } catch (error) {
                console.error('Failed to delete event', error);
            }
        }

        onMounted(fetchEvents);

        return { 
            events, 
            loading, 
            showForm, 
            newEvent, 
            deleteEvent, 
            submitEvent, 
            editEvent, 
            openForm, 
            cancelForm 
        };
    }
};
</script>