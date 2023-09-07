<template>
  <div class="app">
    <div class="app-wrapper">
      <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
          <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
              <h1 class="app-page-title mb-0">Customer Orders</h1>
            </div>
            <div class="col-auto">
              <div class="page-utilities">
                <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                  <div class="col-auto">
                    <form class="table-search-form row gx-1 align-items-center">
                      <div class="col-auto">
                        <input type="text" id="search-orders" name="searchorders" class="form-control search-orders"
                          placeholder="Search" v-model="form.search" @keyup="search" />
                      </div>
                    </form>
                  </div>
                  <!--//col-->


                </div>
                <!--//row-->
              </div>
              <!--//table-utilities-->
            </div>
            <!--//col-auto-->
          </div>
          <!--//row-->

          <nav id="orders-table-tab" class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(0)" :class="{ active: currentTab == 0 }">
              <i class="iconify fs-2" data-icon="ic:round-pending-actions"></i>&nbsp;Pending
            </a>
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(1)" :class="{ active: currentTab == 1 }">
              <i class="iconify fs-2" data-icon="icon-park-outline:transaction-order"></i>&nbsp;Order is Placed
            </a>
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(2)" :class="{ active: currentTab == 2 }">
              <i class="iconify fs-2" data-icon="mingcute:package-line"></i>&nbsp;Packaging Order
            </a>
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(3)" :class="{ active: currentTab == 3 }">
              <i class="iconify fs-2" data-icon="tabler:truck"></i>&nbsp;To Ship Order
            </a>
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(4)" :class="{ active: currentTab == 4 }">
              <i class="iconify fs-2" data-icon="game-icons:cash"></i>&nbsp;To Receive Order
            </a>
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(5)" :class="{ active: currentTab == 5 }">
              <i class="iconify fs-2" data-icon="mdi:truck-check-outline"></i>&nbsp;Order Complete
            </a>
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(6)" :class="{ active: currentTab == 6 }">
              <i class="iconify fs-3"
                data-icon="streamline:interface-favorite-star-reward-rating-rate-social-star-media-favorite-like-stars"></i>&nbsp;Rated
            </a>
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(7)" :class="{ active: currentTab == 7 }">
              <i class="iconify fs-2" data-icon="mdi:cancel-box-outline"></i>&nbsp;Cancelled
            </a>
            <!-- <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(1)"
              :class="{ active: currentTab == 1 }">Cancelled</a>
            <a class="flex-sm-fill text-sm-center nav-link" @click="changeTab(2)"
              :class="{ active: currentTab == 2 }">Expired</a> -->
          </nav>

          <div class="tab-content" id="orders-table-tab-content">
            <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
              <div class="app-card app-card-orders-table shadow-sm mb-5">
                <div class="app-card-body">
                  <div class="table-responsive">
                    <table class="table-res app-table-hover mb-0 text-left">
                      <thead>
                        <tr>
                          <th class="cell">Product</th>
                          <th class="cell" style="width:120px;">Variation</th>
                          <th class="cell">Image</th>
                          <th class="cell">Category</th>
                          <th class="cell">Order By</th>
                          <th class="cell">Price</th>
                          <th class="cell" style="width:50px;">Qty</th>
                          <th class="cell" style="width:70px;">Fee</th>
                          <th class="cell">Total</th>
                          <th class="cell" v-if="isOrderCancelled">Cancelled By</th>
                          <th class="cell" v-if="isOrderCancelled">Notes</th>
                          <th class="cell" v-else></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="filteredOrders && filteredOrders.length === 0">
                          <td colspan="11" class="text-center" v-if="isOrderCancelled">
                            No available data yet.
                          </td>

                          <td colspan="10" class="text-center" v-else>
                            No available data yet.
                          </td>
                        </tr>
                        <tr v-else v-for="(cart_item, index) in filteredOrders" :key="cart_item._id + '_' + index">
                          <td class="cell" data-label="Item">
                            {{ cart_item.title }}
                            <div class="order-id">
                              <div class="mt-2"><b>ORDER&nbsp;#</b><br><span class="text-primary fw-bold">{{
                                cart_item.order_tracking_id }}</span></div>
                            </div>
                          </td>
                          <td class="cell" data-label="Variation">
                         
                            <span class="badge bg-secondary mx-1" v-for="option in getOrderVariation(cart_item)"
                              :key="option.id">
                              {{ option.value }}
                            </span>
                          </td>
                          <td class="cell" data-label="Image">
                            <img v-if="cart_item.image" :src="cart_item.main_picture" alt="Product Image"
                              style="object-fit: contain; height: 100px; width: 100px" />
                            <img v-else :src="require('/storage/img-icon/no-image-icon.png')
                              " alt="No Image" height="100" width="100" />
                          </td>
                          <td class="cell" data-label="Category">
                            {{ cart_item.name }}
                          </td>
                          <td class="cell" data-label="Order By" style="font-size:12px !important;">

                            <b>{{ cart_item.customer }}</b>
                            <br>
                            <span class="text-danger"
                              v-if="cart_item.province == null && cart_item.mobile_number == null">
                              No assign address yet.
                            </span>
                            <span v-else>
                              {{ cart_item.province }}<br>
                              {{ cart_item.city }}<br>
                              {{ cart_item.barangay }}<br>
                              <b>Cp # {{ cart_item.mobile_number }}</b><br>
                              Landmark: {{ cart_item.landmark }}
                            </span>
                          </td>
                          <td class="cell small" data-label="Price">
                            {{ formatPrice(cart_item.isOnSale === null ? cart_item.price : salePrice(cart_item)) }}

                            <span class="old-price" v-if="cart_item.isOnSale !== null">
                              <del>
                                <span class="old-price-discount text-muted"
                                  style="text-decoration: line-through !important;">
                                  <span class="iconify" data-icon="pepicons-pop:peso"
                                    style="position: relative; top: -3px;"></span>
                                  {{ cart_item.price }}
                                </span>
                              </del>
                              <span class="old-price-discount text-danger">
                                ({{ cart_item.discount_percentage }}% off)
                              </span>
                            </span>

                          </td>
                          <td class="cell" data-label="Qty">
                            {{ cart_item.qty }}
                          </td>
                          <td class="cell" data-label="Shipping Fee">
                            {{ formatPrice(cart_item.shipping_fee) }}
                          </td>
                          <td class="cell" data-label="Total">
                            {{ formatPrice(cart_item.total_amount) }}
                          </td>
                          <td class="cell" :data-label="cart_item.status == 7 ? 'Cancelled By' : 'Action'">
                            <div v-if="cart_item.status == 5 || cart_item.status == 6">

                              <button class="btn btn-secondary btn-sm" type="button"
                                style="opacity:0.6;cursor: not-allowed;">Choose
                                Action</button>


                            </div>
                            <div v-else>

                              <div v-if="cart_item.status == 7">
                                {{ cart_item.cancelled_by == 1 ? 'Buyer' : 'Admin' }}
                              </div>
                              <div v-else>
                                <div class="dropdown">
                                  <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                    id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                    Choose Action
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <div v-if="cart_item.status == 0">
                                      <li><a class="dropdown-item" href="#" @click.prevent="markOrder(cart_item)">Place
                                          Order</a>
                                      </li>

                                      <li><a class="dropdown-item" href="#" @click.prevent="cancelOrder(cart_item)">Cancel
                                          Order</a>
                                      </li>
                                    </div>

                                    <div v-else-if="cart_item.status == 1">
                                      <li><a class="dropdown-item" href="#" @click.prevent="markOrder(cart_item)">Package
                                          Item</a>
                                      </li>

                                      <li><a class="dropdown-item" href="#" @click.prevent="cancelOrder(cart_item)">Cancel
                                          Order</a>
                                      </li>
                                    </div>

                                    <div v-else-if="cart_item.status == 2">
                                      <li><a class="dropdown-item" href="#" @click.prevent="markOrder(cart_item)">Ship
                                          Item</a></li>
                                    </div>

                                    <div v-else-if="cart_item.status == 3">
                                      <li><a class="dropdown-item" href="#" @click.prevent="markOrder(cart_item)">Mark as
                                          Received</a>
                                      </li>
                                    </div>

                                    <div v-else-if="cart_item.status == 4">
                                      <li><a class="dropdown-item" href="#" @click.prevent="markOrder(cart_item)">Mark as
                                          Complete</a>
                                      </li>
                                    </div>

                                  </ul>
                                </div>
                              </div>


                            </div>
                          </td>
                          <td class="cell" data-label="Notes" v-if="isOrderCancelled">{{ cart_item.notes }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!--//table-responsive-->
                </div>
                <!--//app-card-body-->
              </div>
              <!--//app-card-->

            </div>
            <!--//tab-pane-->
          </div>
          <!--//tab-content-->
        </div>
        <!--//container-fluid-->
      </div>
      <!--//app-content-->


      <div class="modal fade" id="cancelItem" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header border-0 p-0">
              <button type="button" class="btn-close me-1 mt-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <div class="mb-2">
                <span class="iconify h1 fs-1" data-icon="noto-v1:sad-but-relieved-face"></span>
                <h5>Are you certain that this order will be cancelled?</h5>
                <div class="d-flex flex-column gap-1">
                  <i>Please be aware that this action is irreversible and cannot be undone.</i>
                </div>

                <div v-if="notes">
                  <div class="form-group text-left">
                    <textarea class="form-control mt-3 mb-2"
                      placeholder="Please enter the reason for cancelling this item" v-model="cancelForm.notes"
                      :class="{ 'is-invalid': cancelForm.errors.has('notes') }"></textarea>

                    <div v-if="cancelForm.errors.has('notes')" class="invalid-feedback">
                      {{ cancelForm.errors.get('notes') }}
                    </div>

                  </div>
                </div>
                <div class="text-center" v-if="!notes">
                  <a @click.prevent="inputNotes()" href="#"
                    class="btn btn-danger btn-sm col-4 text-center mt-2 text-white">Yes, cancel it</a>
                </div>

                <div class="text-center" v-else>
                  <a @click.prevent="confirmCancellation()" href="#"
                    class="btn btn-danger btn-sm col-4 text-center mt-2 text-white">Submit</a>
                </div>

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
import formatPriceMixin from '../../mixins/formatPriceMixin';
import salePriceMixin from '../../mixins/salePriceMixin';

export default {
  components: {
    CustomForm,
    Footer,
  },
  mixins: [formatPriceMixin, salePriceMixin],
  data() {
    return {
      currentTab: 0,
      orders: [],
      getVariationOptions: [],
      form: {
        search: "",
      },
      cancelForm: new CustomForm({
        notes: "",
      }),
      notes: false,
      notesErrorMessage: '',
      selectedId: null,
    };
  },
  computed: {
    filteredOrders() {
      if (this.currentTab == 0) {
        return this.orders;
      } else {
        return this.orders.filter((order) => order.status === this.currentTab);
      }
    },
    getStatus() {
      return function (status) {
        return status === 0
          ? '<span class="badge bg-danger">Pending</span>'
          : status === 1
            ? '<span class="badge bg-secondary">Order is Placed</span>'
            : status === 2
              ? '<span class="badge bg-warning">Packaging</span>'
              : status === 3
                ? '<span class="badge bg-primary">Ready to Ship</span>'
                : status === 4
                  ? '<span class="badge bg-info">Received</span>'
                  : status === 5
                    ? '<span class="badge bg-success">Complete</span>'
                    : "";
      };
    },
    isOrderCancelled() {
      return this.orders.some(order => order.status === 7);
    },

  },
  mounted() {
    this.loadCart(this.currentTab);
  },
  methods: {
    async changeTab(tabIndex) {
      this.currentTab = tabIndex;
      await this.loadCart(this.currentTab);
    },

    async loadCart(status) {
      try {
        const { data } = await axios.get(`/api/track-orders?status=${status}`, {
          params: {
            search: this.form.search,
          },
        });
        this.orders = data.track_orders;
        this.getVariationOptions = data.getVariationOptions;
      } catch (error) {
        console.error(error);
      }
    },

    getOrderVariation(item) {
      const variationOptionIds = item.variation_option_id.split(',').map(Number);
      return this.getVariationOptions.filter((option) =>
        variationOptionIds.includes(option.id)
      );
    },

    search() {
      this.loadCart(this.currentTab);
    },

    async markOrder(item) {
      try {
        const { data } = await axios.post('/api/mark-order', { order_id: item.order_id, order_status: item.status });

        this.loadCart();
        this.changeTab(data.tab);
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

      } catch (error) {
        console.log(error);
      }
    },





    async cancelOrder(item) {
      this.cancelForm.selectedId = item.order_id;
      $('#cancelItem').modal('show');
    },

    inputNotes() {
      this.notes = true;
    },


    async confirmCancellation() {
      if (this.cancelForm.selectedId !== null) {

        try {
          const response = await axios.post(`/api/cancel-order`, {
            id: this.cancelForm.selectedId,
            notes: this.cancelForm.notes,
          });


          if (response.data && response.data.message) {
            // Check if the response contains a message
            this.$swal.fire({
              position: 'top-middle',
              icon: 'success',
              title: response.data.message,
              showConfirmButton: false,
              timer: 1500,
              customClass: {
                popup: 'my-custom-alert',
                title: 'my-custom-alert-title',
                icon: 'my-custom-alert-icon'
              }
            });

            this.changeTab(response.data.tab);
            this.cancelForm.errors.set('');
            this.notes = false;
            this.cancelForm.notes = '';
            $('#cancelItem').modal('hide');

            return;
          }


        } catch (error) {
          if (error.response && error.response.status === 422) {
            this.cancelForm.errors.set(error.response.data.errors);
          } else {
            console.error(error);
          }
        } finally {
          this.loadCart(this.currentTab);
        }
      }
    },




  },
};
</script>

