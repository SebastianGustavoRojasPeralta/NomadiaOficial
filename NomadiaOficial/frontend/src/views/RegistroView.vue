<template>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title mb-3">Create your account</h3>
            <p class="text-muted">Register to book or list experiences with local guides.</p>

            <form @submit.prevent="onSubmit">
              <div class="mb-3">
                <label class="form-label">Account Type</label>
                <select v-model="form.accountType" class="form-select" required>
                  <option value="traveler">Traveler</option>
                  <option value="guide">Local Guide</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input v-model="form.fullName" type="text" class="form-control" placeholder="Your full name" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input v-model="form.email" type="email" class="form-control" placeholder="you@example.com" required />
              </div>

              <div class="mb-3">
                <label class="form-label">Password</label>
                <input v-model="form.password" type="password" class="form-control" placeholder="Choose a secure password" required />
              </div>

              <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit">Create Account</button>
              </div>

              <div class="text-center mt-3 text-muted">or</div>

              <div class="d-flex gap-2 justify-content-center mt-3">
                <button type="button" class="btn btn-outline-danger">Continue with Google</button>
                <button type="button" class="btn btn-outline-primary">Continue with Facebook</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api/axiosConfig';

const router = useRouter();

const form = ref({
  accountType: 'traveler',
  fullName: '',
  email: '',
  password: '',
});

async function onSubmit() {
  try {
    // simple register call; backend endpoint expects { name, email, password, role }
    const payload = {
      name: form.value.fullName,
      email: form.value.email,
      password: form.value.password,
      role: form.value.accountType === 'guide' ? 'guide' : 'traveler',
    };
    const res = await api.post('/register', payload);
    // assume successful -> navigate to home or login
    router.push({ name: 'home' });
  } catch (err) {
    console.error('Register error', err);
    // TODO: show user-friendly error
  }
}
</script>

<style scoped>
.card-title {
  font-weight: 600;
}
</style>
