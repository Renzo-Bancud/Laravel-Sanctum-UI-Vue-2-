<template>
    <div>
        <Loader v-if="isLoading" />

        <!-- Navigation-->
        <Navbar />

        <section class="py-5">
            <div class="container px-4 px-lg-5">

                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0 text-uppercase">My Purchase</h1>
                    </div>
                    <div class="col-auto">
                        <div class="page-utilities">
                            <div class="d-flex">
                                <div class="mx-1">
                                    <input type="text" id="search-orders" name="searchorders" v-model="searchform.search"
                                        @keyup="search" class="form-control search-orders" placeholder="Search" />
                                </div>
                                <!--//col-->

                            </div>
                            <!--//row-->
                        </div>
                        <!--//table-utilities-->
                    </div>
                    <!--//col-auto-->
                </div>
                <!--//row-->

                <nav id="orders-table-tab" class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
                    <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(0)"
                        :class="{ active: currentTab == 0 }">
                        <i class="iconify fs-2" data-icon="fluent:select-all-off-20-regular"></i>&nbsp;All
                    </a>
                    <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(2)"
                        :class="{ active: currentTab == 2 }">
                        <i class="iconify fs-2" data-icon="bi:box-seam"></i>&nbsp;To Package
                    </a>
                    <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(3)"
                        :class="{ active: currentTab == 3 }">
                        <i class="iconify fs-2" data-icon="tabler:truck"></i>&nbsp;To Ship
                    </a>
                    <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(4)"
                        :class="{ active: currentTab == 4 }">
                        <i class="iconify fs-2" data-icon="icon-park-outline:receive"></i>&nbsp;To Receive
                    </a>
                    <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(5)"
                        :class="{ active: currentTab == 5 }">
                        <i class="iconify fs-2" data-icon="solar:cart-check-linear"></i>&nbsp;Completed
                    </a>
                    <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(7)"
                        :class="{ active: currentTab == 7 }">
                        <i class="iconify fs-2" data-icon="icons8:cancel-2"></i>&nbsp;Cancelled
                    </a>
                    <!-- <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(1)"
                :class="{ active: currentTab == 1 }">Cancelled</a>
              <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(2)"
                :class="{ active: currentTab == 2 }">Expired</a> -->
                </nav>

                <div class="tab-content" id="orders-table-tab-content">
                    <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
                        <div class="app-card app-card-orders-table shadow-sm mb-5">
                            <div class="app-card-body">

                                <table class="table-res app-table-hover mb-0 text-left">
                                    <tbody>
                                        <tr v-if="filteredOrders && filteredOrders.length === 0">
                                            <td colspan="7" class="text-center">
                                                <span class="iconify display-1 mb-1"
                                                    data-icon="icon-park-outline:transaction-order"></span><br>
                                                <h5>{{ message }}</h5>
                                            </td>
                                        </tr>
                                        <tr v-else v-for="(cart_item, index) in  filteredOrders"
                                            :key="cart_item._id + '_' + index">

                                            <td class="custom-td">
                                                <div class="d-flex justify-content-between mt-3 mb-0">
                                                    <div style="text-align:left !important;">
                                                        <h6 class="text-muted">Order #</h6>
                                                        <h5 class="text-danger">{{ cart_item.order_track_id }}</h5>
                                                    </div>
                                                    <div class="d-flex">
                                                        <span class="text-muted" v-if="cart_item.status === 5"><span
                                                                class="iconify fs-4"
                                                                data-icon="mdi:truck-check"></span>&nbsp;Parcel has been
                                                            delivered&nbsp;&nbsp;|</span>
                                                        <span class="text-uppercase text-danger mx-2">
                                                            {{
                                                                cart_item.status === 0 ? 'Pending' :
                                                                (cart_item.status === 1 ? 'Order Placed' :
                                                                    (cart_item.status === 2 ? 'To Package' :
                                                                        (cart_item.status === 3 ? 'To Ship' :
                                                                            (cart_item.status === 4 ? 'To Receive' :
                                                                                (cart_item.status === 5 ? 'Completed' :
                                                                                    (cart_item.status === 6 ? 'Rated' :
                                                                                        (cart_item.status === 7 ? 'Cancelled' : ''))))))) }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <hr class="dashed-2">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex">
                                                        <div>
                                                            <img v-if="cart_item.main_picture" :src="cart_item.main_picture"
                                                                alt="Product Image"
                                                                style="object-fit: auto; height: 85px; width: 85px;" />
                                                            <img v-else :src="require('/storage/img-icon/no-image-icon.png')
                                                                " alt="No Image" height="85" width="85" />
                                                        </div>
                                                        <div style="text-align:left !important;" class="px-2">
                                                            <a class="h6 fw-bold" href="javascript:void(0)"
                                                                @click.prevent="redirectToOrderDetails(cart_item)">{{
                                                                    cart_item.title }}</a>
                                                            <br>
                                                            <span class="h6">x {{ cart_item.qty }}</span>
                                                            <br>
                                                            <span class="badge bg-secondary mx-1"
                                                                v-for="option in getVariationOptionsByItem(cart_item)"
                                                                :key="option.id">
                                                                {{ option.value }}
                                                            </span>

                                                        </div>


                                                    </div>
                                                    <div style="text-align:right !important;">
                                                        <div v-if="cart_item.isOnSale === null">
                                                            {{ formatPrice(cart_item.price) }}
                                                        </div>
                                                        <div v-else>

                                                            <span class="text-uppercase  ms-0 ps-0">{{ salePrice(cart_item)
                                                            }}</span>
                                                            <br>
                                                            <span class="old-price">
                                                                <del>
                                                                    <span class="old-price-discount text-muted"
                                                                        style="text-decoration:line-through !important;">
                                                                        <span class="iconify" data-icon="pepicons-pop:peso"
                                                                            style="position: relative; top: -3px;"></span>
                                                                        {{ cart_item.price }}
                                                                    </span>
                                                                </del>
                                                                <span class="old-price-discount text-danger">
                                                                    ({{ cart_item.discount_percentage }}% off)
                                                                </span>
                                                            </span>
                                                        </div>

                                                    </div>
                                                </div>
                                                <hr class="dashed-2">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div class="mt-1">
                                                        <h6 class="mx-2  text-secondary">
                                                            {{ (cart_item.cancelled_by === 1 ? 'Cancelled by you' :
                                                                (cart_item.isRated === 1 ? 'Item is already Rated' : ''))
                                                            }}</h6>
                                                    </div>
                                                    <div class="text-secondary" style="font-size:15px;">Order Total:
                                                        <span class="text-danger fs-5 fw-bold">{{
                                                            formatPrice(cart_item.isOnSale === null ? cart_item.price :
                                                                salePrice(cart_item) * cart_item.qty +
                                                                cart_item.shipping_fee) }}</span>
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end">
                                                    <div v-if="cart_item.status === 5">

                                                        <div v-if="cart_item.isRated == null">

                                                            <button class="btn btn-info btn-sm mx-1 rounded-0"
                                                                @click.prevent="redirectToOrderDetails(cart_item)">Order
                                                                Details</button>

                                                            <button class="btn btn-warning btn-sm mx-1 rounded-0"
                                                                @click.prevent="contactSeller()">Contact Seller</button>

                                                            <button class="btn btn-primary btn-sm mx-1 rounded-0"
                                                                style="opacity:0.5;cursor: not-allowed">Buy
                                                                Again</button>

                                                            <button
                                                                class="btn btn-danger btn-sm mx-1 mt-2 mt-lg-0 rounded-0"
                                                                @click.prevent="rateItem(cart_item)">Rate</button>
                                                        </div>
                                                        <div v-else>
                                                            <button class="btn btn-info btn-sm mx-1 rounded-0"
                                                                @click.prevent="redirectToOrderDetails(cart_item)">Order
                                                                Details</button>

                                                            <button class="btn btn-warning btn-sm mx-1 rounded-0"
                                                                @click.prevent="contactSeller()">Contact Seller</button>

                                                            <button class="btn btn-primary btn-sm mx-1 rounded-0"
                                                                @click.prevent="addToCartHandler(cart_item)">Buy
                                                                Again</button>
                                                            <button
                                                                class="btn btn-danger btn-sm mx-1 mt-2 mt-lg-0 rounded-0"
                                                                style="opacity:0.5;cursor: not-allowed">Rate</button>
                                                        </div>

                                                    </div>

                                                    <div v-else-if="cart_item.status === 6">
                                                        <div v-if="cart_item.isRated == null">
                                                            <button class="btn btn-info btn-sm mx-1 rounded-0"
                                                                @click.prevent="redirectToOrderDetails(cart_item)">Order
                                                                Details</button>

                                                            <button class="btn btn-warning btn-sm mx-1 rounded-0"
                                                                @click.prevent="contactSeller()">Contact Seller</button>

                                                            <button class="btn btn-primary btn-sm mx-1 rounded-0"
                                                                @click.prevent="addToCartHandler(cart_item)">Buy
                                                                Again</button>
                                                            <button
                                                                class="btn btn-danger btn-sm mx-1 mt-2 mt-lg-0 rounded-0"
                                                                style="opacity:0.5;cursor:not-allowed;">Rate</button>
                                                        </div>
                                                        <div v-else>
                                                            <button class="btn btn-info btn-sm mx-1 rounded-0"
                                                                @click.prevent="redirectToOrderDetails(cart_item)">Order
                                                                Details</button>

                                                            <button class="btn btn-warning btn-sm mx-1 rounded-0"
                                                                @click.prevent="contactSeller()">Contact Seller</button>


                                                            <button class="btn btn-primary btn-sm mx-1 rounded-0"
                                                                style="opacity:0.5;cursor:not-allowed">Buy
                                                                Again</button>

                                                            <button
                                                                class="btn btn-danger btn-sm mx-1 mt-2 mt-lg-0 rounded-0"
                                                                @click.prevent="rateItem(cart_item)">Rate</button>
                                                        </div>
                                                    </div>

                                                    <div v-else>
                                                        <button class="btn btn-info btn-sm mx-1 rounded-0"
                                                            @click.prevent="redirectToOrderDetails(cart_item)">Order
                                                            Details</button>

                                                        <button class="btn btn-warning btn-sm mx-1 rounded-0"
                                                            @click.prevent="contactSeller()">Contact Seller</button>

                                                        <button class="btn btn-primary btn-sm mx-1 rounded-0"
                                                            style="opacity:0.5;cursor:not-allowed">Buy
                                                            Again</button>
                                                        <button class="btn btn-danger btn-sm mx-1 mt-2 mt-lg-0 rounded-0"
                                                            style="opacity:0.5;cursor:not-allowed;">Rate</button>
                                                    </div>

                                                </div>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!--//table-responsive-->
                            </div>
                            <!--//app-card-body-->
                        </div>

                    </div>
                    <!--//tab-pane-->
                </div>
                <!--//tab-content-->
            </div>
        </section>

        <div class="modal fade" id="ratethisItem" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="staticBackdropLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">



                        <div class="mb-2">
                            <div id="rating-stars" class="mb-3 text-center">
                                <a @click="rate(1)" :class="{ 'star-rated': rating >= 1 }"><i class="iconify h3"
                                        data-icon="pepicons-pencil:star"></i></a>
                                <a @click="rate(2)" :class="{ 'star-rated': rating >= 2 }"><i class="iconify h3"
                                        data-icon="pepicons-pencil:star"></i></a>
                                <a @click="rate(3)" :class="{ 'star-rated': rating >= 3 }"><i class="iconify h3"
                                        data-icon="pepicons-pencil:star"></i></a>
                                <a @click="rate(4)" :class="{ 'star-rated': rating >= 4 }"><i class="iconify h3"
                                        data-icon="pepicons-pencil:star"></i></a>
                                <a @click="rate(5)" :class="{ 'star-rated': rating >= 5 }"><i class="iconify h3"
                                        data-icon="pepicons-pencil:star"></i></a>
                            </div>

                            <form @submit.prevent="submitRating()">
                                <div class="form-group">
                                    <textarea class="form-control" placeholder="How was your experience about our product?"
                                        rows="8" v-model="ratingForm.feedback" :class="{
                                            'is-invalid': ratingForm.errors.has('feedback'),
                                        }"></textarea>

                                    <div v-if="feedbackErrorMessage" class="invalid-feedback">
                                        {{ feedbackErrorMessage }}
                                    </div>

                                </div>
                                <div class="text-center">
                                    <button type="submit" :disabled="ratingForm.busy"
                                        class="btn btn-outline-secondary hover-pulse rounded-0 mt-3 modal-btn"></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="contactSeller" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <button type="button" class="btn-close me-1 mt-2" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-2">
                            <span class="iconify h1 display-1" data-icon="flat-color-icons:phone-android"></span>
                            <h3 class="mt-3 fw-bold">{{ this.contacts.phone }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Footer />

    </div>
</template>
  
<script>
import { Form as CustomForm } from "vform";
import { mapState } from "vuex";
import Navbar from "../template/shop/Navbar.vue";
import salePriceMixin from '../mixins/salePriceMixin';
import variationOptionMixin from '../mixins/VariationOptionMixin';
import Footer from "../template/shop/Footer.vue";


export default {
    mixins: [salePriceMixin, variationOptionMixin],
    components: {
        Navbar,
        Footer
    },
    data() {
        return {
            currentTab: 0,
            orders: [],
            product: [],
            contacts: [],
            searchform: {
                search: "",
            },
            ratingForm: new CustomForm({
                feedback: "",
            }),
            message: '',
            rating: 0,
            selectedId: null,
            rateItemValue: null,
            feedbackErrorMessage: "",
        };
    },
    computed: {
        ...mapState(["auth", "user"]),

        filteredOrders() {
            if (!this.orders || !this.orders.filter) {
                this.message = 'No data available';
                return [];
            }

            if (this.currentTab === 0) {
                this.message = '';
                return this.orders;
            }

            const filtered = this.orders.filter((order) => order.status === this.currentTab);
            this.message = filtered.length === 0 ? 'No data available' : '';

            return filtered;
        },

        getStatus() {
            return function (status) {
                return status === 0
                    ? '<span class="badge bg-danger">Pending</span>'
                    : status === 1
                        ? '<span class="badge bg-secondary">Cancelled</span>'
                        : status === 2
                            ? '<span class="badge bg-warning">Expired</span>'
                            : status === 3
                                ? '<span class="badge bg-primary">Validation</span>'
                                : status === 4
                                    ? '<span class="badge bg-info">Paid</span>'
                                    : status === 5
                                        ? '<span class="badge bg-success">For Delivery</span>'
                                        : "";
            };
        },

        isLoading() {
            return this.$store.state.isLoading;
        },

    },
    methods: {
        async changeTab(tabIndex) {
            this.currentTab = tabIndex;
            await this.loadCart(this.currentTab);
        },
        async loadCart(status) {
            try {
                let uid = this.user.id;
                let { data } = await axios.get(`/api/get-purchase-orders/${uid}?status=${status}`, {
                    params: {
                        search: this.searchform.search,
                    },
                });
                this.orders = data.user_carts;
            } catch (error) {
                console.error(error);
            }
        },

        search() {
            this.loadCart(1, this.searchform.search);
        },

        async redirectToOrderDetails(cartItem) {
            try {
                this.$store.dispatch('showLoader');
                const response = await axios.post('/api/encrypt', { data: cartItem.order_id });
                const encryptedOrderId = response.data.encryptedData;
                const slug = cartItem.slug;

                this.$router.push({
                    name: 'order-details',
                    params: { orderId: encryptedOrderId, slug: slug },
                });
            } catch (error) {
                console.error(error);
            } finally {
                this.$store.dispatch('hideLoader');
            }
        },

        formatPrice(price) {
            const formattedPrice = parseFloat(price);
            if (isNaN(formattedPrice)) {
                return "";
            }
            return formattedPrice.toLocaleString("en-PH", {
                style: "currency",
                currency: "PHP",
            });
        },

        contactSeller() {
            $("#contactSeller").modal("show");
        },

        async rateItem(item) {
            $(".modal-title").text("Review and Rating");
            $(".modal-btn").text("Submit Rating");
            //this.selectedId = item.item_id;
            this.ratingForm.selectedId = item.order_id;
            //console.log(this.selectedId)
            $("#ratethisItem").modal("show");
        },

        rate(rating) {
            this.rating = rating;
            this.ratingForm.rateItemValue = rating
            //console.log(this.rating)
        },
        async submitRating() {
            if (this.ratingForm.selectedId !== null) {
                //let selectedRating = this.rating; // Access the clicked rating value here
                const payload = {
                    orderId: this.ratingForm.selectedId,
                    rating: this.ratingForm.rateItemValue
                };

                //console.log(payload)

                await this.ratingForm
                    .post('/api/save-rating/', payload) // im getting null on the server but when i console it is value for rating
                    .then((response) => {

                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'success',
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 3000,
                            customClass: {
                                popup: 'my-custom-alert',
                                title: 'my-custom-alert-title',
                                icon: 'my-custom-alert-icon',
                            },
                        });

                        $("#ratethisItem").modal("hide");
                        this.loadCart();

                    }).catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const errors = error.response.data.errors;
                            this.feedbackErrorMessage = errors.feedback ? errors.feedback[0] : "";
                        } else {
                            this.$swal.fire({
                                position: 'top-middle',
                                icon: 'error',
                                title: "An error occurred. Please try again.",
                                showConfirmButton: false,
                                timer: 3000,
                                customClass: {
                                    popup: 'my-custom-alert',
                                    title: 'my-custom-alert-title',
                                    icon: 'my-custom-alert-icon',
                                },
                            });
                        }
                    });

            } else {
                this.$swal.fire({
                    position: 'top-middle',
                    icon: 'error',
                    title: "An error occurred. Please try again.",
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'my-custom-alert',
                        title: 'my-custom-alert-title',
                        icon: 'my-custom-alert-icon',
                    },
                });

            }

        },

        async addToCartHandler(cart_item) {

            let slug = cart_item.slug;
            let { data } = await axios.get("/api/product/item/" + slug);
            this.product = data;

            const cart_payload = {
                item: cart_item.prod_id,
                delivery_id: cart_item.delivery_id,
            };

            try {
                await axios.post("/api/my-cart", cart_payload);
                // After the successful request, load the updated cart items from the server
                await this.$store.dispatch("loadMyCart");
            } catch (error) {
                console.error("Error saving item to server:", error);
            } finally {
                this.$nextTick(() => {
                    this.$router.push({
                        name: "my-cart-list",
                        params: { productId: this.product.product_id, deliveryId: cart_item.delivery_id, selected: true },
                    });
                });

            }
        },

        async loadContacts() {
            try {
                const response = await axios.get("/api/contact-us");
                this.contacts = response.data;
            } catch (error) {
                console.error(error);
            }
        },

    },

    mounted() {
        this.loadCart(this.currentTab);
        this.loadContacts();
    },
};
</script>
  
<style>
.custom-td {
    margin-bottom: 20px;
}

.star-rated {
    color: orange !important;
}
</style>