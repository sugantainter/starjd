import './bootstrap';
import { createApp } from 'vue';
import { createHead } from '@unhead/vue/client';
import AppRoot from './AppRoot.vue';
import router from './router';

const app = createApp(AppRoot);
const head = createHead();

app.use(router);
app.use(head);
app.mount('#app');
