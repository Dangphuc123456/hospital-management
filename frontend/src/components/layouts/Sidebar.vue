<template>
    <aside :class="['sidebar', { collapsed }]">
        <div class="toggle-button" @click="toggleSidebar">
            <i :class="collapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
        </div>
        <nav>
            <RouterLink to="/dashboard">🏠 <span v-if="!collapsed">Trang chủ</span></RouterLink>
            <RouterLink to="/patients">🧍 <span v-if="!collapsed">Bệnh nhân</span></RouterLink>
            <router-link to="/medical-records">📁 <span v-if="!collapsed">Hồ sơ bệnh án</span></router-link>
            <RouterLink to="/doctors">🩺 <span v-if="!collapsed">Bác sĩ</span></RouterLink>
            <RouterLink to="/appointments">📅 <span v-if="!collapsed">Lịch hẹn</span></RouterLink>
            <RouterLink to="/invoices">💵 <span v-if="!collapsed">Hóa đơn</span></RouterLink>
            <router-link to="/medications">💊 <span v-if="!collapsed">Kho thuốc</span></router-link>
            <RouterLink to="/statistics">📊 <span v-if="!collapsed">Thống kê</span></RouterLink>
            <RouterLink to="/users">🧑 <span v-if="!collapsed">Tài khoản</span></RouterLink>
        </nav>
    </aside>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import { ref } from 'vue'
import '@fortawesome/fontawesome-free/css/all.css'

const collapsed = ref(false)

const toggleSidebar = () => {
    collapsed.value = !collapsed.value
}
</script>

<style scoped>
.sidebar {
    width: 150px;
    background-color: #34495e;
    color: white;
    height: 100vh;
    padding: 20px 10px;
    position: relative;
    transition: width 0.3s;
}

.sidebar.collapsed {
    width: 60px;
}
nav {
    display: flex;
    flex-direction: column;
    gap: 36px;
    margin-top: 40px;
    margin-left: 12px;
}

a {
    color: white;
    text-decoration: none;
    transition: 0.3s;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 8px;
}

.router-link-exact-active {
    font-weight: bold;
    color: #1abc9c;
}

/* Toggle button */
.toggle-button {
    position: absolute;
    top: 40%;
    right: -12px;
    transform: translateY(-50%);
    background-color: #1abc9c;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 1;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    font-size: 12px;
    /* thu nhỏ icon bên trong */
    transition: all 0.3s ease;
}
</style>
