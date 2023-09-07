<template>
   <div>
      <header class="app-header fixed-top"
         v-show="$route.name == 'dashboard' || $route.name == 'category-list'
            || $route.name == 'product-list' || $route.name == 'product-deliveries'
            || $route.name == 'inventory-list' || $route.name == 'purchase-list'
            || $route.name == 'shop-list' || $route.name == 'client-orders' || $route.name == 'customer-list'
            || $route.name == 'rating-list' || $route.name == 'logs-list' || $route.name == 'slogan-setting' || $route.name == 'settings' || $route.name == 'variation-setting'"
         v-if="user && user.email_verified_at && auth">
         <div class="app-header-inner">
            <div class="container-fluid py-2">
               <div class="app-header-content">
                  <div class="row justify-content-between align-items-center">

                     <div class="col-auto">
                        <a id="sidepanel-toggler" class="sidepanel-toggler d-inline-block d-xl-none" href="#"
                           @click="toggleSidePanel">
                           <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img">
                              <title>Menu</title>
                              <path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"
                                 d="M4 7h22M4 15h22M4 23h22"></path>
                           </svg>
                        </a>
                     </div><!--//col-->


                     <div class="app-utilities col-auto">


                        <div class="app-utility-item app-notifications-dropdown dropdown">
                           <a class="dropdown-toggle no-toggle-arrow" id="notifications-dropdown-toggle"
                              data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" title="Notifications">
                              <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="1.8em" height="1.8em" fill="currentColor"
                                 class="bi bi-star" viewBox="0 0 16 16">
                                 <path
                                    d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z" />
                              </svg>

                              <span class="icon-badge">{{ countRatings }}</span>
                           </a><!--//dropdown-toggle-->

                           <div class="dropdown-menu p-0" aria-labelledby="notifications-dropdown-toggle">
                              <div class="dropdown-menu-header p-3">
                                 <h5 class="dropdown-menu-title mb-0">Ratings</h5>
                              </div><!--//dropdown-menu-title-->
                              <div class="dropdown-menu-content">
                                 <div class="item p-3" v-if="orderRatings.length == 0">
                                    <div class="row gx-2 text-center">
                                       <div>
                                          <span class="iconify display-1" data-icon="fluent:star-off-16-regular"></span>
                                       </div>
                                       <div> No ratings found</div>
                                    </div>
                                 </div>
                                 <div class="item p-3" v-else v-for="rating in orderRatings" :key="rating.order_id">
                                    <div class="row gx-2 justify-content-between align-items-center">
                                       <div class="col-auto">
                                          <img v-if="rating.profile" class="profile-image" :src="rating.profile"
                                             alt="User Profile">
                                          <img v-else class="profile-image"
                                             :src="require('/storage/img-icon/no-image-icon.png')" alt="No Image">
                                       </div><!--//col-->
                                       <div class="col">
                                          <div class="info">
                                             <div class="desc">
                                                {{ rating.name }}<br>
                                                <div class="d-flex">
                                                   <div>
                                                      <b><small>Product</small></b>&nbsp;:&nbsp;<small>{{ rating.title
                                                      }}</small>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="meta">

                                                <span v-for="star in 5" :key="star"
                                                   :class="{ 'star-filled': star <= rating.rate_star }">
                                                   <i class="fas fa-star"></i>
                                                </span>


                                             </div>
                                          </div>
                                       </div><!--//col-->
                                    </div><!--//row-->
                                    <!-- <a class="link-mask" href="notifications.html"></a> -->
                                 </div><!--//item-->

                              </div><!--//dropdown-menu-content-->

                              <div class="dropdown-menu-footer p-2 text-center" v-if="orderRatings.length > 0">
                                 <router-link href="#" :to="{ name: 'rating-list' }"
                                    class="btn btn-info border-dark btn-sm"><span class="iconify fs-5"
                                       data-icon="clarity:eye-solid"></span>&nbsp;View all</router-link>
                              </div>

                           </div><!--//dropdown-menu-->
                        </div><!--//app-utility-item-->

                        <div class="app-utility-item app-user-dropdown dropdown">
                           <router-link :to="{ name: 'settings' }">
                              <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear icon" fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd"
                                    d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z" />
                                 <path fill-rule="evenodd"
                                    d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z" />
                              </svg>
                           </router-link>
                        </div><!--//app-user-dropdown-->


                        <div class="app-utility-item app-user-dropdown dropdown">
                           <a class="dropdown-toggle" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#"
                              role="button" aria-expanded="false">
                              <img v-if="getuser.profile !== null" :src="getuser.profile" alt="profile">
                              <span v-else class="iconify fs-3" data-icon="iconoir:profile-circle"></span>

                           </a>
                           <ul class="dropdown-menu" aria-labelledby="user-dropdown-toggle">
                              <li><a class="dropdown-item" @click.prevent="editInfo('name')" href="#"><span
                                       class="iconify fs-5" data-icon="ph:user-bold"></span>&nbsp;Update Name</a></li>
                              <li><a class="dropdown-item" @click.prevent="editInfo('email')" href="#"><span
                                       class="iconify fs-5" data-icon="mdi:email-outline"></span>&nbsp;Change Email</a>
                              </li>
                              <li><a class="dropdown-item" @click.prevent="editInfo('profile')" href="#"><span
                                       class="iconify fs-5" data-icon="ri:profile-line"></span>&nbsp;Update Profile</a>
                              </li>
                              <li><a class="dropdown-item" @click.prevent="editInfo('password')" href="#"><span
                                       class="iconify fs-5" data-icon="mdi:password-outline"></span>&nbsp;
                                    Reset Password</a></li>
                              <li>
                                 <hr class="dropdown-divider">
                              </li>
                              <li><a class="dropdown-item" href="#" @click.prevent="logout"><span class="iconify fs-5"
                                       data-icon="tabler:logout"></span>&nbsp;Log Out</a></li>
                           </ul>
                        </div><!--//app-user-dropdown-->
                     </div><!--//app-utilities-->
                  </div><!--//row-->
               </div><!--//app-header-content-->
            </div><!--//container-fluid-->
         </div><!--//app-header-inner-->





         <Sidebar />

      </header><!--//app-header-->


      <div class="modal fade right" id="showInfo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title"></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body" v-if="auth">

                  <form @submit.prevent="updateAccountInfo">
                     <div v-if="type === 'name'">
                        <div class="form-group">
                           <label for="fname" class="form-label fw-bold">Name:</label>
                           <input type="text" class="form-control" v-model="accountInfoForm.name"
                              :class="{ 'is-invalid': accountInfoForm.errors.has('name') }">
                           <div v-if="errorNameMessage" class="invalid-feedback">
                              {{ errorNameMessage }}
                           </div>
                        </div>
                     </div>
                     <div v-else-if="type === 'email'">
                        <div class="form-group">
                           <label for="email" class="form-label fw-bold">Email Address:</label>
                           <input type="email" class="form-control" v-model="accountInfoForm.email"
                              :class="{ 'is-invalid': accountInfoForm.errors.has('email') }">
                           <div v-if="errorEmailMessage" class="invalid-feedback">
                              {{ errorEmailMessage }}
                           </div>
                        </div>
                     </div>
                     <div v-else-if="type === 'password'">
                        <div class="form-group mb-3">
                           <label for="password" class="form-label fw-bold">Old Password:</label>
                           <input type="password" class="form-control" v-model="accountInfoForm.old_password"
                              :class="{ 'is-invalid': accountInfoForm.errors.has('old_password') }">
                           <div v-if="errorOldPasswordMessage" class="invalid-feedback">
                              {{ errorOldPasswordMessage }}
                           </div>
                        </div>

                        <div class="form-group mb-3">
                           <label for="password" class="form-label fw-bold">New Password:</label>
                           <input type="password" class="form-control" v-model="accountInfoForm.password"
                              :class="{ 'is-invalid': accountInfoForm.errors.has('password') }">
                           <div v-if="errorPasswordMessage" class="invalid-feedback">
                              {{ errorPasswordMessage }}
                           </div>
                        </div>
                        <div class="form-group">
                           <label for="password" class="form-label fw-bold">Re-type Password:</label>
                           <input type="password" class="form-control" v-model="accountInfoForm.password_confirmation">
                        </div>

                        <small style="position:relative;top:10px;"><i>The password will be updated if a new password
                              is provided in the
                              input.</i></small>
                     </div>

                     <div v-else-if="type === 'number'">
                        <div class="form-group">
                           <label for="email" class="form-label fw-bold">Phone Number:</label>
                           <input type="number" class="form-control" v-model="accountInfoForm.number"
                              :class="{ 'is-invalid': accountInfoForm.errors.has('number') }">
                           <div v-if="errorNumberMessage" class="invalid-feedback">
                              {{ errorNumberMessage }}
                           </div>
                        </div>
                     </div>

                     <div v-else-if="type === 'profile'">
                        <div class="form-group">
                           <label for="profile" class="form-label fw-bold">Profile Picture:</label>

                           <input type="file" class="form-control mt-2" name="profile" id="profile" @change="onImageChange"
                              :class="{ 'is-invalid': accountInfoForm.errors.has('profile') }" />
                           <div v-if="errorProfileMessage" class="invalid-feedback">
                              {{ errorProfileMessage }}
                           </div>
                        </div>
                     </div>


                     <button type="submit" class="btn btn-secondary border-dark btn-sm hover-pulse mt-4">Save
                        Changes</button>
                  </form>
               </div>
            </div>
         </div>
      </div>

   </div>
