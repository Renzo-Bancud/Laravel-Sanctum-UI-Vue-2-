<template>
    <div class="app">
        <div class="app-wrapper">
            <div class="app-content pt-3 p-md-3 p-lg-4">
                <div class="container-xl">
                    <div class="row g-3 mb-4 align-items-center justify-content-between">
                        <div class="col-auto">
                            <h1 class="app-page-title mb-0">Inventory</h1>
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
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th class="cell">Product</th>
                                            <th class="cell">Qty In</th>
                                            <th class="cell">Qty Out</th>
                                            <th class="cell">Remaining</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!inventories.data || Object.keys(inventories.data).length === 0">
                                            <td colspan="4" align="middle">
                                                There is no inventory found
                                            </td>
                                        </tr>
                                        <tr v-else v-for="inventory in  inventories.data" :key="inventory.id">

                                            <td class="cell" data-label="Product">
                                                <span v-if="inventory.product">
                                                    {{ inventory.product.title }}</span>
                                                <span v-else> {{ inventory.product_id }} </span>
                                            </td>

                                            <td class="cell" data-label="Quantity IN">
                                                {{ inventory.qty_in }}
                                            </td>

                                            <td class="cell" data-label="Quantity OUT">
                                                <span v-if="inventory.qty_out == null">
                                                ---
                                                </span>
                                                <span v-else>
                                                {{ inventory.qty_out }}
                                                </span>
                                            </td>

                                            <td class="cell" data-label="Remaining">
                                                <span v-if="inventory.qty_out == null">
                                                ---
                                                </span>
                                                <span v-else>
                                                {{ inventory.qty_in - inventory.qty_out  }}
                                                </span>
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
                                <pagination :data="inventories" @pagination-change-page="loadInventory"></pagination>
                            </div>
                            <div class="mb-2 mb-md-0">
                                <span class="d-block text-center mb-1">Showing {{ inventories.from }} to {{ inventories.to
                                }}
                                    of
                                    {{ inventories.total }} entries</span>
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

export default {
    components: {
        Footer,
    },
    data() {
        return {
            inventories: {},
            paginate: 10,
            form: {
                search: "",
            },
        };
    },

    watch: {
        paginate: function (value) {
            this.loadInventory();
        },
    },

    methods: {
        async loadInventory(page = 1, search = "") {
            await axios
                .get("/api/product-inventory?page=" + page + "&paginate=" + this.paginate + "&search=" + search)
                .then((response) => {
                    this.inventories = response.data;
                    //console.log(this.inventories)
                });
        },

        search() {
            this.loadInventory(1, this.form.search);
        },

    },
    mounted() {
        this.loadInventory();
    },
}
</script>

<style></style>