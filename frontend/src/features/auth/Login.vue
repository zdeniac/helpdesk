<template>
    <div class="hold-transition login-page">
        <div class="login-box">

            <!-- Logo -->
            <div class="login-logo">
                <b>Admin</b> Login
            </div>

            <!-- Card -->
            <div class="card">
                <div class="card-body login-card-body">

                    <p class="login-box-msg">Sign in to start your session</p>

                    <form @submit.prevent="login">

                        <!-- Email -->
                        <div class="input-group mb-3">
                            <input v-model="email" type="email" class="form-control" placeholder="Email">
                            <div class="input-group-append input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="input-group mb-3">
                            <input v-model="password" type="password" class="form-control" placeholder="Password">
                            <div class="input-group-append input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Login
                                </button>
                            </div>
                        </div>

                    </form>

                    <!-- Error message -->
                    <p v-if="error" class="text-danger mt-2">{{ error }}</p>

                </div>
            </div>

        </div>
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
                // ELLENŐRIZNI A ROLE-ra
                router.push("/events");

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
