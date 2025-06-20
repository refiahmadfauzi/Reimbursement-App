<script setup>
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
</script>
<template>
  <Navbar />
  <div class="container my-5">
    <h2 class="mb-4">List Log Activity</h2>

    <div class="table-responsive">
      <table id="logsTable" class="table table-bordered table-striped table-sm">
        <thead>
          <tr>
            <th>No</th>
            <th>Name</th>
            <th>Action</th>
            <th>Description</th>
            <th>Created at</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in Logs" :key="item.id">
            <td>{{ index + 1 }}</td>
            <td>
              <b>{{ item?.user?.name }}</b>
              <p>{{ item?.user?.email }}</p>
              <p>{{ item?.user?.role }}</p>
            </td>
            <td>{{ item.action }}</td>
            <td>{{ item.description }}</td>
            <td>{{ formatTanggal(item.created_at) }}</td>
          </tr>
        </tbody>
      </table>
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
      Logs: [],
    }
  },
  mounted() {
    this.fetchLogs()
  },
  methods: {
    async fetchLogs() {
      const res = await api.get('/logs')
      this.Logs = res.data
      setTimeout(() => {
        $('#logsTable').DataTable()
      }, 100)
    },
    formatTanggal(isoDate) {
      const date = new Date(isoDate)
      return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'long',
        timeStyle: 'short',
        timeZone: 'Asia/Jakarta',
      }).format(date)
    },
  },
}
</script>
