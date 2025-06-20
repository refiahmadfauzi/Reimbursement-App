<template>
  <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card p-4 shadow-lg w-100" style="max-width: 400px">
      <h3 class="text-center mb-4">Login</h3>
      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input v-model="form.email" type="email" class="form-control" id="email" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="form-control"
            id="password"
            required
          />
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          Login
        </button>

        <div v-if="error" class="alert alert-danger mt-3" role="alert">
          {{ error }}
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import api from '../components/Api'

export default {
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      loading: false,
      error: '',
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true
      this.error = ''
      try {
        const response = await api.post('/login', {
          email: this.form.email,
          password: this.form.password,
        })

        const token = response.data.token
        const user = response.data.user

        localStorage.setItem('token', token)
        localStorage.setItem('user_id', user.id)
        localStorage.setItem('name', user.name)
        localStorage.setItem('email', user.email)
        localStorage.setItem('role', user.role)
        api.defaults.headers['Authorization'] = `Bearer ${token}`

        this.$router.push('/dashboard')
      } catch (err) {
        this.error = err.response?.data?.message || 'Login failed. Please check your credentials.'
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<style scoped>
body {
  background-color: #f8f9fa;
}
</style>
