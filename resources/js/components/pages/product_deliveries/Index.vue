<template>
  <div class="app">
    <div class="app-wrapper">
      <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
          <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
              <h1 class="app-page-title mb-0">Product Deliveries</h1>
            </div>
            <div class="col-auto">
              <div class="page-utilities">
                <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                  <div class="col-auto">
                    <div class="table-search-form row gx-1 align-items-center">
                      <div class="col-auto">
                        <input type="text" id="search-orders" name="searchorders" v-model="form.search" @keyup="search"
                          class="form-control search-orders" placeholder="Search" />
                      </div>
                      <div class="col-auto d-flex">
                        <select v-model="paginate" class="form-select w-auto">
                          <option value="10">10</option>
                          <option value="20">20</option>
                          <option value="30">30</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <!--//col-->
                  <div class="col-auto d-none">
                    <select class="form-select w-auto">
                      <option selected value="option-1">All</option>
                      <option value="option-2">This week</option>
                      <option value="option-3">This month</option>
                      <option value="option-4">Last 3 months</option>
                    </select>
                  </div>
                  <div class="col-auto">
                    <a href="#" class="btn app-btn-secondary" @click.prevent="addDelivery()">
                      Create Delivery
                      <span class="iconify fs-4" data-icon="ic:outline-library-add"></span>
                    </a>
                  </div>
                </div>
                <!--//row-->
              </div>
              <!--//table-utilities-->
            </div>
            <!--//col-auto-->
          </div>
          <!--//row-->

          <div class="app-card app-card-orders-table shadow-sm mb-4">
            <div class="app-card-body">
              <div class="table-responsive">
                <table class="table-res app-table-hover table-striped mb-0 text-left">
                  <thead>
                    <tr class="text-uppercase">
                      <th class="cell">Product</th>
                      <th class="cell">Variations</th>
                      <th class="cell">Qty</th>
                      <th class="cell">Status</th>
                      <th class="cell">Notes</th>
                      <th class="cell">Created At</th>
                      <th class="cell">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!deliveries.data || Object.keys(deliveries.data).length === 0">
                      <td colspan="7" align="middle">
                        There is no delivery found
                      </td>
                    </tr>
                    <tr v-else v-for="delivery in  deliveries.data" :key="delivery.id">
                      <td class="cell" data-label="Product">
                        <span v-if="delivery.product">
                          {{ delivery.product.title }}</span>
                        <span v-else> {{ delivery.product_id }} </span>
                      </td>

                      <td class="cell" data-label="Variations">
                        <table class="table">
                          <thead>
                            <th class="border border-dark" v-for="(variationName, variationId) in delivery.variations" :key="variationId">
                              {{ variationName }}
                            </th>
                          </thead>
                          <tbody>
                            <tr>
                              <td class="border border-dark"
                              v-for="(optionValue, optionId) in delivery.options" :key="optionId"
                              >{{ optionValue }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </td>

                      <td class="cell" data-label="Qty">
                        {{ delivery.quantity }}
                      </td>
                      <td class="cell" data-label="Status">
                        <span v-if="delivery.status === 1">Available</span>
                        <span v-else>Not Available</span>
                      </td>
                      <td class="cell" data-label="Notes">
                        <span v-if="delivery.notes == null">---</span>
                        <span v-else>
                          {{
                            delivery.notes.length > 100
                            ? delivery.notes.substring(0, 100) + "..."
                            : delivery.notes
                          }}
                        </span>
                      </td>
                      <td class="cell" data-label="Created At">
                        {{ formattedDateTime(delivery.created_at) }}
                      </td>
                      <td class="cell" data-label="Action">
                        <a class="btn btn-info btn-sm p-1" href="#" @click.prevent="editDelivery(delivery)"><span
                            class="iconify fs-4" data-icon="vaadin:edit"></span> Edit Qty</a>
                        <a class="btn btn-danger btn-sm p-1" href="#" @click.prevent="deleteDelivery(delivery)"><span
                            class="iconify fs-4" data-icon="bi:trash"></span> Delete</a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!--//table-responsive-->
            </div>
            <!--//app-card-body-->
          </div>
          <!--//app-card-->
          <nav class="app-pagination">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
              <div class="d-flex justify-content-center align-items-center mb-2 mb-md-0">
                <pagination :data="deliveries" @pagination-change-page="loadDeliveries"></pagination>
              </div>
              <div class="mb-2 mb-md-0">
                <span class="d-block text-center mb-1">Showing {{ deliveries.from }} to {{ deliveries.to }} of
                  {{ deliveries.total }} entries</span>
              </div>
            </div>
          </nav>
          <!--//app-pagination-->
        </div>
        <!--//container-fluid-->
      </div>
      <!--//app-content-->


      <div class="modal fade" id="DeliveryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header border-0">
              <h5 class="modal-title" id="staticBackdropLabel"></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="formAction()">
                <div class="form-group row mb-2 add">
                  <label for="product" class="col-sm-2 col-form-label fw-bold">Product:</label>
                  <div class="col-sm-10">
                    <select name="product" id="product" class="form-control" v-model="deliveryForm.product" :class="{
                      'is-invalid': deliveryForm.errors.has('product'),
                    }" @change="loadDeliveries(1, '', deliveryForm.product)">
                      <option style="display: none" value="" selected>
                        -- Choose Product --
                      </option>
                      <option :value="product.id" v-for="product in products" :key="product.id">
                        {{ product.title }}
                      </option>
                    </select>
                    <div v-if="productErrorMessage" class="invalid-feedback">
                      {{ productErrorMessage }}
                    </div>
                  </div>
                </div>


                <div class="form-group row mb-2 add">
                  <label for="variations" class="col-form-label fw-bold col-sm-2">Variations:</label>
                  <div class="row col-sm-10">
                    <!-- Check if productVariations array is empty -->
                    <template v-if="productVariations.length === 0">
                      <p class="text-muted mt-2">No variation found!</p>
                    </template>
                    <template v-else>
                      <!-- Loop through the productVariations array -->
                      <div class="col-md-4 px-2 mb-2" v-for="Getvariation in productVariations" :key="Getvariation.id">
                        <!-- Add a conditional check to ensure variation is not null -->
                        <label class="col-form-label">{{ Getvariation.variation.variation_name }}</label>
                        <select v-model="deliveryForm.selectedVariations[Getvariation.variation.id]" class="form-control"
                          style="font-size: 11px;">
                          <option value="" selected disabled>
                            -- Choose Option --
                          </option>
                          <!-- Check if Getvariation.options array is not empty -->
                          <template v-if="Getvariation.options.length > 0">
                            <!-- Loop through the options array within each Getvariation -->
                            <option v-for="option in Getvariation.options" :key="option.id" :value="option.id">
                              {{ option.value }}
                            </option>
                          </template>
                          <template v-else>
                            <option disabled>No options available</option>
                          </template>
                        </select>
                      </div>
                    </template>

                    <div v-if="selectedVariationErrorMessage" class="invalid-feedback d-block">
                      {{ selectedVariationErrorMessage }}
                    </div>
                  </div>
                </div>

                <div class="form-group row mb-2 add">
                  <label for="qty" class="col-sm-2 col-form-label fw-bold">Qty:</label>
                  <div class="col-sm-10">
                    <input type="number" id="qty" name="qty" v-model="deliveryForm.qty" class="form-control"
                      :class="{ 'is-invalid': deliveryForm.errors.has('qty') }" placeholder="Enter product qty" />
                    <div v-if="quantityErrorMessage" class="invalid-feedback">
                      {{ quantityErrorMessage }}
                    </div>
                  </div>
                </div>

                <div class="form-group row add">
                  <label for="notes" class="col-sm-2 col-form-label fw-bold">Notes:</label>
                  <div class="col-sm-10">
                    <textarea rows="5" id="notes" name="notes" v-model="deliveryForm.notes" class="form-control h-100"
                      placeholder="Optional"></textarea>
                  </div>
                </div>

                <p class="edit text-muted" style="position:relative;top:-7px;">Your action will affect the inventory as
                  well.</p>

                <div class="form-group row mb-2 edit">
                  <label for="product" class="col-sm-4 col-form-label fw-bold">Type of Action:</label>
                  <div class="col-sm-8">
                    <select name="action" id="product" class="form-control" v-model="deliveryForm.action" :class="{
                      'is-invalid': deliveryForm.errors.has('action'),
                    }">
                      <option style="display: none" value="" selected>
                        -- Choose Action --
                      </option>
                      <option value="1">Add Quantity</option>
                      <option value="2">Deduct Quantity</option>
                    </select>
                    <div v-if="actionErrorMessage" class="invalid-feedback">
                      {{ actionErrorMessage }}
                    </div>
                  </div>
                </div>

                <div class="form-group row mb-2 edit">
                  <label for="qty" class="col-sm-4 col-form-label fw-bold">Specify Qty:</label>
                  <div class="col-sm-8">
                    <input type="number" id="qty_action" name="qty_action" v-model="deliveryForm.qty_action"
                      class="form-control" :class="{ 'is-invalid': deliveryForm.errors.has('qty_action') }"
                      placeholder="Enter product qty" />
                    <div v-if="quantityActionErrorMessage" class="invalid-feedback">
                      {{ quantityActionErrorMessage }}
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                  <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">
                    Close
                  </button>
                  <button type="submit" class="btn btn-primary modal-btn" :disabled="deliveryForm.busy"></button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="removeItem" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header border-0 p-0">
              <button type="button" class="btn-close me-1 mt-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <div class="mb-2">
                <span class="iconify h1 fs-1" data-icon="noto-v1:sad-but-relieved-face"></span>
                <h5>Are you certain that this action will remove the item from the inventory?</h5>
                <div class="d-flex flex-column gap-1">
                  <i>Please be aware that this action is irreversible and cannot be undone.</i>
                </div>

                <a @click.prevent="confirmDeleteDelivery()" href="#"
                  class="btn btn-danger btn-sm col-4 text-center mt-2 text-white">Yes, delete it</a>


              </div>
            </div>
          </div>
        </div>
      </div>

      <Footer />
    </div>
    <!--//app-wrapper-->
  </div>
