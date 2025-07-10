<template>
  <div>
    <h2>Danh sách bệnh nhân</h2>
    <button @click="showAddModal = true" class="add-button">+ Thêm bệnh nhân</button>
    <table v-if="!loading">
      <thead>
        <tr>
          <th>#</th>
          <th>Tên bệnh nhân</th>
          <th>Giới tính</th>
          <th>Ngày sinh</th>
          <th>SĐT</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="patient in patients.data" :key="patient.id">
          <td>{{ patient.id }}</td>
          <td>{{ patient.name }}</td>
          <td>{{ patient.gender }}</td>
          <td>{{ patient.date_of_birth }}</td>
          <td>{{ patient.phone }}</td>
          <td>
            <button class="btn-detail" @click="goToDetailPage(patient.id)"><i class="fa fa-eye"></i></button>
            <button class="btn-edit" @click="openEditModal(patient)"><i class="fa fa-edit"></i></button>
            <button class="btn-delete" @click="openDeleteModal(patient)"><i class="fa fa-trash"></i></button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-else>Đang tải...</div>

    <!-- Phân trang -->
    <div class="pagination" v-if="patients.meta">
      <button :disabled="!patients.links.prev" @click="fetchPatients(patients.meta.current_page - 1)">Trước</button>
      <span>Trang {{ patients.meta.current_page }} / {{ patients.meta.last_page }}</span>
      <button :disabled="!patients.links.next" @click="fetchPatients(patients.meta.current_page + 1)">Tiếp</button>
    </div>

    <!-- Modal Thêm -->
    <div v-if="showAddModal" class="modal">
      <h3>Thêm bệnh nhân</h3>
      <div class="form-row">
        <div class="form-group half">
          <label>Họ tên</label>
          <input v-model="editForm.name" placeholder="Họ tên" />
        </div>
        <div class="form-group half">
          <label>Địa chỉ</label>
          <input v-model="editForm.address" placeholder="Địa chỉ" />
        </div>
      </div>
          <div class="form-row">
        <div class="form-group half">
          <label>Giới tính</label>
          <select v-model="editForm.gender">
            <option disabled value="">-- Chọn giới tính --</option>
            <option value="male">Nam</option>
            <option value="female">Nữ</option>
            <option value="other">Khác</option>
          </select>
        </div>
        <div class="form-group half">
          <label>Nhóm máu</label>
          <select v-model="editForm.blood_type">
            <option disabled value="">-- Chọn nhóm máu --</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="AB">AB</option>
            <option value="O">O</option>
          </select>
        </div>
      </div>
          <div class="form-row">
        <div class="form-group half">
          <label>Ngày sinh</label>
          <input type="date" v-model="editForm.date_of_birth" />
        </div>
        <div class="form-group half">
          <label>Số điện thoại</label>
          <input v-model="editForm.phone" placeholder="SĐT" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group half">
          <label>Người liên hệ khẩn</label>
          <input v-model="editForm.emergency_contact_name" placeholder="Tên người liên hệ" />
        </div>
        <div class="form-group half">
          <label>SĐT người nhà</label>
          <input v-model="editForm.emergency_contact_phone" placeholder="SĐT khẩn cấp" />
        </div>
      </div>
      <button class="btn-save" @click="submitAdd">Lưu</button>
      <button class="btn-cancel" @click="showAddModal = false">Hủy</button>
    </div>

    <!-- Modal Sửa -->
    <div v-if="showEditModal" class="modal">
  <h3>Thông tin bệnh nhân</h3>
  <div class="form-row">
    <div class="form-group half">
      <label>Họ tên</label>
      <input v-model="editForm.name" placeholder="Họ tên" />
    </div>
    <div class="form-group half">
      <label>Địa chỉ</label>
      <input v-model="editForm.address" placeholder="Địa chỉ" />
    </div>
  </div>
  <div class="form-row">
    <div class="form-group half">
      <label>Giới tính</label>
      <select v-model="editForm.gender">
        <option disabled value="">-- Chọn giới tính --</option>
        <option value="male">Nam</option>
        <option value="female">Nữ</option>
        <option value="other">Khác</option>
      </select>
    </div>
    <div class="form-group half">
      <label>Nhóm máu</label>
      <select v-model="editForm.blood_type">
        <option disabled value="">-- Chọn nhóm máu --</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="AB">AB</option>
        <option value="O">O</option>
      </select>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group half">
      <label>Ngày sinh</label>
      <input type="date" v-model="editForm.date_of_birth" />
    </div>
    <div class="form-group half">
      <label>Số điện thoại</label>
      <input v-model="editForm.phone" placeholder="SĐT" />
    </div>
  </div>
  <div class="form-row">
    <div class="form-group half">
      <label>Người liên hệ khẩn</label>
      <input v-model="editForm.emergency_contact_name" placeholder="Tên người liên hệ" />
    </div>
    <div class="form-group half">
      <label>SĐT người nhà</label>
      <input v-model="editForm.emergency_contact_phone" placeholder="SĐT khẩn cấp" />
    </div>
  </div>
  <div class="button-row">
    <button class="btn-save" @click="saveEdit">Lưu</button>
    <button class="btn-cancel" @click="showEditModal = false">Hủy</button>
  </div>
