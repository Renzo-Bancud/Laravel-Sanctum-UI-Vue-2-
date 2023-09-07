<template>
    <div class="app">
        <div class="app-wrapper">
            <div class="app-content pt-3 p-md-3 p-lg-4">
                <div class="container-xl">
                    <div class="row g-3 mb-4 align-items-center justify-content-between">
                        <div class="col-auto">
                            <h1 class="app-page-title mb-0">Activity Logs</h1>
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
                                            <th class="cell">User</th>
                                            <th class="cell">Activity Type</th>
                                            <th class="cell">Activity Description</th>
                                            <th class="cell">User Agent</th>
                                            <th class="cell">IP Address</th>
                                            <th class="cell">Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!logs.data || Object.keys(logs.data).length === 0">
                                            <td colspan="6" align="middle">
                                                There is no logs found
                                            </td>
                                        </tr>
                                        <tr v-else v-for="log in logs.data" :key="log.id">

                                            <td class="cell" data-label="User">
                                                {{ log.name }}
                                            </td>
                                            <td class="cell" data-label="Activity Type">
                                                {{ log.activity_type }}
                                            </td>
                                            <td class="cell" data-label="Activity Description">
                                                {{ log.activity_description }}
                                            </td>
                                            <td class="cell" data-label="User Agent">
                                                {{ log.user_agent }}
                                            </td>

                                            <td class="cell" data-label="IP Address">
                                                {{ log.ip_address }}
                                            </td>

                                            <td class="cell" data-label="Created At">
                                                {{ log.logs_created }}
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
                                <pagination :data="logs" @pagination-change-page="loadLogs"></pagination>
                            </div>
                            <div class="mb-2 mb-md-0">
                                <span class="d-block text-center mb-1">Showing {{ logs.from }} to {{ logs.to }} of
                                    {{ logs.total }} entries</span>
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
            logs: {},
            paginate: 10,
            form: {
                search: "",
            },
        };
    },

    watch: {
        paginate: function (value) {
            this.loadLogs();
        },
    },

    methods: {
        async loadLogs(page = 1, search = "") {
            await axios
                .get("/api/activity-logs?page=" + page + "&paginate=" + this.paginate + "&search=" + search)
                .then((response) => {
                    this.logs = response.data.logs;
                });
        },


        search() {
            this.loadLogs(1, this.form.search);
        },

        


    },
    mounted() {
        this.loadLogs();

    },
};
</script>
  
  