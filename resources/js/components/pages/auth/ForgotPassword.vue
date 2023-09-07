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
                            <h2 class="auth-heading text-center">Forgot Password</h2>
                            <small>To reset your password, please enter your email address below.</small>
                        </div>

                        <div class="auth-form-container text-start">
                            <form class="auth-form login-form" @submit.prevent="emailAccount()">

                                <div class="code mb-3">
                                    <label class="sr-only" for="signin-email"></label>
                                    <input id="signin-code" name="signin-email" type="email"
                                        class="form-control signin-code" v-model="emailAccountForm.email"
                                        placeholder="Enter your email address"
                                        :class="{ 'is-invalid': emailAccountForm.errors.has('email') || emailAccountErrorMessage }">
                                    <div v-if="emailAccountErrorMessage" class="invalid-feedback mt-2 d-block text-white">
                                        {{ emailAccountErrorMessage }}
                                    </div>
                                </div><!--//form-group-->


                                <div class="text-center">
                                    <button type="submit" :disabled="emailAccountForm.busy"
                                        class="btn app-btn-primary w-100 theme-btn mx-auto">Submit</button>
                                </div>
                            </form>

                            <div class="auth-option text-center pt-3 hover-pulse">
                                <router-link :to="{ name: 'home' }" class="mt-1 text-decoration-none"><span
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

export default {
    components: { CustomForm },
    data() {
        return {
            emailAccountForm: new CustomForm({
                email: '',
            }),
            emailAccountErrorMessage: ""
        }
    },
    methods: {
        async emailAccount() {
            try {
                const response = await axios.post('/api/forgot-password', {
                    email: this.emailAccountForm.email
                });

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

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    this.emailAccountErrorMessage = errors.email ? errors.email[0] : "";
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

<style></style>