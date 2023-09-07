<template>
    <div class="app">
        <div class="app-wrapper">
            <div class="app-content pt-3 p-md-3 p-lg-4">
                <div class="container-xl">
                    <div class="row g-3 mb-4 align-items-center justify-content-between">
                        <div class="col-auto">
                            <h1 class="app-page-title mb-0">Customer Ratings</h1>
                        </div>
                        <div class="col-auto">
                            <div class="page-utilities">
                                <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                                    <div class="col-auto">
                                        <div class="table-search-form row gx-1 align-items-center">
                                            <div class="col-auto">
                                                <input type="text" id="search-orders" name="searchorders"
                                                    v-model="form.search" @keyup="search" class="form-control search-orders"
                                                    placeholder="Search" />
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
                                    <thead class="text-uppercase">
                                        <tr>
                                            <th class="cell">Image</th>
                                            <th class="cell">Order ID</th>
                                            <th class="cell">Product</th>
                                            <th class="cell">Price</th>
                                            <th class="cell">Rating</th>
                                            <th class="cell">Feedback</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!ratings.data || Object.keys(ratings.data).length === 0">
                                            <td colspan="8" align="middle">
                                                There is no rating found
                                            </td>
                                        </tr>
                                        <tr v-else v-for="rating in ratings.data" :key="rating.id">

                                            <td class="cell" data-label="Image">
                                                <img v-if="rating.image" :src="rating.image" alt="Product Image"
                                                    style="object-fit:conatin;height:100px;width:100px;" />
                                                <img v-else :src="require('/storage/img-icon/no-image-icon.png')"
                                                    alt="No Image" height="100" width="100" />
                                            </td>
                                            <td class="cell" data-label="Order ID">
                                                {{ rating.order_tracking_id }}
                                            </td>
                                            <td class="cell" data-label="Product">
                                                {{ rating.title }}
                                            </td>
                                            <td class="cell" data-label="Price">
                                                {{ formatPrice(rating.price) }}
                                            </td>

                                            <td class="cell" data-label="Rating">
                    

                                                <span v-for="star in 5" :key="star"
                                                    :class="{ 'star-filled': star <= rating.rate_star }">
                                                    <i class="fas fa-star"></i>
                                                </span>

                                            </td>

                                            <td class="cell" data-label="Feedback">
                                                {{ rating.feedback }}
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
                                <pagination :data="ratings" @pagination-change-page="loadAllRatings"></pagination>
                            </div>
                            <div class="mb-2 mb-md-0">
                                <span class="d-block text-center mb-1">Showing {{ ratings.from }} to {{ ratings.to }} of
                                    {{ ratings.total }} entries</span>
                            </div>
                        </div>
                    </nav>
                    <!--//app-pagination-->
                </div>
                <!--//container-fluid-->
            </div>
            <!--//app-content-->



            <Footer />
        </div>
        <!--//app-wrapper-->
    </div>
</template>
  
<script>
import Footer from "../../template/Footer.vue";
import formatPriceMixin from '../../mixins/formatPriceMixin';

export default {
    mixins: [formatPriceMixin],
    components: {
        Footer,
    },
    data() {
        return {
            ratings: {},
            paginate: 10,
            form: {
                search: "",
            },
        };
    },

    watch: {
        paginate: function (value) {
            this.loadAllRatings();
        },
    },

    methods: {
        async loadAllRatings(page = 1, search = "") {
            await axios
                .get("/api/get-all-ratings?page=" + page + "&paginate=" + this.paginate + "&search=" + search)
                .then((response) => {
                    this.ratings = response.data.ratings;
                });
        },

        search() {
            this.loadAllRatings(1, this.form.search);
        },

    },
    mounted() {
        this.loadAllRatings();

    },
};
</script>
  
  