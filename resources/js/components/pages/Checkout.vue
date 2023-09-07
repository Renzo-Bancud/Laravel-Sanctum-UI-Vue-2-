<template>
    <div>
        <Loader v-if="isLoading" />

        <!-- Navigation-->
        <Navbar />

        <section class="py-5">
            <div class="container px-4 px-lg-5">

                <div class="row g-3 mb-4 align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="app-page-title mb-0 text-uppercase">To Checkout
                            <span v-if="hasBillingAddress() == false">| Delivery
                                Information</span>
                        </h1>
                    </div>
                    <div class="col-auto">
                        <div class="page-utilities">
                            <div class="d-flex">
                                <div class="mx-1 d-none">
                                    <input type="text" id="search-orders" name="searchorders"
                                        class="form-control search-orders" placeholder="Search" />
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
                        <form @submit.prevent="saveOrder">
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between lh-sm">
                                    <div>
                                        <h6 class="my-0">Cash on Delivery</h6>
                                        <small class="text-muted">Pay when you receive</small>
                                    </div>
                                    <input name="cod" type="radio" class="form-check-input"
                                        v-model="saveOrderForm.paymentType" value="cod" />
                                </li>
                                <li class="list-group-item d-flex justify-content-between lh-sm d-none">
                                    <div>
                                        <h6 class="my-0">Gcash</h6>
                                        <small class="text-muted">Gcash e-wallet</small>
                                    </div>
                                    <input name="e-wallet" type="radio" class="form-check-input"
                                        v-model="saveOrderForm.paymentType" value="e-wallet" />
                                </li>
                            </ul>

                            <div class="mb-3" v-if="Items.length > 0">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-2">Shipping Location</h4>
                                    </div>
                                    <div>
                                        <a href="#" class="hover-pulse"
                                            @click.prevent="editShippingAddress(billingAddress)">
                                            <span class="iconify fs-4" data-icon="basil:edit-outline"></span>
                                        </a>
                                    </div>
                                </div>

                                <div v-if="billingAddress && Object.keys(billingAddress).length > 0
                                    ">
                                    <span class="iconify fs-3"
                                        data-icon="streamline:travel-map-location-pin-navigation-map-maps-pin-gps-location"></span>
                                    {{ billingAddress.province }}, {{ billingAddress.city }},
                                    {{ billingAddress.barangay }}
                                </div>
                                <div v-else>
                                    <!-- Display a message when there is no data available -->
                                    <p>No address available</p>
                                </div>
                            </div>

                            <h3>Order Summary</h3>
                            <div class="d-flex justify-content-between">
                                <div>Subtotal:</div>
                                <div>
                                    {{ formatPrice(calculateSubtotal()) }}
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>Shipping Fee:</div>
                                <div>
                                    {{ formatPrice(calculateShippingFee()) }}
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>Grand Total:</div>
                                <div>
                                    <b class="text-danger fs-3">{{
                                        formatPrice(calculateTotal())
                                    }}</b>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit"
                                    v-if="billingAddress && Object.keys(billingAddress).length > 0 && Items.length > 0"
                                    class="btn btn-info border-dark rounded-0 form-control text-uppercase hover-pulse">
                                    Place Order Now
                                </button>

                                <button type="button" v-else-if="Items.length == 0"
                                    style="cursor:not-allowed  !important;opacity:0.5;"
                                    class="btn btn-info border-dark rounded-0 form-control text-uppercase">
                                    Place Order Now
                                </button>




                            </div>
                        </form>

                    </div>

                    <div class="col-md-7 col-lg-8">
                        <div class="app-card app-card-orders-table shadow-sm mb-5">
                            <div class="app-card-body p-3" v-if="hasBillingAddress() == false">
                                <small class="text-danger">*You have a pending order. Please fill out the delivery
                                    information to place your order.</small>

                                <form @submit.prevent="submitDeliveryInformation" class="mt-3">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="form-group mb-3">
                                                <label for="firstName" class="form-label">First name</label>
                                                <input type="text" class="form-control" id="firstName"
                                                    v-model="deliveryInfoForm.firstname" :class="{
                                                        'is-invalid':
                                                            deliveryInfoForm.errors.has('firstname'),
                                                    }" />
                                                <div v-if="errorFirstnameMessage" class="invalid-feedback">
                                                    {{ errorFirstnameMessage }}
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="lastName" class="form-label">Last name</label>
                                                <input type="text" class="form-control" id="lastName"
                                                    v-model="deliveryInfoForm.lastname" :class="{
                                                        'is-invalid': deliveryInfoForm.errors.has('lastname'),
                                                    }" />
                                                <div v-if="errorLastnameMessage" class="invalid-feedback">
                                                    {{ errorLastnameMessage }}
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="mobile" class="form-label">Mobile Number</label>
                                                <input type="text" class="form-control" id="mobile"
                                                    v-model="deliveryInfoForm.mobile" :class="{
                                                        'is-invalid': deliveryInfoForm.errors.has('mobile'),
                                                    }" />
                                                <div v-if="errorMobileMessage" class="invalid-feedback">
                                                    {{ errorMobileMessage }}
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="landmark" class="form-label">Landmark near me</label>
                                                <input type="text" class="form-control" id="landmark"
                                                    v-model="deliveryInfoForm.landmark" :class="{
                                                        'is-invalid':
                                                            deliveryInfoForm.errors.has('landmark'),
                                                    }" />
                                                <div v-if="errorLandmarkMessage" class="invalid-feedback">
                                                    {{ errorLandmarkMessage }}
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="notes" class="form-label">Notes (Optional)</label>
                                                <input type="text" class="form-control" id="notes"
                                                    v-model="deliveryInfoForm.notes" />
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group mb-3">
                                                <label for="houseDetails" class="form-label">House Number, Building and
                                                    Street
                                                    Name</label>
                                                <input type="text" class="form-control" id="houseDetails"
                                                    v-model="deliveryInfoForm.houseDetails" :class="{
                                                        'is-invalid':
                                                            deliveryInfoForm.errors.has('houseDetails'),
                                                    }" />
                                                <div v-if="errorHouseDetailsMessage" class="invalid-feedback">
                                                    {{ errorHouseDetailsMessage }}
                                                </div>
                                            </div>




                                            <div class="form-group mb-3">
                                                <label for="province" class="form-label">Province</label>
                                                <select class="form-control" id="province"
                                                    v-model="deliveryInfoForm.selectedProvince" @change="updateMunicipality"
                                                    :class="{
                                                        'is-invalid':
                                                            deliveryInfoForm.errors.has('selectedProvince'),
                                                    }">
                                                    <option value="">Select Province</option>
                                                    <option v-for="(province, provinceKey) in sortedProvinces"
                                                        :value="province" :key="provinceKey">
                                                        {{ province }}
                                                    </option>
                                                </select>
                                                <div v-if="errorProvinceMessage" class="invalid-feedback">
                                                    {{ errorProvinceMessage }}
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="municipality" class="form-label">City/Municipality</label>
                                                <select class="form-control" id="municipality"
                                                    v-model="deliveryInfoForm.selectedMunicipality" @change="updateBarangay"
                                                    :class="{
                                                        'is-invalid': deliveryInfoForm.errors.has(
                                                            'selectedMunicipality'
                                                        ),
                                                    }">
                                                    <option value="">Select City/Municipality</option>
                                                    <option v-for="(municipality, index) in municipalities"
                                                        :value="municipality" :key="index">
                                                        {{ municipality }}
                                                    </option>
                                                </select>
                                                <div v-if="errorMunicipalityMessage" class="invalid-feedback">
                                                    {{ errorMunicipalityMessage }}
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <div class="form-group">
                                                    <label for="barangay" class="form-label">Barangay</label>
                                                    <select class="form-control" id="barangay"
                                                        v-model="deliveryInfoForm.selectedBarangay" :class="{
                                                            'is-invalid':
                                                                deliveryInfoForm.errors.has('selectedBarangay'),
                                                        }">
                                                        <option value="">Select Barangay</option>
                                                        <option v-for="(barangay, index) in barangays" :value="barangay"
                                                            :key="index">
                                                            {{ barangay }}
                                                        </option>
                                                    </select>
                                                    <div v-if="errorBarangayMessage" class="invalid-feedback">
                                                        {{ errorBarangayMessage }}
                                                    </div>
                                                </div>
                                            </div>

                                            <label for="location" class="form-label">Select label for effective
                                                delivery:</label>

                                            <div :class="{ 'has-error': !isLocationSelected }">
                                                <div class="form-check">
                                                    <input id="office" name="office" type="radio" class="form-check-input"
                                                        v-model="deliveryInfoForm.selectedLocation" value="office" />
                                                    <label class="form-check-label" for="office">Office</label>
                                                </div>

                                                <div class="form-check">
                                                    <input id="home" name="home" type="radio" class="form-check-input"
                                                        value="home" v-model="deliveryInfoForm.selectedLocation" />
                                                    <label class="form-check-label" for="home">Home</label>
                                                </div>
                                            </div>

                                            <!-- Validation error message -->
                                            <p v-if="!isLocationSelected" class="error-message">
                                                Please select a location.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        <button class="btn btn-info btn-sm rounded-0 border-dark" type="submit">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="app-card-body" v-else>
                                <table class="table-res app-table-hover mb-0 text-left">
                                    <thead>
                                        <tr>
                                            <th class="cell">
                                                Product
                                            </th>
                                            <th class="cell">Unit Price</th>
                                            <th class="cell">Variation</th>
                                            <th class="cell">Quantity</th>
                                            <th class="cell">Item SubTotal</th>
                                            <th class="cell">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="Items.length == 0">
                                            <td class="text-center" colspan="6">
                                                <span class="iconify display-1" data-icon="tabler:shopping-cart-off"></span>
                                                <p class="mt-3 mb-2 fw-bold">No item found on your order</p>
                                                <div class="mb-3">
                                                    <router-link :to="{ name: 'home' }"
                                                        class="btn btn-secondary  border-dark btn-sm"><span
                                                            class="iconify fs-2" data-icon="flat-color-icons:shop"></span>
                                                        Continue Shopping</router-link>

                                                    <router-link :to="{ name: 'my-cart-list' }"
                                                        class="btn btn-info  border-dark btn-sm"><span class="iconify fs-2"
                                                            data-icon="noto-v1:shopping-cart"></span>
                                                        My Cart</router-link>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-else v-for="myItem in Items" :key="myItem.cart_item_id">
                                            <td class="custom-td">
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
                                            <td class="custom-td" data-label="Unit Price">{{ formatPrice(myItem.isOnSale
                                                === null ? myItem.price : salePrice(myItem))
                                            }}
                                            </td>
                                            <td class="custom-td" data-label="Variation">
                                                <span class="badge bg-secondary mx-1"
                                                    v-for="option in myItem.variation_options" :key="option.id">
                                                    {{ option.value }}
                                                </span>
                                            </td>
                                            <td class="custom-td" data-label="Quantity">
                                                {{ getCartItemQuantity(myItem) }}
                                            </td>
                                            <td class="custom-td text-secondary" data-label="Item Subtotal">
                                                {{ formatPrice(myItem.isOnSale === null ? myItem.price : salePrice(myItem) *
                                                    getCartItemQuantity(myItem)) }}
                                            </td>

                                            <td class="custom-td" data-label="Action">

                                                <button class="btn btn-outline-danger rounded-0 hover-pulse"
                                                    @click="removePendingItem(myItem)" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" title="Item(s) will be removed from order">
                                                    <span class="iconify" data-icon="bi:trash"></span>
                                                    Delete</button>

                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <!--//app-card-body-->
                        </div>
                    </div>




                </div>



                <div class="modal fade right" id="editAddress" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel2"></h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="updateDeliveryInformation">
                                    <div class="form-group mb-3">
                                        <label for="house_no" class="form-label">House No.</label>
                                        <textarea class="form-control" id="houseDetails"
                                            v-model="deliveryInfoForm.houseDetails" :class="{
                                                'is-invalid':
                                                    deliveryInfoForm.errors.has(
                                                        'houseDetails'
                                                    ),
                                            }"></textarea>

                                        <div v-if="errorHouseDetailsMessage" class="invalid-feedback">
                                            {{ errorHouseDetailsMessage }}
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="landmark" class="form-label">Landmark</label>
                                        <input class="form-control" id="landmark" v-model="deliveryInfoForm.landmark"
                                            :class="{
                                                'is-invalid':
                                                    deliveryInfoForm.errors.has(
                                                        'landmark'
                                                    ),
                                            }">

                                        <div v-if="errorLandmarkMessage" class="invalid-feedback">
                                            {{ errorLandmarkMessage }}
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="province" class="form-label">Province</label>
                                        <select class="form-control" id="province"
                                            v-model="deliveryInfoForm.selectedProvince" @change="updateMunicipality" :class="{
                                                'is-invalid':
                                                    deliveryInfoForm.errors.has(
                                                        'selectedProvince'
                                                    ),
                                            }">
                                            <option value="">Select Province</option>
                                            <option v-for="(
                                            province, provinceKey
                                        ) in sortedProvinces" :value="province" :key="provinceKey">
                                                {{ province }}
                                            </option>
                                        </select>
                                        <small><i>selected province will auto populate the cities</i></small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="municipality" class="form-label">City/Municipality</label>
                                        <select class="form-control" id="municipality" v-model="deliveryInfoForm.selectedMunicipality
                                            " @change="updateBarangay"
                                            :class="{ 'is-invalid': deliveryInfoForm.errors.has('selectedMunicipality'), }">
                                            <option value="">
                                                Select City/Municipality
                                            </option>
                                            <option v-for="(
                                            municipality, index
                                        ) in municipalities" :value="municipality" :key="index">
                                                {{ municipality }}
                                            </option>
                                        </select>
                                        <small><i>selected city will auto populate the barangays</i></small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="barangay" class="form-label">Barangay</label>
                                        <select class="form-control" id="barangay"
                                            v-model="deliveryInfoForm.selectedBarangay" :class="{
                                                'is-invalid':
                                                    deliveryInfoForm.errors.has(
                                                        'selectedBarangay'
                                                    ),
                                            }">
                                            <option value="">Select Barangay</option>
                                            <option v-for="(barangay, index) in barangays" :value="barangay" :key="index">
                                                {{ barangay }}
                                            </option>
                                        </select>
                                    </div>

                                    <label for="location" class="form-label">Selected label for delivery:</label>
                                    <div class="d-flex mb-4">
                                        <div class="form-check">
                                            <input id="office" name="office" type="radio" class="form-check-input"
                                                v-model="deliveryInfoForm.selectedLocation" value="office" />
                                            <label class="form-check-label" for="office">Office</label>
                                        </div>

                                        <div class="form-check mx-3">
                                            <input id="home" name="home" type="radio" class="form-check-input" value="home"
                                                v-model="deliveryInfoForm.selectedLocation" />
                                            <label class="form-check-label" for="home">Home</label>
                                        </div>
                                    </div>


                                    <button type="submit" class="btn btn-secondary float-end modal-btn"
                                        :disabled="deliveryInfoForm.busy"></button>
                                </form>
                            </div>
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
import { Form as CustomForm } from "vform";
import formatPriceMixin from '../mixins/formatPriceMixin';
import salePriceMixin from '../mixins/salePriceMixin';
import Footer from "../template/shop/Footer.vue";

