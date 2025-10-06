<template>
  <div>
    <product-form 
      :edit-product="editProduct" 
      @product-created="onProductChanged"
      @product-updated="onProductUpdated"
      @edit-cancelled="onEditCancelled"
    />
    <product-list 
      ref="productList"
      @edit-product="onEditProduct" 
    />
  </div>
</template>

<script>
import ProductForm from './ProductForm.vue';
import ProductList from './ProductList.vue';

export default {
  components: {
    ProductForm,
    ProductList
  },
  data() {
    return {
      editProduct: null
    };
  },
  methods: {
    onEditProduct(product) {
      this.editProduct = product;
    },
    onProductChanged() {
      // Refresh the product list when a new product is created
      this.$refs.productList.fetchProducts();
      this.editProduct = null;
    },
    onProductUpdated() {
      // Refresh the product list when a product is updated
      this.$refs.productList.fetchProducts();
      this.editProduct = null;
    },
    onEditCancelled() {
      this.editProduct = null;
    }
  }
};
</script>
