<template>
  <div>
    <Loader v-if="isLoading" />
    <div class="app" v-if="user.email_verified_at != null && auth">
      <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4" v-if="user.role == 0">
          <div class="container-xl">
            <h1 class="app-page-title">Dashboard</h1>

            <div class="row g-4 mb-4">
              <div class="col-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                  <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Total Complete Order</h4>
                    <div class="stats-figure">{{ formatPrice(CompleteSales) }}</div>
                    <!-- <div class="stats-meta text-success">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up" fill="currentColor"
                      xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd"
                        d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z" />
                    </svg>
                    20%
                  </div> -->
                  </div>
                  <!--//app-card-body-->
                  <a class="app-card-link-mask" href="#"></a>
                </div>
                <!--//app-card-->
              </div>
              <!--//col-->

              <div class="col-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                  <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Total Pending Order</h4>
                    <div class="stats-figure">{{ formatPrice(PendingSales) }}</div>

                  </div>
                  <!--//app-card-body-->
                  <a class="app-card-link-mask" href="#"></a>
                </div>
                <!--//app-card-->
              </div>
              <!--//col-->
              <div class="col-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                  <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Total Customer</h4>
                    <div class="stats-figure">{{ customers }}</div>
                    <!-- <div class="stats-meta">Open</div> -->
                  </div>
                  <!--//app-card-body-->
                  <a class="app-card-link-mask" href="#"></a>
                </div>
                <!--//app-card-->
              </div>
              <!--//col-->
              <div class="col-6 col-lg-3">
                <div class="app-card app-card-stat shadow-sm h-100">
                  <div class="app-card-body p-3 p-lg-4">
                    <h4 class="stats-type mb-1">Total Product</h4>
                    <div class="stats-figure">{{ products }}</div>
                  </div>
                  <!--//app-card-body-->
                  <a class="app-card-link-mask" href="#"></a>
                </div>
                <!--//app-card-->
              </div>
              <!--//col-->
            </div>
            <!--//row-->
            <div class="row g-4 mb-4">
              <div class="col-12">
                <div class="app-card app-card-chart h-100 shadow-sm">
                  <div class="app-card-body p-3 p-lg-4">

                    <div class="mb-3 d-flex justify-content-between">
                      <h3>Total Sales</h3>
                      <select v-model="selectedValue" @change="updateChart"
                        class="form-select form-select-sm ms-auto d-inline-flex w-auto">
                        <option value="1">This week</option>
                        <option value="2">Today</option>
                        <option value="3">This Month</option>
                        <option value="4">This Year</option>
                      </select>
                    </div>
                    <div class="chart-container">
                      <canvas ref="chart"></canvas>
                      <div class="message-response" v-if="messageResponse">{{ messageResponse }}</div>
                    </div>
                  </div>
                  <!--//app-card-body-->
                </div>
                <!--//app-card-->
              </div>
              <!--//col-->
            </div>
            <!--//row-->
          </div>
          <!--//container-fluid-->
        </div>

        <footer />
      </div>
      <!--//app-wrapper-->
    </div>
  </div>
</template>


<script>
import { Chart } from "chart.js/auto";
import formatPriceMixin from '../../mixins/formatPriceMixin';

export default {
  mixins: [formatPriceMixin],
  data() {
    return {
      selectedValue: "1",
      chart: null,
      messageResponse: '',
      CompleteSales: [],
      PendingSales: [],
      customers: [],
      products: []
    };
  },
  methods: {
    async loadDashboard() {
      try {
        let response = await axios.get("/api/load-dashboard-data");
        this.CompleteSales = response.data.totalCompleteSales;
        this.PendingSales = response.data.totalPendingSales;
        this.customers = response.data.totalCustomers;
        this.products = response.data.totalProducts;

      } catch (error) {
        console.error(error);
      }

    },

    createChart() {
      const ctx = this.$refs.chart.getContext("2d");
      this.chart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: [],
          datasets: [
            {
              label: "Monthly Sales",
              data: [],
              backgroundColor: 'rgba(75, 192, 192, 0.6)',
              borderColor: 'black',
              borderWidth: 2,
            },
          ],
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                display: false,
              },
            },
            x: {
              grid: {
                display: false,
              },
            },
          },
          plugins: {
            legend: {
              display: false,
            },
          },
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: {
              left: 0,
              right: 0,
              top: 0,
              bottom: 0,
            },
          },
          barThickness: 9, // Adjust the width of the bars
          barPercentage: 0.8, // Adjust the width of the bars relative to the available space
          categoryPercentage: 0.9, // Adjust the width of the bars within each category group
        },
      });

      this.updateChart();
    },
    async updateChart() {
      const selectedValue = this.selectedValue;

      // Make an AJAX request to your server-side endpoint
      axios
        .get('/api/complete-order-chart', {
          params: {
            selectedValue: selectedValue
          }
        })
        .then(response => {
          const data = response.data;
          const labels = data.labels;
          const chartData = data.data;
          const message = data.message;

          // Update the message response
          this.messageResponse = message;

          if (message === 'No data available') {
            // Display "No data" message
            this.chart.data.labels = [];
            this.chart.data.datasets[0].data = [];
            this.chart.options.plugins.tooltip.enabled = false;
            this.chart.options.plugins.noDataMessage = message;
          } else {
            // Update the chart with the retrieved data
            this.chart.data.labels = labels;
            this.chart.data.datasets[0].data = chartData;
            this.chart.options.plugins.tooltip.enabled = true;
            this.chart.options.plugins.noDataMessage = null;
          }

          this.chart.update();
        })
        .catch(error => {
          console.error(error);
        });
    },


  },
  computed: {
    auth() {
      return this.$store.getters.getAuthenticated;
    },
    user() {
      return this.$store.getters.getUser;
    },
    isLoading() {
      return this.$store.state.isLoading;
    },
  },
  mounted() {
    this.createChart();
    this.loadDashboard();
  },
};
</script>


<style>
.chart-container {
  max-height: 400px !important;
}

.message-response {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 20px;
  font-weight: bold;
  color: #888;
}


@media (max-width: 576px) {
  .chart-container {
    padding: 0px;
  }
}
</style>
