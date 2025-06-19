<template>
     <div class="user-table-wrapper">
      <div class="container">
        <div class="left">
            <h2>Thông tin bệnh nhân</h2>
            <div class="form-group">
                <label>Họ tên</label>
                <input type="text" :value="patient?.name" readonly />
            </div>
            <div class="form-row">
                <div class="form-group half">
                    <label>Giới tính</label>
                    <input type="text" :value="translateGender(patient?.gender)" readonly />
                </div>
                <div class="form-group half">
                    <label>Nhóm máu</label>
                    <input type="text" :value="patient?.blood_type" readonly />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group half">
                    <label>Ngày sinh</label>
                    <input type="text" :value="formatDate(patient?.date_of_birth)" readonly />
                </div>
                <div class="form-group half">
                    <label>Số điện thoại</label>
                    <input type="text" :value="patient?.phone" readonly />
                </div>
            </div>
            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" :value="patient?.address" readonly></input>
            </div>
            <div class="form-row">
                <div class="form-group half">
                    <label>Người liên hệ khẩn</label>
                    <input type="text" :value="patient?.emergency_contact_name" readonly />
                </div>
                <div class="form-group half">
                    <label>SĐT người nhà</label>
                    <input type="text" :value="patient?.emergency_contact_phone" readonly />
                </div>
            </div>
        </div>
        <div class="right">
            <div class="record-header">
                <h2>Hồ sơ bệnh án</h2>
                <button @click="showModal = true" class="create-button">+ Tạo hồ sơ bệnh án</button>
            </div>
        <div v-if="medicalRecords.length === 0">Chưa có hồ sơ.</div>
            <table v-else class="medical-table">
                <thead >
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
                        <a v-if="record.file_attachment" :href="`/uploads/${record.file_attachment}`" target="_blank">
                            Tải file
                        </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
     </div>
  </div>
  <div class="modal" v-if="showModal">
  <div class="modal-content">
    <h3>Thêm hồ sơ bệnh án</h3>
    <form @submit.prevent="createMedicalRecord">
        <div class="form-group ">
            <label>Bác sĩ</label>
            <select v-model="newRecord.doctor_id" required>
            <option disabled value="">-- Chọn bác sĩ --</option>
            <option v-for="doc in doctors" :key="doc.id" :value="doc.id">{{ doc.name }}</option>
            </select>
        </div>
      <div class="form-group">
        <label>Chẩn đoán</label>
        <textarea v-model="newRecord.diagnosis" required></textarea>
      </div>

      <div class="form-group">
        <label>Điều trị</label>
        <textarea v-model="newRecord.treatment" required></textarea>
      </div>

      <div class="form-group">
        <label>File đính kèm</label>
        <input type="file" @change="handleFileUpload" />
      </div>

      <div class="modal-actions">
        <button type="submit">Lưu</button>
        <button type="button" @click="showModal = false">Hủy</button>
      </div>
    </form>
  </div>
</div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import API from '@/utils/axios'
import { useToast } from 'vue-toastification'
import '@/pages/patients/PatientDetail.css'

const toast = useToast()

const route = useRoute()
const id = route.params.id

const patient = ref(null)
const medicalRecords = ref([])

const formatDate = (dateStr) => {
  return dateStr ? new Date(dateStr).toLocaleDateString('vi-VN') : ''
}

const translateGender = (gender) => {
  switch (gender) {
    case 'male': return 'Nam'
    case 'female': return 'Nữ'
    case 'other': return 'Khác'
    default: return ''
  }
}
const showModal = ref(false)
const newRecord = ref({
  doctor_id: '',
  appointment_id: '',
  diagnosis: '',
  treatment: '',
  file: null
})

const doctors = ref([])
const appointments = ref([])

const handleFileUpload = (e) => {
  newRecord.value.file = e.target.files[0]
}

const createMedicalRecord = async () => {
  const formData = new FormData()
  formData.append('patient_id', patient.value.id)
  formData.append('doctor_id', newRecord.value.doctor_id)
  formData.append('appointment_id', newRecord.value.appointment_id)
  formData.append('diagnosis', newRecord.value.diagnosis)
  formData.append('treatment', newRecord.value.treatment)
  if (newRecord.value.file) {
    formData.append('file_attachment', newRecord.value.file)
  }

  try {
    const res = await API.post('/medical-records', formData)
    medicalRecords.value.push(res.data.record)
    showModal.value = false
    newRecord.value = {
      doctor_id: '',
      diagnosis: '',
      treatment: '',
      file: null
    }
  } catch (err) {
    console.error('Lỗi tạo hồ sơ:', err)
  }
}
onMounted(async () => {
  try {
    const res = await API.get(`/patients/${id}/details`)
    patient.value = res.data.patient
    medicalRecords.value = res.data.records

    // Lấy danh sách bác sĩ và lịch hẹn
    const [docRes, apptRes] = await Promise.all([
      API.get('/doctors'),
    ])
    doctors.value = docRes.data
    appointments.value = apptRes.data
  } catch (err) {
    console.error('Lỗi khi tải dữ liệu:', err)
  }
})

</script>


