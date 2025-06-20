<script setup>
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
</script>
<template>
  <Navbar />
  <div class="container mt-5">
    <h2 class="mb-4">List Reimbursement</h2>
    <button class="btn btn-primary mb-3" @click="showModal = true" v-if="role == 'employee'">
      + Add
    </button>

    <div class="table-responsive">
      <table id="reimbursementsTable" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th v-if="role == 'manager'">User</th>
            <th>Title</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <th>File</th>
            <th v-if="role == 'manager'">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in reimbursements" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td v-if="role == 'manager'">
              <b>{{ item?.user?.name }}</b>
              <p>{{ item?.user?.email }}</p>
            </td>
            <td>{{ item.title }}</td>
            <td :title="item.description">
              {{ truncate(item.description) }}
            </td>

            <td>Rp {{ formatCurrency(item.amount) }}</td>
            <td>
              <span :class="statusBadgeClass(item.status)">
                {{ item.status }}
              </span>
            </td>
            <td>{{ item.submitted_at }}</td>
            <td>
              <a
                :href="`http://127.0.0.1:8000/bukti/${item.file_path}`"
                target="_blank"
                rel="noopener noreferrer"
              >
                Lihat Bukti
              </a>
            </td>
            <td v-if="role === 'manager'">
              <div class="d-flex justify-content-center">
                <button
                  class="btn btn-success btn-sm me-2"
                  @click="updateStatus(item.id, 'approve')"
                >
                  Approve
                </button>
                <button class="btn btn-danger btn-sm me-2" @click="updateStatus(item.id, 'reject')">
                  Reject
                </button>
                <button class="btn btn-outline-danger btn-sm" @click="deleteReimbursement(item.id)">
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Tambah -->
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
          <form @submit.prevent="submitReimbursement">
            <div class="modal-header">
              <h5 class="modal-title">Add</h5>
              <button type="button" class="btn-close" @click="showModal = false"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Category</label>
                <select v-model="form.category_id" class="form-select" required>
                  <option value="" disabled>Select Category</option>
                  <option v-for="kategori in categories" :key="kategori.id" :value="kategori.id">
                    {{ kategori.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Title</label>
                <input v-model="form.title" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea
                  class="form-control"
                  v-model="form.description"
                  cols="4"
                  rows="4"
                ></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Amount</label>
                <input v-model="form.amount" type="number" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Date</label>
                <input v-model="form.date" type="date" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Upload File</label>
                <input
                  type="file"
                  class="form-control"
                  @change="handleFileUpload"
                  required
                  accept="image/jpeg,image/jpg,image/png,application/pdf"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showModal = false">
                Close
              </button>
              <button type="submit" class="btn btn-primary">Send</button>
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
      reimbursements: [],
      showModal: false,
      form: {
        title: '',
        description: '',
        amount: '',
        category_id: '',
        date: '',
        user_id: '1', // atau ambil dari token decode
      },
      file: null,
      role: null,
      categories: [],
    }
  },
  mounted() {
    this.fetchDataCategory()
    this.fetchData()
  },
  methods: {
    async fetchData() {
      try {
        const response = await api.get('/reimbursements')
        this.reimbursements = response.data
        this.role = localStorage.getItem('role')
        this.$nextTick(() => {
          $('#reimbursementsTable').DataTable()
        })
      } catch (err) {
        console.error(err)
      }
    },
    async fetchDataCategory() {
      try {
        const response = await api.get('/categories')
        this.categories = response.data
      } catch (err) {
        console.error(err)
      }
    },
    handleFileUpload(event) {
      this.file = event.target.files[0]
    },
    formatCurrency(value) {
      return Number(value).toLocaleString('id-ID')
    },
    async submitReimbursement() {
      let user_id = localStorage.getItem('user_id')
      try {
        const formData = new FormData()
        formData.append('title', this.form.title || 'Rembes')
        formData.append('description', this.form.description || '')
        formData.append('amount', this.form.amount)
        formData.append('category_id', this.form.category_id)
        formData.append('date', this.form.date)
        formData.append('user_id', user_id)
        formData.append('file', this.file)

        await api.post('/reimbursements', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        })

        this.showModal = false
        this.form = {
          title: '',
          description: '',
          amount: '',
          category_id: '',
          date: '',
        }
        this.file = null
        setTimeout(function () {
          window.location.reload(true)
        }, 1000)
        await Swal.fire('Success!', 'Addition of the application was successful', 'success')
      } catch (err) {
        Swal.fire('Failed', err.response.data.message, 'error')
        console.error(err)
      }
    },
    statusBadgeClass(status) {
      switch (status) {
        case 'pending':
          return 'badge bg-info'
        case 'approved':
          return 'badge bg-success'
        case 'rejected':
          return 'badge bg-danger'
        default:
          return 'badge bg-secondary'
      }
    },
    async updateStatus(id, status) {
      const confirm = await Swal.fire({
        title: `Are you sure you want to ${status}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
      })

      if (!confirm.isConfirmed) return

      try {
        await api.post(`/reimbursements/${id}/${status}`)
        await Swal.fire('Success!', `Status has been updated to "${status}".`, 'success')
        $('#reimbursementsTable').DataTable().destroy()
        this.fetchData()
      } catch (error) {
        Swal.fire('Failed', 'An error occurred while updating status.', 'error')
        console.error(error)
      }
    },

    async deleteReimbursement(id) {
      const confirm = await Swal.fire({
        title: 'Are you sure you want to delete this?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
      })

      if (!confirm.isConfirmed) return

      try {
        await api.delete(`/reimbursements/${id}`)
        await Swal.fire('Deleted!', 'The reimbursement has been deleted.', 'success')
        $('#reimbursementsTable').DataTable().destroy()
        this.fetchData()
      } catch (error) {
        Swal.fire('Failed', 'Unable to delete the reimbursement.', 'error')
        console.error(error)
      }
    },

    truncate(text, length = 50) {
      return text.length > length ? text.substring(0, length) + '...' : text
    },
  },
}
</script>
