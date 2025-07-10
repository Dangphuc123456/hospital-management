<template>
  <div class="user-table-wrapper">
    <router-link to="/medical-records" class="back-button">
      <i class="fa fa-arrow-left"></i>
    </router-link>
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
          <input type="text" :value="patient?.address" readonly />
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

      <!-- Bảng hồ sơ bệnh án -->
      <div class="right">
        <div class="record-header">
          <h2>Hồ sơ bệnh án</h2>
          <button @click="openCreateModal" class="create-button">+ Tạo hồ sơ bệnh án</button>
        </div>

        <div v-if="medicalRecords.length === 0" class="no-records">
          Chưa có hồ sơ.
        </div>
        <table v-else class="medical-table">
          <thead>
            <tr>
              <th>Ngày</th>
              <th>Chẩn đoán</th>
              <th>Điều trị</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in medicalRecords" :key="record.id">
              <td>{{ formatDate(record.created_at) }}</td>
              <td>{{ record.diagnosis }}</td>
              <td>{{ record.treatment }}</td>
              <td class="activity">
                <div class="button-wrapper">
                  <button class="btn-detail" @click="openDetailModal(record)"><i class="fa fa-eye"></i></button>
                  <button class="btn-edit"  @click="openEditModal(record)"><i class="fa fa-edit"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  <div class="modal" v-if="showCreateModal">
  <div class="modal-content">
    <h3>Thêm hồ sơ bệnh án</h3>
    <form @submit.prevent="createMedicalRecord">
      <div class="form-two-column">
        <div class="column-left">
          <div class="form-group">
            <label>Bác sĩ</label>
            <select v-model="newRecord.doctor_id" required>
              <option disabled value="">-- Chọn bác sĩ --</option>
              <option v-for="doc in doctors" :key="doc.id" :value="doc.id">
                {{ doc.user?.name || 'Không rõ' }}
              </option>
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
            <label>Ghi chú</label>
            <input v-model="newRecord.notes" required></input>
          </div>
        </div>

        <div class="column-right">
          <div class="form-group">
            <label>Thuốc kê đơn</label>
            <div
              v-for="(med, idx) in newRecord.medications"
              :key="idx"
              class="medication-item"
            >
              <input
                type="text"
                v-model="med.search"
                @input="onSearchMedication(idx)"
                placeholder="Gõ tên thuốc..."
                autocomplete="off"
              />
              <ul v-if="med.suggestions?.length > 0" class="suggestions">
                <li
                  v-for="s in med.suggestions"
                  :key="s.id"
                  @click="selectMedication(idx, s)">
                  {{ s.name }}
                </li>
              </ul>
              <div v-if="med.medication_id" class="selected-med">
                <strong>{{ med.selectedName }}</strong>
              </div>
              <input v-model="med.dosage" placeholder="Liều dùng" required />
              <input v-model="med.duration" placeholder="Thời gian dùng" required />
              <button type="button" @click="removeMedication(idx)">X</button>
            </div>
            <button type="button" @click="addMedication">+ Thêm thuốc</button>
          </div>

          <div class="modal-actions">
            <button type="submit">Lưu</button>
            <button type="button" @click="showCreateModal = false">Hủy</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

    <div class="modal" v-if="showEditModal">
      <div class="modal-content">
        <h3>Sửa hồ sơ bệnh án</h3>
        <form @submit.prevent="updateMedicalRecord">
          <div class="form-group">
            <label>Chẩn đoán</label>
            <textarea v-model="editRecord.diagnosis" required></textarea>
          </div>
          <div class="form-group">
            <label>Điều trị</label>
            <textarea v-model="editRecord.treatment" required></textarea>
          </div>
          <div class="modal-actions">
            <button type="submit">Cập nhật</button>
            <button type="button" @click="showEditModal = false">Hủy</button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal" v-if="showDetailModal">
      <div class="modal-content">
        <h3>Chi tiết hồ sơ bệnh án</h3>
        <div class="form-two-column">
          <div class="column-left">
            <div class="field">
              <label>Ngày tạo:</label>
              <input type="text" :value="formatDate(viewRecord.created_at)" readonly />
            </div>
            <div class="field">
              <label>Bác sĩ:</label>
              <input type="text" :value="getDoctorName(viewRecord.doctor_id)" readonly />
            </div>
            <div class="field">
              <label>Chẩn đoán:</label>
              <textarea readonly>{{ viewRecord.diagnosis }}</textarea>
            </div>
            <div class="field">
              <label>Điều trị:</label>
              <textarea readonly>{{ viewRecord.treatment }}</textarea>
            </div>
          </div>
          <div class="column-right">
            <div class="field">
              <label>Thuốc kê đơn:</label>
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
            <div class="modal-actions">
              <button class="btn-cancel" @click="showDetailModal = false">Đóng</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute }      from 'vue-router'
