<template>
  <div class="products-list-container">
    <div class="header-section">
      <h2 class="page-title">Products</h2>
      
      <div class="filters-section">
        <div class="filter-group">
          <label for="sort-select" class="filter-label">Sort by price:</label>
          <select id="sort-select" v-model="sortBy" @change="fetchProducts" class="filter-select">
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="category-select" class="filter-label">Filter by category:</label>
          <select id="category-select" v-model="categoryId" @change="fetchProducts" class="filter-select">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
      </div>
    </div>

    <div v-if="products.length > 0" class="products-section">
      <div class="products-grid">
        <div v-for="product in products" :key="product.id" class="product-card">
          <div v-if="product.image" class="product-image">
            <img :src="getImageUrl(product.image)" :alt="product.name" />
          </div>
          <div class="product-content">
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-description">{{ product.description }}</p>
            <p class="product-price">${{ parseFloat(product.price).toFixed(2) }}</p>
            <div v-if="product.categories && product.categories.length > 0" class="product-categories">
              <span v-for="category in product.categories" :key="category.id" class="category-badge">
                {{ category.name }}
              </span>
            </div>
          </div>
          <div class="product-actions">
            <button @click="editProduct(product)" class="btn-edit">Edit</button>
            <button @click="deleteProduct(product.id)" class="btn-delete">Delete</button>
          </div>
        </div>
      </div>
    </div>
    
    <div v-else class="empty-state">
      <p class="empty-message">No products found</p>
    </div>
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
    },
    getImageUrl(imagePath) {
      if (!imagePath) return '';
      if (imagePath.startsWith('http')) return imagePath;
      return `/storage/${imagePath.replace('products/', 'products/')}`;
    },
    editProduct(product) {
      this.$emit('edit-product', product);
    },
    async deleteProduct(productId) {
      if (!confirm('Are you sure you want to delete this product?')) {
        return;
      }
      const res = await fetch(`/api/products/${productId}`, {
        method: 'DELETE'
      });
      if (res.ok) {
        alert('Product deleted successfully!');
        this.fetchProducts();
      } else {
        alert('Failed to delete product');
      }
    }
  }
};
</script>

<style scoped>
.products-list-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.header-section {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.filters-section {
  display: flex;
  gap: 1.5rem;
  flex-wrap: wrap;
  background: #ffffff;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
  min-width: 200px;
}

.filter-label {
  font-weight: 500;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.filter-select {
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
  background-color: #ffffff;
}

.filter-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.products-section {
  margin-top: 2rem;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.product-card {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: box-shadow 0.2s;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

.product-image {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background-color: #f3f4f6;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-content {
  padding: 1.25rem;
  flex: 1;
}

.product-name {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.product-description {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.75rem;
  line-height: 1.5;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #3b82f6;
  margin-bottom: 0.75rem;
}

.product-categories {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.category-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  background-color: #e0e7ff;
  color: #4338ca;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-message {
  font-size: 1.125rem;
  color: #6b7280;
}

.product-actions {
  display: flex;
  gap: 0.5rem;
  padding: 1rem 1.25rem;
  border-top: 1px solid #e5e7eb;
}

.btn-edit,
.btn-delete {
  flex: 1;
  padding: 0.625rem 1rem;
  border: none;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-edit {
  background-color: #3b82f6;
  color: white;
}

.btn-edit:hover {
  background-color: #2563eb;
}

.btn-delete {
  background-color: #ef4444;
  color: white;
}

.btn-delete:hover {
  background-color: #dc2626;
}

@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .filters-section {
    flex-direction: column;
  }
  
  .filter-group {
    min-width: 100%;
  }
}
</style>