</div>

    <!-- Modal Xóa -->
    <div v-if="showDeleteModal" class="modal">
      <p>
        Bạn có chắc muốn xóa bệnh nhân
        <strong>{{ deleteTarget.user?.name }}</strong>?
      </p>
      <div style="display: flex; justify-content: center; gap: 12px; margin-top: 16px;">
        <button
          @click="confirmDelete"
          style="
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
          "
        >
          Xác nhận xóa
        </button>
        <button
          @click="showDeleteModal = false"
          style="
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
          "
        >
          Hủy
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import API from '@/utils/axios'
import { useToast } from 'vue-toastification'
import '@/pages/patients/Patient.css'

const toast = useToast()
const router = useRouter()

const patients = ref({})
const loading = ref(true)
const perPage = 10

const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)

const newPatient = ref({
  name: '',
  gender: '',
  date_of_birth: '',
  blood_type: '',
  phone: '',
  address: '',
  emergency_contact_name: '',
  emergency_contact_phone: ''
})

const editForm = ref({})
const deleteTarget = ref({})

// Lấy danh sách bệnh nhân
const fetchPatients = async (page = 1) => {
  loading.value = true
  try {
    const res = await API.get('/patients', {
      params: { page, per_page: perPage }
    })
    patients.value = res.data
  } catch (err) {
    toast.error('Không thể tải danh sách bệnh nhân')
  } finally {
    loading.value = false
  }
}

// Thêm bệnh nhân mới
const submitAdd = async () => {
  try {
    await API.post('/patients', newPatient.value)
    toast.success('Thêm bệnh nhân thành công')
    showAddModal.value = false
    fetchPatients()
    newPatient.value = {
      name: '',
      gender: '',
      date_of_birth: '',
      blood_type: '',
      phone: '',
      address: '',
      emergency_contact_name: '',
      emergency_contact_phone: ''
    }
  } catch (err) {
    toast.error('Thêm thất bại')
    console.error(err)
  }
}

// Điều hướng chi tiết
const goToDetailPage = (id) => {
  router.push({ name: 'patient-detail', params: { id } })
}


// Sửa
const openEditModal = (patient) => {
  editForm.value = { ...patient }
  showEditModal.value = true
}

const saveEdit = async () => {
  try {
    const { status, data } = await API.put(
      `/patients/${editForm.value.id}`,
      {
        name: editForm.value.name,
        gender: editForm.value.gender,
        date_of_birth: editForm.value.date_of_birth,
        blood_type: editForm.value.blood_type,
        phone: editForm.value.phone,
        address: editForm.value.address,
        emergency_contact_name: editForm.value.emergency_contact_name,
        emergency_contact_phone: editForm.value.emergency_contact_phone,
      }
    );

    if (status >= 200 && status < 300) {
      toast.success(data.message);
      showEditModal.value = false;
      try {
        await fetchPatients(patients.value.meta.current_page);
      } catch (e) {
        console.error('Lỗi khi tải lại:', e);
      }
    }
  } catch (err) {
    if (err.response?.status === 422) {
      const errors = err.response.data.errors || {};
      const msgs = Object.values(errors).flat().join('\n');
      toast.error(msgs);
    } else {
      const msg = err.response?.data?.message || 'Cập nhật thất bại';
      toast.error(msg);
    }
    console.error(err);
  }
}
// Xóa
const openDeleteModal = (patient) => {
  deleteTarget.value = patient
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  try {
    await API.delete(`/patients/${deleteTarget.value.id}`)
    toast.success('Đã xóa bệnh nhân')
    showDeleteModal.value = false
    fetchPatients(patients.value.meta.current_page)
  } catch (err) {
    toast.error('Xóa thất bại')
  }
}

onMounted(() => {
  fetchPatients()
})
</script>
