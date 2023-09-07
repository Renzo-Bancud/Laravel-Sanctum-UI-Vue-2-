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
            <h2 class="auth-heading text-center mb-5">Sign In to Mindoro Bodega Wholesale and Retail</h2>
            <div class="auth-form-container text-start">
              <form class="auth-form login-form" method="post" @submit.prevent="login()">
                <div class="email mb-3">
                  <label class="sr-only" for="signin-email">Email</label>
                  <input id="signin-email" name="email" type="email" class="form-control signin-email"
                    placeholder="Email address" v-model="loginForm.email"
                    :class="{ 'is-invalid': loginForm.errors.has('email') }" />


                  <div v-if="errorMessage" class="invalid-feedback mt-2 d-block text-white">
                    {{ errorMessage }}
                  </div>
                  <div v-else>
                    <div v-if="emailErrorMessage" class="invalid-feedback mt-2 d-block text-white">
                      {{ emailErrorMessage }}
                    </div>
                  </div>
                </div>
                <!--//form-group-->

                <div class="password mb-3">
                  <label class="sr-only" for="signin-password">Password</label>

                  <div class="password-input">
                    <input id="signin-password" name="signin-password" :type="showPassword ? 'text' : 'password'"
                      class="form-control signin-password" placeholder="Password" v-model="loginForm.password"
                      :class="{ 'is-invalid': loginForm.errors.has('password') }" />
                    <span class="toggle-password" @click.prevent="toggleShowPassword"
                      v-html="showPassword ? '<i class=\'fas fa-eye-slash\'></i>' : '<i class=\'fas fa-eye\'></i>'"></span>
                  </div>


                  <div v-if="passwordErrorMessage" class="invalid-feedback mt-2 d-block text-white">
                    {{ passwordErrorMessage }}
                  </div>

                  <div class="extra mt-3 row justify-content-between">
                    <div class="col-6">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="RememberPassword" />
                        <label class="form-check-label" for="RememberPassword">
                          Remember me
                        </label>
                      </div>
                    </div>
                    <!--//col-6-->
                    <div class="col-6">
                      <div class="forgot-password text-end">
                        <router-link :to="{ name: 'forgot' }" class="text-dark">Forgot password?</router-link>
                      </div>
                    </div>
                    <!--//col-6-->
                  </div>
                  <!--//extra-->
                </div>
                <!--//form-group-->

                <div class="text-center">
                  <button type="submit" :disabled="loginForm.busy" class="btn app-btn-primary w-100 theme-btn mx-auto">
                    Log In
                  </button>
                </div>
              </form>

              <div class="auth-option text-center pt-3">
                <div>
                  No Account? Sign up
                  <router-link class="text-link" :to="{ name: 'signup' }">here</router-link>.
                </div>
              </div>
              <div class="text-center hover-pulse">
                <router-link :to="{ name: 'home' }" class="mt-1 text-decoration-none hover-pulse"><span
                    class="iconify fs-2" data-icon="flat-color-icons:shop"></span>
                  <br />
                  <span class="fw-bold text-dark">Go to Shop</span></router-link>
              </div>
            </div>
            <!--//auth-form-container-->
          </div>
          <!--//auth-body-->

          <footer class="app-auth-footer">
            <div class="container text-center py-3">
              <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
              <small class="copyright">Copyright
                <span class="iconify" data-icon="emojione-v1:copyright"></span> All rights
                reserved.</small>
            </div>
          </footer>
          <!--//app-auth-footer-->
        </div>
        <!--//flex-column-->
      </div>
      <!--//auth-main-col-->
      <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
        <div class="auth-background-holder"></div>
        <div class="auth-background-mask"></div>
        <div class="auth-background-overlay p-3 p-lg-5">
          <div class="d-flex flex-column align-content-end h-100">
            <div class="h-100"></div>
            <div class="overlay-content p-3 p-lg-4 rounded d-none">
              <h5 class="mb-3 overlay-title">Explore Portal Admin Template</h5>
              <div>
                Portal is a free Bootstrap 5 admin dashboard template. You can download
                and view the template license
                <a
                  href="https://themes.3rdwavemedia.com/bootstrap-templates/admin-dashboard/portal-free-bootstrap-admin-dashboard-template-for-developers/">here</a>.
              </div>
            </div>
          </div>
        </div>
        <!--//auth-background-overlay-->
      </div>
      <!--//auth-background-col-->
    </div>
    <!--//row-->

  </div>
</template>

<script>
import { mapState } from "vuex";
import { Form as CustomForm } from "vform";
import { refreshCsrfToken } from "../../services/apiService";

export default {
  components: { CustomForm },
  data() {
    return {
      loginForm: new CustomForm({
        email: "",
        password: "",
      }),
      accountErrorMessage: "",
      emailErrorMessage: "",
      passwordErrorMessage: "",
      errorMessage: "",
      verifyForm: new CustomForm({
        verificationCode: "",
      }),
      verifyErrorMessage: "",
      getuser: [],
      orderRatings: [],
      countRatings: 0,
      showPassword: false,

    };
  },
  methods: {
    async login() {
      try {
        this.$store.dispatch('showLoader');
        await axios.get("/sanctum/csrf-cookie");
        await this.loginForm.post("/login");
      } catch (error) {

        if (error.response) {
          if (error.response.status === 422) {
            // Validation errors
            const errors = error.response.data.errors;
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

        const response = await axios.get("/api/user");
        let user = response.data;

        if (user && user.email_verified_at === null) {

          await axios.post('/api/delete-unverified');

          this.$swal.fire({
            position: 'success',
            icon: 'info',
            title: "Unverified Account. Register again!",
            showConfirmButton: false,
            timer: 3000,
            customClass: {
              popup: 'my-custom-alert',
              title: 'my-custom-alert-title',
              icon: 'my-custom-alert-icon'
            }
          });

          this.$router.push({ name: 'signup' });

        } else {
          if (user.role == 0) {

            this.$swal.fire({
              position: 'top-middle',
              icon: 'success',
              title: "Account Login",
              showConfirmButton: false,
              timer: 2000,
              customClass: {
                popup: 'my-custom-alert',
                title: 'my-custom-alert-title',
                icon: 'my-custom-alert-icon'
              }, didClose: () => {
                setTimeout(() => {
                  window.location.reload();
                }, 0.1);
              }
            });

            await this.getUserData();
            this.$router.push({ name: "dashboard" });

          } else {

            this.$swal.fire({
              position: 'top-middle',
              icon: 'success',
              title: "Account Login",
              showConfirmButton: false,
              timer: 2000,
              customClass: {
                popup: 'my-custom-alert',
                title: 'my-custom-alert-title',
                icon: 'my-custom-alert-icon'
              }

            });

            await this.getUserData();
            this.$router.push({ name: "home" });

          }
        }
      }
    },



    toggleShowPassword() {
      this.showPassword = !this.showPassword;
    },

    async getUserData() {
      const response = await axios.get("/api/user");
      let user = response.data;
      this.$store.commit("SET_USER", user);
      this.$store.commit("SET_AUTHENTICATED", true);
      localStorage.setItem("auth", true);
    },
  },
  computed: {
    ...mapState(["cartItems"]),
  },
  created() {
    // console.log(this.cartItems)
  },
};
</script>

<style scoped>
.password-input {
  position: relative;
}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  cursor: pointer;
}
</style>
