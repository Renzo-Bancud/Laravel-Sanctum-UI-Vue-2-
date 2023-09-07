<template>
    <div>
        <!-- Navigation-->
        <Navbar />

        <!-- Section-->
        <section class="py-5">
            <div class="container px-4 px-lg-5">
                <div class="row">

                    <div class="col-lg-4">
                        <div class="card mb-4 border-none">
                            <div class="card-header bg-white border-0 ms-auto">
                                <button class="btn btn-info border-dark rounded-0 btn-sm border-dark ms-auto"
                                    @click="editInfo('profile')">
                                    <span class="iconify fs-5" data-icon="uil:edit"></span>&nbsp;change</button>
                            </div>
                            <div class="card-body text-center">
                                <img class="rounded-circle img-fluid" v-if="user.profile == null"
                                    :src="require('../../assets/images/account.png')" alt="avatar" style="width: 135px" />
                                <img class="rounded-circle img-fluid" v-else :src="getuser.profile" alt="avatar"
                                    style="width: 139px;height: 139px;object-fit: contain;border:2px solid black;" /><br>
                                <h5 class="my-3">{{ user.name }}</h5>
                                <p class="text-muted mb-1 lead">Mindoro Bodega Sale Buyer</p>
                                <p class="text-muted" v-if="billingAddress.province != null"> {{ billingAddress.house_no }}, {{ billingAddress.province }},
                                    {{ billingAddress.city }},
                                    {{ billingAddress.barangay }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card mb-4 border-none">
                            <div class="card-body">
                                <div v-if="getuser && Object.keys(getuser).length > 0">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Full Name</p>
                                        </div>
                                        <div class="col-sm-9 d-flex justify-content-between">
                                            <p class="text-muted mb-0">{{ getuser.name }}</p>
                                            <button class="btn btn-info border-dark btn-sm rounded-0"
                                                @click="editInfo('name')">
                                                <span class="iconify fs-5" data-icon="uil:edit"></span>&nbsp;change
                                            </button>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Email</p>
                                        </div>
                                        <div class="col-sm-9 d-flex justify-content-between">
                                            <p class="text-muted mb-0">{{ getuser.email }}</p>
                                            <button class="btn btn-info border-dark btn-sm rounded-0"
                                                @click="editInfo('email')">
                                                <span class="iconify fs-5" data-icon="uil:edit"></span>&nbsp;change
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">Password</p>
                                    </div>
                                    <div class="col-sm-9 d-flex justify-content-between">
                                        <p class="text-muted mb-0">Password hidden for security.</p>
                                        <button class="btn btn-info border-dark btn-sm rounded-0"
                                            @click="editInfo('password')">
                                            <span class="iconify fs-5" data-icon="uil:edit"></span>&nbsp;change
                                        </button>
                                    </div>
                                </div>
                                <hr />
                                <div v-if="billingAddress && Object.keys(billingAddress).length > 0
                                    ">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Phone</p>
                                        </div>
                                        <div class="col-sm-9 d-flex justify-content-between">
                                            <p class="text-muted mb-0">
                                                {{ billingAddress.mobile_number }}
                                            </p>
                                            <button class="btn btn-info border-dark btn-sm rounded-0"
                                                @click="editInfo('number')">
                                                <span class="iconify fs-5" data-icon="uil:edit"></span>&nbsp;change
                                            </button>
                                        </div>
                                    </div>
                                    <hr />

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Address</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <p class="text-muted mb-0">
                                                        {{ billingAddress.house_no }},
                                                        {{ billingAddress.province }},
                                                        {{ billingAddress.city }},
                                                        {{ billingAddress.barangay }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <button class="btn btn-info border-dark btn-sm rounded-0 d-flex"
                                                        @click="editInfo('address')">
                                                        <span class="iconify fs-5" data-icon="uil:edit"></span>&nbsp;change
                                                    </button>
                                                </div>
                                            </div>
                                            <b class="text-primary">{{
                                                billingAddress.isCurrentLocation == 1
                                                ? "Current Address"
                                                : ""
                                            }}</b>
                                        </div>
                                    </div>
                                </div>

                                <div v-else>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Phone</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">--</p>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <p class="mb-0">Address</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">--</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <div class="modal fade right" id="loadInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel2"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" v-if="auth">

                        <form @submit.prevent="updateAccountInfo">
                            <div v-if="type === 'name'">
                                <div class="form-group">
                                    <label for="fname" class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model="accountInfoForm.name"
                                        :class="{ 'is-invalid': accountInfoForm.errors.has('name') }">
                                    <div v-if="errorNameMessage" class="invalid-feedback">
                                        {{ errorNameMessage }}
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="type === 'email'">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="text" class="form-control" v-model="accountInfoForm.email"
                                        :class="{ 'is-invalid': accountInfoForm.errors.has('email') }">
                                    <div v-if="errorEmailMessage" class="invalid-feedback">
                                        {{ errorEmailMessage }}
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="type === 'password'">
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Old Password</label>
                                    <input type="password" class="form-control" v-model="accountInfoForm.old_password"
                                        :class="{ 'is-invalid': accountInfoForm.errors.has('old_password') }">
                                    <div v-if="errorOldPasswordMessage" class="invalid-feedback">
                                        {{ errorOldPasswordMessage }}
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" v-model="accountInfoForm.password"
                                        :class="{ 'is-invalid': accountInfoForm.errors.has('password') }">
                                    <div v-if="errorPasswordMessage" class="invalid-feedback">
                                        {{ errorPasswordMessage }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="form-label">Re-type Password</label>
                                    <input type="password" class="form-control"
                                        v-model="accountInfoForm.password_confirmation">
                                </div>

                                <small style="position:relative;top:10px;"><i>The password will be updated if a new password
                                        is provided in the
                                        input.</i></small>
                            </div>

                            <div v-else-if="type === 'number'">
                                <div class="form-group">
                                    <label for="email" class="form-label">Phone Number</label>
                                    <input type="number" class="form-control" v-model="accountInfoForm.number"
                                        :class="{ 'is-invalid': accountInfoForm.errors.has('number') }">
                                    <div v-if="errorNumberMessage" class="invalid-feedback">
                                        {{ errorNumberMessage }}
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="type === 'address'">

                                <div class="form-group mb-3">
                                    <label for="house_no" class="form-label">House No.</label>
                                    <textarea class="form-control" id="houseDetails" v-model="accountInfoForm.houseDetails"
                                        :class="{
                                            'is-invalid':
                                                accountInfoForm.errors.has(
                                                    'houseDetails'
                                                ),
                                        }"></textarea>

                                    <div v-if="errorHouseDetailsMessage" class="invalid-feedback">
                                        {{ errorHouseDetailsMessage }}
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="landmark" class="form-label">Landmark</label>
                                    <input class="form-control" id="landmark" v-model="accountInfoForm.landmark" :class="{
                                        'is-invalid':
                                            accountInfoForm.errors.has(
                                                'landmark'
                                            ),
                                    }">

                                    <div v-if="errorLandmarkMessage" class="invalid-feedback">
                                        {{ errorLandmarkMessage }}
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="province" class="form-label">Province</label>
                                    <select class="form-control" id="province" v-model="accountInfoForm.selectedProvince"
                                        @change="updateMunicipality" :class="{
                                            'is-invalid':
                                                accountInfoForm.errors.has(
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
                                    <select class="form-control" id="municipality" v-model="accountInfoForm.selectedMunicipality
                                        " @change="updateBarangay"
                                        :class="{ 'is-invalid': accountInfoForm.errors.has('selectedMunicipality'), }">
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
                                    <select class="form-control" id="barangay" v-model="accountInfoForm.selectedBarangay"
                                        :class="{
                                            'is-invalid':
                                                accountInfoForm.errors.has(
                                                    'selectedBarangay'
                                                ),
                                        }">
                                        <option value="">Select Barangay</option>
                                        <option v-for="(barangay, index) in barangays" :value="barangay" :key="index">
                                            {{ barangay }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div v-else-if="type === 'profile'">
                                <div class="form-group">
                                    <label for="profile" class="form-label">Profile Picture</label>

                                    <input type="file" class="form-control mt-2" name="profile" id="profile"
                                        @change="onImageChange"
                                        :class="{ 'is-invalid': accountInfoForm.errors.has('profile') }" />
                                    <div v-if="errorProfileMessage" class="invalid-feedback">
                                        {{ errorProfileMessage }}
                                    </div>
                                </div>
                            </div>


                            <button type="submit" :disabled="accountInfoForm.busy"
                                class="btn btn-secondary border-dark btn-sm hover-pulse mt-4">Save
                                Changes</button>
                        </form>



                    </div>
                </div>
            </div>
        </div>

        <!-- Footer-->
        <Footer />

    </div>
</template>
  
<script>
import Navbar from "../template/shop/Navbar.vue";
import { Form as CustomForm } from "vform";
import { objectToFormData } from "object-to-formdata";
import Footer from "../template/shop/Footer.vue";

export default {
    components: {
        Navbar,
        objectToFormData,
        Footer,
    },
    data() {
        return {
            accountInfoForm: new CustomForm({
                name: "",
                email: "",
                password: "",
                old_password: "",
                password_confirmation: "",
                number: "",
                houseDetails: "",
                landmark: "",
                selectedProvince: "",
                selectedMunicipality: "",
                selectedBarangay: "",
                profile: "",
            }),
            errorNameMessage: "",
            errorEmailMessage: "",
            errorPasswordMessage: "",
            errorOldPasswordMessage: "",
            errorNumberMessage: "",
            errorHouseDetailsMessage: "",
            errorLandmarkMessage: "",
            errorProvinceMessage: "",
            errorProfileMessage: "",
            billingAddress: {},
            getuser: {},
            show: false,
            type: '',
            selectedId: null,
            provinces: [],
            municipalities: [],
            barangays: [],
        };
    },
    methods: {
        async loadBillingAddress() {
            let uid = this.user.id;
            let response = await axios.get("/api/get-billing-address/" + uid);
            this.billingAddress = response.data;
        },

        async loadUser() {
            let response = await axios.get("/api/load-user");
            this.getuser = response.data.user;
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
            if (this.accountInfoForm.selectedProvince) {
                const selectedProvinceData =
                    this.provinces[this.accountInfoForm.selectedProvince];
                this.municipalities = Object.keys(
                    selectedProvinceData.municipality_list
                );
            } else {
                this.municipalities = [];
            }
        },
        updateBarangay() {
            if (this.accountInfoForm.selectedMunicipality) {
                const selectedProvinceData =
                    this.provinces[this.accountInfoForm.selectedProvince];
                const selectedMunicipalityData =
                    selectedProvinceData.municipality_list[
                    this.accountInfoForm.selectedMunicipality
                    ];
                this.barangays = Object.values(selectedMunicipalityData.barangay_list);
            } else {
                this.barangays = [];
            }
        },

        editInfo(type) {
            $(".modal-title").html(
                "<span class='iconify fs-1' data-icon='solar:user-id-linear'></span> My Account"
            );
            this.type = type;
            this.accountInfoForm.name = this.getuser.name;
            this.accountInfoForm.email = this.getuser.email;
            this.accountInfoForm.password = '';
            this.accountInfoForm.number = this.billingAddress.mobile_number;

            this.accountInfoForm.houseDetails = this.billingAddress.house_no;
            this.accountInfoForm.landmark = this.billingAddress.landmark;

            this.accountInfoForm.selectedProvince = this.billingAddress.province;
            this.accountInfoForm.profile = this.getuser.profile;
            this.accountInfoForm.selectedId = this.user.id;
            $("#loadInfo").modal("show");
        },

        async updateAccountInfo() {
            if (this.accountInfoForm.selectedId !== null) {
                let id = this.accountInfoForm.selectedId;

                const config = {
                    transformRequest: [
                        function (data, headers) {
                            return objectToFormData(data);
                        },
                    ],
                    onUploadProgress: (e) => {
                        console.log(e);
                    },
                };

                await this.accountInfoForm.post(`/api/edit-account-information/${id}`, {}, config).then((response) => {

                    // Hide the modal first
                    $("#loadInfo").modal("hide");

                    this.$swal.fire({
                        position: 'top-middle',
                        icon: 'success',
                        title: 'Account updated',
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: 'my-custom-alert',
                            title: 'my-custom-alert-title',
                            icon: 'my-custom-alert-icon',
                        },
                    });

                    this.loadBillingAddress();
                    this.loadUser();


                }).catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        this.errorNameMessage = errors.name ? errors.name[0] : "";
                        this.errorEmailMessage = errors.email ? errors.email[0] : "";
                        this.errorPasswordMessage = errors.password ? errors.password[0] : "";
                        this.errorOldPasswordMessage = errors.old_password ? errors.old_password[0] : "";
                        this.errorNumberMessage = errors.number ? errors.number[0] : "";
                        this.errorHouseDetailsMessage = errors.houseDetails ? errors.houseDetails[0] : "";
                        this.errorLandmarkMessage = errors.landmark ? errors.landmark[0] : "";
                        this.errorProvinceMessage = errors.selectedProvince ? errors.selectedProvince[0] : "";
                        this.errorProfileMessage = errors.profile ? errors.profile[0] : "";
                    }

                    let errorMessage = "Please fill in empty fields: ";
                    const emptyFields = [];

                    if (this.errorNameMessage) {
                        emptyFields.push("Name");
                        this.editInfo('name')
                    }

                    if (this.errorEmailMessage) {
                        emptyFields.push("Email");
                        this.editInfo('email')
                    }

                    if (this.errorNumberMessage) {
                        emptyFields.push("Number");
                        this.editInfo('number')
                    }

                    if (this.errorHouseDetailsMessage) {
                        emptyFields.push("House Details");
                        this.editInfo('address')
                    }

                    if (this.errorLandmarkMessage) {
                        emptyFields.push("Landmark");
                        this.editInfo('address')
                    }

                    if (emptyFields.length > 0) {
                        errorMessage += emptyFields.join(", ") + ". Thank you";
                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'success',
                            title: errorMessage,
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
            }
        },
        onImageChange(e) {
            const file = e.target.files[0];
            this.accountInfoForm.profile = file;
        },

    },
    computed: {
        auth() {
            return this.$store.getters.getAuthenticated;
        },
        user() {
            return this.$store.getters.getUser;
        },
        sortedProvinces() {
            return Object.keys(this.provinces).sort();
        },
    },
    mounted() {
        this.loadProvince();
        this.loadBillingAddress();
        this.loadUser();
    },
};
</script>
  
