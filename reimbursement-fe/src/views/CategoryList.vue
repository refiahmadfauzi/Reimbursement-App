<script setup>
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
</script>
<template>
  <Navbar />
  <div class="container mt-5">
    <h2 class="mb-4">List Category</h2>
    <button class="btn btn-primary mb-3" @click="openAddModal">+ Add</button>

    <div class="table-responsive">
      <table id="categoryTable" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Name</th>
            <th>Limit in Month</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in Categorys" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>{{ item.name }}</td>
            <td>Rp {{ formatCurrency(item.limit_per_month) }}</td>
            <td>
              <div class="d-flex justify-content-center">
                <button class="btn btn-info btn-sm me-2" @click="openEditModal(item)">Edit</button>
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
          <form @submit.prevent="saveCategory">
            <div class="modal-header">
              <h5 class="modal-title">{{ isEdit ? 'Edit Category' : 'Add Category' }}</h5>
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
                <label class="form-label">Limit in Month</label>
                <input v-model="form.limit_per_month" type="number" class="form-control" required />
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
      Categorys: [],
      isEdit: false,
      form: {
        name: '',
        limit_per_month: '',
      },
      selectedId: null,
      showModal: false,
    }
  },
  mounted() {
    this.fetchCategorys()
  },
  methods: {
    async fetchCategorys() {
      const res = await api.get('/categories')
      this.Categorys = res.data
      setTimeout(() => {
        $('#categoryTable').DataTable()
      }, 100)
    },
    openAddModal() {
      this.isEdit = false
      this.form = { name: '', limit_per_month: '' }
      this.showModal = true
    },
    openEditModal(Category) {
      this.isEdit = true
      this.selectedId = Category.id
      this.form = {
        id: Category?.id,
        name: Category.name,
        limit_per_month: Category.limit_per_month,
      }
      this.showModal = true
    },
    async saveCategory() {
      try {
        if (this.isEdit) {
          await api.put(`/categories/update`, this.form)
          await Swal.fire('Updated!', 'Category updated successfully.', 'success')
        } else {
          await api.post('/categories/create', this.form)
          await Swal.fire('Saved!', 'Category added successfully.', 'success')
        }
        setTimeout(function () {
          window.location.reload(true)
        }, 1000)
        this.showModal = false
      } catch (err) {
        Swal.fire('Error', err.response?.data?.message || 'Something went wrong.', 'error')
      }
    },

    formatCurrency(value) {
      return Number(value).toLocaleString('id-ID')
    },
  },
}
</script>