import API               from '@/utils/axios'
import { useToast }      from 'vue-toastification'
import debounce          from 'lodash/debounce'
import '@/pages/patients/PatientDetail.css'

const toast           = useToast()
const route           = useRoute()
const patientId       = route.params.id

const patient         = ref(null)
const medicalRecords  = ref([])
const doctors         = ref([])
const showCreateModal = ref(false)
const showEditModal   = ref(false)
const showDetailModal = ref(false)

const newRecord = ref({
  doctor_id: '',
  diagnosis: '',
  treatment: '',
  file: null,
  medications: [
    { search: '', suggestions: [], medication_id: null, selectedName: '', dosage: '', duration: '' }
  ]
})

const editRecord      = ref({ id: '', doctor_id: '', diagnosis: '', treatment: '' })
const viewRecord      = ref({})

const formatDate = d => d ? new Date(d).toLocaleDateString('vi-VN') : ''
const translateGender = g => ({ male:'Nam', female:'Nữ', other:'Khác' })[g] || ''

onMounted(async () => {
  try {
    const [{ data: pRes }, { data: dRes }] = await Promise.all([
      API.get(`/patients/${patientId}/details`),
      API.get('/doctors')
    ])
    patient.value        = pRes.patient
    medicalRecords.value = pRes.records
    doctors.value        = dRes.doctors

  } catch (err) {
    console.error(err)
    toast.error('Không thể tải dữ liệu.')
  }
})

const openCreateModal = () => showCreateModal.value = true
const openEditModal   = rec => {
  editRecord.value = { ...rec }
  showEditModal.value = true
}
const openDetailModal = rec => {
  viewRecord.value = {
    ...rec,
    medications: rec.prescription?.medications?.map(med => ({
      name: med.medication?.name || 'Không rõ',
      dosage: med.dosage,
      duration: med.duration
    })) || []
  }
  showDetailModal.value = true
}


const getDoctorName = id => {
  const d = doctors.value.find(x => x.id === id)
  return d ? d.user?.name || d.name : 'Không rõ'
}

const fetchMedicationSuggestions = debounce(async (idx, q) => {
  if (!q) {
    newRecord.value.medications[idx].suggestions = []
    return
  }
  try {
    const res = await API.get('/medications', { params: { search: q } })
    newRecord.value.medications[idx].suggestions = res.data.medications
  } catch (e) {
    console.error(e)
  }
}, 300)

const onSearchMedication = idx => {
  const q = newRecord.value.medications[idx].search
  fetchMedicationSuggestions(idx, q)
}

const selectMedication = (idx, item) => {
  const m = newRecord.value.medications[idx]
  m.medication_id = item.id
  m.selectedName  = item.name
  m.search        = item.name
  m.suggestions   = []
}

const addMedication = () => {
  newRecord.value.medications.push({
    search: '',
    suggestions: [],
    medication_id: null,
    selectedName: '',
    dosage: '',
    duration: ''
  })
}
const removeMedication = idx => {
  newRecord.value.medications.splice(idx, 1)
}

const createMedicalRecord = async () => {
  const formData = new FormData()
  formData.append('patient_id', patient.value.id)
  formData.append('doctor_id', newRecord.value.doctor_id)
  formData.append('diagnosis', newRecord.value.diagnosis)
  formData.append('diagnosis', newRecord.value.notes)
  formData.append('treatment', newRecord.value.treatment)
  if (newRecord.value.file) formData.append('file_attachment', newRecord.value.file)

  const meds = newRecord.value.medications.map(m => ({
    medication_id: m.medication_id,
    dosage: m.dosage,
    duration: m.duration
  }))
  formData.append('medications', JSON.stringify(meds))

  try {
    const res = await API.post('/medical-records', formData)
    medicalRecords.value.unshift(res.data.record)
    toast.success('Tạo hồ sơ thành công')
    showCreateModal.value = false

    newRecord.value = {
      doctor_id: '',
      diagnosis: '',
      treatment: '',
      notes: '',
      file: null,
      medications: [
        { search: '', suggestions: [], medication_id: null, selectedName: '', dosage: '', duration: '' }
      ]
    }
  } catch (err) {
    console.error('Lỗi tạo hồ sơ:', err)
    toast.error('Tạo hồ sơ thất bại')
  }
}

const updateMedicalRecord = async () => {
  try {
    const { data } = await API.put(`/medical-records/${editRecord.value.id}`, editRecord.value)
    const idx = medicalRecords.value.findIndex(r => r.id === data.record.id)
    if (idx !== -1) medicalRecords.value[idx] = data.record
    toast.success('Cập nhật thành công')
    showEditModal.value = false
  } catch {
    toast.error('Cập nhật thất bại')
  }
}
</script>
