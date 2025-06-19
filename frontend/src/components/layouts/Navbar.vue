<template>
  <header class="navbar">
    <div class="logo-title">
      <img src="@/assets/benhvien.png" alt="Logo" class="logo" />
      <h2>Hospital Management</h2>
    </div>

    <div class="user-info">
      <template v-if="isLoggedIn">
        <span>Xin chào, {{ user.name }}</span>
        <button @click="logout">Đăng xuất</button>
      </template>

      <template v-else>
        <router-link to="/auth/login">Đăng nhập</router-link>
        <router-link to="/auth/register">Đăng ký</router-link>
      </template>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const user = ref({ name: 'Người dùng' })
const isLoggedIn = computed(() => !!localStorage.getItem('token'))

const logout = () => {
  localStorage.removeItem('token')
  location.reload() 
}

onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    user.value = JSON.parse(storedUser)
  }
})
</script>

<style scoped>
.navbar {
  height: 60px;
  background-color: #2c3e50;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
}

.user-info {
  display: flex;
  gap: 10px;
  align-items: center;
}

button {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  cursor: pointer;
  border-radius: 4px;
}

a {
  color: #ecf0f1;
  text-decoration: none;
  padding: 6px 12px;
  background: #3498db;
  border-radius: 4px;
}

.logo-title {
  display: flex;
  align-items: center;
}

.logo {
  width: 40px;
  height: 40px;
  margin-right: 10px;
}

</style>