</template>

<script>
import { Form as CustomForm } from "vform";
import { objectToFormData } from "object-to-formdata";
import { eventBus } from '../../bus/eventBus.js';
import Sidebar from './Sidebar.vue';

export default {
   components: {
      Sidebar,
      objectToFormData,
   },
   data() {
      return {
         accountInfoForm: new CustomForm({
            name: "",
            email: "",
            password: "",
            old_password: "",
            password_confirmation: "",
            profile: "",
         }),
         errorNameMessage: "",
         errorEmailMessage: "",
         errorPasswordMessage: "",
         errorOldPasswordMessage: "",
         errorProfileMessage: "",
         selectedId: null,
         getuser: [],
         orderRatings: [],
         countRatings: 0,
         type: '',
      }
   },
   methods: {
      async logout() {
         await axios.post('/logout').then(response => {

            this.$swal.fire({
               position: 'top-middle',
               icon: 'success',
               title: 'Logged out successfully',
               showConfirmButton: false,
               timer: 3000,
               customClass: {
                  popup: 'my-custom-alert',
                  title: 'my-custom-alert-title',
                  icon: 'my-custom-alert-icon'
               },
               didClose: () => {
                  setTimeout(() => {
                     window.location.reload();
                  }, 0.1);
               }
            });


            localStorage.removeItem("auth");
            localStorage.clear();
            this.$store.commit('SET_AUTHENTICATED', false);
            this.$router.push({ name: 'login' });
         }).catch(error => {

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

         });
      },

      async loadUser() {
         let response = await axios.get("/api/load-user");
         this.getuser = response.data.user;
      },

      async fetchRatings() {
         try {
            let { data } = await axios.get("/api/load-count-ratings");
            this.orderRatings = data.orderRatings;
            this.countRatings = data.countRating;
            //console.log(this.orderRatings)
         } catch (error) {
            console.log(error);
         }
      },

      editInfo(type) {
         $(".modal-title").html(
            "<span class='iconify fs-1' data-icon='solar:user-id-linear'></span> My Account"
         );
         $("#showInfo").modal("show");
         this.type = type;
         this.accountInfoForm.name = this.getuser.name;
         this.accountInfoForm.email = this.getuser.email;
         this.accountInfoForm.old_password = '';
         this.accountInfoForm.password = '';
         this.accountInfoForm.selectedId = this.user.id;

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
               $("#showInfo").modal("hide");

               this.$swal.fire({
                  position: 'top-middle',
                  icon: 'success',
                  title: 'Account updated',
                  showConfirmButton: false,
                  timer: 1500,
                  customClass: {
                     popup: 'my-custom-alert',
                     title: 'my-custom-alert-title',
                     icon: 'my-custom-alert-icon',
                  },
               });

               this.loadUser();
               this.fetchRatings();


            }).catch((error) => {
               if (error.response && error.response.status === 422) {
                  const errors = error.response.data.errors;
                  this.errorNameMessage = errors.name ? errors.name[0] : "";
                  this.errorEmailMessage = errors.email ? errors.email[0] : "";
                  this.errorPasswordMessage = errors.password ? errors.password[0] : "";
                  this.errorOldPasswordMessage = errors.old_password ? errors.old_password[0] : "";
                  this.errorProfileMessage = errors.profile ? errors.profile[0] : "";
               }
            });
         }
      },

      onImageChange(e) {
         const file = e.target.files[0];
         this.accountInfoForm.profile = file;
      },

      toggleSidePanel() {
         eventBus.$emit('toggleSidePanel'); // Emit the 'toggleSidePanel' event
      }

   },
   computed: {
      auth() {
         return this.$store.getters.getAuthenticated;
      },
      user() {
         return this.$store.getters.getUser;
      }
   },
   mounted() {
      if (this.$store.getters.getAuthenticated) {
         this.fetchRatings();
         this.loadUser();
      }

      // Listen for the 'toggleSidePanel' event
      eventBus.$on('toggleSidePanel', () => {
         // Code to show/hide the sidebar or perform any action you want
         // For example, you can add a class to the sidebar to show/hide it
         const sidePanel = document.getElementById('app-sidepanel');
         if (sidePanel) {
            sidePanel.classList.toggle('sidepanel-visible');
            sidePanel.classList.toggle('sidepanel-hidden');
         }
      });

   },
};
</script>





