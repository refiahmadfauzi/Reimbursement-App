<template>
  <div class="">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <router-link class="navbar-brand" to="/">Reimbursement App</router-link>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto" v-if="token">
            <li class="nav-item">
              <router-link class="nav-link" to="/dashboard">Dashboard</router-link>
            </li>
            <li class="nav-item" v-if="role != 'employee'">
              <router-link class="nav-link" to="/user">User</router-link>
            </li>
            <li class="nav-item" v-if="role == 'admin'">
              <router-link class="nav-link" to="/category">Category</router-link>
            </li>
            <li class="nav-item" v-if="role == 'admin'">
              <router-link class="nav-link" to="/logs">Logs</router-link>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link" @click.prevent="logout">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
export default {
  data() {
    return {
      token: null,
      role: null,
      name: null,
      email: null,
    }
  },
  mounted() {
    this.token = localStorage.getItem('token')
    this.role = localStorage.getItem('role')
    this.email = localStorage.getItem('email')
    this.name = localStorage.getItem('name')
  },
  methods: {
    logout() {
      localStorage.clear()
      setTimeout(function () {
        window.location.href = '/'
      }, 1000) // 5000 milliseconds = 5 seconds
    },
  },
}
</script>
