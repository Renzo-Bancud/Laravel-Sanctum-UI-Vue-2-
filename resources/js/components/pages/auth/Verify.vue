<template>
    <div class="app app-login p-0">
        <div class="row g-0 app-auth-wrapper-login">
            <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
                <div class="d-flex flex-column align-content-end">
                    <div class="app-auth-body mx-auto">
                        <div class="app-auth-branding mb-3">

                            <router-link :to="{ name: 'home' }" class="app-logo"><img class="logo-icon"
                                    :src="require('../../../assets/images/logo.png')" alt="logo"
                                    style="object-fit:contain;width:200px;" /></router-link>

                        </div>
                        <div class="mb-5">
                            <h2 class="auth-heading text-center">Account Verification</h2>
                            <small>Please check your mailbox&nbsp;<span class="iconify fs-4"
                                    data-icon="uil:fast-mail"></span> for your 6-digit code.</small>
                        </div>

                        <div class="auth-form-container text-start">
                            <form class="auth-form login-form" method="post" @submit.prevent="verify()">

                                <div class="code mb-4">
                                    <label class="sr-only" for="signin-email"></label>

                                    <div class="input-group">
                                        <input v-for="(digit, index) in verifyForm.verificationCode" :key="index"
                                            v-model="verifyForm.verificationCode[index]" ref="verificationInput"
                                            id="signin-code" name="signin-email" type="text"
                                            class="form-control text-center mx-1 rounded-0 p-2" maxlength="1"
                                            @input="onInput(index)" />
                                    </div>


                                    <div v-if="verifyErrorMessage" class="invalid-feedback mt-2 d-block text-white">
                                        {{ verifyErrorMessage }}
                                        <button v-if="showResendButton" @click="requestAnotherCode"
                                            class="btn btn-sm btn-dark p-1">Get Another Code</button>
                                    </div>

                                </div><!--//form-group-->


                                <div class="text-center">
                                    <button type="submit" :disabled="verifyForm.busy"
                                        class="btn app-btn-primary w-100 theme-btn mx-auto">Submit</button>
                                </div>
                            </form>

                            <div class="auth-option text-center pt-3">
                                <router-link :to="{ name: 'home' }" class="mt-1 text-decoration-none hover-pulse"><span
                                        class="iconify fs-2" data-icon="flat-color-icons:shop"></span><br>
                                    <span class="fw-bold text-dark">Go to Shop</span></router-link>
                            </div>
                        </div><!--//auth-form-container-->

                    </div><!--//auth-body-->

                    <footer class="app-auth-footer">
                        <div class="container text-center py-3">
                            <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
                            <small class="copyright">Copyright <span class="iconify"
                                    data-icon="emojione-v1:copyright"></span> All rights reserved.</small>

                        </div>
                    </footer><!--//app-auth-footer-->
                </div><!--//flex-column-->
            </div><!--//auth-main-col-->
            <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
                <div class="auth-background-holder">
                </div>
                <div class="auth-background-mask"></div>
                <div class="auth-background-overlay p-3 p-lg-5">
                    <div class="d-flex flex-column align-content-end h-100">
                        <div class="h-100"></div>
                        <div class="overlay-content p-3 p-lg-4 rounded d-none">
                            <h5 class="mb-3 overlay-title">Explore Portal Admin Template</h5>
                            <div>Portal is a free Bootstrap 5 admin dashboard template. You can download and view the
                                template license <a
                                    href="https://themes.3rdwavemedia.com/bootstrap-templates/admin-dashboard/portal-free-bootstrap-admin-dashboard-template-for-developers/">here</a>.
                            </div>
                        </div>
                    </div>
                </div><!--//auth-background-overlay-->
            </div><!--//auth-background-col-->

        </div><!--//row-->

    </div>
</template>

<script>
import { Form as CustomForm } from 'vform'
import { refreshCsrfToken } from "../../services/apiService"; 

export default {
    components: { CustomForm },
    data() {
        return {
            verifyForm: new CustomForm({
                verificationCode: Array(6).fill(""),
            }),
            verifyErrorMessage: "",
            showResendButton: false
        }
    },
    methods: {
        async verify() {
            try {
                const email = this.$route.query.email;
                await axios.get("/sanctum/csrf-cookie");
                const response = await this.verifyForm.post(`/api/verify?email=${email}`);
                if (response.data.call === 'kKks*^M8x6tU04@p*k%pu0a2Ab9JSn') {

                    this.$swal.fire({
                        position: 'top-middle',
                        icon: 'success',
                        title: "Account Login",
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: 'my-custom-alert',
                            title: 'my-custom-alert-title',
                            icon: 'my-custom-alert-icon'
                        }
                    });

                    await this.getUserData();
                    this.$router.push({ name: "home" });

                }
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 422) {
                        // Validation errors
                        const errors = error.response.data.errors;
                        this.verifyErrorMessage = errors.verificationCode ? errors.verificationCode[0] : "";
                    } else if (error.response.status === 429) {
                        // Too Many Requests
                        this.errorMessage = "Too many login attempts. Please try again later.";
                    } else if (error.response.status === 419) {
                        // Authentication Timeout (CSRF token issue)
                        this.errorMessage = "Session expired. Please refresh the page and try again.";
                    } else if (error.response.status === 400) {
                        if (error.response.data.message === 'Verification code has expired.') {
                            this.verifyErrorMessage = error.response.data.message;
                            this.showResendButton = true;
                        } else {
                            this.verifyErrorMessage = error.response.data.message
                        }
                    } else if (error.response.status === 401) {
                        // Invalid credentials
                        this.errorMessage = error.response.data.message;
                    } else if (error.response.status === 500) {
                        // Internal Server Error
                        this.errorMessage = "An internal server error occurred. Please try again later.";
                    }
                } else {
                    // Network error or other unexpected errors
                    this.errorMessage = "An error occurred. Please check your internet connection and try again.";
                }
            }
        },

        async requestAnotherCode() {
            try {
                // Make an API call to send the email with a new verification code
                const response = await axios.post('/api/request-code');

                this.updateVerificationCode(response.data.verificationCode);

                this.$swal.fire({
                    position: 'top-middle',
                    icon: 'success',
                    title: "New code resent successfully",
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'my-custom-alert',
                        title: 'my-custom-alert-title',
                        icon: 'my-custom-alert-icon'
                    }
                });


                this.verifyForm.verificationCode = ''
                this.showResendButton = false;

            } catch (error) {
                // console.error(error);
            }
        },

        async updateVerificationCode(newVerificationCode) {
            try {
                // Make an API call to update the email_verification_code in the database
                await axios.put('/api/get-new-code', {
                    verificationCode: newVerificationCode
                });
            } catch (error) {
                // console.error(error);
            }
        },

        async getUserData() {
            const response = await axios.get('/api/user');
            let user = response.data;
            this.$store.commit('SET_USER', user);
            this.$store.commit('SET_AUTHENTICATED', true);
            localStorage.setItem("auth", true);
        },

        onInput(index) {
            const nextInput = this.$refs.verificationInput[index + 1];
            if (nextInput && this.verifyForm.verificationCode[index].length === 1) {
                nextInput.focus();
            }
        },
    },
}
</script>

<style></style>