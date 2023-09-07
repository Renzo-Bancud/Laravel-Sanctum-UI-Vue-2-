<template>
  <div>
    <Loader v-if="isLoading" />

    <!-- Navigation-->
    <Navbar />

    <!-- Header-->
    <header class="bg-dark py-5">
      <div class="container px-4 px-lg-5 my-5">
        <div class="text-center">
          <h1 class="display-4 text-white text-uppercase fw-bold">On Sale Items</h1>
        </div>
      </div>
    </header>

    <!-- Section-->
    <section class="py-5">

      <div class="container px-4 px-lg-5">
        <div class="d-flex flex-wrap mb-4">
          <div class="p-2 mt-2">Show</div>

          <div class="p-2 flex-grow-1">
            <select v-model="paginate" class="form-select w-100">
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="30">30</option>
            </select>
          </div>

          <div class="p-2 flex-grow-1">
            <select name="category" id="product_category" class="form-control w-100" v-model="form.selectCategory"
              @change="getCategory">
              <option style="display: none" value="" selected>Pick Categories</option>
              <option value="">All</option>
              <option :value="category.id" v-for="category in categories" :key="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="p-2 flex-grow-1">
            <input type="text" id="search-orders" name="searchorders" v-model="form.search" @keyup="search"
              class="form-control w-100" placeholder="What are you looking for?" />
          </div>
        </div>


        <div class="row g-4">
          <div v-if="!products.data || Object.keys(products.data).length === 0" class="col-12 w-100">
            <div class="card border-0 h-100">
              <div class="card-body text-center">
                <div class="mt-5">
                  <span class="iconify display-1" data-icon="tabler:shopping-cart-off"></span>
                  <p class="mt-3 fw-bold">There is no product found</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-xl-3 col-xxl-2" v-else v-for="product in products.data" :key="product.id">

            <div class="app-card app-card-doc shadow-sm  h-100 hover-pulse">

              <div class="app-card-thumb-holder p-3">
                <router-link :to="{ name: 'item-details', params: { slug: product.slug } }" class="text-decoration-none">
                  <div class="app-card-thumb">

                    <!-- Category badge-->
                    <div class="d-flex justify-content-between" v-if="product.isOnSale != null">
                      <div class="badge bg-dark text-white position-absolute"
                        style="top: 0.5rem; left: 0.5rem;width:70px;">{{ product.discount_percentage }}% Off</div>
                      <div class="badge bg-warning position-absolute text-dark" style="top: 0.5rem; right: 0.5rem">Sale
                      </div>
                    </div>


                    <img v-if="product.main_picture" :src="product.main_picture" class="thumb-image"
                      :alt="product.title" />
                    <img v-else :src="require('/storage/img-icon/no-image-icon.png')" alt="No Image"
                      class="thumb-image" />

                  </div>
                </router-link>
              </div>

              <div class="app-card-body p-3 has-card-actions">
                <div class="badge bg-info text-white position-absolute" style="top: 0.5rem;">{{
                  product.category.name }}</div>

                <h4 class="app-doc-title truncate mb-0 mt-3">
                  <router-link :to="{ name: 'item-details', params: { slug: product.slug } }"
                    class="text-decoration-none">
                    {{
                      product.title.length > 16
                      ? product.title.substring(0, 16) + "..."
                      : product.title
                    }}
                  </router-link>
                </h4>
                <div class="app-doc-meta text-center">
                  <ul class="list-unstyled mb-0">
                    <li>
                      <div class="rating-stars mb-3">
                        <span v-for="star in 5" :key="star"
                          :class="{ 'star-filled': star <= getAverageRating(product.id) }">
                          <i class="fas fa-star"></i>
                        </span>
                      </div>
                    </li>
                    <li>
                      <div v-if="product.isOnSale != null">

                        <div class="d-md-flex justify-content-md-between align-items-md-center">
                          <div>
                            <span class="text-dark fw-bold">
                              <span class="iconify mt-n1" style="position: relative; top: -3px;"
                                data-icon="mdi:philippine-peso"></span>
                              {{ salePrice(product) }}
                            </span>
                          </div>

                          <div class="mt-2 mt-md-0">
                            <span class="old-price mb-1">
                              <del>
                                <span class="old-price-discount text-danger" style="text-decoration:line-through !important;">
                                  <span class="iconify" data-icon="pepicons-pop:peso"
                                    style="position: relative; top: -3px;"></span>
                                  {{ product.price }}
                                </span>
                              </del>
                              <span class="old-price-discount text-danger">
                                ({{ product.discount_percentage }}% off)
                              </span>
                            </span>
                          </div>
                        </div>

                      </div>

                      <div v-else>
                        <span class="text-dark fw-bold"> <span class="iconify mt-n1"
                            style="position: relative; top: -3px;" data-icon="mdi:philippine-peso"></span>{{
                              product.price
                            }}</span>
                      </div>
                    </li>
                    <li class="mt-3">
                      <router-link :to="{ name: 'item-details', params: { slug: product.slug } }"
                        class="btn btn-outline-dark mt-auto btn-sm">Buy now</router-link>
                    </li>
                  </ul>
                </div>
                <!--//app-doc-meta-->
                <div class="app-card-actions d-none">
                  <div class="badge bg-dark text-white" v-if="product.isOnSale != null">Sale
                  </div>
                </div>
              </div>
              <!--//app-card-body-->
            </div>
            <!--//app-card-->
          </div>

        </div>
        <!--//row-->

      </div>
    </section>

  <Footer />

  </div>
