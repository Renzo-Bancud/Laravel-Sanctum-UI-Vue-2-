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
                        <div class="mb-3">
                            <h2 class="auth-heading text-center">Reset Password</h2>
                            <small>Provide the following information to reset your password.</small>
                        </div>

                        <div class="auth-form-container text-start">
                            <form class="auth-form login-form" @submit.prevent="resetPassword()">
                                <div class="code mb-1">
                                    <label class="sr-only" for="signin-email"></label>
                                    <input id="signin-code" name="signin-email" type="email"
                                        class="form-control signin-code" v-model="resetPasswordForm.email"
                                        placeholder="Enter your email address" :disabled="isDisabled"
                                        :class="{ 'is-invalid': resetPasswordForm.errors.has('email') || resetEmailPasswordErrorMessage }">
                                    <div v-if="resetEmailPasswordErrorMessage"
                                        class="invalid-feedback mt-2 d-block text-white">
                                        {{ resetEmailPasswordErrorMessage }}
                                    </div>
                                </div><!--//form-group-->


                                <div class="code mb-1">
                                    <label class="sr-only" for="signin-email"></label>
                                    <input id="signin-code" name="signin-new" type="password"
                                        class="form-control signin-code" v-model="resetPasswordForm.password"
                                        placeholder="Your new password"
                                        :class="{ 'is-invalid': resetPasswordForm.errors.has('password') || resetNewPasswordErrorMessage }">
                                    <div v-if="resetNewPasswordErrorMessage"
                                        class="invalid-feedback mt-2 d-block text-white">
                                        {{ resetNewPasswordErrorMessage }}
                                    </div>
                                </div><!--//form-group-->



                                <div class="code mb-3">
                                    <label class="sr-only" for="signin-email"></label>
                                    <input id="signin-code" name="signin-email" type="password"
                                        class="form-control signin-code" v-model="resetPasswordForm.password_confirmation"
                                        placeholder="Re-type your password"
                                        :class="{ 'is-invalid': resetPasswordForm.errors.has('password_confirmation') || resetConfirmPasswordErrorMessage }">
                                    <div v-if="resetConfirmPasswordErrorMessage"
                                        class="invalid-feedback mt-2 d-block text-white">
                                        {{ resetConfirmPasswordErrorMessage }}
                                    </div>
                                </div><!--//form-group-->


                                <div class="text-center">
                                    <button type="submit" :disabled="resetPasswordForm.busy"
                                        class="btn app-btn-primary w-100 theme-btn mx-auto">Submit</button>
                                </div>
                            </form>

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
            isDisabled: true,
            resetPasswordForm: new CustomForm({
                email: this.$route.query.email,
                password: '',
                password_confirmation: '',
            }),
            resetEmailPasswordErrorMessage: "",
            resetNewPasswordErrorMessage: "",
            resetConfirmPasswordErrorMessage: ""
        }
    },
    methods: {
        async resetPassword() {
            try {
                await axios.get("/sanctum/csrf-cookie");
                const response = await axios.post('/api/reset-password', {
                    email: this.$route.query.email,
                    token: this.$route.params.token,
                    password: this.resetPasswordForm.password,
                    password_confirmation: this.resetPasswordForm.password_confirmation
                });

                this.$router.push({ name: "login" });

                this.$swal.fire({
                    position: 'top-middle',
                    icon: 'success',
                    title: response.data.message,
                    showConfirmButton: false,
                    timer: 5000,
                    customClass: {
                        popup: 'my-custom-alert',
                        title: 'my-custom-alert-title',
                        icon: 'my-custom-alert-icon'
                    }
                });



            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    this.resetEmailPasswordErrorMessage = errors.email ? errors.email[0] : "";
                    this.resetNewPasswordErrorMessage = errors.password ? errors.password[0] : "";
                    this.resetConfirmPasswordErrorMessage = errors.password_confirmation ? errors.password_confirmation[0] : "";

                } else {
                    const errorMessage = error.response ? error.response.data.message : 'An error occurred. Please try again later.';

                    this.$swal.fire({
                        position: 'top-middle',
                        icon: 'error',
                        title: errorMessage,
                        showConfirmButton: false,
                        timer: 3000,
                        customClass: {
                            popup: 'my-custom-alert',
                            title: 'my-custom-alert-title',
                            icon: 'my-custom-alert-icon'
                        }
                    });
                }
            }
        },
    },
}
</script>

<style>
input[disabled] {
    cursor: not-allowed;
}
</style>