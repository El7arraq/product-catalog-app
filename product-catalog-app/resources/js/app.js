
require('./bootstrap');

import { createApp } from 'vue';

import ProductManager from './components/ProductManager.vue';
import CategoryManager from './components/CategoryManager.vue';

const app = createApp({});
app.component('product-manager', ProductManager);
app.component('category-manager', CategoryManager);
app.mount('#app');
