<template>
  <div>
    <h2>Product List</h2>
    <div>
      <label>Sort by price:</label>
      <select v-model="sortBy" @change="fetchProducts">
        <option value="asc">Ascending</option>
        <option value="desc">Descending</option>
      </select>
      <label>Filter by category:</label>
      <select v-model="categoryId" @change="fetchProducts">
        <option value="">All</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
    </div>
    <ul>
      <li v-for="product in products" :key="product.id">
        <strong>{{ product.name }}</strong> - {{ product.price }}
        <br />{{ product.description }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      products: [],
      categories: [],
      sortBy: 'asc',
      categoryId: ''
    };
  },
  mounted() {
    this.fetchCategories();
    this.fetchProducts();
  },
  methods: {
    async fetchProducts() {
      let url = `/api/products?sortBy=${this.sortBy}`;
      if (this.categoryId) url += `&categoryId=${this.categoryId}`;
      const res = await fetch(url);
      this.products = await res.json();
    },
    async fetchCategories() {
      const res = await fetch('/api/categories');
      this.categories = await res.json();
    }
  }
};
</script>