export default {
    mixins: [formatPriceMixin, salePriceMixin],
    components: {
        Navbar,
        Footer
    },
    data() {
        return {
            deliveryInfoForm: new CustomForm({
                firstname: "",
                lastname: "",
                mobile: "",
                notes: "",
                houseDetails: "",
                landmark: "",
                selectedProvince: "",
                selectedMunicipality: "",
                selectedBarangay: "",
                selectedLocation: "",
            }),
            saveOrderForm: new CustomForm({
                paymentType: 'cod',
            }),
            provinces: [],
            municipalities: [],
            barangays: [],
            billingAddress: {},
            errorFirstnameMessage: "",
            errorLastnameMessage: "",
            errorMobileMessage: "",
            errorHouseDetailsMessage: "",
            errorLandmarkMessage: "",
            errorProvinceMessage: "",
            errorMunicipalityMessage: "",
            errorBarangayMessage: "",

            Items: [],
            formFilledOut: false,
            formSubmitted: false,
        };
    },
    methods: {
        async myCartItem() {
            try {
                this.$store.dispatch('showLoader');
                let { data } = await axios.get("/api/load-ordered-item");
                this.Items = data.selectedItems;
                // console.log(this.Items)
            } catch (error) {
                console.error(error);
            } finally {
                this.$store.dispatch('hideLoader');
            }
        },
        loadProvince() {
            fetch("/philippine_provinces_cities_municipalities_and_barangays_2019v2.json")
                .then((response) => response.json())
                .then((data) => {
                    const provinceList = Object.values(data).reduce((acc, region) => {
                        return { ...acc, ...region.province_list };
                    }, {});
                    this.provinces = provinceList;
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        },
        updateMunicipality() {
            if (this.deliveryInfoForm.selectedProvince) {
                const selectedProvinceData =
                    this.provinces[this.deliveryInfoForm.selectedProvince];
                this.municipalities = Object.keys(
                    selectedProvinceData.municipality_list
                );
            } else {
                this.municipalities = [];
            }
        },
        updateBarangay() {
            if (this.deliveryInfoForm.selectedMunicipality) {
                const selectedProvinceData =
                    this.provinces[this.deliveryInfoForm.selectedProvince];
                const selectedMunicipalityData =
                    selectedProvinceData.municipality_list[
                    this.deliveryInfoForm.selectedMunicipality
                    ];
                this.barangays = Object.values(selectedMunicipalityData.barangay_list);
            } else {
                this.barangays = [];
            }
        },

        async loadBillingAddress() {
            let uid = this.user.id;
            let response = await axios.get("/api/get-billing-address/" + uid);
            this.billingAddress = response.data;
        },

        async submitDeliveryInformation() {
            // Set formSubmitted to true
            this.formSubmitted = true;

            // Include the selectedLocation value in the request payload so that you can sent the value on the server if you have radio type inpput on form
            const payload = {
                ...this.deliveryInfoForm,
                selectedLocation: this.deliveryInfoForm.selectedLocation,
            };

            await this.deliveryInfoForm
                .post("/api/delivery-information", payload)
                .then(({ data }) => {
                    this.deliveryInfoForm.firstname = "";
                    this.deliveryInfoForm.lastname = "";
                    this.deliveryInfoForm.mobile = "";
                    this.deliveryInfoForm.notes = "";
                    this.deliveryInfoForm.houseDetails = "";
                    this.deliveryInfoForm.landmark = "";
                    this.deliveryInfoForm.selectedProvince = "";
                    this.deliveryInfoForm.selectedMunicipality = "";
                    this.deliveryInfoForm.selectedBarangay = "";
                    this.deliveryInfoForm.selectedLocation = "";


                    this.$swal.fire({
                        position: 'top-middle',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: 'my-custom-alert',
                            title: 'my-custom-alert-title',
                            icon: 'my-custom-alert-icon'
                        }
                    });


                    this.loadBillingAddress();

                    // axios.post("/api/order-items", { cartItems: this.cartItems }).then(response => {
                    // }).catch(error => {
                    //     console.error(error);
                    // });

                    this.myCartItem();


                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const errorData = error.response.data.errors;

                        if (Object.keys(errorData).length > 0) {
                            this.errorFirstnameMessage = errorData.firstname
                                ? errorData.firstname[0]
                                : "";
                            this.errorLastnameMessage = errorData.lastname
                                ? errorData.lastname[0]
                                : "";
                            this.errorHouseDetailsMessage = errorData.houseDetails
                                ? errorData.houseDetails[0]
                                : "";
                            this.errorLandmarkMessage = errorData.landmark
                                ? errorData.landmark[0]
                                : "";
                            this.errorProvinceMessage = errorData.selectedProvince
                                ? errorData.selectedProvince[0]
                                : "";
                            this.errorMunicipalityMessage = errorData.selectedMunicipality
                                ? errorData.selectedMunicipality[0]
                                : "";
                            this.errorBarangayMessage = errorData.selectedBarangay
                                ? errorData.selectedBarangay[0]
                                : "";
                        }

                        if (errorData.mobile && errorData.mobile.length > 0) {
                            this.errorMobileMessage = errorData.mobile[0];
                        } else {
                            this.errorMobileMessage = "Invalid mobile format.";
                            this.formSubmitted = false;
                        }

                        this.formSubmitted = false;
                    } else {
                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'error',
                            title: 'An error occurred. Please try again.',
                            showConfirmButton: false,
                            timer: 3000,
                            customClass: {
                                popup: 'my-custom-alert',
                                title: 'my-custom-alert-title',
                                icon: 'my-custom-alert-icon'
                            }
                        });
                    }
                });
        },



        getCartItemQuantity(item) {
            const cartItem = this.Items.find((cartItem) => cartItem.id === item.id);
            return cartItem ? cartItem.qty : 0;
        },

        calculateSubtotal() {
            let subtotal = 0;
            for (let item of this.Items) {

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
            for (let item of this.Items) {
                    shippingFee += item.shipping_fee;
            }
            return shippingFee;
        },

        calculateTotal() {
            let subtotal = 0;
            let shippingFee = 0;
            for (let item of this.Items) {
                    if (item.isOnSale === null) {
                        subtotal += item.price * item.qty;
                    } else {
                        subtotal += this.salePrice(item) * item.qty;
                    }
                    shippingFee += item.shipping_fee;
            }
            return subtotal + shippingFee;
        },


        removeSelectedItem(item) {
            // Dispatch an action called 'removeCartItem' to the Vuex store
            this.$store.dispatch('removeSelectedItem', item)
                .then(() => {
                    this.myCartItem();
                })
                .catch(error => {
                    console.error(error);
                });
        },
        hasBillingAddress() {
            return this.billingAddress && Object.keys(this.billingAddress).length > 0;
        },

        async editShippingAddress(address) {
            //console.log(address.house_no)
            $(".modal-title").text("Shipping Address");
            $(".modal-btn").text("Update Changes");

            this.deliveryInfoForm.houseDetails = address.house_no;
            this.deliveryInfoForm.landmark = address.landmark;
            this.deliveryInfoForm.selectedProvince = address.province;
            this.deliveryInfoForm.selectedMunicipality = address.city;
            this.deliveryInfoForm.selectedBarangay = address.barangay;
            this.deliveryInfoForm.selectedLocation = address.to_deliver;
            this.deliveryInfoForm.selectedId = address.id;

            $("#editAddress").modal("show");
        },

        async updateDeliveryInformation() {

            if (this.deliveryInfoForm.selectedId !== null) {
                let id = this.deliveryInfoForm.selectedId;

                // const payload = {
                //     ...this.deliveryInfoForm,
                //     selectedLocation: this.deliveryInfoForm.selectedLocation,
                // };

                // console.log(payload)

                await this.deliveryInfoForm
                    .post(`/api/edit-delivery-information/${id}`)
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
                                icon: 'my-custom-alert-icon'
                            }
                        });


                        this.loadBillingAddress();

                        $("#editAddress").modal("hide");
                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const errors = error.response.data.errors;
                            this.errorHouseDetailsMessage = errors.houseDetails ? errors.houseDetails[0] : "";
                            this.errorLandmarkMessage = errors.landmark ? errors.landmark[0] : "";
                            this.errorProvinceMessage = errors.selectedProvince ? errors.selectedProvince[0] : "";
                            this.errorMunicipalityMessage = errors.selectedMunicipality ? errors.selectedMunicipality[0] : "";
                            this.errorBarangayMessage = errors.selectedBarangay ? errors.selectedBarangay[0] : "";
                        } else {
                            this.$swal.fire({
                                position: 'top-middle',
                                icon: 'error',
                                title: 'An error occurred. Please try again.',
                                showConfirmButton: false,
                                timer: 3000,
                                customClass: {
                                    popup: 'my-custom-alert',
                                    title: 'my-custom-alert-title',
                                    icon: 'my-custom-alert-icon'
                                }
                            });
                        }
                    });


            }
        },

        async saveOrder() {
            this.$store.dispatch('showLoader');

            const ordersToUpdate = this.Items.map(order => order.order_id); // Extract order IDs

            const payload = {
                orders: ordersToUpdate,
                paymentType: this.saveOrderForm.paymentType,
            };

           //  console.log(payload)


            try {
                const { data } = await axios.post('/api/update-payment-type', payload);

                this.saveOrderForm.paymentType = '';

                this.$swal.fire({
                    position: 'top-middle',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'my-custom-alert',
                        title: 'my-custom-alert-title',
                        icon: 'my-custom-alert-icon'
                    }
                });


                const orderTrackIds = this.Items.map(order => order.order_track_id);
                //const item_slug = this.Items.map(order => order.slug);

                // Use the order_track_id in the router
                this.$router.push({ name: 'orders', params: { orderTrackId: orderTrackIds[0] } });

            } catch (error) {
                console.log(error);
            } finally {
                this.$store.dispatch('hideLoader');
            }
        },

        removePendingItem(myItem) {
            this.$store.dispatch('removePendingOrderItem', myItem)
                .then(() => {
                    this.myCartItem();
                })
                .catch(error => {
                    console.error(error);
                });
        },

    },
    computed: {
        user() {
            return this.$store.getters.getUser;
        },
        sortedProvinces() {
            return Object.keys(this.provinces).sort();
        },
        isLocationSelected() {
            return this.deliveryInfoForm.selectedLocation !== "";
        },
        isFormValid() {
            return this.formFilledOut || this.formSubmitted;
        },
        isLoading() {
            return this.$store.state.isLoading;
        },
    },

    mounted() {
        this.loadProvince();
        this.loadBillingAddress();
        this.myCartItem();
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
