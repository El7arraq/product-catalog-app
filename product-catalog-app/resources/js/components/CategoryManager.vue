<template>
  <div class="category-manager">
    <h2>Categories</h2>
    <form @submit.prevent="addCategory" class="mb-4">
      <input v-model="form.name" placeholder="Category name" required />
      <select v-model="form.parent_id">
        <option value="">No parent</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
      <button type="submit">Add Category</button>
    </form>
    <ul>
      <li v-for="cat in categories" :key="cat.id">
        <strong>{{ cat.name }}</strong>
        <span v-if="cat.parent_id">(Parent: {{ parentName(cat.parent_id) }})</span>
      </li>
    </ul>
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
      categories: [],
      form: { name: '', parent_id: '' },
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
    async addCategory() {
      this.errors = null;
      const res = await fetch('/api/categories', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.form)
      });
      const data = await res.json();
      if (res.status === 201) {
        this.form = { name: '', parent_id: '' };
        this.fetchCategories();
      } else if (data.errors) {
        this.errors = data.errors;
      }
    },
    parentName(id) {
      const parent = this.categories.find(c => c.id === id);
      return parent ? parent.name : 'Unknown';
    }
  }
};
</script>

<style scoped>
.category-manager {
  background: #f9f9f9;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
input, select, button {
  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
  padding: 0.4rem;
  border-radius: 4px;
  border: 1px solid #ccc;
}
button {
  background: #ef3b2d;
  color: #fff;
  border: none;
  cursor: pointer;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  margin-bottom: 0.5rem;
}
</style>
