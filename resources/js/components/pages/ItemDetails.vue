<template>
  <div id="fromToTop">

    <Loader v-if="isLoading" />

    <!-- Navigation-->
    <!-- <Navbar :cartItems="cartItems" /> i can use this if you want to pass props data to other componet or child component but if you want global access data use store vuew using state and actions -->

    <Navbar :productData="product" />

    <!-- Product section-->
    <section class="py-3">
      <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 align-items-center">
          <div class="col-md-6">

            <div class="position-relative" v-if="selectedProducts.isOnSale === null || product.isOnSale === null">
              <div class="d-flex justify-content-between">
                <div>
                  <h2 class="position-absolute d-lg-none d-block" style="top: 0.5rem; left: 0.5rem">
                    <i class="iconify fs-5" style="position: relative; top: -4px;left:5px;"
                      data-icon="mdi:philippine-peso"></i>

                    {{ selectedProducts.price ||
                      product.price }}
                  </h2>
                </div>
                <div>
                  <span class="badge bg-primary position-absolute text-white" style="top: 0.7rem; right: 0.5rem">
                    {{ product.category.name }}
                  </span>
                </div>
              </div>
              <img v-if="selectedProducts || product" class="card-img-top mb-1 mb-md-0 w-100"
                :src="selectedImage || selectedProducts.main_picture || product.main_picture" alt="..." />
            </div>

            <div v-else class="position-relative">
              <div class="d-flex justify-content-between" v-if="product.isOnSale != null">
                <div>
                  <span class="badge bg-dark text-white position-absolute"
                    style="top: 0.5rem; left: 0.5rem;width:70px;">{{
                      product.discount_percentage }}% Off</span>

                  <div class="d-lg-none d-block">
                    <h2 class="position-absolute" style="top:2rem; left: 0.1rem">
                      <i class="iconify fs-5" style="position: relative; top: -4px;left:5px;"
                        data-icon="mdi:philippine-peso"></i>
                      {{
                        ((selectedProducts && selectedProducts.price) || (product && product.price)) -
                        (((selectedProducts && selectedProducts.price) || (product && product.price)) *
                          ((selectedProducts && selectedProducts.discount_percentage) || (product &&
                            product.discount_percentage)
                            / 100))
                      }}
                    </h2>
                  </div>

                </div>
                <div>

                  <span class="badge bg-warning position-absolute text-dark" style="top: 0.5rem; right: 0.5rem">Sale
                  </span>

                  <span class="badge bg-primary position-absolute text-white" style="top: 2rem; right: 0.5rem">{{
                    product.category.name }}
                  </span>

                </div>
              </div>

              <img v-if="selectedProducts || product" class="card-img-top mb-1 mb-md-0 w-100"
                :src="selectedImage || selectedProducts.main_picture || product.main_picture" alt="..." />
            </div>


            <div class="row row-cols-2 row-cols-sm-4 my-3 previews">
              <div class="col" v-for="(image, index) in getImageUrls()" :key="index">
                <img class="w-100 mobile-image img-hover mb-3" @click="selectedImage = image" :src="image"
                  alt="Product Image">
              </div>
            </div>




          </div>
          <div class="col-md-6">
            <div class="small mb-1" v-if="selectedProducts.isOnSale != null || product.isOnSale != null">
              <span class="old-price mb-1">
                <del>
                  <span class="old-price-discount text-danger" style="text-decoration: line-through !important;">
                    <span class="iconify" data-icon="pepicons-pop:peso" style="position: relative; top: -3px;"></span>
                    {{ selectedProducts.price || product.price }}
                  </span>
                </del>
                <span class="old-price-discount text-danger">
                  ({{ selectedProducts.discount_percentage || product.discount_percentage }}% off)
                </span>
              </span>
            </div>
            <h1 class="display-5 fw-bolder" v-if="selectedProducts || product">
              {{ selectedProducts.title || product.title }}
            </h1>
            <div class="fs-4 mb-1 d-none d-lg-block">
              <span v-if="selectedProducts || product">
                <span class="iconify fs-5" style="position: relative; top: -4px" data-icon="mdi:philippine-peso"></span>
                <span v-if="selectedProducts.isOnSale === null || product.isOnSale === null" class="position-left lead">{{
                  selectedProducts.price ||
                  product.price }}</span>
                <span v-else class="position-left lead">{{

                  ((selectedProducts && selectedProducts.price) || (product && product.price)) -
                  (((selectedProducts && selectedProducts.price) || (product && product.price)) *
                    ((selectedProducts && selectedProducts.discount_percentage) || (product && product.discount_percentage)
                      / 100))

                }}</span>
              </span>
            </div>
            <p class="lead justify-desc">
              {{ selectedProducts.description || product.description }}
            </p>

            <div class="form-group row mb-2 add pb-0">
              <span for="variations" class="lead">Variations:</span>
              <div class="d-flex justify-content-between">
                <div><small class="mb-2 text-danger text-dark">*Choose variation from first to last</small></div>
                <div><button @click="relodOptions()" class="btn btn-dark hover-pulse" style="font-size:12px;">Reset Boxes</button></div>
              </div>
             
              <!-- Check if variations array is empty -->
              <template v-if="Object.keys(groupedVariations).length === 0">
                <p class="text-muted mt-2">No variation found!</p>
              </template>
              <template v-else>
                <!-- Loop through the groupedVariations array -->
                <div class="col-md-4 mb-2" v-for="(variationName, variationId) in groupedVariations" :key="variationId">
                  <!-- Add a conditional check to ensure variationName is not null -->
                  <h6 class="text-uppercase">{{ variationName }}</h6>
                  <template v-if="groupedOptions[variationId] && groupedOptions[variationId].length > 0">
                    <!-- Loop through the options array for the current variationId -->
                    <div v-for="(option, index) in groupedOptions[variationId]" :key="index">
                      <input type="checkbox" :id="option.option" :value="option"
                        :checked="isOptionSelected(variationId, option.option)"
                        :disabled="isOptionDisabled(variationId, option.option_id)"
                        @change="updateSelect(variationId, option.option, option.option_id)" />
                      <label :for="option.option">{{ option.option }}</label>
                    </div>
                  </template>
                  <template v-else>
                    <p class="text-muted mt-2">No options available</p>
                  </template>
                </div>

              </template>
            </div>

        
            <h6 class="lead">Total Quantity: {{ totalQuantity }}</h6>

            <div class="d-flex justify-content-between">
              <div class="d-flex">
                <input v-if="selectedProducts || product" class="form-control text-center me-1" id="inputQuantity"
                  readonly type="num" :value="remainingQuantity" style="max-width: 4rem" />


                <div v-if="auth">
                  <button class="btn btn-outline-dark flex-shrink-0 me-1" type="button"
                    style="cursor:not-allowed;opacity:0.5;" v-if="remainingQuantity == 0">
                    <i class="bi-cart-fill me-1"></i>
                    Add to cart
                  </button>

                  <button class="btn btn-outline-dark flex-shrink-0 me-1" type="button" v-else
                    @click.prevent="addToCartHandler()">
                    <i class="bi-cart-fill me-1"></i>
                    Add to cart
                  </button>

                  <button class="btn btn-success flex-shrink-0" type="button" @click.prevent="buyHandlerAuth()"
                    v-if="remainingQuantity != 0">Buy</button>
                </div>
                <div v-else>

                  <button class="btn btn-outline-dark flex-shrink-0 me-1" type="button"
                    style="cursor:not-allowed;opacity:0.5;" v-if="remainingQuantity == 0">
                    <i class="bi-cart-fill me-1"></i>
                    Add to cart
                  </button>

                  <button class="btn btn-outline-dark flex-shrink-0 me-1" type="button" @click.prevent="buyHandler"
                    v-else>
                    <i class="bi-cart-fill me-1"></i>
                    Add to cart
                  </button>

                  <button class="btn btn-success flex-shrink-0" type="button" @click.prevent="buyHandler"
                    v-if="remainingQuantity != 0">Buy</button>
                </div>

              </div>

              <div>
                <router-link :to="{ name: 'home' }" class="btn btn-secondary hover-pulse d-none">Shop</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- Related items section-->
    <section class="py-2 bg-light">
      <div class="container px-4 px-lg-5 mt-1">
        <div class="row">
          <div class="col-md-12">
            <h2 class="fw-bolder mb-4">Related products</h2>
            <div id="carouselExampleDark" class="carousel carousel-dark slide">
              <div class="carousel-indicators" v-if="!relatedProducts || relatedProducts.length > 0">
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active"
                  aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1"
                  aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2"
                  aria-label="Slide 3"></button>
              </div>
              <div class="carousel-inner">
                <div v-if="!relatedProducts || relatedProducts.length === 0">
                  <div class="carousel-item active">
                    <div class="mt-5 text-center">
                      <span class="iconify display-1" data-icon="tabler:shopping-cart-off"></span>
                      <p class="mt-3 fw-bold">There is no related product found</p>
                    </div>
                  </div>
                </div>
                <div v-else>
                  <div v-for="(chunkedProducts, index) in chunkedRelatedProducts" :key="chunkedProducts[0].id">
                    <div class="item  carousel-item active" data-bs-interval="10000"
                      :class="{ 'carousel-item': true, 'active': currentIndex === index }">
                      <div class="row">

                        <div class="col-lg-3 mb-3" v-for="related_product in chunkedProducts" :key="related_product.id">
                          <div class="thumb-wrapper">

                            <!-- Category badge-->
                            <div class="mb-3">
                              <div class="badge bg-info text-white position-absolute" style="top: 0.5rem; left: 0.5rem">
                                {{ related_product.category.name }}
                              </div>
                              <!-- Sale badge-->

                              <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem"
                                v-if="related_product.isOnSale != null">
                                {{ related_product.discount_percentage }}% off
                              </div>
                            </div>

                            <div class="img-box">

                              <img v-if="related_product.main_picture" :src="related_product.main_picture"
                                class="img-fluid img-hover" :alt="related_product.title" />
                              <img v-else :src="require('/storage/img-icon/no-image-icon.png')" alt="No Image"
                                class="img-fluid img-hover" />

                            </div>
                            <div class="thumb-content">
                              <h4>{{ related_product.title }}</h4>
                              <div class="star-rating">
                                <ul class="list-inline d-none">
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                  <li class="list-inline-item"><i class="fa fa-star"></i></li>
                                  <li class="list-inline-item"><i class="fa fa-star-o"></i></li>
                                </ul>
                              </div>
                              <div v-if="related_product.isOnSale != null">
                                <p class="item-price"><strike><span class="iconify mt-n1"
                                      style="position: relative; top: -3px;" data-icon="mdi:philippine-peso"></span>{{
                                        related_product.price }}</strike> <b><span class="iconify mt-n1"
                                      style="position: relative; top: -3px;" data-icon="mdi:philippine-peso"></span>
                                    {{

                                      ((related_product.price)) -
                                      (((related_product.price)) *
                                        ((related_product.discount_percentage) / 100))


                                    }}


                                  </b></p>
                              </div>
                              <div v-else>

                                <p class="item-price"><b><span class="iconify mt-n1"
                                      style="position: relative; top: -3px;" data-icon="mdi:philippine-peso"></span> {{
                                        related_product.price }}</b></p>

                              </div>

                              <a href="#0" class="btn btn-primary"
                                @click="loadSelectedProductData(related_product.slug)">Buy
                                now</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark"
                data-bs-slide="prev" @click="handlePrevious">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark"
                data-bs-slide="next" @click="handleNext">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Ratings-->
    <section class="py-5 bg-light">
      <div class="container px-4 px-lg-5 mt-1">
        <h2 class="fw-bolder mb-4">Product Ratings</h2>
        <div class="card border-0 h-100">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-4 text-center">
                <h1 class="text-warning mt-4 mb-4">
                  <b>
                    <span id="average_rating">{{ typeof averageRating === 'number' ? averageRating.toFixed(1) : 'N/A'
                    }}</span>
                    <small style="font-size:25px;">out of 5</small>
                  </b>
                </h1>
                <div class="rating-stars mb-3">
                  <span v-for="star in 5" :key="star" :class="{ 'star-filled': star <= averageRating }">
                    <i class="fas fa-star"></i>
                  </span>
                </div>
                <h3>
                  <span id="total_review">{{ totalRatings }}</span>
                  Review
                </h3>
              </div>

              <div class="col-sm-4">
                <div class="py-4">
                  <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="mb-2">
                    <div class="progress-label-left">
                      <b>{{ star }}</b>
                      <i class="fas fa-star text-warning"></i>
                    </div>
                    <div class="progress-label-right">
                      <span :id="'total_' + star + '_star_review'">({{ getStarReviewCount(star) }})</span>
                    </div>
                    <div class="progress">
                      <div :id="star + '_star_progress'" class="progress-bar bg-warning" role="progressbar"
                        :aria-valuenow="getStarReviewPercentage(star)" aria-valuemin="0" aria-valuemax="100"
                        :style="{ width: getStarReviewPercentage(star) + '%' }"></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="row py-4">
                  <div class="col-sm-4" v-for="star in [5, 4, 3, 2, 1].concat(['All'])" :key="star">
                    <div class="mb-2 d-flex">
                      <button class="btn btn-outline-dark btn-sm flex-fill mx-1" @click="showFeedbacks(star)">
                        <b>{{ star === 'All' ? 'All Review' : star + ' star' }}</b>
                        <span :id="'total_' + star + '_star_review'" v-if="star !== 'All'">({{ getStarReviewCount(star)
                        }})</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>



            <div v-if="viewFeedbacks" class="mt-4">

              <ol class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-start" v-for="feedback in feedbacks"
                  :key="feedback.cart_item_id">
                  <div v-if="feedback.profile == null">
                    <span class="iconify fs-1 mt-1" data-icon="healthicons:ui-user-profile-negative"></span>
                  </div>
                  <div v-else>
                    <img :src="feedback.profile" alt="Profile" style="object-fit:contain;height:100px;width:100px;" />
                  </div>
                  <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ feedback.name }}</div>
                    {{ feedback.feedback }}
                  </div>

                  <span v-for="star in 5" :key="star" :class="{ 'star-filled': star <= feedback.rate_star }">
                    <i class="fas fa-star"></i>
                  </span>
                </li>
              </ol>

            </div>

            <div v-else class="mt-4">

              <ol class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-start" v-for="all in ratings"
                  :key="all.cart_item_id">
                  <div v-if="all.profile == null">
                    <span class="iconify fs-1 mt-1" data-icon="healthicons:ui-user-profile-negative"></span>
                  </div>
                  <div v-else>
                    <img :src="all.profile" alt="Profile" style="object-fit:contain;height:100px;width:100px;" />
                  </div>
                  <div class="ms-2 me-auto">
                    <div class="fw-bold">{{ all.name }}</div>
                    {{ all.feedback }}
                  </div>

                  <span v-for="star in 5" :key="star" :class="{ 'star-filled': star <= all.rate_star }">
                    <i class="fas fa-star"></i>
                  </span>
                </li>
              </ol>

            </div>


          </div>
        </div>
      </div>
    </section>

    <Footer />

  </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import Navbar from "../template/shop/Navbar.vue";
