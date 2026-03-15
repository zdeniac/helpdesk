<template>
    <div class="container-fluid pt-3">

        <!-- Card wrapper -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Eseményeim</h3>
                <button class="btn btn-primary btn-lg ms-auto" @click="showForm = true">
                    Új esemény
                </button>
            </div>

            <div class="card-body">

                <div v-if="loading" class="text-center">
                    Betöltés...
                </div>

                <div v-else>
                    <div v-if="events.data?.length">
                        <table class="table table-bordered table-striped">
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
                                        <button class="btn btn-sm btn-info me-1" @click="editEvent(event)">Szerkesztés</button>
                                        <button class="btn btn-sm btn-danger" @click="deleteEvent(event.id)">Törlés</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else>
                        <p>Nincsenek eseményeid.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal form -->
        <div class="modal fade" :class="{ show: showForm }" style="display: block;" tabindex="-1" role="dialog" v-if="showForm">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title">{{ editingEvent ? 'Esemény szerkesztése' : 'Új esemény' }}</h5>
                        <button type="button" class="btn-close" @click="cancelForm" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="error" class="alert alert-danger">
                            {{ error }}
                        </div>
                        <form @submit.prevent="submitEvent">

                            <div class="mb-3">
                                <label for="title" class="form-label">Cím:</label>
                                <input type="text" id="title" v-model="newEvent.title" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="occurrence" class="form-label">Dátum:</label>
                                <input type="datetime-local" id="occurrence" v-model="newEvent.occurrence" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Leírás:</label>
                                <textarea id="description" v-model="newEvent.description" class="form-control"></textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Mentés</button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- Backdrop -->
        <div v-if="showForm" class="modal-backdrop fade show"></div>
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
        const error = ref(null);
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
            error.value = null;
            showForm.value = true;
            editingEvent.value = null;
            newEvent.value = { title: '', occurrence: '', description: '' };
        };

        const cancelForm = () => {
            error.value = null;
            showForm.value = false;
            editingEvent.value = null;
            newEvent.value = { title: '', occurrence: '', description: '' };
        };

        const submitEvent = async () => {
            const token = localStorage.getItem('token');
            if (!token) return;

            try {
                error.value = null;

                if (editingEvent.value) {
                    // Update
                    const response = await axios.put(`${API_BASE_URL}/api/events/${editingEvent.value.id}`, newEvent.value, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    // updating the table
                    const index = events.value.data.findIndex(e => e.id === editingEvent.value.id);
                    if (index !== -1) events.value.data[index] = response.data.data;
                } else {
                    // Create
                    const response = await axios.post(`${API_BASE_URL}/api/events`, newEvent.value, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                    events.value.data.push(response.data.data);
                }
                cancelForm();
            } catch (err) {
                error.value = err.response?.data?.message
                || (err.response?.data?.errors 
                    ? Object.values(err.response.data.errors).flat().join(' • ')
                    : err.message);
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
                console.error('Failed to delete event');
            }
        }

        onMounted(fetchEvents);

        return { 
            events, 
            loading, 
            showForm, 
            newEvent,
            editingEvent,
            error,
            deleteEvent,
            submitEvent, 
            editEvent,
            openForm,
            cancelForm 
        };
    }
};
</script>