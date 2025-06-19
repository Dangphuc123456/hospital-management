<template>
  <div class="verify-container">
    <h2>Xác thực Email</h2>

    <div v-if="loading" class="status loading">
      Đang xác thực tài khoản của bạn...
    </div>

    <div v-else>
      <div v-if="success" class="status success">
        🎉 {{ message }} <br />
        <router-link to="/login">Đăng nhập ngay</router-link>
      </div>

      <div v-else class="status error">
        ❌ {{ message }} <br />
        <router-link to="/register">Đăng ký lại</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import API from '@/utils/axios'

const route = useRoute()
const message = ref('')
const loading = ref(true)
const success = ref(false)

onMounted(async () => {
  const token = route.query.token

  try {
    const res = await API.get(`/verify-email?token=${token}`)
    message.value = res.data.message
    success.value = true
  } catch (err) {
    message.value = err.response?.data?.message || 'Đã xảy ra lỗi khi xác thực'
    success.value = false
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.verify-container {
  max-width: 500px;
  margin: 60px auto;
  padding: 30px;
  background: #f9f9f9;
  border-radius: 12px;
  text-align: center;
  font-family: 'Segoe UI', sans-serif;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}
.status {
  font-size: 18px;
  margin-top: 20px;
}
.success {
  color: green;
}
.error {
  color: red;
}
.loading {
  color: #3498db;
}
</style>