import salePriceMixin from '../mixins/salePriceMixin';
import Footer from "../template/shop/Footer.vue";

export default {
  mixins: [salePriceMixin],
  components: {
    Navbar,
    Footer
  },
  data() {
    return {
      selectedOptions: {},
      quantity_in: [],
      quantity_out: [],
      defaultQty: '',
      product: [],
      groupedVariations: [],
      groupedOptions: [],
      mycart: [],
      relatedProducts: [],
      selectedProducts: [],
      ratings: [],
      totalRatings: [],
      averageRating: [],
      customerRating: [],
      feedbacks: [],
      viewFeedbacks: false,
      selectedImage: '',
      currentIndex: 0,
      defaultQty: 0,
      deliveryID: '',
      totalQuantity: '',
    };
  },
  watch: {
    selectedSlug: {
      immediate: true,
      handler(slug) {
        if (slug) {
          this.fetchProductData(slug);
        }
      },
    },
  },
  methods: {
    ...mapActions(["addToCart"]),
    ...mapActions(["addToCartSelected"]),
    loginAccount() {
      this.$router.push({ name: "login" });
    },

    async loadProductData() {
      try {
        let slug = this.$route.params.slug;
        let { data } = await axios.get("/api/product/item/" + slug);
        this.$store.dispatch('loadMyCart');
        this.product = data.product;
        this.groupedVariations = data.groupedVariations
        this.groupedOptions = data.groupedOptions
        this.loadRatings(this.product.product_id)
        this.loadRelatedProduct(this.product.category_id, this.product.slug);
        this.mycart = this.$store.state.myItems;
        this.totalQuantity = this.calculateSumLastOptionGroupQtyIn();
      } catch (error) {
        console.error(error);
      }
    },

    relodOptions() { 
      // Reset the selectedOptions object
      this.selectedOptions = {};
      // Reset quantity_in, quantity_out, and deliveryID
      this.quantity_in = 0;
      this.quantity_out = null; // If you want to reset quantity_out to null, use this line; otherwise, set it to 0 or any other default value you prefer
      this.deliveryID = null; // If you want to reset deliveryID to null, use this line; otherwise, set it to 0 or any other default value you prefer
    },

    // calculateSumLastOptionGroupQtyIn() {
    //   const lastOptionGroupKey = Object.keys(this.groupedOptions)[Object.keys(this.groupedOptions).length - 1];
    //   const lastOptionGroup = this.groupedOptions[lastOptionGroupKey];
    //   if (lastOptionGroup) {
    //     return lastOptionGroup.reduce((total, option) => total + option.qty_in, 0);
    //   } else {
    //     return 0; // Return 0 if the lastOptionGroup is not found or is empty
    //   }
    // },


    calculateSumLastOptionGroupQtyIn() {
      const lastOptionGroupKey = Object.keys(this.groupedOptions)[Object.keys(this.groupedOptions).length - 1];
      const lastOptionGroup = this.groupedOptions[lastOptionGroupKey];

      if (lastOptionGroup) {
        // Calculate the sum of 'qty_in' for the last option group
        const sumQtyIn = lastOptionGroup.reduce((total, option) => total + option.qty_in, 0);

        // Calculate the sum of 'qty_out' for the last option group
        const sumQtyOut = lastOptionGroup.reduce((total, option) => total + (option.qty_out || 0), 0);

        // Deduct the 'qty_out' from the sum of 'qty_in' to get the final result
        return sumQtyIn - sumQtyOut;
      } else {
        return 0; // Return 0 if the lastOptionGroup is not found or is empty
      }
    },

    isOptionSelected(variationId, optionValue) {
      return (
        this.selectedOptions[variationId] &&
        this.selectedOptions[variationId].option === optionValue
      );
    },

    isOptionDisabled(variationId, optionId) {
      // Check if there is a selected option in the first group
      const selectedFirstOption = this.selectedOptions[Object.keys(this.groupedVariations)[0]];

      // If there is a selected option in the first group
      if (selectedFirstOption) {
        // Get the option_id of the selected option in the first group
        const selectedFirstOptionId = selectedFirstOption.option_id;

        // If the current option's option_id does not match the selected option's option_id in the first group, disable it
        if (optionId !== selectedFirstOptionId) {
          return true;
        }
      }

      // If no option is selected in the first group or the selected option matches the current option's option_id, enable it
      return false;
    },

    updateSelect(variationId, optionValue, optionId) {
      const lastOptionGroup = Object.keys(this.groupedOptions)[Object.keys(this.groupedOptions).length - 1];


      // // Check if the last option group is selected
      // if (this.selectedOptions[lastOptionGroup]) {
      //   this.checkboxMesasge = 'Select variation from first to last';
      // } else if (this.selectedOptions[firstOptionGroup]) {
      //   this.checkboxMesasge = '';
      // } else {
      //   this.checkboxMesasge = '';
      // }

      // Check if the clicked option is already selected
      const isSelected = this.isOptionSelected(variationId, optionValue);

      // If the clicked option is already selected, deselect it
      if (isSelected) {
        this.selectedOptions[variationId] = null;
        this.quantity_in = 0;
      } else {
        // If the clicked option is not selected, find and select the corresponding option
        const options = this.groupedOptions[variationId];
        let foundOption = null;
        for (const option of options) {
          if (option.option === optionValue && option.option_id === optionId) {
            foundOption = option;
            break;
          }
        }

        if (foundOption) {
          this.selectedOptions[variationId] = foundOption;
          this.quantity_in = this.selectedOptions[variationId].qty_in;
          this.quantity_out = this.selectedOptions[variationId].qty_out;
          this.deliveryID = this.selectedOptions[variationId].delivery_id;
        } else {
          this.selectedOptions[variationId] = null;
          this.quantity_in = 0;
        }

      }
    },

    async loadRelatedProduct(category_id, slug) {
      try {
        let { data } = await axios.get(
          "/api/product/get-related-product/" + category_id + "/" + slug
        );
        this.relatedProducts = data;
      } catch (error) {
        // Handle the error if the API call fails
        console.error(error);
      }
    },

    async loadSelectedProductData(slug) {
      this.$router.push({ name: "item-details", params: { slug: slug } });
      this.loadProductData();
      const goTop = document.getElementById("fromToTop");
      goTop.scrollIntoView({ behavior: "smooth" });
    },

    async fetchProductData(slug) {
      try {
        const response = await axios.get(`/api/product/item/${slug}`);
        this.product = response.data;
        this.loadRelatedProduct(this.product.category_id, this.product.slug);
      } catch (error) {
        console.error("Error loading product data:", error);
      }
    },

    async loadRatings(product_id) {
      try {
        let { data } = await axios.get("/api/load-ratings/" + product_id);
        this.ratings = data.ratings;
        this.totalRatings = data.totalRatings;
        this.averageRating = data.averageRating;
      } catch (error) {
        console.log(error);
      }
    },

    getStarReviewCount(star) {
      // Calculate and return the count of reviews for the given star rating
      // You can use your own logic or retrieve the data from an API or database
      // Here's an example using a computed property 'ratings' that holds the review data
      return this.ratings.filter(review => review.rate_star === star).length;
    },
    getStarReviewPercentage(star) {
      // Calculate and return the percentage of reviews for the given star rating
      // You can use your own logic or retrieve the data from an API or database
      // Here's an example using a computed property 'totalRatings' that holds the total review count
      const count = this.getStarReviewCount(star);
      return (count / this.totalRatings) * 100;
    },


    async showFeedbacks(star) {
      const product_id = this.product.product_id;
      let response = await axios.get("/api/show-feedback/" + product_id + "/" + star);
      let { data } = response;
      this.viewFeedbacks = true;
      this.feedbacks = data.get_feedbacks;
      this.customerRating = data.customerRating;
    },

    async addToCartHandler() {
      if (this.remainingQuantity > 0) {
        const product_prodid = Array.isArray(this.product) ? this.product.map(productItem => productItem.delivery_id) : [this.product.delivery_id];
        const mycart_prodid = this.mycart.map(mycartItem => mycartItem.delivery_id);
        const isItemAlreadyInCart = product_prodid.some(id => mycart_prodid.includes(id));

        if (isItemAlreadyInCart) {
          this.$swal.fire({
            position: 'top-middle',
            icon: 'info',
            title: 'Item is already on your cart',
            showConfirmButton: false,
            timer: 3000,
            customClass: {
              popup: 'my-custom-alert',
              title: 'my-custom-alert-title',
            }
          });

        } else {


          // Check if the last option group is selected
          const lastOptionGroup = Object.keys(this.groupedOptions)[Object.keys(this.groupedOptions).length - 1];
          if (!this.selectedOptions[lastOptionGroup]) {
            // Show an alert to prompt the user to select an option from the last group
            this.$swal.fire({
              position: 'top-middle',
              icon: 'info',
              title: 'Please select an option.',
              showConfirmButton: false,
              timer: 3000,
              customClass: {
                popup: 'my-custom-alert',
                title: 'my-custom-alert-title',
              }
            });
            return; // Exit the function without adding to cart
          }


          const cart_payload = {
            item: this.product.product_id,
            delivery_id: this.deliveryID,
          };

          try {
            await axios.post("/api/my-cart", cart_payload);
            this.fetchCart

            this.$swal.fire({
              position: 'top-middle',
              icon: 'success',
              title: 'Item has been added to your cart',
              showConfirmButton: false,
              timer: 3000,
              customClass: {
                popup: 'my-custom-alert',
                title: 'my-custom-alert-title',
              }
            });

            await this.$store.dispatch("loadMyCart");
            this.loadProductData();

          } catch (error) {
            console.error("Error saving item to server:", error);
          } finally {
            const goTop = document.getElementById("fromToTop");
            goTop.scrollIntoView({ behavior: "smooth" });
          }
        }
      }
    },

    async buyHandler() {
      this.$router.push({ name: "login" });
    },

    async buyHandlerAuth() {
      const cart_payload = {
        item: this.product.product_id,
        delivery_id: this.deliveryID,
      };

      try {
        await axios.post("/api/my-cart", cart_payload);
        // After the successful request, load the updated cart items from the server
        await this.$store.dispatch("loadMyCart");
        this.loadProductData();
      } catch (error) {
        console.error("Error saving item to server:", error);
      } finally {
        this.$nextTick(() => {
          this.$router.push({
            name: "my-cart-list",
            params: { productId: this.product.product_id, deliveryId: this.deliveryID, selected: true },
          });
        });

      }
    },

    getImageUrls() {
      const images = this.selectedProducts.image || this.product.image;
      if (images) {
        return images.split(',');
      }
      return [];
    },

    setActiveIndex(index) {
      this.currentIndex = index;
    },
    handleNext() {
      const numItems = this.relatedProducts.length;
      this.currentIndex = (this.currentIndex + 1) % numItems;
    },
    handlePrevious() {
      const numItems = this.relatedProducts.length;
      this.currentIndex = (this.currentIndex - 1 + numItems) % numItems;
    },

  },
  computed: {
    ...mapState(["myItems", "selectedSlug"]),
    auth() {
      return this.$store.getters.getAuthenticated;
    },
    remainingQuantity() {
      let totalQuantity = this.quantity_in ? this.quantity_in : 0;
      let currentTotalCart = totalQuantity;
      let purchaseItem = this.quantity_out ? this.quantity_out : 0;
      return Math.max(currentTotalCart - purchaseItem, 0);
    },

    isLoading() {
      return this.$store.state.isLoading;
    },
    chunkedRelatedProducts() {
      const chunkSize = 4;
      const chunks = [];
      for (let i = 0; i < this.relatedProducts.length; i += chunkSize) {
        chunks.push(this.relatedProducts.slice(i, i + chunkSize));
      }
      return chunks;
    },



  },


  mounted() {
    this.loadProductData();
  },
};
</script>

