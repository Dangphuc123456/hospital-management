<template>
  <div class="layout">
    <Navbar @toggle-sidebar="toggleSidebar" />
    <div class="content-wrapper">
      <Sidebar :collapsed="isSidebarCollapsed" />
      <div class="main-content" :class="{ expanded: isSidebarCollapsed }">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from './Sidebar.vue'
import Navbar from './Navbar.vue'

const isSidebarCollapsed = ref(false)

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
}
</script>

<style scoped>
.layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.content-wrapper {
  display: flex;
  flex: 1;
  overflow: hidden;
}

.main-content {
  flex: 1;
   padding: 20px 20px 10px 20px;
  transition: margin-left 0.3s;
}

.main-content.expanded {
  margin-left: 60px; /* Khi sidebar thu gọn */
}
</style>
