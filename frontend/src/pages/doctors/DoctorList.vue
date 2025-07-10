<template>
  <div class="doctor-list">
    <h2>Danh sách bác sĩ</h2>
    <table v-if="!loading">
      <thead>
        <tr>
          <th>Tên</th>
          <th>Chuyên khoa</th>
          <th>Email</th>
          <th>Điện thoại</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="doc in doctors" :key="doc.id">
          <td>{{ doc.user?.name }}</td>
          <td>{{ doc.specialization }}</td>
          <td>{{ doc.user?.email }}</td>
          <td>{{ doc.phone }}</td>
          <td class="activity">
            <div class="button-wrapper">
              <button class="btn-detail" @click="openDetailModal(doc)"><i class="fa fa-eye"></i></button>
              <button class="btn-edit" @click="openEditModal(doc)"><i class="fa fa-edit"></i></button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-else>Đang tải...</div>
  </div>
  <div v-if="showDetailModal" class="modal">
  <h3>Chi tiết bác sĩ</h3>
  <div v-if="selectedDoctor">
    <div style="display: flex; gap: 10px; align-items: center;">
      <div class="form-group" style="flex: 0.8;">
        <label>Họ tên:</label>
        <input
          type="text"
          :value="selectedDoctor.user?.name"
          disabled
          style="width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" />
      </div>
      <div class="form-group" style="flex: 1.2;">
        <label>Email:</label>
        <input
          type="email"
          :value="selectedDoctor.user?.email"
          disabled
          style="width: 100%;" />
      </div>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 10px;margin-left:13px;">
      <div class="form-group" style="flex: 1;">
        <label>Chuyên khoa:</label>
        <input type="text" :value="selectedDoctor.specialization" disabled />
      </div>
      <div class="form-group" style="flex: 1.4;">
        <label>Số điện thoại:</label>
        <input type="text" :value="selectedDoctor.phone" disabled />
      </div>
      <div class="form-group" style="flex: 1;">
        <label>Chức danh:</label>
        <input type="text" :value="selectedDoctor.degree" disabled />
      </div>
    </div>
      <div style="display: flex; gap: 10px; margin-top: 5px;margin-left:13px;">
        <div class="form-group" style="flex: 1;">
          <label>Số năm kinh nghiệm:</label>
          <input type="number" :value="selectedDoctor.years_of_experience" disabled />
        </div>
        <div class="form-group" style="flex: 1;">
          <label>Giấy phép hành nghề:</label>
          <input type="text" :value="selectedDoctor.license_number" disabled />
        </div>
      </div>
    <div class="form-group" style="margin-top: 10px;margin-left:12px;">
      <label>Giới thiệu:</label>
      <textarea :value="selectedDoctor.bio" disabled></textarea>
    </div>

  </div>
  <button class="btn-cancel" @click="showDetailModal = false">Đóng</button>
</div>
<div v-if="showEditModal" class="modal">
  <h3>Sửa thông tin bác sĩ</h3>
  <div v-if="editDoctor">
    <div style="display: flex; gap: 10px; align-items: center;">
      <div class="form-group" style="flex: 0.8;">
        <label>Họ tên:</label>
        <input
          type="text"
          v-model="editDoctor.user.name"
          style="width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" />
      </div>
      <div class="form-group" style="flex: 1.2;">
        <label>Email:</label>
        <input
          type="email"
          v-model="editDoctor.user.email"
          style="width: 100%;" />
      </div>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 10px;margin-left:13px;">
      <div class="form-group" style="flex: 1;">
        <label>Chuyên khoa:</label>
        <input type="text" v-model="editDoctor.specialization" />
      </div>
      <div class="form-group" style="flex: 1.4;">
        <label>Số điện thoại:</label>
        <input type="text" v-model="editDoctor.phone" />
      </div>
      <div class="form-group" style="flex: 1;">
        <label>Chức danh:</label>
        <input type="text" v-model="editDoctor.degree" />
      </div>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 5px;margin-left:13px;">
      <div class="form-group" style="flex: 1;">
        <label>Số năm kinh nghiệm:</label>
        <input type="number" v-model="editDoctor.years_of_experience" />
      </div>
      <div class="form-group" style="flex: 1;">
        <label>Giấy phép hành nghề:</label>
        <input type="text" v-model="editDoctor.license_number" />
      </div>
    </div>

    <div class="form-group" style="margin-top: 10px;margin-left:12px;">
      <label>Giới thiệu:</label>
      <textarea v-model="editDoctor.bio"></textarea>
    </div>
  </div>

  <button class="btn-update" @click="updateDoctor">Lưu</button>
  <button class="btn-cancel" @click="showEditModal = false">Hủy</button>
</div>

</template>

<script setup>
import { ref, onMounted } from 'vue'
import API from '@/utils/axios'
import '@/pages/doctors/Doctors.css'
import { useToast } from 'vue-toastification'

const toast = useToast()

const doctors = ref([])
const loading = ref(true)
const showDetailModal = ref(false)
const selectedDoctor = ref(null)
const showEditModal = ref(false)
const editDoctor = ref(null)

const fetchDoctors = async () => {
  try {
    const response = await API.get('/doctors')
    doctors.value = response.data.doctors
  } catch (error) {
    console.error('Lỗi khi tải danh sách bác sĩ:', error)
  } finally {
    loading.value = false
  }
}
const openDetailModal = async (doctor) => {
  try {
    const response = await API.get(`/doctors/${doctor.id}`) 
    selectedDoctor.value = response.data.doctor
    showDetailModal.value = true
  } catch (error) {
    console.error('Lỗi khi lấy chi tiết bác sĩ:', error)
  }
}
const openEditModal = (doctor) => {
  editDoctor.value = JSON.parse(JSON.stringify(doctor))
  showEditModal.value = true
}
const updateDoctor = async () => {
  try {
    const response = await API.put(`/doctors/${editDoctor.value.id}`, {
      ...editDoctor.value,
      user: undefined,
    })

    toast.success(response.data.message || 'Cập nhật thành công!')

    showEditModal.value = false
    fetchDoctors()
  } catch (error) {
    console.error('Lỗi khi cập nhật bác sĩ:', error)

    const errorMsg = error.response?.data?.message || 'Cập nhật thất bại.'
    toast.error(errorMsg)
  }
}

onMounted(fetchDoctors)
</script>
