<template>
  <div>
    <h2>Create Product</h2>
    <form @submit.prevent="submitForm">
      <input v-model="form.name" placeholder="Name" required />
      <textarea v-model="form.description" placeholder="Description" required></textarea>
      <input v-model.number="form.price" type="number" placeholder="Price" required />
      <input v-model="form.image" placeholder="Image URL" />
      <label>Categories:</label>
      <select v-model="form.categories" multiple>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
      <button type="submit">Create</button>
    </form>
    <div v-if="errors">
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error.join(', ') }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        name: '',
        description: '',
        price: '',
        image: '',
        categories: []
      },
      categories: [],
      errors: null
    };
  },
  mounted() {
    this.fetchCategories();
  },
  methods: {
    async fetchCategories() {
      const res = await fetch('/api/categories');
      this.categories = await res.json();
    },
    async submitForm() {
      this.errors = null;
      const res = await fetch('/api/products', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.form)
      });
      const data = await res.json();
      if (res.status === 201) {
        alert('Product created!');
        this.form = { name: '', description: '', price: '', image: '', categories: [] };
      } else if (data.errors) {
        this.errors = data.errors;
      }
    }
  }
};
</script>
