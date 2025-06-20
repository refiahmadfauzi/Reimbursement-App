<script setup>
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
</script>
<template>
  <Navbar />
  <div class="container mt-5">
    <h2 class="mb-4">List User</h2>
    <button class="btn btn-primary mb-3" @click="openAddModal">+ Add</button>

    <div class="table-responsive">
      <table id="usersTable" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in users" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.email }}</td>
            <td>{{ item.role }}</td>
            <td>
              <div class="d-flex justify-content-center">
                <button class="btn btn-danger btn-sm me-2" @click="openEditModal(item)">
                  Edit
                </button>
                <button class="btn btn-outline-danger btn-sm" @click="deleteUser(item.id)">
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div
      class="modal fade"
      tabindex="-1"
      :class="{ show: showModal }"
      :style="{ display: showModal ? 'block' : 'none' }"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="saveUser">
            <div class="modal-header">
              <h5 class="modal-title">{{ isEdit ? 'Edit User' : 'Add User' }}</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                @click="showModal = false"
              ></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input v-model="form.name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" class="form-control" required />
                <small class="text-danger">*Input Email Active</small>
              </div>
              <div class="mb-3" v-if="!isEdit">
                <label class="form-label">Password</label>
                <input v-model="form.password" type="password" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Role</label>
                <select v-model="form.role" class="form-select" required>
                  <option disabled value="">-- Select Role --</option>
                  <option value="admin">Admin</option>
                  <option value="manager">Manager</option>
                  <option value="employee">Employee</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
                @click="showModal = false"
              >
                Cancel
              </button>
              <button type="submit" class="btn btn-primary">
                {{ isEdit ? 'Update' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <Footer />
</template>

<script>
import api from '../components/api'
import $ from 'jquery'
import 'datatables.net-bs5'
import Swal from 'sweetalert2'

export default {
  data() {
    return {
      users: [],
      isEdit: false,
      form: {
        name: '',
        email: '',
        password: '',
        role: '',
      },
      selectedId: null,
      showModal: false,
    }
  },
  mounted() {
    this.fetchUsers()
  },
  methods: {
    async fetchUsers() {
      const res = await api.get('/users')
      this.users = res.data
      setTimeout(() => {
        $('#usersTable').DataTable()
      }, 100)
    },
    openAddModal() {
      this.isEdit = false
      this.form = { name: '', email: '', password: '', role: '' }
      this.showModal = true
    },
    openEditModal(user) {
      this.isEdit = true
      this.selectedId = user.id
      this.form = {
        name: user.name,
        email: user.email,
        password: '', // not shown
        role: user.role,
      }
      this.showModal = true
    },
    async saveUser() {
      try {
        if (this.isEdit) {
          await api.put(`/users/${this.selectedId}`, this.form)
          await Swal.fire('Updated!', 'User updated successfully.', 'success')
        } else {
          await api.post('/users', this.form)
          await Swal.fire('Saved!', 'User added successfully.', 'success')
        }
        setTimeout(function () {
          window.location.reload(true)
        }, 1000)
        this.showModal = false
      } catch (err) {
        Swal.fire('Error', err.response?.data?.message || 'Something went wrong.', 'error')
      }
    },
    async deleteUser(id) {
      const confirm = await Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
      })

      if (!confirm.isConfirmed) return

      try {
        await api.delete(`/users/${id}`)
        await Swal.fire('Deleted!', 'User has been deleted.', 'success')
        $('#usersTable').DataTable().destroy()
        this.fetchUsers()
      } catch (err) {
        Swal.fire('Failed', 'Could not delete user.', 'error')
      }
    },
  },
}
</script>