<style scoped>
.justify-desc {
  text-align: justify;
}

@media (max-width: 576px) {
  .mobile-image {
    object-fit: contain;
    height: 200px !important;
    width: 200px !important;
  }

}


.carousel {
  margin: 50px auto;
  padding: 0 70px;
}

.carousel-inner {
  height: auto;
}

.carousel .item {
  color: #747d89;
  min-height: 325px;
  text-align: center;
  overflow: hidden;
}

.carousel .thumb-wrapper {
  padding: 25px 15px;
  background: #fff;
  border-radius: 6px;
  text-align: center;
  position: relative;
  box-shadow: 0 2px 3px rgba(0, 0, 0, 0.2);
}

.carousel .item .img-box {
  height: 120px;
  margin-bottom: 20px;
  width: 100%;
  position: relative;
}


.carousel .item img {
  max-width: 100%;
  max-height: 100%;
  display: inline-block;
  position: absolute;
  bottom: 0;
  margin: 0 auto;
  left: 0;
  right: 0;
}

.carousel .item h4 {
  font-size: 18px;
}

.carousel .item h4,
.carousel .item p,
.carousel .item ul {
  margin-bottom: 5px;
}

.carousel .thumb-content .btn {
  color: #7ac400;
  font-size: 11px;
  text-transform: uppercase;
  font-weight: bold;
  background: none;
  border: 1px solid #7ac400;
  padding: 6px 14px;
  margin-top: 5px;
  line-height: 16px;
  border-radius: 20px;
}

