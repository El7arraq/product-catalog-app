
require('./bootstrap');

import { createApp } from 'vue';
import ProductList from './components/ProductList.vue';
import ProductForm from './components/ProductForm.vue';

const app = createApp({});
app.component('product-list', ProductList);
app.component('product-form', ProductForm);
app.mount('#app');
