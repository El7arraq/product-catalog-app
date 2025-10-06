<template>
  <div class="product-form-container">
    <h2 class="product-form-title">{{ isEditMode ? 'Edit Product' : 'Create Product' }}</h2>
    
    <form @submit.prevent="submitForm" class="product-form-box">
      <div class="product-form-field">
        <label for="product-name" class="product-form-label">Name:</label>
        <input 
          id="product-name" 
          v-model="form.name" 
          placeholder="Name" 
          required 
          class="product-form-input" 
        />
      </div>

      <div class="product-form-field">
        <label for="product-description" class="product-form-label">Description:</label>
        <textarea 
          id="product-description" 
          v-model="form.description" 
          placeholder="Description" 
          required 
          rows="4"
          class="product-form-textarea"
        ></textarea>
      </div>

      <div class="product-form-field">
        <label for="product-price" class="product-form-label">Price:</label>
        <input 
          id="product-price" 
          v-model.number="form.price" 
          type="number" 
          step="0.01"
          min="0"
          placeholder="0.00" 
          required 
          class="product-form-input" 
        />
      </div>

      <div class="product-form-field">
        <label for="product-image" class="product-form-label">Image URL:</label>
        <input 
          id="product-image" 
          v-model="form.image" 
          placeholder="Image URL" 
          class="product-form-input" 
        />
      </div>

      <div class="product-form-field">
        <label for="product-categories" class="product-form-label">Categories:</label>
        <select 
          id="product-categories" 
          v-model="form.categories" 
          multiple 
          class="product-form-select"
          size="5"
        >
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
        <small class="product-form-hint">Hold Ctrl/Cmd to select multiple categories</small>
      </div>

      <div class="form-actions">
        <button type="submit" class="product-form-button">
          {{ isEditMode ? 'Update Product' : 'Create Product' }}
        </button>
        <button v-if="isEditMode" type="button" @click="cancelEdit" class="cancel-button">
          Cancel
        </button>
      </div>
    </form>

    <div v-if="errors" class="product-form-errors">
      <h3 class="error-title">Please fix the following errors:</h3>
      <ul class="error-list">
        <li v-for="(error, field) in errors" :key="field" class="error-item">
          <strong>{{ field }}:</strong> {{ error.join(', ') }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    editProduct: {
      type: Object,
      default: null
    }
  },
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
      errors: null,
      isEditMode: false,
      editProductId: null
    };
  },
  watch: {
    editProduct: {
      handler(newProduct) {
        if (newProduct) {
          this.loadProductForEdit(newProduct);
        }
      },
      immediate: true
    }
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
      
      if (this.isEditMode) {
        // Update product
        const res = await fetch(`/api/products/${this.editProductId}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        });
        const data = await res.json();
        if (res.ok) {
          alert('Product updated successfully!');
          this.resetForm();
          this.$emit('product-updated');
        } else if (data.errors) {
          this.errors = data.errors;
        }
      } else {
        // Create product
        const res = await fetch('/api/products', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        });
        const data = await res.json();
        if (res.status === 201) {
          alert('Product created successfully!');
          this.resetForm();
          this.$emit('product-created');
        } else if (data.errors) {
          this.errors = data.errors;
        }
      }
    },
    loadProductForEdit(product) {
      this.form.name = product.name;
      this.form.description = product.description;
      this.form.price = product.price;
      this.form.image = product.image || '';
      this.form.categories = product.categories ? product.categories.map(cat => cat.id) : [];
      this.isEditMode = true;
      this.editProductId = product.id;
      // Scroll to top of form
      this.$el.scrollIntoView({ behavior: 'smooth' });
    },
    resetForm() {
      this.form = { name: '', description: '', price: '', image: '', categories: [] };
      this.isEditMode = false;
      this.editProductId = null;
      this.errors = null;
    },
    cancelEdit() {
      this.resetForm();
      this.$emit('edit-cancelled');
    }
  }
};
</script>

<style scoped>
.product-form-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.product-form-title {
  font-size: 1.75rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.product-form-box {
  background: #ffffff;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.product-form-field {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.product-form-label {
  font-weight: 500;
  font-size: 0.875rem;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.product-form-input,
.product-form-textarea,
.product-form-select {
  width: 100%;
  box-sizing: border-box;
  padding: 0.625rem 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.product-form-input:focus,
.product-form-textarea:focus,
.product-form-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.product-form-textarea {
  resize: vertical;
  min-height: 100px;
  font-family: inherit;
}

.product-form-input-file {
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  cursor: pointer;
}

.product-form-select {
  cursor: pointer;
}

.product-form-hint {
  font-size: 0.75rem;
  color: #6b7280;
  font-style: italic;
}

.form-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.product-form-button {
  flex: 1;
  padding: 0.75rem 1.5rem;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.product-form-button:hover {
  background-color: #2563eb;
}

.product-form-button:active {
  background-color: #1d4ed8;
}

.cancel-button {
  padding: 0.75rem 1.5rem;
  background-color: #6b7280;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.cancel-button:hover {
  background-color: #4b5563;
}

.product-form-errors {
  margin-top: 1.5rem;
  padding: 1rem;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 6px;
}

.error-title {
  font-size: 1rem;
  font-weight: 600;
  color: #991b1b;
  margin-bottom: 0.75rem;
}

.error-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.error-item {
  padding: 0.5rem 0;
  color: #dc2626;
  font-size: 0.875rem;
}

.error-item strong {
  text-transform: capitalize;
}
</style>
