<template>
  <div class="app app-signup p-0">
    <div class="row g-0 app-auth-wrapper">
      <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
        <div class="d-flex flex-column align-content-end">
          <div class="app-auth-body mx-auto">
            <div class="app-auth-branding mb-3"> <router-link :to="{ name: 'home' }" class="app-logo"><img
                  class="logo-icon" :src="require('../../../assets/images/logo.png')" alt="logo"
                  style="object-fit:contain;width:200px;" /></router-link></div>
            <h5 class="auth-heading text-center mb-2">Sign Up to Mindoro Bodega Wholesale and Retail</h5>


            <div v-if="errorMessage" class="invalid-feedback mt-2 d-block text-white">
              {{ errorMessage }}
            </div>

            <div class="auth-form-container text-start mx-auto">
              <form class="auth-form auth-signup-form" method="post" @submit.prevent="createUser()">
                <div class="email mb-1">
                  <label class="sr-only" for="signup-email">First Name</label>
                  <input id="signup-fname" name="signup-fname" type="text" class="form-control signup-fname"
                    placeholder="First Name" v-model="signupForm.firstname"
                    :class="{ 'is-invalid': signupForm.errors.has('firstname') }">

                  <div v-if="firstnameErrorMessage" class="invalid-feedback mt-1 d-block text-white">
                    {{ firstnameErrorMessage }}
                  </div>
                </div>

                <div class="email mb-1">
                  <label class="sr-only" for="signup-email">Last Name</label>
                  <input id="signup-lname" name="signup-lname" type="text" class="form-control signup-lname"
                    placeholder="Last Name" v-model="signupForm.lastname"
                    :class="{ 'is-invalid': signupForm.errors.has('lastname') }">

                  <div v-if="lastnameErrorMessage" class="invalid-feedback mt-1 d-block text-white">
                    {{ lastnameErrorMessage }}
                  </div>
                </div>


                <div class="email mb-1">
                  <label class="sr-only" for="signup-email">Your Email</label>
                  <input id="signup-email" name="signup-email" type="text" class="form-control signup-email"
                    placeholder="Email" v-model="signupForm.email"
                    :class="{ 'is-invalid': signupForm.errors.has('email') }">

                  <div v-if="emailErrorMessage" class="invalid-feedback mt-1 d-block text-white">
                    {{ emailErrorMessage }}
                  </div>
                </div>


                <div class="password mb-1">
                  <label class="sr-only" for="signup-password">Password</label>
                  <input id="signup-password" name="signup-password" type="password" class="form-control signup-password"
                    placeholder="Create a password" v-model="signupForm.password"
                    :class="{ 'is-invalid': signupForm.errors.has('password') }">

                  <div v-if="passwordErrorMessage" class="invalid-feedback mt-1 d-block text-white">
                    {{ passwordErrorMessage }}
                  </div>
                </div>

                <div class="password mb-2">
                  <label class="sr-only" for="signup-cpassword">Confirm Password</label>
                  <input id="signup-cpassword" name="signup-cpassword" type="password"
                    class="form-control signup-password" placeholder="Re-type password"
                    v-model="signupForm.password_confirmation">
                </div>

                <div class="extra mb-3 d-none">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="RememberPassword" checked>
                    <label class="form-check-label" for="RememberPassword">
                      I agree to Portal's <a href="#" class="app-link">Terms of Service</a> and <a href="#"
                        class="app-link">Privacy Policy</a>.
                    </label>
                  </div>

                </div><!--//extra-->

                <div class="text-center">
                  <button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto"
                    :disabled="signupForm.busy">Sign Up</button>
                </div>
              </form><!--//auth-form-->

              <div class="auth-option text-center pt-3">Already have an account? <router-link class="text-link"
                  :to="{ name: 'login' }">Log in</router-link>
              </div>

              <div class="text-center hover-pulse">
                <router-link :to="{ name: 'home' }" class="mt-1 text-decoration-none hover-pulse"><span
                    class="iconify fs-2" data-icon="flat-color-icons:shop"></span>
                  <br />
                  <span class="fw-bold text-dark">Go to Shop</span></router-link>
              </div>

            </div><!--//auth-form-container-->



          </div><!--//auth-body-->

          <footer class="app-auth-footer">
            <div class="container text-center py-3">
              <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
              <small class="copyright">Copyright <span class="iconify" data-icon="emojione-v1:copyright"></span> All
                rights reserved.</small>

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
              <div>Portal is a free Bootstrap 5 admin dashboard template. You can download and view the template license
                <a
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
      signupForm: new CustomForm({
        firstname: '',
        lastname: '',
        email: '',
        password: '',
        password_confirmation: '',
      }),
      firstnameErrorMessage: "",
      lastnameErrorMessage: "",
      emailErrorMessage: "",
      passwordErrorMessage: "",
      errorMessage: "",
    }
  },
  methods: {
    async createUser() {
      try {
        this.$store.dispatch('showLoader');
        await axios.get("/sanctum/csrf-cookie");
        await this.signupForm.post('/register');
      } catch (error) {
        if (error.response) {
          if (error.response.status === 422) {
            // Validation errors
            const errors = error.response.data.errors;
            this.firstnameErrorMessage = errors.firstname ? errors.firstname[0] : "";
            this.lastnameErrorMessage = errors.lastname ? errors.lastname[0] : "";
            this.emailErrorMessage = errors.email ? errors.email[0] : "";
            this.passwordErrorMessage = errors.password ? errors.password[0] : "";
          } else if (error.response.status === 429) {
            // Too Many Requests
            this.errorMessage = "Too many login attempts. Please try again later.";
          } else if (error.response.status === 419) {
            // Authentication Timeout (CSRF token issue)
            this.errorMessage = "Session expired. Please refresh the page and try again.";
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
      } finally {
        this.$store.dispatch('hideLoader');
        const email = this.signupForm.email
        const response = await axios.post('/api/encrypt', { data: email });
        const encryptedData = response.data.encryptedData;

        this.$swal.fire({
          position: 'success',
          icon: 'success',
          title: "Account Verification is Required",
          showConfirmButton: false,
          timer: 3000,
          customClass: {
            popup: 'my-custom-alert',
            title: 'my-custom-alert-title',
            icon: 'my-custom-alert-icon'
          }
        });

        this.$router.push({
          name: 'verify',
          query: { email: encryptedData }
        });
      }
    },

  },
  mounted() { }
}
</script>

