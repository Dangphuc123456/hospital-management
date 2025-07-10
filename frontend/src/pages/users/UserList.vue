<template>
  <div class="user-table-wrapper">
    <div >
      <h2>Danh sách người dùng</h2>
      <div v-if="loading" class="loading">Đang tải...</div>
      <table v-else>
        <thead>
          <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users.data" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role?.name }}</td>
            <td class="actions">
              <button style="color: black;background-color: #FFC107;" @click="openEditModal(user)"><i class="fa fa-edit"></i></button>
              <button class="delete" @click="openDeleteModal(user)"><i class="fa fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Modal Sửa -->
      <div v-if="showEditModal" class="modal-overlay">
        <div class="modal">
          <h3>Sửa người dùng</h3>

          <label>Tên:</label>
          <input v-model="editForm.name" type="text" />

          <label>Email:</label>
          <input v-model="editForm.email" type="email" />

          <div class="row-inline">
            <div class="column">
              <label>Vai trò:</label>
              <select v-model="editForm.role_id">
                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
              </select>
            </div>
            <div class="column switch-column">
              <label>Hoạt động:</label>
              <label class="switch">
                <input type="checkbox" v-model="editForm.is_active" />
                <span class="slider round"></span>
              </label>
            </div>
          </div>

          <div class="actions">
            <button class="edit" @click="saveEdit">Lưu</button>
            <button @click="showEditModal = false">Hủy</button>
          </div>
        </div>
      </div>

      <!-- Modal Xóa -->
      <div v-if="showDeleteModal" class="modal-overlay">
        <div class="modal">
          <p>Bạn có chắc chắn muốn xóa <strong>{{ deleteTarget.name }}</strong>?</p>
          <div class="actions">
            <button
              class="delete"
              :disabled="deleteCountdown > 0"
              @click="confirmDelete"
            >
              Xóa<span v-if="deleteCountdown > 0"> ({{ deleteCountdown }})</span>
            </button>
            <button @click="closeDeleteModal">Hủy</button>
          </div>
        </div>
      </div>


      <!-- Pagination -->
      <div class="table-controls">
        <div class="per-page">
          <label>Số người dùng/trang:</label>
          <select v-model="perPage" @change="fetchUsers">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="20">20</option>
          </select>
        </div>

        <div class="pagination">
          <button :disabled="!users.prev_page_url" @click="fetchUsers(users.current_page - 1)">Trước</button>
          <span>Trang {{ users.current_page }} / {{ users.last_page }}</span>
          <button :disabled="!users.next_page_url" @click="fetchUsers(users.current_page + 1)">Tiếp</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import API from '@/utils/axios'
import '@/pages/users/User.css'
import { useToast } from 'vue-toastification'

const toast = useToast()
const users = ref({})
const roles = ref([])
const perPage = ref(10)
const loading = ref(true)

const showEditModal = ref(false)
const showDeleteModal = ref(false)
const editForm = ref({})
const deleteTarget = ref({})
const deleteCountdown = ref(3)
const deleteInterval = ref(null)

const fetchUsers = async (page = 1) => {
  loading.value = true
  try {
    const res = await API.get('/users', {
      params: {
        page,
        per_page: perPage.value
      }
    })
    users.value = res.data
  } catch (err) {
    console.error('Lỗi khi tải user:', err)
  } finally {
    loading.value = false
  }
}

const openEditModal = (user) => {
  editForm.value = {
    ...user,
    role_id: user.role_id || user.role?.id,
    is_active: user.is_active === 1 || user.is_active === true
  }
  showEditModal.value = true
}

const saveEdit = async () => {
  try {
    const res = await API.put(`/users/${editForm.value.id}`, {
      name: editForm.value.name,
      email: editForm.value.email,
      role_id: editForm.value.role_id,
      is_active: editForm.value.is_active
    })
    toast.success(res.data.message || 'Cập nhật thành công!')
    showEditModal.value = false
    fetchUsers(users.value.current_page)
  } catch (err) {
    if (err.response?.data?.message) {
      toast.error(err.response.data.message)
    } else {
      toast.error('Lỗi khi cập nhật người dùng!')
    }
    console.error('Lỗi sửa:', err)
  }
}


const confirmDelete = async () => {
  try {
    const res = await API.delete(`/users/${deleteTarget.value.id}`)
    toast.success(res.data.message || 'Xóa người dùng thành công!')
    showDeleteModal.value = false
    fetchUsers(users.value.current_page)
  } catch (err) {
    if (err.response?.data?.message) {
      toast.error(err.response.data.message)
    } else {
      toast.error('Lỗi khi xóa người dùng!')
    }
    console.error('Lỗi xóa:', err)
  }
}
const openDeleteModal = (user) => {
  deleteTarget.value = user
  showDeleteModal.value = true
  deleteCountdown.value = 3

  // Bắt đầu đếm ngược mỗi giây
  deleteInterval.value = setInterval(() => {
    deleteCountdown.value--
    if (deleteCountdown.value === 0) {
      clearInterval(deleteInterval.value)
    }
  }, 1000)
}
const closeDeleteModal = () => {
  showDeleteModal.value = false
  clearInterval(deleteInterval.value)
}


onMounted(async () => {
  await fetchUsers()
  try {
    const res = await API.get('/roles')
    roles.value = res.data
  } catch (err) {
    console.error('Không tải được roles:', err)
  }
})
</script>
