<template>
    <div class="app">
        <div class="app-wrapper">
            <div class="app-content pt-3 p-md-3 p-lg-4">
                <div class="container-xl">
                    <div class="row g-3 mb-4 align-items-center justify-content-between">
                        <div class="col-auto">
                            <h1 class="app-page-title mb-0">Product Variations</h1>
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
                                        <a href="#" class="btn app-btn-secondary" @click.prevent="addVariation()">
                                            Create Variation
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
                                    <thead class="text-uppercase">
                                        <tr>
                                            <th class="cell">Variation</th>
                                            <th class="cell">Variation Options</th>
                                            <th class="cell">Status</th>
                                            <th class="cell">Date Created</th>
                                            <th class="cell"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!variations.data || Object.keys(variations.data).length === 0">
                                            <td colspan="5" align="middle">
                                                There is no variation found
                                            </td>
                                        </tr>
                                        <tr v-else v-for="variation in variations.data" :key="variation.id">


                                            <td class="cell" data-label="Variation">
                                                {{ variation.variation_name }}
                                            </td>

                                            <td class="cell" data-label="Variation Options">
                                                <ul class="list-unstyled">
                                                    <li v-for="option in variation.options" :key="option.id"
                                                        class="list-inline-item">
                                                        <span class="border border-dark p-1 mb-2 rounded">{{ option.value
                                                        }}</span>
                                                    </li>
                                                </ul>
                                            </td>

                                            <td class="cell" data-label="Status">
                                                {{ variation.status == 0 ? 'Active' : 'Inactive' }}
                                            </td>

                                            <td class="cell" data-label="Date Created">
                                                {{ formatDateTime(variation.created_at) }}
                                            </td>


                                            <td class="cell">
                                                <div v-if="variation.status == 0">
                                                    <a class="btn btn-info btn-sm p-1" href="#"
                                                        @click.prevent="editVariation(variation)"><span class="iconify fs-4"
                                                            data-icon="vaadin:edit"></span> Edit</a>
                                                    <a class="btn btn-danger btn-sm p-1" href="#"
                                                        @click.prevent="deactivateVariation(variation)"><span
                                                            class="iconify fs-4"
                                                            data-icon="fluent-mdl2:deactivate-orders"></span> Deactivate</a>
                                                </div>
                                                <div v-else>
                                                    <a class="btn btn-success btn-sm p-1" href="#"
                                                        @click.prevent="activateVariation(variation)"><span
                                                            class="iconify fs-4"
                                                            data-icon="fluent-mdl2:activate-orders"></span> Activate</a>
                                                </div>

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
                                <pagination :data="variations" @pagination-change-page="loadVariations"></pagination>
                            </div>
                            <div class="mb-2 mb-md-0">
                                <span class="d-block text-center mb-1">Showing {{ variations.from }} to {{ variations.to }}
                                    of
                                    {{ variations.total }} entries</span>
                            </div>
                        </div>
                    </nav>
                    <!--//app-pagination-->
                </div>
                <!--//container-fluid-->
            </div>
            <!--//app-content-->

            <div class="modal fade" id="VariationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                                    <label for="variation_title" class="col-sm-2 col-form-label fw-bold">Variation:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="variation_title" name="variation_title"
                                            v-model="variationForm.variation" class="form-control"
                                            :class="{ 'is-invalid': variationForm.errors.has('variation') }"
                                            placeholder="Enter variation" />
                                        <div v-if="variationErrorMessage" class="invalid-feedback">
                                            {{ variationErrorMessage }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Variation Options -->
                                <div class="form-group row mb-2">
                                    <label for="variation_options" class="col-sm-2 col-form-label fw-bold">Options:</label>
                                    <div class="col-sm-10">
                                        <textarea id="variation_options" rows="8" name="variation_options"
                                            v-model="variationForm.options" class="form-control"
                                            :class="{ 'is-invalid': variationForm.errors.has('options') }"
                                            placeholder="Enter variation options ( comma-separated or one option per line )"></textarea>
                                        <div v-if="variationOptionsErrorMessage" class="invalid-feedback">
                                            {{ variationOptionsErrorMessage }}
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary modal-btn"
                                        :disabled="variationForm.busy"></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="removeVariationModal" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 p-0">
                            <button type="button" class="btn-close me-1 mt-2" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-2">
                                <span class="iconify h1 fs-1 deactivate" data-icon="noto-v1:sad-but-relieved-face"></span>
                                <span class="iconify h1 fs-1 activate" data-icon="noto-v1:smiling-face"></span>
                                <h3 class="deactivate">Are you sure?</h3>
                                <h3 class="activate">Do you want to activate this?</h3>
                                <div class="d-flex flex-column gap-1">
                                    <i class="deactivate">You won't be able to use this once its deactivated!</i>
                                    <i class="activate">You can now use this variation once its activated</i>
                                    <div class="text-center">
                                        <a @click.prevent="confirmStatusVariation()" href="#"
                                            class="btn btn-danger btn-sm col-4 text-center mt-2 text-white deactivate">Yes,
                                            deactivate
                                            it</a>

                                        <a @click.prevent="confirmStatusVariation()" href="#"
                                            class="btn btn-success btn-sm col-4 text-center mt-2 text-white activate">Yes,
                                            activate
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
        Footer,
        CustomForm,
    },
    data() {
        return {
            variations: {},
            paginate: 10,
            form: {
                search: "",
            },
            variationForm: new CustomForm({
                variation: "",
                options: "",
                _method: null,
            }),
            variationErrorMessage: "",
            variationOptionsErrorMessage: "",
            selectedId: null,
        };
    },

    watch: {
        paginate: function (value) {
            this.loadVariations();
        },
    },

    methods: {
        async loadVariations(page = 1, search = "") {
            try {
                const response = await axios.get("/api/variation?page=" + page + "&paginate=" + this.paginate + "&search=" + search);
                this.variations = response.data;
            } catch (error) {
                console.error(error);
            }
        },

        search() {
            this.loadVariations(1, this.form.search);
        },

        async addVariation() {
            $(".modal-title").text("Add Variation");
            $(".modal-btn").text("Save");
            this.variationForm.reset();
            $("#VariationModal").modal("show");
        },

        async createVariation() {

            // Split options by new lines and join them with commas
            const options = this.variationForm.options
                .split(/\r?\n/)
                .map((option) => option.trim())
                .filter((option) => option !== "")
                .join(",");

            // Include the comma-separated options in the form data sent to the server
            this.variationForm.options = options;

            await this.variationForm
                .post("/api/variation")
                .then(({ data }) => {
                    //clear input after insert
                    this.variationForm.variation = "";
                    this.variationForm.options = "";


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
                            icon: 'my-custom-alert-icon',
                        },
                    });

                    $("#VariationModal").modal("hide");

                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        this.variationErrorMessage = errors.variation ? errors.variation[0] : "";
                        this.variationOptionsErrorMessage = errors.options ? errors.options[0] : "";
                    } else {
                        //why this alert is showing even successfully inserted
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
                }).finally(() => {
                    // Common code to execute regardless of success or error
                    this.loadVariations();
                });
        },


        async editVariation(variation) {
            $(".modal-title").text("Edit Variation");
            $(".modal-btn").text("Update");

            this.variationForm.variation = variation.variation_name;
            this.variationForm.options = variation.options.map((option) => option.value).join('\n');  // Extract the variation options' values and join them with new lines to create a formatted string
            this.variationForm.selectedId = variation.id;

            // console.log(this.variationForm.selectedId)

            $("#VariationModal").modal("show");
        },


        async updateVariation() {
            if (this.variationForm.selectedId !== null) {
                let id = this.variationForm.selectedId;

                // Split options by new lines and join them with commas
                const options = this.variationForm.options
                    .split(/\r?\n/)
                    .map((option) => option.trim())
                    .filter((option) => option !== "")
                    .join(",");

                // Include the comma-separated options in the form data sent to the server
                this.variationForm.options = options;


                await this.variationForm
                    .post(`/api/variation/${id}`)
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


                        $("#VariationModal").modal("hide");

                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const errors = error.response.data.errors;
                            this.variationErrorMessage = errors.variation ? errors.variation[0] : "";
                            this.variationOptionsErrorMessage = errors.options ? errors.options[0] : "";
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
                    }).finally(() => {
                        this.loadVariations();
                    });
            }
        },


        formAction() {
            if (this.variationForm.selectedId) {
                this.variationForm._method = "put";
                this.updateVariation();
            } else {
                this.variationForm._method = "post";
                this.createVariation();
            }
        },

        async deactivateVariation(variation) {
            this.variationForm.selectedId = variation.id;
            $(".deactivate").show();
            $(".activate").hide();
            $("#removeVariationModal").modal("show");
        },

        async activateVariation(variation) {
            this.variationForm.selectedId = variation.id;
            $(".deactivate").hide();
            $(".activate").show();
            $("#removeVariationModal").modal("show");
        },

        async confirmStatusVariation() {
            if (this.variationForm.selectedId !== null) {
                await axios
                    .delete(`/api/variation/${this.variationForm.selectedId}`)
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

                    });
                this.loadVariations();
                $("#removeVariationModal").modal("hide");
            }
        },


        formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            const formattedDate = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
            const formattedTime = `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
            return `${formattedDate} ${formattedTime}`;
        },





    },

    mounted() {
        this.loadVariations();

    },
};
</script>


<style scoped>
.list-group-item {
    display: inline-block;
    width: calc(33.33% - 12px);
}
</style>
