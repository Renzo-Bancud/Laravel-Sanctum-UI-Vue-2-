<template>
  <div>
    <!--====== Navbar Style 4 Part Start ======-->
    <section class="navbar-dark-wrapper navigation">
      <!--====== menu style 4 Part Starts ======-->
      <header class="menu-style-4 menu-dark-4 position-relative" id="goToNavbar">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-12 position-static">
              <div class="navbar-container navbar-sidebar-4">
                <!-- menu top Start -->
                <div class="menu-top-area d-lg-flex justify-content-between">
                  <!-- menu top left Start -->
                  <div class="menu-top-left">
                    <ul class="menu-top-link">
                      <li>
                        <router-link :to="{ name: 'about' }">About</router-link>
                      </li>
                      <li class="text-white">|</li>
                      <li>
                        <router-link :to="{ name: 'contact' }">Contacts
                        </router-link>
                      </li>
                    </ul>
                  </div>
                  <!-- menu top left Ends -->
                  <!-- logo Start -->
                  <div class="logo d-lg-block text-center text-lg-left">
                    <router-link :to="{ name: 'home' }"><img :src="require('../../../assets/images/logo.png')" alt="Logo"
                        width="200" height="80" /></router-link>
                  </div>
                  <!-- logo Ends -->
                  <!-- menu top right Start -->
                  <div class="menu-top-right">
                    <ul class="menu-top-link" v-if="user && user.email_verified_at !== null && auth">
                      <li>
                        <a href="/assets/apk/app-apk-64ce23d492d70-1691231188.apk" download>
                          <i class="iconify fs-3" data-icon="basil:android-outline"></i> App
                        </a>
                      </li>
                      <li class="text-white">|</li>
                      <li>
                        <a href="#" @click.prevent="logout">
                          <i class="iconify fs-3" data-icon="tabler:logout-2"></i> Logout
                        </a>
                      </li>
                    </ul>
                    <ul class="menu-top-link" v-else>
                      <li>
                        <a href="/assets/apk/app-apk-64ce23d492d70-1691231188.apk" download>
                          <i class="iconify fs-3" data-icon="basil:android-outline"></i> App
                        </a>
                      </li>
                      <li class="text-white">|</li>
                      <li>
                        <router-link :to="{ name: 'login' }">
                          <i class="iconify fs-3" data-icon="material-symbols:login-sharp"></i> Login
                        </router-link>
                      </li>
                      <li class="text-white">|</li>
                      <li>
                        <router-link :to="{ name: 'signup' }">
                          <i class="iconify fs-3" data-icon="mdi:register-outline"></i> Register
                        </router-link>
                      </li>
                    </ul>
                  </div>
                  <!-- menu top right Ends -->
                </div>
                <!-- menu top Ends -->

                <!-- main menu Start -->
                <nav class="main-navbar d-lg-flex align-items-center">
                  <!-- navbar menu Start -->
                  <div class="navbar-menu">
                    <div class="navbar-menu-toggle d-lg-block d-lg-block-desktop">
                      <button id="toggle-menu-4" class="menu-toggle collapsed" data-bs-toggle="collapse"
                        data-bs-target="#menuVertical4-1" aria-controls="menuVertical4-1" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="toggle-icon"></span>
                        <span class="toggle-icon"></span>
                        <span class="toggle-icon"></span>
                      </button>
                    </div>

                    <div class="navbar-menu-toggle d-lg-block-show-desktop d-none">
                      <a href="#0" id="toggle-menu-4" class="this-menu d-flex" data-bs-toggle="collapse"
                        data-bs-target="#menuVertical4-1" aria-controls="menuVertical4-1" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="iconify fs-3 mx-2" data-icon="iconamoon:category-thin"></span><span
                          style="position: relative;top:-5px;">Categories</span>
                      </a>
                    </div>

                    <ul class="main-menu menu-vertical-4 collapse" id="menuVertical4-1" v-if="isMenuVisible">
                      <li v-for="category in categories" :key="category.slug">
                        <a href="#0" @click="loadCategoryData(category.slug)" class="hover-pulse"
                          style="font-size:11px !important;">{{ category.name }}
                          <i class="iconify fs-5" data-icon="carbon:chevron-right"
                            style="position:relative;top:-1px;"></i></a>
                      </li>
                    </ul>
                  </div>
                  <!-- navbar menu Ends -->


                  <!-- navbar menu Start -->
                  <ul class="main-menu-horizontal d-lg-block d-lg-flex  pt-0 pt-lg-3 py-0 px-0"
                    style="width:100% !important;">
                    <li class="menu-item-has-children">

                      <div v-if="user && user.email_verified_at !== null && auth">

                        <a href="#0" class="collapsed pb-2" data-bs-toggle="collapse" data-bs-target="#menuVertical4-12"
                          aria-controls="menuVertical4-12" aria-expanded="false" aria-label="Toggle navigation">
                          <img :src="user.profile" alt="User Profile" width="30" height="30" class="mx-1"
                            v-if="user.profile">
                          <span v-else class="iconify fs-3" data-icon="gg:profile"></span>
                          <span style="position: relative;top:2px;">{{ user.name }}'s Account</span></a>

                        <!-- sub menu Start -->
                        <ul class="sub-menu collapse" id="menuVertical4-12">
                          <li>
                            <router-link :to="{ name: 'account-details' }" class="hover-pulse"
                              style="font-size:11px !important;">
                              My Account
                            </router-link>
                          </li>
                          <li class="menu-item-has-children d-none">
                            <a href="#0" class="collapsed" data-bs-toggle="collapse" data-bs-target="#menuVertical4-13"
                              aria-controls="menuVertical4-13" aria-expanded="false"
                              aria-label="Toggle navigation">Dropped
                              Menu 2 <i class="mdi mdi-chevron-right"></i></a>
                            <ul class="sub-menu collapse" id="menuVertical4-13">
                              <li><a href="#0">Sub Dropped Menu 1</a></li>
                              <li><a href="#0">Sub Dropped Menu 2</a></li>
                              <li><a href="#0">Sub Dropped Menu 3</a></li>
                              <li class="menu-item-has-children">
                                <a href="#0" class="collapsed" data-bs-toggle="collapse"
                                  data-bs-target="#menuVertical4-14" aria-controls="menuVertical4-14"
                                  aria-expanded="false" aria-label="Toggle navigation">Sub
                                  Dropped Menu 4
                                  <i class="mdi mdi-chevron-right"></i></a>
                                <ul class="sub-menu collapse" id="menuVertical4-14">
                                  <li><a href="#0">Sub Dropped Menu 1</a></li>
                                  <li><a href="#0">Sub Dropped Menu 2</a></li>
                                  <li><a href="#0">Sub Dropped Menu 3</a></li>
                                  <li><a href="#0">Sub Dropped Menu 4</a></li>
                                </ul>
                              </li>
                            </ul>
                          </li>
                          <li>
                            <router-link :to="{ name: 'purchases' }" class="hover-pulse"
                              style="font-size:11px !important;">
                              My Purchase
                            </router-link>
                          </li>
                          <li>
                            <router-link :to="{ name: 'checkout-details' }" class="hover-pulse"
                              style="font-size:11px !important;">
                              My Checkout
                            </router-link>
                          </li>
                          <li>
                            <router-link :to="{ name: 'home' }" class="hover-pulse" style="font-size:11px !important;">
                              Shop
                            </router-link>
                          </li>

                        </ul>
                        <!-- sub menu Ends -->
                      </div>

                      <div v-else>
                        <a href="#0" class="collapsed" data-bs-toggle="collapse" style="visibility:hidden;"
                          data-bs-target="#menuVertical4-12" aria-controls="menuVertical4-12" aria-expanded="false"
                          aria-label="Toggle navigation"></a>
                      </div>


                    </li>
                  </ul>
                  <!-- navbar menu Ends -->

                  <div class="navbar-search-cart d-lg-flex justify-content-end mb-3 mb-lg-0">
                    <!-- navbar search start -->
                    <div class="navbar-search search-dark search-style-5">
                      <div class="search-btn w-100 d-flex">

                        <router-link :to="{ name: 'home' }" class="d-flex this-menu mx-1">
                          <i class="iconify fs-3 mt-lg-1 mt-1" data-icon="bi:shop"></i>&nbsp;<span
                            style="position: relative;top:1px;">Shop</span></router-link>

                        <router-link :to="{ name: 'about' }" class="d-flex this-menu mx-1 d-lg-block-desktop">
                          <i class="iconify fs-3 mt-lg-1 mt-1" data-icon="solar:info-square-linear"></i>&nbsp;<span
                            style="position: relative;top:1px;">About</span>
                        </router-link>

                        <router-link :to="{ name: 'contact' }" class="d-flex this-menu d-lg-block-desktop">
                          <i class="iconify fs-3 mt-lg-1 mt-1" data-icon="fluent:phone-32-regular"></i>&nbsp;<span
                            style="position: relative;top:1px;">Contacts</span>
                        </router-link>

                      </div>
                      <div class="search-btn">
                        <div class="navbar-cart">
                          <a class="icon-btn primary-icon-text icon-text-btn mt-1" href="#0" @click="loadMyCartData"
                            @mouseover="loadMyCartData"><img :src="require('../../../assets/images/cart-6.svg')"
                              alt="Icon" /><span class="icon-text text-style-1">{{ $store.state.cartItemCount
                              }}</span></a>

                          <div class="navbar-cart-dropdown">
                            <div class="checkout-style-2 checkout-style-dark-2 text-start">
                              <div class="checkout-header d-flex justify-content-between">
                                <h6 class="title">Shopping Cart</h6>
                              </div>

                              <div class="checkout-table">
                                <table class="table w-100">
                                  <tbody>
                                    <tr v-if="carts.length === 0">
                                      <div class="text-center">
                                        <span class="iconify display-1" data-icon="tabler:shopping-cart-off"></span>
                                        <p class="mt-3 fw-bold">No item found on your cart</p>
                                      </div>
                                    </tr>
                                    <tr v-else v-for="(cart, index) in carts" :key="index">
                                      <div v-if="$route.name == 'item-details'">
                                        <td class="checkout-product">
                                          <div class="product-cart d-flex">
                                            <div class="product-thumb">
                                              <img :src="cart.main_picture" alt="Product" />
                                            </div>
                                            <div class="product-content media-body">
                                              <h5 class="title pt-2">
                                                <a href="#0" @click="selectItem(cart.slug)"> {{

                                                  cart.title.length > 8
                                                  ? cart.title.substring(0, 8) + "..."
                                                  : cart.title

                                                }}</a>
                                              </h5>

                                            </div>
                                          </div>
                                        </td>
                                        <td class="checkout-quantity">
                                          <div class="product-quantity d-inline-flex">

                                            <input type="text" :value="cart.qty_cart" />

                                          </div>
                                        </td>
                                        <td class="checkout-price">
                                          <p class="price mt-3">{{ formatPrice(cart.isOnSale === null ? cart.price *
                                            cart.qty_cart : salePrice(cart) * cart.qty_cart) }}</p>
                                        </td>
                                      </div>
                                      <div v-else>
                                        <td class="checkout-product">


                                          <div class="product-cart d-flex">
                                            <div class="product-thumb">
                                              <img :src="cart.main_picture" alt="Product" />
                                            </div>
                                            <div class="product-content media-body">
                                              <h5 class="title pt-2">
                                                <a href="#0" @click="viewItemDetails(cart)"> {{

                                                  cart.title.length > 8
                                                  ? cart.title.substring(0, 8) + "..."
                                                  : cart.title


                                                }}</a>
                                              </h5>

                                            </div>
                                          </div>
                                        </td>
                                        <td class="checkout-quantity">
                                          <div class="product-quantity d-inline-flex">

                                            <input type="text" :value="cart.qty_cart" />

                                          </div>
                                        </td>
                                        <td class="checkout-price">
                                          <p class="price mt-3">{{ formatPrice(cart.isOnSale === null ? cart.price *
                                            cart.qty_cart : salePrice(cart) * cart.qty_cart) }}</p>

                                        </td>
                                      </div>

                                    </tr>


                                  </tbody>
                                </table>
                              </div>

                              <div class="checkout-footer" v-if="$route.name != 'my-cart-list' && auth">
                                <div class="checkout-btn d-flex justify-content-between">

                                  <div v-if="remainingItems > 0" class="pt-2 text-white">{{ remainingItems }} More
                                    Products in Cart</div>
                                  <div><a class="main-btn primary-btn hover-pulse" @click.prevent="viewMyCart()">
                                      <span class="iconify fs-4" data-icon="ep:view"></span> My
                                      Cart</a></div>

                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- navbar search Ends -->
                  </div>

                </nav>
                <!-- main navbar Ends -->
              </div>
            </div>
          </div>
        </div>
      </header>
      <!--====== menu style 4 Part Ends ======-->
    </section>
    <!--====== Navbar Style 4 Part Ends ======-->
  </div>
