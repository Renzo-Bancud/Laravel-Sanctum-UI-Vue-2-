<template>
    <div>
        <!--====== Footer Dark Style 3 Part Start ======-->
        <section class="footer-style-3 footer-dark-style-3 pt-4 pb-2">
            <div class="container">
                <div class="footer-top">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 col-md-7 col-sm-10">
                            <div class="footer-logo text-center">
                                <a href="#0">
                                    <img :src="require('../../../assets/images/logo_footer.png')" alt="" width="200" />
                                </a>
                            </div>
                            <div class="footer-widget-subscribe">
                                <p>
                                    Be the first to know when new products drop and get
                                    behind-the-scenes content straight.
                                </p>

                                <div class="subscribe-form">
                                    <form @submit.prevent="submitForm">
                                        <div class="single-form form-white">
                                            <label>For more updates. Enter your email address</label>
                                            <div class="form-input">
                                                <input type="text" placeholder="Enter your email"
                                                    v-model="email" />
                                                <i class="mdi mdi-account text-white"></i>
                                                <button type="submit" class="main-btn secondary-1-btn">
                                                    <span class="iconify hover-pulse" style="position: relative; top: -3px;"
                                                        data-icon="fluent:send-24-regular"></span>
                                                </button>
                                            </div>

                                            <div v-if="errorMessage" class="error-message">
                                                {{ errorMessage }}
                                            </div>

                                            <!-- Show success message -->
                                            <div v-if="successMessage" class="success-message">
                                                {{ successMessage }}
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="footer-follow text-center">
                                <a :href="this.contacts.facebook"
                                    style="line-height: 36px;color: white;font-size: 16px;padding-top:5px;"><span
                                        class="iconify fs-1" data-icon="devicon:facebook"></span> Facebook</a>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-copyright text-center">
                    <p>
                        &copy; Mindoro Bodega Sale. All rights reserved.
                    </p>
                </div>
            </div>
        </section>
        <!--====== Footer Dark Style 3 Part Ends ======-->
    </div>
</template>

<script>
export default {
    data() {
        return {
            contacts: [],
            email: '',
            successMessage: "", // To store the success message
            errorMessage: "",   // To store the validation error message
        };
    },
    methods: {
        async loadContacts() {
            try {
                const response = await axios.get("/api/contact-us");
                this.contacts = response.data;
            } catch (error) {
                console.error(error);
            }
        },

        async submitForm() {
            try {
                const response = await axios.post("/api/subscribe", {
                    email: this.email,
                });

                // If the request was successful
                this.successMessage = response.data.message;
                this.errorMessage = ""; // Clear any previous error message
                // Clear the email input after successful submission
                this.email = "";

                setTimeout(() => {
                    this.successMessage = "";
                }, 3000);

            } catch (error) {
                // Handle errors here
                if (error.response && error.response.status === 422) {
                    this.errorMessage = error.response.data.errors.email[0];
                } else {
                    this.errorMessage = "An error occurred. Please try again later.";
                }
                this.successMessage = ""; // Clear any previous success message

                setTimeout(() => {
                    this.errorMessage = "";
                }, 3000);
            }
        },
    },
    mounted() {
        this.loadContacts();
    },
};
</script>
<style scoped>
.error-message {
    color: red;
    /* ... other styles ... */
}

.success-message {
    color: Yellow;
}
</style>