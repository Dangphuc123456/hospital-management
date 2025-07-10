import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import VueTippy from 'vue-tippy'

import App from './App.vue'
import router from './router'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const app = createApp(App)
app.use(createPinia())
app.use(Toast)
app.use(router)
app.use(VueTippy, {
  defaultProps: {
    placement: 'top',
    arrow: true,
    delay: [0, 0],
  },
})
app.mount('#app')
