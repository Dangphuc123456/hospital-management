<template>
  <div class="medical-records">
    <h2>Danh sách hồ sơ bệnh án</h2>
    <table v-if="!loading">
      <thead>
        <tr>
          <th>Bệnh nhân</th>
          <th>Bác sĩ</th>
          <th>Chẩn đoán</th>
          <th>Điều trị</th>
          <th>Ngày lập</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="record in medicalRecords" :key="record.id">
          <td>{{ record.patient?.name || 'Không rõ' }}</td>
          <td>{{ record.doctor?.user?.name || 'Không rõ' }}</td>
          <td>{{ record.diagnosis }}</td>
          <td>{{ record.treatment }}</td>
          <td>{{ formatDate(record.created_at) }}</td>
          <td>
            <button class="btn-detail" @click="openDetailModal(record)"><i class="fa fa-eye"></i></button>
            <button class="btn-delete" @click="deleteMedicalRecord(record.id)"><i class="fa fa-trash"></i></button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-else>Đang tải...</div>

    <!-- Modal Chi tiết -->
    <div class="modal" v-if="showDetailModal">
      <div class="modal-content">
        <h3>Chi tiết hồ sơ bệnh án</h3>
        <div class="modal-grid">
          <div class="modal-left">
            <div class="form-row">
              <div class="form-group">
                <label>Bệnh nhân:</label>
                <input type="text" :value="viewRecord.patient?.name || 'Không rõ'" readonly />
              </div>

              <div class="form-group">
                <label>Bác sĩ:</label>
                <input type="text" :value="viewRecord.doctor?.user?.name || 'Không rõ'" readonly />
              </div>
            </div>
            <div class="form-group">
              <label>Chẩn đoán:</label>
              <textarea rows="3" readonly>{{ viewRecord.diagnosis }}</textarea>
            </div>

            <div class="form-group">
              <label>Điều trị:</label>
              <textarea rows="3" readonly>{{ viewRecord.treatment }}</textarea>
            </div>

            <div class="form-group">
              <label>Ngày lập:</label>
              <input type="text" :value="formatDate(viewRecord.created_at)" readonly />
            </div>
          </div>
          <div class="modal-right">
            <label>Đơn thuốc:</label>
            <table class="medication-table" v-if="viewRecord.prescription?.medications?.length">
              <thead>
                <tr>
                  <th style="background-color: #000080;">Tên thuốc</th>
                  <th style="background-color: #000080;">Liều dùng</th>
                  <th style="background-color: #000080;">Thời gian</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(med, idx) in viewRecord.prescription.medications" :key="idx">
                  <td>{{ med.medication?.name || 'Không rõ' }}</td>
                  <td>{{ med.dosage }}</td>
                  <td>{{ med.duration }}</td>
                </tr>
              </tbody>
            </table>
            <div v-else>Không có thuốc kê đơn</div>
          </div>
        </div>

        <div class="modal-actions">
          <button class="btn-cancel" @click="showDetailModal = false">Đóng</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import API from '@/utils/axios'

const loading = ref(true)
const medicalRecords = ref([])
const showDetailModal = ref(false)
const viewRecord = ref({})

const fetchMedicalRecords = async () => {
  loading.value = true
  try {
    const response = await API.get('/medical-records')
    medicalRecords.value = response.data.records
  } catch (error) {
    console.error('Lỗi khi tải hồ sơ:', error)
  } finally {
    loading.value = false
  }
}

const openDetailModal = (record) => {
  viewRecord.value = record
  showDetailModal.value = true
}

const deleteMedicalRecord = async (id) => {
  if (!confirm('Bạn có chắc chắn muốn xoá hồ sơ này?')) return
  try {
    await API.delete(`/medical-records/${id}`)
    medicalRecords.value = medicalRecords.value.filter(r => r.id !== id)
    alert('Xoá thành công!')
  } catch (err) {
    console.error('Lỗi xoá hồ sơ:', err)
    alert('Xoá thất bại!')
  }
}

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' }
  return new Date(dateString).toLocaleDateString('vi-VN', options)
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

table {
  width: 100%;
  border-collapse: collapse;
}

thead th {
  background-color: #000080;
  color: white;
  padding: 10px;
}

tbody td {
  border: 1px solid #ccc;
  padding: 10px;
}

button {
  margin-right: 5px;
  padding: 5px 10px;
  cursor: pointer;
}

.modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 65%;
  padding: 20px;
  z-index: 999;
}

.modal-content {
  margin: 0 auto;            
  padding: 24px;
  border-radius: 8px;
  max-width: 900px;
  width: 100%;
}

.modal-grid {
  display: flex;
  gap: 20px;
  margin-top: 15px;
}

.modal-left,
.modal-right {
  flex: 1;
}

.form-group {
  margin-bottom: 5px;
}

.form-group label {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #f9f9f9;
  font-size: 14px;
}

.medication-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.medication-table th,
.medication-table td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
  font-size: 14px;
}

.medication-table th {
  background-color: #f0f0f0;
}

.modal-actions {
  text-align: right;
  margin-top: 20px;
}
.form-row {
  display: flex;
  gap: 20px;
}

.form-row .form-group {
  flex: 1;
}
.btn-detail {
  background-color: #000080;
  color: white;
  border: none;
  padding: 8px 10px;
  margin-right: 5px;
  border-radius: 4px;
  cursor: pointer;
}

 .btn-delete {
  background-color: #DC3545;
  color: white;
  border: none;
  padding: 8px 10px;
  border-radius: 4px;
  cursor: pointer;
}
.btn-cancel{
  background-color: #666465;
  color: white;
  border: none;
  padding: 8px 10px;
  border-radius: 4px;
  cursor: pointer;
}
</style>
