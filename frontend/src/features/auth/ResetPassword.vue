<template>
  <div class="container mt-5">
    <h3>Reset password</h3>
    <form @submit.prevent="resetPassword" class="mt-3">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input v-model="email" type="email" id="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="token" class="form-label">Token</label>
        <input v-model="token" type="text" id="token" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Új jelszó</label>
        <input v-model="password" type="password" id="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="passwordConfirm" class="form-label">Új jelszó újra</label>
        <input v-model="passwordConfirm" type="password" id="passwordConfirm" class="form-control" required>
      </div>

      <button class="btn btn-primary" type="submit" :disabled="loading">
        {{ loading ? 'Mentés...' : 'Jelszó visszaállítása' }}
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
import { useRouter } from 'vue-router';

export default {
  setup() {
    const email = ref('');
    const token = ref('');
    const password = ref('');
    const passwordConfirm = ref('');
    const loading = ref(false);
    const message = ref('');
    const error = ref('');
    const router = useRouter();

    const resetPassword = async () => {
      if (password.value !== passwordConfirm.value) {
        error.value = 'A jelszavak nem egyeznek.';
        return;
      }

      loading.value = true;
      message.value = '';
      error.value = '';

      try {
        const res = await axios.post(`${API_BASE_URL}/password/reset`, {
          email: email.value,
          token: token.value,
          password: password.value
        });
        message.value = res.data.message;
        setTimeout(() => router.push('/login'), 2000);
      } catch (err) {
        error.value = err.response?.data?.message || 'Hiba történt.';
      } finally {
        loading.value = false;
      }
    };

    return { email, token, password, passwordConfirm, loading, message, error, resetPassword };
  }
};
</script>