<template>
  <div class="register-form">
    <h2>Đăng ký</h2>
    <form @submit.prevent="handleRegister">
      <div>
        <label>Họ tên</label>
        <input v-model="form.name" type="text" required :disabled="loading" />
      </div>
      <div>
        <label>Email</label>
        <input v-model="form.email" type="email" required :disabled="loading" />
      </div>
      <div>
        <label>Mật khẩu</label>
        <input v-model="form.password" type="password" required :disabled="loading" />
      </div>
      <div>
        <label>Nhập lại mật khẩu</label>
        <input v-model="confirmPassword" type="password" required :disabled="loading" />
        <small v-if="confirmPassword && confirmPassword !== form.password" class="error-text">
          Mật khẩu không khớp
        </small>
      </div>
      <div>
        <label>Chọn vai trò</label>
        <select v-model="form.role_id" required :disabled="loading">
          <option disabled value="">-- Chọn vai trò --</option>
          <option v-for="role in roles" :key="role.id" :value="role.id">
            {{ role.name }}
          </option>
        </select>
      </div>
      <button type="submit" :disabled="loading">
        <span v-if="loading">Đang xử lý...</span>
        <span v-else>Đăng ký</span>
      </button>
      <p style="text-align: center;">
        Nếu đã có tài khoản 
        <span>
          <router-link to="/auth/login" style="color: #000080; font-weight: bold;">Đăng nhập</router-link>
        </span>
      </p>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import API from '@/utils/axios'
import '@/pages/auth/Register.css'

const toast = useToast()
const loading = ref(false)

const form = reactive({
  name: '',
  email: '',
  password: '',
  role_id: '',
})

const confirmPassword = ref('')
const roles = ref([])

onMounted(async () => {
  try {
    const res = await API.get('/roles')
    roles.value = res.data.filter(r => 
      r.name === 'Doctor' || r.name === 'Staff'
    )
  } catch (err) {
    toast.error('Không tải được vai trò: ' + (err.response?.data?.message || err.message))
  }
})

const resetForm = () => {
  form.name = ''
  form.email = ''
  form.password = ''
  form.role_id = ''
  confirmPassword.value = ''
}

const handleRegister = async () => {
  if (form.password !== confirmPassword.value) {
    toast.error('Mật khẩu không khớp')
    return
  }

  loading.value = true
  try {
    const res = await API.post('/register', {
      name: form.name,
      email: form.email,
      password: form.password,
      role_id: form.role_id
    })
    toast.success(res.data.message || 'Đăng ký thành công!')
    resetForm()
  } catch (err) {
    toast.error('Lỗi đăng ký: ' + (err.response?.data?.message || err.message))
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.error-text {
  display: block;
  margin-top: -12px;
  margin-bottom: 12px;
  color: red;
}
</style>
