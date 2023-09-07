<template>
    <div class="app">
        <div class="app-wrapper">
            <div class="app-content pt-3 p-md-3 p-lg-4">
                <div class="container-xl">
                    <h1 class="app-page-title">Settings</h1>
                    <hr class="mb-4">
                    <div class="row g-4 settings-section">
                        <div class="col-12 col-md-4">
                            <h3 class="section-title">Contact Us</h3>
                            <div class="section-intro">The Contact Settings section allows you to modify the Contact Us
                                details and Seller Contact Number.</div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="app-card app-card-settings shadow-sm p-4">

                                <div class="app-card-body">
                                    <form class="settings-form" @submit.prevent="updateDetails()">
                                        <div class="mb-3">
                                            <label for="setting-input-1" class="form-label">Phone Number:</label>
                                            <input type="number" class="form-control" id="setting-input-1"
                                                :class="{ 'is-invalid': detailsForm.errors.has('phone') }"
                                                v-model="detailsForm.phone">
                                            <div v-if="phoneErrorMessage" class="invalid-feedback d-block">
                                                {{ phoneErrorMessage }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="setting-input-2" class="form-label">Address:</label>
                                            <input type="text" class="form-control" id="setting-input-2"
                                                :class="{ 'is-invalid': detailsForm.errors.has('address') }"
                                                v-model="detailsForm.address">
                                            <div v-if="addressErrorMessage" class="invalid-feedback d-block">
                                                {{ addressErrorMessage }}
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="setting-input-3" class="form-label">Gmail:</label>
                                            <input type="email" class="form-control" id="setting-input-3"
                                                :class="{ 'is-invalid': detailsForm.errors.has('gmail') }"
                                                v-model="detailsForm.gmail">
                                            <div v-if="gmailErrorMessage" class="invalid-feedback d-block">
                                                {{ gmailErrorMessage }}
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="setting-input-4" class="form-label">Facebook URL:</label>
                                            <input type="email" class="form-control" id="setting-input-4"
                                                :class="{ 'is-invalid': detailsForm.errors.has('facebook') }"
                                                v-model="detailsForm.facebook">
                                            <div v-if="facebookErrorMessage" class="invalid-feedback d-block">
                                                {{ facebookErrorMessage }}
                                            </div>
                                        </div>
                                        <button type="submit" class="btn app-btn-primary">Save Changes</button>
                                    </form>
                                </div><!--//app-card-body-->

                            </div><!--//app-card-->
                        </div>
                    </div><!--//row-->
                    <hr class="my-4">
                    <div class="row g-4 settings-section">
                        <div class="col-12 col-md-4">
                            <h3 class="section-title">About Us</h3>
                            <div class="section-intro">The About Us Settings section allows you to modify the About Us
                                details</div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="app-card app-card-settings shadow-sm p-4">

                                <div class="app-card-body">
                                    <form class="settings-form" @submit.prevent="updateDetails()">
                                        <div class="mb-3">
                                            <label for="setting-input-6" class="form-label">About Us Details:</label>
                                            <textarea class="form-control" id="setting-input-6" rows="15"
                                                v-model="detailsForm.description"   :class="{ 'is-invalid': detailsForm.errors.has('description') }"></textarea>
                                            <div v-if="descriptionErrorMessage" class="invalid-feedback d-block">
                                                {{ descriptionErrorMessage }}
                                            </div>
                                        </div>
                                        <button type="submit" class="btn app-btn-primary">Save Changes</button>
                                    </form>
                                </div><!--//app-card-body-->

                            </div><!--//app-card-->
                        </div>
                    </div><!--//row-->
                </div><!--//container-fluid-->
            </div><!--//app-content-->

            <Footer />

        </div><!--//app-wrapper-->
    </div>
</template>


<script>
import { Form as CustomForm } from "vform";
import Footer from "../../template/Footer.vue";

export default {
    components: {
        Footer,
        CustomForm,
    },

    data() {
        return {
            details: [],
            detailsForm: new CustomForm({
                phone: "",
                address: "",
                gmail: "",
                facebook: "",
                description: "",
            }),
            phoneErrorMessage: "",
            addressErrorMessage: "",
            gmailErrorMessage: "",
            facebookErrorMessage: "",
            descriptionErrorMessage: "",
        };
    },
    methods: {
        async loadDetails() {
            try {
                const response = await axios.get("/api/company-details");
                this.details = response.data.details;


                this.detailsForm.phone = this.details[0].phone;
                this.detailsForm.address = this.details[0].address;
                this.detailsForm.gmail = this.details[0].gmail;
                this.detailsForm.facebook = this.details[0].facebook;
                this.detailsForm.description = this.details[0].about;

            } catch (error) {
                console.error(error);
            }
        },


        async updateDetails() {
            try {
                // Send the detailsForm data as JSON to the server
                const response = await axios.post("/api/update-company-details", this.detailsForm);

                 
                this.phoneErrorMessage = '';
                this.addressErrorMessage = '';
                this.gmailErrorMessage = '';
                this.facebookErrorMessage = '';
                this.descriptionErrorMessage = '';

                this.$swal.fire({
                    position: "top-middle",
                    icon: "success",
                    title: response.data.message,
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: "my-custom-alert",
                        title: "my-custom-alert-title",
                        icon: "my-custom-alert-icon",
                    },
                });

                this.loadDetails();

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    this.phoneErrorMessage = errors.phone ? errors.phone[0] : "";
                    this.addressErrorMessage = errors.address ? errors.address[0] : "";
                    this.gmailErrorMessage = errors.gmail ? errors.gmail[0] : "";
                    this.facebookErrorMessage = errors.facebook ? errors.facebook[0] : "";
                    this.descriptionErrorMessage = errors.description ? errors.description[0] : "";
                } else {
                    this.$swal.fire({
                        position: "top-middle",
                        icon: "error",
                        title: "An error occurred. Please try again.",
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: "my-custom-alert",
                            title: "my-custom-alert-title",
                            icon: "my-custom-alert-icon",
                        },
                    });
                }
            } 
        }
    },

    async created() {
        await this.loadDetails();
    },
};
</script>