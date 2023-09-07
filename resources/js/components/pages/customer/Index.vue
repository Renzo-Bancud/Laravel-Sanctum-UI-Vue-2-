<template>
    <div class="app">
        <div class="app-wrapper">
            <div class="app-content pt-3 p-md-3 p-lg-4">
                <div class="container-xl">
                    <div class="row g-3 mb-4 align-items-center justify-content-between">
                        <div class="col-auto">
                            <h1 class="app-page-title mb-0">Customers</h1>
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
                                    <div class="col-auto">
                                        <a href="#" class="btn app-btn-secondary" @click.prevent="addCustomer()">
                                            Create Customer
                                            <span class="iconify fs-4" data-icon="ic:outline-library-add"></span>
                                        </a>
                                    </div>
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
                                            <th class="cell">Profile</th>
                                            <th class="cell">Name</th>
                                            <th class="cell" style="width:200px;">Email</th>
                                            <th class="cell">Contact</th>
                                            <th class="cell">House No.</th>
                                            <th class="cell">Province</th>
                                            <th class="cell">Municipality</th>
                                            <th class="cell">Barangay</th>
                                            <th class="cell" style="width:200px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!customers.data || Object.keys(customers.data).length === 0">
                                            <td colspan="9" align="middle">
                                                There is no customer found
                                            </td>
                                        </tr>
                                        <tr v-else v-for="customer in  customers.data" :key="customer.id">

                                            <td class="cell" data-label="Profile">
                                                <img v-if="customer.profile" :src="customer.profile" alt="User Profile"
                                                    style="object-fit:contain;height:100px;width:100px;" />
                                                <img v-else :src="require('/storage/img-icon/no-image-icon.png')"
                                                    alt="No Image" height="100" width="100" />
                                            </td>

                                            <td class="cell" data-label="Customer">
                                                {{ customer.name }}
                                            </td>

                                            <td class="cell" data-label="Email">
                                                {{

                                                    customer.email.length > 20
                                                    ? customer.email.substring(0, 20) + "..."
                                                    : customer.email


                                                }}
                                            </td>

                                            <td class="cell" data-label="Contact">
                                                <span v-if="customer.user_contact">
                                                    {{ customer.user_contact.mobile_number }}</span>
                                                <span v-else> --- </span>
                                            </td>


                                            <td class="cell" data-label="House No.">
                                                <div v-if="customer.user_contact">
                                                    {{ customer.user_contact.house_no }}
                                                </div>
                                                <div v-else> --- </div>
                                            </td>

                                            <td class="cell" data-label="Province">
                                                <div v-if="customer.user_contact">
                                                    {{ customer.user_contact.province }}
                                                </div>
                                                <div v-else> --- </div>
                                            </td>


                                            <td class="cell" data-label="Municipality">
                                                <div v-if="customer.user_contact">
                                                    {{ customer.user_contact.city }}
                                                </div>
                                                <div v-else> --- </div>
                                            </td>

                                            <td class="cell" data-label="Barangay">
                                                <div v-if="customer.user_contact">
                                                    {{ customer.user_contact.barangay }}
                                                </div>
                                                <div v-else> --- </div>
                                            </td>

                                            <td class="cell">
                                                <a class="btn btn-info btn-sm p-1" href="#"
                                                   @click.prevent="editCustomer(customer)"><span class="iconify fs-4"
                                                        data-icon="vaadin:edit"></span> Edit</a>
                                                <a class="btn btn-danger btn-sm p-1" href="#"
                                                   @click.prevent="deleteCustomer(customer)"><span class="iconify fs-4"
                                                        data-icon="bi:trash"></span> Delete</a>
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
                                <pagination :data="customers" @pagination-change-page="loadCustomer"></pagination>
                            </div>
                            <div class="mb-2 mb-md-0">
                                <span class="d-block text-center mb-1">Showing {{ customers.from }} to {{ customers.to
                                }}
                                    of
                                    {{ customers.total }} entries</span>
                            </div>
                        </div>
                    </nav>
                    <!--//app-pagination-->
                </div>
                <!--//container-fluid-->
            </div>
            <!--//app-content-->


            <div class="modal fade" id="customer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="staticBackdropLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="formAction()">

                                <div class="form-group row mb-2">
                                    <label for="qty" class="col-sm-3 col-form-label fw-bold">First Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="fname" name="fname" v-model="customerForm.firstname"
                                            class="form-control"
                                            :class="{ 'is-invalid': customerForm.errors.has('firstname') }"
                                            placeholder="Enter First Name" />
                                        <div v-if="fnameErrorMessage" class="invalid-feedback">
                                            {{ fnameErrorMessage }}
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group row mb-2">
                                    <label for="qty" class="col-sm-3 col-form-label fw-bold">Last Name:</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="lname" name="lname" v-model="customerForm.lastname"
                                            class="form-control"
                                            :class="{ 'is-invalid': customerForm.errors.has('lastname') }"
                                            placeholder="Enter Last Name" />
                                        <div v-if="lnameErrorMessage" class="invalid-feedback">
                                            {{ lnameErrorMessage }}
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group row mb-2">
                                    <label for="qty" class="col-sm-3 col-form-label fw-bold">Email:</label>
                                    <div class="col-sm-9">
                                        <input type="text" id="email" name="email" v-model="customerForm.email"
                                            class="form-control" :class="{ 'is-invalid': customerForm.errors.has('email') }"
                                            placeholder="Enter Email Address" />
                                        <div v-if="emailErrorMessage" class="invalid-feedback">
                                            {{ emailErrorMessage }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-2 mt-3">
                                    <label for="qty" class="col-sm-3 col-form-label fw-bold">Password:</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'"
                                                v-model="customerForm.password" class="form-control"
                                                :class="{ 'is-invalid': customerForm.errors.has('password') }"
                                                placeholder="Enter New Password">
                                            <span class="input-group-text" @click.prevent="toggleShowPassword"> {{
                                                showPassword
                                                ?
                                                'Hide' : 'Show'
                                            }}</span>

                                            <div class="invalid-feedback" v-if="passwordErrorMessage">
                                                {{ passwordErrorMessage }}
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-sm btn-dark mt-2 hover-pulse"
                                            @click.prevent="generateRandomPassword">Generate Password</button>


                                        <br>
                                        <small style="position:relative;top:10px;" v-if="editMessage"><i>The password will
                                                be updated if a new password
                                                is provided in the
                                                input.</i></small>

                                    </div>
                                </div>




                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" :disabled="customerForm.busy"
                                        class="btn btn-primary modal-btn"></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




            <div class="modal fade" id="removeCustomer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 p-0">
                            <button type="button" class="btn-close me-1 mt-2" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-2">
                                <span class="iconify h1 fs-1" data-icon="noto-v1:sad-but-relieved-face"></span>
                                <h3>Are you sure?</h3>
                                <div class="d-flex flex-column gap-1">
                                    <i>You won't be able to revert this!</i>
                                    <div class="text-center">
                                        <a @click.prevent="confirmDeleteCustomer()" href="#"
                                            class="btn btn-danger btn-sm col-4 text-center mt-2 text-white">Yes, delete
                                            it</a>
                                    </div>
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

export default {
    components: {
        CustomForm,
        Footer,
    },
    data() {
        return {
            customerForm: new CustomForm({
                firstname: "",
                lastname: "",
                email: "",
                password: "",
                _method: null,
            }),
            fnameErrorMessage: "",
            lnameErrorMessage: "",
            emailErrorMessage: "",
            passwordErrorMessage: "",
            showPassword: false,
            selectedId: null,
            customers: {},
            paginate: 10,
            form: {
                search: "",
            },
            editMessage: false,
        };
    },

    watch: {
        paginate: function (value) {
            this.loadCustomer();
        },
    },

    methods: {
        async loadCustomer(page = 1, search = "") {
            await axios
                .get("/api/customer?page=" + page + "&paginate=" + this.paginate + "&search=" + search)
                .then((response) => {
                    this.customers = response.data;
                });
        },

        search() {
            this.loadCustomer(1, this.form.search);
        },

        async addCustomer() {
            $(".modal-title").html("<span class='iconify fs-3' data-icon='typcn:user-add-outline'></span> Add Customer");
            $(".modal-btn").text("Save");
            $("#customer").modal("show");
        },

        async createCustomer() {
            await this.customerForm
                .post("/api/customer")
                .then(({ data }) => {
                    //clear input after insert
                    this.customerForm.firstname = "";
                    this.customerForm.lastname = "";
                    this.customerForm.email = "";
                    this.customerForm.password = "";

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


                    this.loadCustomer();
                    $("#customer").modal("hide");
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        this.fnameErrorMessage = errors.firstname ? errors.firstname[0] : "";
                        this.lnameErrorMessage = errors.lastname ? errors.lastname[0] : "";
                        this.emailErrorMessage = errors.email ? errors.email[0] : "";
                        this.passwordErrorMessage = errors.password ? errors.password[0] : "";
                    } else {
                        // console.log(error)
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
                });
        },

        async editCustomer(customer) {
            $(".modal-title").html("<span class='iconify fs-3' data-icon='la:user-edit'></span> Edit Customer");
            $(".modal-btn").text("Update");
            $("#customer").modal("show");
            this.editMessage = true;
            const [firstname, lastname] = customer.name.split(" ");
            this.customerForm.firstname = firstname;
            this.customerForm.lastname = lastname;
            this.customerForm.email = customer.email;
            this.customerForm.password = '';
            this.customerForm.selectedId = customer.id;
        },

        async updateCustomer() {
            if (this.customerForm.selectedId !== null) {
                let id = this.customerForm.selectedId;

                await this.customerForm
                    .put(`/api/customer/${id}`)
                    .then((response) => {

                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'success',
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 3000,
                            customClass: {
                                popup: 'my-custom-alert',
                                title: 'my-custom-alert-title',
                                icon: 'my-custom-alert-icon',
                            },
                        });


                        $("#customer").modal("hide");
                        this.loadCustomer();
                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const errors = error.response.data.errors;
                            this.fnameErrorMessage = errors.firstname ? errors.firstname[0] : "";
                            this.lnameErrorMessage = errors.lastname ? errors.lastname[0] : "";
                            this.emailErrorMessage = errors.email ? errors.email[0] : "";
                            this.passwordErrorMessage = errors.password ? errors.password[0] : "";
                        } else {

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
                    });




            }

        },

        async deleteCustomer(customer) {
            this.customerForm.selectedId = customer.id;
            $("#removeCustomer").modal("show");
        },


        async confirmDeleteCustomer() {
            if (this.customerForm.selectedId !== null) {
                await axios
                    .delete(`/api/customer/${this.customerForm.selectedId}`)
                    .then(() => {
                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'success',
                            title: 'Customer deleted',
                            showConfirmButton: false,
                            timer: 3000,
                            customClass: {
                                popup: 'my-custom-alert',
                                title: 'my-custom-alert-title',
                                icon: 'my-custom-alert-icon',
                            },
                        });
                    });

                $("#removeCustomer").modal("hide");
                this.loadCustomer();

            }
        },

        generateRandomPassword() {
            const randomPassword = Math.random().toString(36).slice(-8); // Generate random 8-character password
            this.customerForm.password = randomPassword;
        },

        toggleShowPassword() {
            this.showPassword = !this.showPassword;
        },


        formAction() {
            if (this.customerForm.selectedId) {
                this.customerForm._method = "put";
                this.updateCustomer();
            } else {
                this.customerForm._method = "post";
                this.createCustomer();
            }
        },

    },
    mounted() {
        this.loadCustomer();
    },
}
</script>

<style></style>