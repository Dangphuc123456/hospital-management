<template>
  <div class="medical-records">
    <h2>Danh sách hồ sơ bệnh án</h2>

    <table class="record-table">
      <thead>
        <tr>
          <th>Ngày</th>
          <th>Chẩn đoán</th>
          <th>Điều trị</th>
          <th>File</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="record in medicalRecords" :key="record.id">
          <td>{{ formatDate(record.created_at) }}</td>
          <td>{{ record.diagnosis }}</td>
          <td>{{ record.treatment }}</td>
          <td>
            <a v-if="record.file_attachment" :href="fileUrl(record.file_attachment)" target="_blank">
              {{ record.file_attachment }}
            </a>
            <span v-else>Không có</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import API from '@/utils/axios'

const medicalRecords = ref([])

const fetchMedicalRecords = async () => {
  try {
    const response = await API.get('/medical-records')
    medicalRecords.value = response.data
  } catch (error) {
    console.error('Lỗi khi tải hồ sơ:', error)
  }
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' }
  return new Date(dateString).toLocaleDateString('vi-VN', options)
}

const fileUrl = (fileName) => {
  return `http://localhost:8000/storage/uploads/${fileName}`
}

onMounted(() => {
  fetchMedicalRecords()
})
</script>


<style scoped>
.medical-records {
  padding: 20px;
}

h2 {
  margin-bottom: 20px;
}

.record-table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
}

.record-table th {
  background-color: #000080;
  color: white;
  padding: 10px;
  text-align: left;
}

.record-table td {
  border: 1px solid #ccc;
  padding: 10px;
}
</style>