.carousel .thumb-content .btn:hover,
.carousel .thumb-content .btn:focus {
  color: #fff;
  background: #7ac400;
  box-shadow: none;
}

.carousel .thumb-content .btn i {
  font-size: 14px;
  font-weight: bold;
  margin-left: 5px;
}

.carousel .item-price {
  font-size: 13px;
  padding: 2px 0;
}

.carousel .item-price strike {
  opacity: 0.7;
  margin-right: 5px;
}

.carousel-control-prev,
.carousel-control-next {
  height: 44px;
  width: 40px;
  background: #7ac400;
  margin: auto 0;
  border-radius: 4px;
  opacity: 0.8;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
  background: #78bf00;
  opacity: 1;
}

.carousel-control-prev i,
.carousel-control-next i {
  font-size: 36px;
  position: absolute;
  top: 50%;
  display: inline-block;
  margin: -19px 0 0 0;
  z-index: 5;
  left: 0;
  right: 0;
  color: #fff;
  text-shadow: none;
  font-weight: bold;
}

.carousel-control-prev i {
  margin-left: -2px;
}

.carousel-control-next i {
  margin-right: -4px;
}

.carousel-indicators {
  bottom: -50px;
}

.carousel-indicators li,
.carousel-indicators li.active {
  width: 10px;
  height: 10px;
  margin: 4px;
  border-radius: 50%;
  border: none;
}

.carousel-indicators li {
  background: rgba(0, 0, 0, 0.2);
}

.carousel-indicators li.active {
  background: rgba(0, 0, 0, 0.6);
}

.carousel .wish-icon {
  position: absolute;
  right: 10px;
  top: 10px;
  z-index: 99;
  cursor: pointer;
  font-size: 16px;
  color: #abb0b8;
}

.carousel .wish-icon .fa-heart {
  color: #ff6161;
}

.star-rating li {
  padding: 0;
}

.star-rating i {
  font-size: 14px;
  color: #ffc000;
}

/* .img-fluid {
  max-width: 100%;
  height: 100px;
} */
</style>