</template>

<script>
import { mapActions } from "vuex";
import formatPriceMixin from "../../mixins/formatPriceMixin";
import salePriceMixin from "../../mixins/salePriceMixin";
import { refreshCsrfToken } from "../../services/apiService";

export default {
  mixins: [formatPriceMixin, salePriceMixin],

  beforeRouteEnter(to, from, next) {
    const slug = to.params.slug;
    axios
      .get(`/api/product/item/${slug}`)
      .then((response) => {
        const productData = response.data;
        next((vm) => {
          vm.productData = productData;
        });
      })
      .catch((error) => {
        console.error("Error loading product data:", error);
        next(false); // Prevent entering the route
      });
  },

  data() {
    return {
      carts: [],
      categories: [],
      Items: [],
      contacts: [],
      productData: null,
      form: {
        search: "",
        selectCategory: "",
      },
      cartItemCount: 0,
      remainingItems: 0,
      selectedId: null,
      cartItems: this.$store.state.cartItems,
      isMenuVisible: true,
    };
  },
  methods: {
    ...mapActions(["setSelectedSlug"]),
    async logout() {
      await axios
        .post("/logout").then((response) => {
          this.$swal.fire({
            position: "top-middle",
            icon: "success",
            title: "Logged out successfully",
            showConfirmButton: false,
            timer: 3000,
            showConfirmButton: false,
            didClose: () => {
              setTimeout(() => {
                window.location.reload();
              }, 0.1);
            },
            customClass: {
              popup: "my-custom-alert",
              title: "my-custom-alert-title",
              icon: "my-custom-alert-icon",
            },
          });
          localStorage.removeItem("auth");
          //localStorage.clear();
          this.$store.commit("SET_AUTHENTICATED", false);
          this.$router.push({ name: "login" });
        })
        .catch((error) => {
          this.$swal.fire({
            position: "top-middle",
            icon: "success",
            title: "An error occurred. Please try again.",
            showConfirmButton: false,
            timer: 3000,
            showConfirmButton: false,
            customClass: {
              popup: "my-custom-alert",
              title: "my-custom-alert-title",
              icon: "my-custom-alert-icon",
            },
          });
        });
    },

    async fetchMyCart() {
      try {
        await this.$store.dispatch("loadMyCart");
        this.carts = this.$store.state.carts;
        this.cartItemCount = this.$store.state.cartItemCount;
        this.remainingItems = this.$store.state.remainingItems;
      } catch (error) {
        console.log(error);
      }
    },

    loadMyCartData() {
      if (this.$store.getters.getAuthenticated) {
        this.fetchMyCart();
      }
    },

    formatTitle(title) {
      const maxLength = 13; // Define your desired maximum length
      if (title.length > maxLength) {
        return title.slice(0, maxLength) + "...";
      }
      return title;
    },
    viewCart() {
      $(".modal-title").html(
        "<span class='iconify fs-1' data-icon='fluent-emoji-flat:shopping-bags'></span> Cart"
      );
      $("#showMyCart").modal("show");
      this.fetchMyCart();
    },
    viewMyCart() {
      this.$router.push({ name: "my-cart-list" });
      $("#showMyCart").modal("hide");
    },

    selectItem(slug) {
      this.setSelectedSlug(slug);
      $("#showMyCart").modal("hide");
    },

    viewItemDetails(cart) {
      this.$router.push({ name: "item-details", params: { slug: cart.slug } });
      $("#showMyCart").modal("hide");
    },

    loginAccount() {
      this.$router.push({ name: "login" });
      $("#showMyCart").modal("hide");
    },

    async loadContacts() {
      try {
        const response = await axios.get("/api/contact-us");
        this.contacts = response.data;
      } catch (error) {
        console.error(error);
      }
    },

    async loadCategories() {
      try {
        const response = await axios.get("/api/categories");
        this.categories = response.data;
      } catch (error) {
        console.error(error);
      }
    },

    loadCategoryData(slug) {
      this.$store.dispatch('loadSelectedCategory', slug);
      // Use router.push to navigate to categories.vue with the selected category slug
      this.isMenuVisible = false;
      this.$router.push({ name: 'item-category', params: { slug: slug } });
      setTimeout(() => {
        this.isMenuVisible = true;
      }, 100);
    },

  },
  computed: {
    //...mapState(["cartItems"]),
    auth() {
      return this.$store.getters.getAuthenticated;
    },
    user() {
      return this.$store.getters.getUser;
    },
    selectedCategory() {
      return this.$store.state.selectedCategory;
    },
  },

  created() {
    if (this.$store.getters.getAuthenticated) {
      this.fetchMyCart();
    }
  },

  mounted() {
    this.loadContacts();
    this.loadCategories();
  },
};
</script>

<style scoped>
.this-menu {
  line-height: 36px;
  margin-top: 7px;
  color: white;
  font-size: 13px;
  text-transform: uppercase;
  font-weight: 500;
  transition: all 0.3s linear;
  border-radius: 4px;
}

@media (min-width: 992px) {
  .d-lg-block-desktop {
    display: none !important;
  }

  .d-lg-block-show-desktop {
    display: block !important;
  }
}
</style>
