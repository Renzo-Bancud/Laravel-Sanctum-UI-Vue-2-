<template>
    <div>
        <Loader v-if="isLoading" />

        <!-- Navigation-->
        <Navbar />

        <section class="py-5">
            <div class="container px-4 px-lg-5">

                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0 text-uppercase">My Cart</h1>
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
                <div class="row g-5">
                    <div class="col-md-5 col-lg-4 order-md-last">
                        <form>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Cash on Delivery</h6>
                                        <small class="text-muted">Pay when you receive</small>
                                    </div>
                                    <input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked
                                        required />
                                </li>
                                <li class="list-group-item d-flex justify-content-between lh-sm d-none">
                                    <div>
                                        <h6 class="my-0">Gcash</h6>
                                        <small class="text-muted">Gcash e-wallet</small>
                                    </div>
                                    <input id="credit" name="paymentMethod" type="radio" class="form-check-input"
                                        required />
                                </li>
                            </ul>

                            <h3>Order Summary</h3>
                            <div class="d-flex justify-content-between">
                                <div>Subtotal:</div>
                                <div v-if="this.$route.params.productId && this.$route.params.deliveryId">
                                    {{ formatPrice(subtotal) }}
                                </div>
                                <div v-else>{{ formatPrice(calculateSubtotal()) }}</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>Shipping Fee:</div>
                                <div v-if="this.$route.params.productId && this.$route.params.deliveryId">
                                    {{ formatPrice(shippingFee) }}
                                </div>
                                <div v-else>{{ formatPrice(calculateShippingFee()) }}</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>Grand Total:</div>
                                <div v-if="this.$route.params.productId && this.$route.params.deliveryId">
                                    <b class="text-danger fs-3">{{ formatPrice(total) }}</b>
                                </div>
                                <div v-else>
                                    <b class="text-danger fs-3">{{
                                        formatPrice(calculateTotal())
                                    }}</b>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div v-if="this.$route.params.productId && this.$route.params.deliveryId">
                                    <button type="submit"
                                        class="btn btn-info text-uppercase form-control rounded-0 border-dark"
                                        @click.prevent="placeCheckout()">
                                        Proceed to Checkout({{ getTotalSelectedItems }})
                                    </button>
                                </div>
                                <div v-else>
                                    <div v-if="getTotalSelectedItems === 0">
                                        <button type="submit"
                                            class="btn btn-info text-uppercase form-control rounded-0 border-dark"
                                            :disabled="!isFormValid">
                                            Proceed to Checkout({{ getTotalSelectedItems }})
                                        </button>
                                    </div>
                                    <div v-else>
                                        <button type="submit"
                                            class="btn btn-info text-uppercase form-control rounded-0 border-dark"
                                            @click.prevent="placeCheckout()">
                                            Proceed to Checkout({{ getTotalSelectedItems }})
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    <div class="col-md-7 col-lg-8">
                        <div class="app-card app-card-orders-table shadow-sm mb-5">
                            <div class="app-card-body">
                                <div class="table-responsive">
                                    <table class="table-res app-table-hover mb-0 text-left">
                                        <thead>
                                            <tr>
                                                <th class="cell">
                                                    <input type="checkbox" style="float:left" title="Select All"
                                                        v-model="selectAll" @change="toggleAllCheckboxes">
                                                    Product
                                                </th>
                                                <th class="cell">Unit Price</th>
                                                <th class="cell">Variation</th>
                                                <th class="cell">Quantity</th>
                                                <th class="cell">Total Price</th>
                                                <th class="cell">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="Items.length == 0">
                                                <td class="text-center" colspan="6">
                                                    <span class="iconify display-1"
                                                        data-icon="tabler:shopping-cart-off"></span>
                                                    <p class="mt-3 fw-bold">No item found on your cart</p>
                                                </td>
                                            </tr>

                                            <tr v-else v-for="(myItem, index) in Items" :key="index">


                                                <td class="custom-td"
                                                    :class="{ 'bg-white opacity-25': (myItem.qty_in - myItem.qty_out) <= 0 }">
                                                    <small style="float:left;" class="d-lg-none d-block">Click to purchase</small><br>
                                                    <input type="checkbox" style="float:left" class="form-check-input this-box"
                                                        :checked="isItemSelected(myItem)"
                                                        @change="updateSelectedItems(myItem)"
                                                        v-if="myItem.qty_in - myItem.qty_out > 0">




                                                    <div class="product-container">
                                                        <div class="d-flex flex-column">
                                                            <div>
                                                                <img class="" :src="myItem.main_picture" :alt="myItem.title"
                                                                    style="width: 100px; height: 100px;object-fit: contain;" />

                                                            </div>
                                                            <div class="mt-2 fw-bold">
                                                                {{ myItem.title }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="custom-td" data-label="Unit Price"
                                                    :class="{ 'bg-white opacity-25': (myItem.qty_in - myItem.qty_out) <= 0 }">
                                                    <div v-if="myItem.isOnSale === null">
                                                       <span> {{ formatPrice(myItem.price) }} </span>
                                                    </div>
                                                    <div v-else>
                                                        {{ salePrice(myItem) }}
                                                        <br>
                                                        <span class="old-price">
                                                            <del>
                                                                <span class="old-price-discount text-muted" style="text-decoration: line-through !important;">
                                                                    <span class="iconify" data-icon="pepicons-pop:peso"
                                                                        style="position: relative; top: -3px;"></span>
                                                                    {{ myItem.price }}
                                                                </span>
                                                            </del>
                                                            <span class="old-price-discount text-danger">
                                                                ({{ myItem.discount_percentage }}% off)
                                                            </span>
                                                        </span>

                                                    </div>
                                                </td>
                                                <td class="custom-td" data-label="Variation">
                                                    <span  class="badge bg-secondary mx-1" v-for="option in getVariationOptionsByItem(myItem)"
                                                        :key="option.id">
                                                        {{ option.value }}
                                                    </span>
                                                </td>
                                                <td class="custom-td" data-label="Quantity"
                                                    :class="{ 'bg-white opacity-25': (myItem.qty_in - myItem.qty_out) <= 0 }">


                                                    <div v-if="myItem.qty_in - myItem.qty_out <= 0" class="qty-container">
                                                        <button
                                                            class="qty-btn-minus btn btn-outline-secondary hover-pulse rounded-0"
                                                            type="button"><i class="fa fa-minus"></i></button>

                                                        <input type="text" name="qty" value="0" class="input-qty" />

                                                        <button
                                                            class="qty-btn-plus btn btn-outline-dark hover-pulse rounded-0"
                                                            type="button"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                    <div v-else class="qty-container">
                                                        <button
                                                            class="qty-btn-minus btn btn-outline-secondary hover-pulse rounded-0"
                                                            type="button" @click="decreaseQuantity(myItem)"><i
                                                                class="fa fa-minus"></i></button>

                                                        <input type="text" name="qty" :value="getCartItemQuantity(myItem)"
                                                            class="input-qty" />


                                                        <button
                                                            class="qty-btn-plus btn btn-outline-dark hover-pulse rounded-0"
                                                            type="button" @click="increaseQuantity(myItem)">
                                                            <i class="fa fa-plus"></i>
                                                        </button>

                                                    </div>


                                                    <span class="text-danger" style="position: relative;top:7px;"
                                                        v-if="myItem.qty_out != null">{{
                                                            getTotalItemLeft(myItem) - myItem.qty_out
                                                        }} items left</span>


                                                    <br>
                                                    <b v-if="myItem.qty_in - myItem.qty_out <= 0" class="fw-bolder"
                                                        style="position: relative;top:10px;">SOLD OUT</b>


                                                </td>
                                                <td class="custom-td text-secondary" data-label="Total Price"
                                                    :class="{ 'bg-white opacity-25': (myItem.qty_in - myItem.qty_out) <= 0 }">
                                                    <div v-if="myItem.isOnSale === null">
                                                        {{ formatPrice(myItem.price * getCartItemQuantity(myItem)) }}
                                                    </div>
                                                    <div v-else>
                                                        {{ formatPrice(salePrice(myItem) * getCartItemQuantity(myItem)) }}
                                                    </div>

                                                </td>
                                                <td class="custom-td" data-label="Action"
                                                    :class="{ 'bg-white opacity-25': (myItem.qty_in - myItem.qty_out) <= 0 }">
                                                    <div v-if="myItem.qty_in - myItem.qty_out <= 0">
                                                        <button class="btn btn-dark rounded-0 hover-pulse"
                                                            @click="removeItem(myItem)" data-bs-toggle="tooltip"
                                                            data-bs-placement="bottom"
                                                            title="Item(s) will be removed from order">
                                                            <span class="iconify" data-icon="bi:trash"></span>
                                                            Delete</button>
                                                    </div>
                                                    <div v-else>
                                                        <button class="btn btn-outline-danger rounded-0 hover-pulse"
                                                            @click="removeItem(myItem)" data-bs-toggle="tooltip"
                                                            data-bs-placement="bottom"
                                                            title="Item(s) will be removed from order">
                                                            <span class="iconify" data-icon="bi:trash"></span>
                                                            Delete</button>
                                                    </div>

                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <!--//table-responsive-->
                            </div>
                            <!--//app-card-body-->
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <Footer />
    </div>
</template>

<script>
import Navbar from "../template/shop/Navbar.vue";
import formatPriceMixin from '../mixins/formatPriceMixin';
import salePriceMixin from '../mixins/salePriceMixin';
import cartCalculationsMixin from '../mixins/cartCalculationMixin';
import variationOptionMixin from '../mixins/VariationOptionMixin';
import Footer from "../template/shop/Footer.vue";

export default {
    mixins: [formatPriceMixin, cartCalculationsMixin, salePriceMixin, variationOptionMixin],
    components: {
        Navbar,
        Footer,
    },
    data() {
        return {
            Items: [],
            Trackorders: [],
            searchform: {
                search: "",
            },
            selectedItems: [],
            selectAll: false,
            subtotal: 0,
            shippingFee: 0,
            total: 0,
        };
    },

    methods: {
        async myCart() {
            const params = {
                search: this.searchform.search,
                productId: null,
            };
            try {
                this.$store.dispatch('showLoader');
               let { data } = await axios.get("/api/load-my-cart", { params: params, });
                this.Items = data.MyCartitems;
            } catch (error) {
                console.error(error);
            } finally {
                this.$store.dispatch('hideLoader');
            }
        },

        async placeCheckout() {
            this.$store.dispatch('showLoader');
            if (!this.selectedItems) {
                console.error('No selected items');
                return;
            }
            // Map the selected items
            const selectedItems = this.selectedItems.map(orderItem => {
                return {
                    id: orderItem.item_id,
                    prod_id: orderItem.product_id,
                    price: orderItem.price,
                    isOnSale: orderItem.isOnSale,
                    discount_percentage: orderItem.discount_percentage,
                    shipping_fee: orderItem.shipping_fee,
                    qty_cart: orderItem.qty_cart,
                    cart_item_id: orderItem.item_id,
                    delivery_id: orderItem.delivery_id,
                    selected: orderItem.selected
                };
            });

            try {
                const response = await axios.post('/api/proceed-to-checkout', { selectedItems });
                const message = response.data.message;
                if (message === 'Exist') {
                    const get_prod_id = this.selectedItems.map(orderItem => orderItem.product_id);

                    const response = await axios.get("/api/track-order-status", { params: { prod_id: get_prod_id } });
                    this.Trackorders = response.data.track_items;

                    if (this.Trackorders.some(item => item.status === 1)) {

                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'info',
                            title: 'Order placed. Check your purchases.',
                            showConfirmButton: false,
                            timer: 3000,
                            customClass: {
                                popup: 'my-custom-alert',
                                title: 'my-custom-alert-title',
                                icon: 'my-custom-alert-icon'
                            }
                        });

                        this.$router.push({ name: 'purchases' });

                    }
                    else if (this.Trackorders.some(item => item.status === 0)) {

                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'info',
                            title: 'Item pending. Place this order to proceed.',
                            showConfirmButton: false,
                            timer: 3000,
                            customClass: {
                                popup: 'my-custom-alert',
                                title: 'my-custom-alert-title',
                                icon: 'my-custom-alert-icon'
                            }
                        });

                        this.$router.push({ name: 'checkout-details' });
                    }

                } else {
                    this.myCart();
                    this.$router.push({ name: 'checkout-details' });
                }

            } catch (error) {
                console.error(error);
            } finally {
                this.$store.dispatch('hideLoader');
            }
        },
        search() {
            this.myCart(1, this.searchform.search);
        },
        getCartItemQuantity(item) {
            const cartItem = this.Items.find((cartItem) => cartItem.delivery_id === item.delivery_id);
            return cartItem ? cartItem.qty_cart : 0;
        },
        getTotalItemLeft(item) {
            const cartItem = this.Items.find((cartItem) => cartItem.delivery_id === item.delivery_id);
            return cartItem.qty_in;
        },
        increaseQuantity(item) {
            if (this.getCartItemQuantity(item) == item.qty_in) {
                this.$swal.fire({
                    position: 'top-middle',
                    icon: 'info',
                    title: 'No quantity left',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'my-custom-alert',
                        title: 'my-custom-alert-title',
                        icon: 'my-custom-alert-icon'
                    }
                });
            } else if (this.getCartItemQuantity(item) === this.getTotalItemLeft(item) - item.qty_out) {
                this.$swal.fire({
                    position: 'top-middle',
                    icon: 'info',
                    title: 'No quantity left',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'my-custom-alert',
                        title: 'my-custom-alert-title',
                        icon: 'my-custom-alert-icon'
                    }
                });
            } else {
                item.qty_cart++;
            }
        },
        decreaseQuantity(item) {
            if (item.qty_cart > 1) {
                item.qty_cart--;
            }
        },

        toggleAllCheckboxes() {
            this.Items.forEach((item) => {
                if (item.qty_in - item.qty_out > 0) {
                    item.selected = this.selectAll;
                    if (this.selectAll) {
                        if (!this.selectedItems.includes(item)) {
                            this.selectedItems.push(item);
                        }
                    } else {
                        if (this.selectedItems.includes(item)) {
                            const index = this.selectedItems.indexOf(item);
                            this.selectedItems.splice(index, 1);
                        }
                    }
                }
            });
        },



        updateSelectedItems(item) {
            // Get the route parameters
            const productId = this.$route.params.productId;
            const deliveryId = this.$route.params.deliveryId;

            if (productId === item.product_id && deliveryId === item.delivery_id) {

                this.$swal.fire({
                    position: 'top-middle',
                    icon: 'info',
                    title: 'You have deselected your item',
                    showConfirmButton: false,
                    timer: 3000,
                    didClose: () => {
                        setTimeout(() => {
                            window.location.reload();
                        }, 0.1);
                    },
                    customClass: {
                        popup: 'my-custom-alert',
                        title: 'my-custom-alert-title',
                        icon: 'my-custom-alert-icon',
                    },
                });

                // Auto-select the item
                item.selected = true;
            } else {
                // Manual selection
                item.selected = !item.selected; // Toggle the selected state for manual checking/unchecking
            }

            this.selectedItems = this.Items.filter((item) => item.selected);
        },


        isItemSelected(myItem) {
            // Get the route parameters
            const productId = this.$route.params.productId;
            const deliveryId = this.$route.params.deliveryId;

            if (productId === myItem.product_id && deliveryId === myItem.delivery_id) {
                // For auto-selection, we don't rely on myItem.selected
                myItem.selected = true;
                this.recalculate();
                if (!this.selectedItems.includes(myItem)) {
                    this.selectedItems.push(myItem);
                }

                return true;
            } else {
                // For manual selection, use the myItem.selected property
                return myItem.selected;


            }
        },



        recalculate() {
            this.subtotal = this.calculateSubtotal();
            this.shippingFee = this.calculateShippingFee();
            this.total = this.calculateTotal();
        },


        removeItem(item) {
            this.$store.dispatch('removeCartItem', item)
                .then(() => {
                    this.myCart();
                })
                .catch(error => {
                    console.error(error);
                });
        },

    },
    computed: {
        isFormValid() {
            return this.formFilledOut || this.formSubmitted;
        },
        getTotalSelectedItems() {
            // If there is a productId in the route params, return the count of selected items,
            // otherwise, return the count of all selected items.
            if (this.$route.params.productId && this.$route.params.deliveryId) {
                return this.selectedItems.length > 0 ? this.selectedItems.length : 1;
            } else {
                return this.selectedItems.length;
            }
        },
        isLoading() {
            return this.$store.state.isLoading;
        },
    },


    mounted() {
        this.myCart();
        this.recalculate();
    },

};
</script>

<style>
.justify-desc {
    text-align: justify;
}

.list-group-flush {
    border: none;
}

.list-group-transparent {
    background-color: transparent;
}
</style>
