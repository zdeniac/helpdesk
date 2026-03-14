<template>
  <div>
    <h1>Login</h1>

    <form @submit.prevent="login">
      <div>
        <input v-model="email" type="email" placeholder="Email">
      </div>

      <div>
        <input v-model="password" type="password" placeholder="Password">
      </div>

      <button type="submit">Login</button>
    </form>

    <p v-if="error">{{ error }}</p>
  </div>
</template>

<script>
import axios from "axios";
import { API_BASE_URL } from "../../config";
import { useRouter } from "vue-router";
import { ref } from "vue";

export default {
    setup() {
        const email = ref("");
        const password = ref("");
        const error = ref(null);
        const router = useRouter();

        const login = async () => {
            try {
                const response = await axios.post(`${API_BASE_URL}/api/login`, {
                    email: email.value,
                    password: password.value,
                });

                localStorage.setItem("token", response.data.access_token);

                router.push("/dashboard");

            } catch (e) {
                let message = "Login failed";
                if (e.response && e.response.data && e.response.data.message) {
                    message = e.response.data.message;
                } else if (e.message) {
                    message = e.message;
                }
                localStorage.removeItem("token");
                error.value = message;
            }
        };

        return { email, password, error, login };
    }
};
</script>