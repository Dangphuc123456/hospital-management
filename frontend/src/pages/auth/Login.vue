<template>
  <div class="login-form">
    <h2>Đăng nhập</h2>
    <form @submit.prevent="handleLogin">
      <div>
        <label>Email</label>
        <input v-model="form.email" type="email" required :disabled="loading" />
      </div>
      <div>
        <label>Mật khẩu</label>
        <input v-model="form.password" type="password" required :disabled="loading" />
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
        <span v-else>Đăng nhập</span>
      </button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import API from '@/utils/axios'
import '@/pages/auth/Login.css'

const router = useRouter()
const toast = useToast()
const loading = ref(false)

const form = reactive({
  email: '',
  password: '',
  role_id: ''
})

const roles = ref([])

onMounted(async () => {
  try {
    const res = await API.get('/roles')
    roles.value = res.data
  } catch (err) {
    toast.error('Không tải được vai trò: ' + (err.response?.data?.message || err.message))
  }
})

const handleLogin = async () => {
  loading.value = true
  try {
    const res = await API.post('/login', form)
    localStorage.setItem('token', res.data.token)
    toast.success('Đăng nhập thành công!')
    router.push('/')
  } catch (err) {
    toast.error('Lỗi đăng nhập: ' + (err.response?.data?.message || err.message))
  } finally {
    loading.value = false
  }
}
</script>