</template>

<script>
import { Form as CustomForm } from "vform";
import Navbar from "../template/shop/Navbar.vue";
import Footer from "../template/shop/Footer.vue";
import salePriceMixin from '../mixins/salePriceMixin';

export default {
  mixins: [salePriceMixin],
  components: {
    Navbar,
    Footer,
    CustomForm
  },
  data() {
    return {
      products: {},
      paginate: 10,
      form: {
        search: "",
        selectCategory: "",
      },
      categories: [],
      cartItems: [],
      slogan: [],
      activeIndex: 0, // To keep track of the active carousel item
      averageRating: [],
    };
  },
  watch: {
    paginate: function (value) {
      this.loadSaleProducts();
    },
  },

  methods: {

    async loadSaleProducts(page = 1, search = "", category = "") {
      try {
        // Show the loading overlay
        this.$store.dispatch('showLoader');

        const params = {
          page: page,
          paginate: this.paginate,
          search: search,
          category: category
        };

        const response = await axios.get("/api/sale-products", { params });
        this.products = response.data;
        response.data.data.forEach((product) => {
          this.loadRatings(product.id);
        });

      } catch (error) {
        // Handle errors here if needed
        console.error(error);
      } finally {
        // Hide the loading overlay
        this.$store.dispatch('hideLoader');
      }
    },


    search() {
      this.loadSaleProducts(1, this.form.search);
    },

    async loadRatings(product_id) {
      try {
        let { data } = await axios.get("/api/load-ratings/" + product_id);
        // Push the product's average rating to the averageRating array
        this.averageRating.push({ productId: product_id, rating: data.averageRating });
      } catch (error) {
        console.log(error);
      }
    },



    getAverageRating(productId) {
      // Find the product rating in the averageRating array
      const productRating = this.averageRating.find((rating) => rating.productId === productId);
      // Return the product's average rating if found, otherwise return 0
      return productRating ? productRating.rating : 0;
    },

    async getCategory() {
      this.loadSaleProducts(1, this.form.search || "", this.form.selectCategory);
    },


    loadCategories() {
      axios.get("/api/product-categories").then((response) => {
        this.categories = response.data;
      });
    },



  },

  computed: {
    auth() {
      return this.$store.getters.getAuthenticated;
    },
    user() {
      return this.$store.getters.getUser;
    },
    isLoading() {
      return this.$store.state.isLoading;
    },
  },

  mounted() {
    this.loadSaleProducts(1, "", this.form.selectCategory);
    this.loadCategories();
  },

};
</script>
