<template>
  <div class="container mt-5">
    <h3>Forgotten password</h3>
    <form @submit.prevent="sendResetLink" class="mt-3">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input v-model="email" type="email" id="email" class="form-control" required>
      </div>
      <button class="btn btn-primary" type="submit" :disabled="loading">
        {{ loading ? 'Küldés...' : 'Jelszó visszaállító link küldése' }}
      </button>
    </form>

    <div v-if="message" class="alert alert-success mt-3">{{ message }}</div>
    <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref } from 'vue';
import { API_BASE_URL } from '../../config';

export default {
  setup() {
    const email = ref('');
    const loading = ref(false);
    const message = ref('');
    const error = ref('');

    const sendResetLink = async () => {
      loading.value = true;
      message.value = '';
      error.value = '';

      try {
        const res = await axios.post(`${API_BASE_URL}/password/email`, { email: email.value });
        message.value = res.data.message;
      } catch (err) {
        error.value = err.response?.data?.message || 'Hiba történt.';
      } finally {
        loading.value = false;
      }
    };

    return { email, loading, message, error, sendResetLink };
  }
};
</script>