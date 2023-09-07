<template>
    <div>
        <!-- Navigation-->
        <Navbar />

        <section class="py-5">
            <div class="container px-4 px-lg-5">
                <div v-if="order_status.length > 0">
                    <div v-for="item in order_status" :key="item.id">
                        <div v-if="item.status == 7">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="text-danger fw-bold">Cancellation Completed</h4>
                                    <div v-for="item in order_status" :key="item.id" class="mb-4">
                                        on {{ item.orders_updated_at }}.
                                    </div>
                                </div>
                                <div>
                                    <router-link :to="{ name: 'purchases' }"
                                        class="btn btn-secondary rounded-0 hover-pulse"><span class="iconify fs-3"
                                            data-icon="icon-park-outline:back"></span>&nbsp;Back</router-link>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <tr v-for="placed_order in placed_orders" :key="placed_order.id" class="">
                                    <td class="text-start p-0" colspan="4">
                                        <div class="d-flex">
                                            <img class="img-responsive" :src="placed_order.main_picture"
                                                :alt="placed_order.title" style="width: 150px; height: 150px;" />

                                            <div class="d-flex flex-column bd-highlight mb-3 mx-3">
                                                <div class="p-0 bd-highlight fs-5">{{ placed_order.title }}</div>
                                                <div class="p-0 bd-highlight text-muted">x {{
                                                    getCartItemQuantity(placed_order) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <small><b>{{ formatPrice(placed_order.price * getCartItemQuantity(placed_order))
                                        }}</b></small>
                                    </td>
                                </tr>

                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">Merchandise
                                            Subtotal :</b>
                                    </td>
                                    <td class="text-end">{{ formatPrice(calculateSubtotal()) }}</td>
                                </tr>
                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">
                                            Shipping
                                            Fee :
                                        </b>
                                    </td>
                                    <td class="text-end">{{ formatPrice(calculateShippingFee()) }}</td>
                                </tr>
                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">
                                            Order
                                            Total :
                                        </b>
                                    </td>
                                    <td class="text-end">{{ formatPrice(calculateTotal()) }}</td>
                                </tr>
                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">
                                            Cancel By :
                                        </b>
                                    </td>
                                    <td class="text-end">

                                        <div v-for="item in order_status" :key="item.id">
                                            {{ item.cancel_by == 1 ? 'Buyer' : (item.cancel_by == 2 ? 'Admin' : '') }}
                                        </div>

                                    </td>
                                </tr>
                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">
                                            Payment
                                            Method :
                                        </b>
                                    </td>
                                    <td class="text-end p-0">
                                        Cash on Delivery
                                    </td>
                                </tr>

                            </table>

                        </div>

                        <div v-else>
                            <div v-if="item.payment_method === 'cod' && item.status != 6" class="d-flex">
                                <div v-for="status in [1, 2, 3, 4, 5]" :key="status"
                                    class="card mb-4 border-0 w-100 text-center" style="background: none;">
                                    <div class="card-header" :class="{
                                        'border-primary': shouldBounce(item, status),
                                        'border-bottom-1': !shouldBounce(item, status),
                                    }">
                                        <span class="iconify fs-1" :class="{
                                            'text-primary animated bounce': shouldBounce(item, status),
                                            'text-dark': !shouldBounce(item, status),
                                        }" :data-icon="getIcon(status)"></span>
                                    </div>
                                    <div class="card-body p-0 h-auto">
                                        <h6 class="card-title pricing-card-title mt-1">
                                            <small class="fw-bold">{{ getStatusLabel(status) }}</small>
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <div class="list-group list-group-flush list-group-transparent">
                                        <div class="list-group-item d-flex justify-content-between align-items-start px-0"
                                            v-if="billingAddress &&
                                                Object.keys(billingAddress).length > 0
                                                ">
                                            <div class="mt-2">
                                                <label class="fw-bold h5 mx-2">Delivery Address</label>
                                                <div v-if="billingAddress &&
                                                    Object.keys(billingAddress).length >
                                                    0
                                                    " class="mx-2">


                                                    <ul class="p-0" style="list-style: none">
                                                        <li class="text-muted">
                                                            {{
                                                                billingAddress.mobile_number
                                                            }}
                                                        </li>
                                                        <li>
                                                            {{
                                                                billingAddress.house_no
                                                            }},
                                                            {{
                                                                billingAddress.barangay
                                                            }}
                                                        </li>
                                                        <li>
                                                            {{
                                                                billingAddress.province
                                                            }},
                                                            {{
                                                                billingAddress.city
                                                            }}
                                                        </li>
                                                    </ul>

                                                </div>
                                                <div v-else>
                                                    <!-- Display a message when there is no data available -->
                                                    <p>No data available</p>
                                                </div>
                                            </div>

                                        </div>


                                    </div>
                                    <div
                                        v-if="item.status == 0 || item.status == 1 || item.status == 2 || item.status == 3 || item.status == 4">
                                        <router-link :to="{ name: 'purchases' }" class="btn btn-secondary rounded-0 mt-3">
                                            <span class="iconify fs-3" data-icon="icon-park-outline:back"></span>&nbsp;
                                            Return back</router-link>
                                    </div>
                                    <div v-else></div>

                                </div>
                                <div class="col-sm-8">
                                    <div v-if="item.status == 6" class="text-center">
                                        <div class="d-flex align-items-center justify-content-center mt-0 mt-lg-3"
                                            style="height: 100%;">
                                            <h4 class="text-danger fw-bold">Order Rated. Thank you for rating.</h4>
                                        </div>
                                        <router-link :to="{ name: 'purchases' }" class="btn btn-secondary rounded-0 mt-3">
                                            <span class="iconify fs-3" data-icon="icon-park-outline:back"></span>&nbsp;
                                            Return back</router-link>
                                    </div>
                                    <div v-else-if="item.status == 5" class="text-center">
                                        <div class="d-flex align-items-center justify-content-center mt-0 mt-lg-3"
                                            style="height: 100%;">
                                            <h4 class="text-success fw-bold">Rate this Item. Thank you.</h4>
                                        </div>
                                        <router-link :to="{ name: 'purchases' }" class="btn btn-secondary rounded-0 mt-3">
                                            <span class="iconify fs-3" data-icon="icon-park-outline:back"></span>&nbsp;
                                            Return back</router-link>
                                    </div>
                                    <div v-else-if="item.status == 0" class="text-center">
                                        <div class="d-flex align-items-center justify-content-center mt-0 mt-lg-3"
                                            style="height: 100%;">
                                            <h4 class="text-danger fw-bold">Order is Pending..</h4>
                                        </div>
                                    </div>
                                    <div class="timeline" v-else>
                                        <div v-if="item.status == 1">
                                            <div class="timeline-container primary">
                                                <div class="timeline-icon">
                                                    <i class="iconify" data-icon="icon-park-outline:transaction-order"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Order Placed<br><span class="text-muted">Your order is
                                                                placed</span>
                                                        </p>
                                                        <p class="text-white">{{ formatDateTime(item.created_at) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else-if="item.status == 2">


                                            <div class="timeline-container secondary">
                                                <div class="timeline-icon">
                                                    <i class="iconify" data-icon="icon-park-outline:box"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Packaging your ordered item<br><span class="text-muted">Seller is
                                                                packaging your item</span></p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_updated) }}</p>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="timeline-container primary">
                                                <div class="timeline-icon">
                                                    <i class="iconify" data-icon="icon-park-outline:transaction-order"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Order Placed<br><span class="text-muted">Your order is
                                                                placed</span>
                                                        </p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_created) }}</p>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                        <div v-else-if="item.status == 3">
                                            <div class="timeline-container danger">
                                                <div class="timeline-icon">
                                                    <i class="iconify"
                                                        data-icon="material-symbols:local-shipping-outline"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Preparing to Ship<br><span class="text-muted">Seller is preparing
                                                                to
                                                                ship
                                                                your parcel</span></p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_updated) }}</p>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="timeline-container secondary">
                                                <div class="timeline-icon">
                                                    <i class="iconify" data-icon="icon-park-outline:box"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Packaging your ordered item<br><span class="text-muted">Seller is
                                                                packaging your item</span></p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_updated) }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="timeline-container primary">
                                                <div class="timeline-icon">
                                                    <i class="iconify" data-icon="icon-park-outline:transaction-order"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Order Placed<br><span class="text-muted">Your order is
                                                                placed</span>
                                                        </p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_created) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else-if="item.status == 4">
                                            <div class="timeline-container success">
                                                <div class="timeline-icon">
                                                    <i class="iconify" data-icon="icon-park-outline:receive"></i>
                                                </div>
                                                <div class="timeline-body"> <!-- Show this if status = 3 -->
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">In Transit<br><span class="text-muted">Seller is ready to deliver
                                                                your item</span></p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_updated) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="timeline-container danger">
                                                <div class="timeline-icon">
                                                    <i class="iconify"
                                                        data-icon="material-symbols:local-shipping-outline"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Preparing to Ship<br><span class="text-muted">Seller is preparing
                                                                to
                                                                ship
                                                                your parcel</span></p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_updated) }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="timeline-container secondary">
                                                <div class="timeline-icon">
                                                    <i class="iconify" data-icon="icon-park-outline:box"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Packaging your ordered item<br><span class="text-muted">Seller is
                                                                packaging your item</span></p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_updated) }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="timeline-container primary">
                                                <div class="timeline-icon">
                                                    <i class="iconify" data-icon="icon-park-outline:transaction-order"></i>
                                                </div>
                                                <div class="timeline-body">
                                                    <div class="d-flex justify-content-between">
                                                        <p class="text-white">Order Placed<br><span class="text-muted">Your order is
                                                                placed</span>
                                                        </p>
                                                        <p class="text-white">{{ formatDateTime(item.order_date_created) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered">
                                <tr v-for="placed_order in placed_orders" :key="placed_order.id" class="">
                                    <td class="text-start p-0" colspan="4">
                                        <div class="d-flex">
                                            <img class="img-responsive" :src="placed_order.main_picture"
                                                :alt="placed_order.title" style="width: 150px; height: 150px;" />

                                            <div class="d-flex flex-column bd-highlight mb-3 mx-3">
                                                <div class="p-0 bd-highlight fs-5">{{ placed_order.title }}</div>
                                                <div class="p-0 bd-highlight text-muted">x {{
                                                    getCartItemQuantity(placed_order) }}
                                                </div>
                                                <div v-if="placed_order.status != 6 && placed_order.status != 5">
                                                    <button class="btn btn-danger btn-sm mt-3 opacity-25"
                                                        data-bs-toggle="tooltip" title="Your item is already preparing"
                                                        v-if="placed_order.status == 2 || placed_order.status == 3 || placed_order.status == 4">Cancel</button>
                                                    <button class="btn btn-outline-dark btn-sm mt-3"
                                                        @click.prevent="cancelItem(placed_order)" v-else>Cancel</button>
                                                </div>


                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <small><b>{{ formatPrice(placed_order.isOnSale === null ? placed_order.price :
                                            salePrice(placed_order) * getCartItemQuantity(placed_order))
                                        }}</b></small>
                                    </td>
                                </tr>

                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">Merchandise
                                            Subtotal :</b>
                                    </td>
                                    <td class="text-end">{{ formatPrice(calculateSubtotal()) }}</td>
                                </tr>
                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">
                                            Shipping
                                            Fee :
                                        </b>
                                    </td>
                                    <td class="text-end">{{ formatPrice(calculateShippingFee()) }}</td>
                                </tr>
                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">
                                            Order
                                            Total :
                                        </b>
                                    </td>
                                    <td class="text-end">{{ formatPrice(calculateTotal()) }}</td>
                                </tr>
                                <tr class="border-top-0 border-start-0 border-end-0 border-bottom-2">
                                    <td class="text-end pe-2" colspan="4" style="border-right:1px solid pink !important;">
                                        <b class="text-muted">
                                            Payment
                                            Method :
                                        </b>
                                    </td>
                                    <td class="text-end p-0">
                                        <div v-for="item_method in order_status" :key="item_method.id" class="p-1">
                                            <div v-if="item_method.payment_method === 'cod'">
                                                Cash on Delivery
                                            </div>
                                            <div v-else>Gcash E-wallet</div>
                                        </div>
                                    </td>
                                </tr>


                            </table>
                        </div>


                    </div>

                </div>
                <div v-else class="text-center mt-5">
                    <span class="iconify display-1" data-icon="tabler:shopping-cart-off"></span>
                    <p class="mt-3 fw-bold">No item found on your cart</p>
                    <router-link :to="{ name: 'home' }" class="btn btn-secondary btn-sm"><span class="iconify fs-2"
                            data-icon="flat-color-icons:shop"></span>
                        Continue Shopping</router-link>
                </div>
            </div>
        </section>

        <div class="modal fade" id="cancelItem" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <button type="button" class="btn-close me-1 mt-2" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-2">
                            <span class="iconify h1 fs-1" data-icon="noto-v1:sad-but-relieved-face"></span>
                            <h5>Are you sure?</h5>
                            <div class="d-flex flex-column gap-1">
                                <i>Feel free to reconsider your purchase. You can still buy this item.</i>
                            </div>

                            <div v-if="notes">
                                <div class="form-group text-left">
                                    <textarea class="form-control mt-3 mb-2"
                                        placeholder="Please enter the reason for cancelling this item"
                                        v-model="cancelForm.notes"
                                        :class="{ 'is-invalid': cancelForm.errors.has('notes') }"></textarea>

                                    <div v-if="cancelForm.errors.has('notes')" class="invalid-feedback">
                                        {{ cancelForm.errors.get('notes') }}
                                    </div>

                                </div>
                            </div>
                            <div class="text-center" v-if="!notes">
                                <a @click.prevent="inputNotes()" href="#"
                                    class="btn btn-danger btn-sm col-4 text-center mt-2 text-white">Yes, cancel it</a>
                            </div>

                            <div class="text-center" v-else>
                                <a @click.prevent="confirmCancellation()" href="#"
                                    class="btn btn-danger btn-sm col-4 text-center mt-2 text-white">Submit</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <Footer />

    </div>
</template>
  
<script>
import { mapState } from "vuex";
import { Form as CustomForm } from "vform";
import Navbar from "../template/shop/Navbar.vue";
import salePriceMixin from '../mixins/salePriceMixin';
import Footer from "../template/shop/Footer.vue";

export default {
    mixins: [salePriceMixin],
    components: {
        Navbar,
        CustomForm,
        Footer
    },
    data() {
        return {
            orders: [],
            placed_orders: [],
            order_status: [],
            billingAddress: {},
            changedStatuses: new Set(), // Set to track changed statuses
            selectedId: null,
            cancelby: '',
            cancelForm: new CustomForm({
                notes: "",
            }),
            notes: false,
            notesErrorMessage: '',
        };
    },
    methods: {
        async loadPlaceOrderDetails() {
            let uid = this.user.id; 
            let order_id = this.$route.params.orderId;
            let response = await axios.get(`/api/placed-order-details/${uid}?order_id=${order_id}`);
            this.placed_orders = response.data;
        },

        async loadOrderStatusDetails() {
            let uid = this.user.id;
            let order_id = this.$route.params.orderId;
            let response = await axios.get(`/api/get-order-items-status-details/${uid}?order_id=${order_id}`);
            this.order_status = response.data;
        },

        async loadBillingAddress() {
            let uid = this.user.id;
            let response = await axios.get("/api/get-billing-address/" + uid);
            this.billingAddress = response.data;
        },
        getIcon(status) {
            switch (status) {
                case 1:
                    return "icon-park-outline:transaction-order";
                case 2:
                    return "bi:box-seam";
                case 3:
                    return "material-symbols:local-shipping-outline";
                case 4:
                    return "icon-park-outline:receive";
                case 5:
                    return "ic:outline-star-rate";
                default:
                    return ""; // Return an empty string for unknown status
            }
        },
        getStatusLabel(status) {
            switch (status) {
                case 1:
                    return "Order Placed";
                case 2:
                    return "Packaging your Item";
                case 3:
                    return "To Ship";
                case 4:
                    return "To Receive";
                case 5:
                    return "Rate";
                default:
                    return "";
            }
        },
        shouldBounce(item, status) {
            if (item.status === status) {
                return true; // Always bounce for the current status
            }

            if (status === 1) {
                // Check if any previous statuses have been changed
                for (let i = 2; i <= 5; i++) {
                    if (this.isStatusChanged(item, i)) {
                        return false; // Do not bounce if any subsequent status is changed
                    }
                }
            }

            if (status === 2) {
                if (this.isStatusChanged(item, 3) || this.isStatusChanged(item, 4) || this.isStatusChanged(item, 5)) {
                    return true; // Bounce if any subsequent status is changed
                }
            }

            if (status === 3) {
                if (this.isStatusChanged(item, 4) || this.isStatusChanged(item, 5)) {
                    return true; // Bounce if any subsequent status is changed
                }
            }

            if (status === 4 && this.isStatusChanged(item, 5)) {
                return true; // Bounce if the subsequent status is changed
            }

            return false;
        },
        isStatusChanged(item, status) {
            return this.changedStatuses.has(`${item.id}-${status}`);
        },

        getCartItemQuantity(placedItem) {
            const orderItem = this.placed_orders.find(
                (orderItem) => orderItem.orders_id === placedItem.orders_id
            );
            return orderItem ? orderItem.qty : 0;
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
        calculateSubtotal() {
            let subtotal = 0;
            for (let item of this.placed_orders) {
                if (item.isOnSale === null) {
                    subtotal += item.price * this.getCartItemQuantity(item);
                } else {
                    subtotal += this.salePrice(item) * this.getCartItemQuantity(item);
                }      
            }
            return subtotal;
        },
        calculateShippingFee() {
            let shippingFee = 0;
            for (let item of this.placed_orders) {
                shippingFee += item.shipping_fee;
            }
            return shippingFee;
        },
        calculateTotal() {
            let subtotal = 0;
            let shippingFee = 0;
            for (let item of this.placed_orders) {
                if (item.isOnSale === null) {
                    subtotal += item.price * item.qty;
                } else {
                    subtotal += this.salePrice(item) * item.qty;
                }
                shippingFee += item.shipping_fee;
            }
            return subtotal + shippingFee;
        },


        async cancelItem(item) {
            this.cancelForm.selectedId = item.orders_id;
            $("#cancelItem").modal("show");
        },

        inputNotes() {
            this.notes = true;
        },

        async confirmCancellation() {
            if (this.cancelForm.selectedId !== null) {
                try {
                    const response = await axios.post(`/api/cancel-item`, {
                        id: this.cancelForm.selectedId,
                        notes: this.cancelForm.notes,
                    });

                    if (response.data && response.data.message) {
                        // Check if the response contains a message
                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'success',
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            customClass: {
                                popup: 'my-custom-alert',
                                title: 'my-custom-alert-title',
                                icon: 'my-custom-alert-icon'
                            }
                        });

                        this.cancelForm.errors.set('');
                        this.notes = false;
                        this.cancelForm.notes = '';
                        $('#cancelItem').modal('hide');
                        this.$router.push('/purchases');

                        return;
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        // Handle validation errors
                        this.cancelForm.errors.set(error.response.data.errors);
                    } else {
                        console.error(error);
                    }
                }
            }
        },

        loadCancelStatus() {
            // Loop through the order_status array
            for (let status of this.order_status) {
                this.cancelby = status.cancel_by == 1 ? 'Buyer' : 'Admin';
            }
        },

        goToPurchases() {
            this.$router.push('/purchases');
        },

        formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            const formattedDate = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
            const formattedTime = `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
            return `${formattedDate} ${formattedTime}`;
        },

    },
    computed: {
        ...mapState(["auth", "user"]),
    },
    mounted() {
        this.loadPlaceOrderDetails();
        this.loadOrderStatusDetails();
        this.loadBillingAddress();
        this.loadCancelStatus();
    },
};
</script>
  
<style scopred>
/* Bounce animation */
@keyframes bounce {

    0%,
    20%,
    50%,
    80%,
    100% {
        transform: translateY(0);
    }

    40% {
        transform: translateY(-20px);
    }

    60% {
        transform: translateY(-10px);
    }
}

.animated.bounce {
    animation: bounce 1s infinite;
}

.border-dashed {
    border-bottom: 1px solid dashed;
}
</style>
  