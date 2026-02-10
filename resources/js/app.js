import './bootstrap';
import { createApp } from 'vue'
import App from '../vue/App.vue'
import router from '../vue/router'
import '../vue/assets/main.css'
import GuestLayout from '../vue/views/Layouts/GuestLayout.vue'
import AuthLayout from '../vue/views/Layouts/AuthLayout.vue'
import { createPinia } from 'pinia'

const pinia = createPinia()
var app = createApp(App)
app.component('GuestLayout', GuestLayout);
app.component('AuthLayout', AuthLayout);
app.use(router);
app.use(pinia);
app.mount('#app');