</template>

<script>
import { Form as CustomForm } from "vform";
import Footer from "../../template/Footer.vue";


export default {
  components: {
    CustomForm,
    Footer,
  },
  data() {
    return {
      deliveries: {},
      paginate: 10,
      form: {
        search: "",
      },
      deliveryForm: new CustomForm({
        product: "",
        selectedVariations: [],
        variationIds: [],
        optionIds: [],
        qty: "",
        qty_action: "",
        action: "",
        notes: "",
        _method: null,
      }),
      qtyAction: false,
      products: [],
      productVariations: [],
      productErrorMessage: "",
      selectedVariationErrorMessage: "",
      quantityErrorMessage: "",
      quantityActionErrorMessage: "",
      actionErrorMessage: "",
      selectedId: null,
    };
  },

  watch: {
    paginate: function (value) {
      this.loadDeliveries();
    },
  },

  methods: {
    async loadDeliveries(page = 1, search = "", product_id = null) {
      await axios
        .get("/api/product_delivery?page=" + page + "&paginate=" + this.paginate + "&search=" + search + "&product_id=" + product_id)
        .then((response) => {
          this.deliveries = response.data.product_deliveries;
          this.productVariations = response.data.product_variations;
          // console.log(this.deliveries)
        });
    },

    formattedDateTime(created_at) {
      // Parse the date string to a JavaScript Date object
      const date = new Date(created_at);

      // Get the UTC offset of the Philippine time zone in minutes
      const utcOffset = -480; // UTC offset for Philippine time is UTC+8:00 (480 minutes ahead)

      // Apply the UTC offset to the date to get the correct local time in Philippine time zone
      date.setMinutes(date.getMinutes() + utcOffset);

      // Format the date using the toLocaleString() method with options for date and time
      const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
        hour12: false, // Use 24-hour format
        timeZone: "Asia/Manila", // Set the time zone to Philippine time
      };

      const formattedDate = date.toLocaleString("en-US", options);

      // Return the formatted date
      return formattedDate;
    },
    search() {
      this.loadDeliveries(1, this.form.search);
    },

    formAction() {
      if (this.deliveryForm.selectedId) {
        this.deliveryForm._method = "put";
        this.updateDelivery();
      } else {
        this.deliveryForm._method = "post";
        this.createDelivery();
      }
    },

    async addDelivery() {
      $(".modal-title").html("<span class='iconify fs-3' data-icon='carbon:delivery-add'></span> Add Delivery");
      $(".modal-btn").text("Save");
      this.deliveryForm.reset();
      $(".add").show();
      $(".edit").hide();
      $("#DeliveryModal").modal("show");
    },

    async createDelivery() {
      try {

        const selectedVariations = this.deliveryForm.selectedVariations;

        // Separate arrays to store variationIds and optionIds
        const variationIds = this.deliveryForm.variationIds;
        const optionIds = this.deliveryForm.optionIds;

        // Extract variationId and optionId from the selectedVariations object and store them in the respective arrays
        Object.keys(selectedVariations).forEach((variationId) => {
          const optionId = selectedVariations[variationId];
          variationIds.push(variationId);
          optionIds.push(optionId);
        });

        const payload = {
          product: this.deliveryForm.product,
          variationIds: variationIds,
          optionIds: optionIds,
          qty: this.deliveryForm.qty,
          qty_action: this.deliveryForm.qty_action,
          action: this.deliveryForm.action,
          notes: this.deliveryForm.notes,
          _method: this.deliveryForm._method,
        };

        // console.log(payload)

        // Make the POST request to the server
        const response = await this.deliveryForm.post("/api/product_delivery", payload);

        // Handle the response here
        const data = response.data;
        // console.log(data);

        // Clear input after insert
        this.deliveryForm.qty = "";
        this.deliveryForm.notes = "";

        // Display success toast notification
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

        this.loadDeliveries();
        $("#DeliveryModal").modal("hide");
      } catch (error) {
        // Handle errors here
        if (error.response && error.response.status === 422) {
          const errors = error.response.data.errors;
          this.productErrorMessage = errors.product ? errors.product[0] : "";
          this.selectedVariationErrorMessage = errors.selectedVariations ? errors.selectedVariations[0] : "";
          this.quantityErrorMessage = errors.qty ? errors.qty[0] : "";
        } else {
          console.error(error);
          this.$swal.fire({
            position: 'top-middle',
            icon: 'error',
            title: 'An error occurred. Please try again.',
            showConfirmButton: false,
            timer: 3000,
            customClass: {
              popup: 'my-custom-alert',
              title: 'my-custom-alert-title',
              icon: 'my-custom-alert-icon',
            },
          });
        }
      }
    },


    async editDelivery(delivery) {
      $("#DeliveryModal").modal("show");
      $(".modal-title").html("<span class='iconify fs-3' data-icon='carbon:delivery-add'></span> Edit Delivery");
      $(".modal-btn").text("Update Changes");
      $(".add").hide();
      $(".edit").show();
      this.deliveryForm.selectedId = delivery.id
    },

    async updateDelivery() {
      if (this.deliveryForm.selectedId !== null) {
        let id = this.deliveryForm.selectedId;

        await this.deliveryForm
          .post(`/api/product_delivery/${id}`)
          .then((response) => {

            // Display success toast notification
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

            $("#DeliveryModal").modal("hide");
            this.loadDeliveries();
          })
          .catch((error) => {
            if (error.response && error.response.status === 422) {
              const errors = error.response.data.errors;
              this.actionErrorMessage = errors.action ? errors.action[0] : "";
              this.quantityActionErrorMessage = errors.qty_action ? errors.qty_action[0] : "";
            } else {

              this.$swal.fire({
                position: 'top-middle',
                icon: 'success',
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

    deleteDelivery(deliveryItem) {
      this.deliveryForm.selectedId = deliveryItem.id
      $('#removeItem').modal('show');
    },

    async confirmDeleteDelivery() {
      if (this.deliveryForm.selectedId !== null) {
        await axios
          .delete(`/api/product_delivery/${this.deliveryForm.selectedId}`)
          .then(() => {

            // Check if the response contains a message
            this.$swal.fire({
              position: 'top-middle',
              icon: 'success',
              title: 'Item Deleted',
              showConfirmButton: false,
              timer: 1500,
              customClass: {
                popup: 'my-custom-alert',
                title: 'my-custom-alert-title',
                icon: 'my-custom-alert-icon'
              }
            });

          });
        this.loadDeliveries();
        $('#removeItem').modal('hide');
      }

    },


    loadProducts() {
      axios.get("/api/product-selection").then((response) => {
        this.products = response.data;
      });
    },

  },
  mounted() {
    this.loadDeliveries();
    this.loadProducts();
  },

}
</script>

