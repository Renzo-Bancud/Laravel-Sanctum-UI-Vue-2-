<template>
    <div class="app">
        <div class="app-wrapper">
            <div class="app-content pt-3 p-md-3 p-lg-4">
                <div class="container-xl">
                    <div class="row g-3 mb-4 align-items-center justify-content-between">
                        <div class="col-auto">
                            <h1 class="app-page-title mb-0">Slogan Carousel</h1>
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
                                        <a href="#" class="btn app-btn-secondary" @click.prevent="addSlogan()">
                                            Create Slogan
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
                                            <th class="cell">Slogan Background</th>
                                            <th class="cell">Title</th>
                                            <th class="cell">Description</th>
                                            <th class="cell"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!slogans.data || Object.keys(slogans.data).length === 0">
                                            <td colspan="4" align="middle">
                                                There is no slogan found
                                            </td>
                                        </tr>
                                        <tr v-else v-for="slogan in slogans.data" :key="slogan.id">

                                            <td class="cell" data-label="Main Image">
                                                <img v-if="slogan.image" :src="slogan.image" alt="slogan Image"
                                                    style="object-fit:contain;height:200px;width:200px;" />
                                                <img v-else :src="require('/storage/img-icon/no-image-icon.png')"
                                                    alt="No Image" height="200" width="200" />
                                            </td>



                                            <td class="cell" data-label="Title">
                                                {{ slogan.title }}
                                            </td>

                                            <td class="cell text-justify" data-label="Description">
                                                {{ slogan.description }}
                                            </td>
                                            <td class="cell">
                                                <a class="btn btn-info btn-sm p-1" href="#"
                                                    @click.prevent="editSlogan(slogan)"><span class="iconify fs-4"
                                                        data-icon="vaadin:edit"></span> Edit</a>
                                                <a class="btn btn-danger btn-sm p-1" href="#"
                                                    @click.prevent="deleteSlogan(slogan)"><span class="iconify fs-4"
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
                                <pagination :data="slogans" @pagination-change-page="loadSlogans"></pagination>
                            </div>
                            <div class="mb-2 mb-md-0">
                                <span class="d-block text-center mb-1">Showing {{ slogans.from }} to {{ slogans.to }} of
                                    {{ slogans.total }} entries</span>
                            </div>
                        </div>
                    </nav>
                    <!--//app-pagination-->
                </div>
                <!--//container-fluid-->
            </div>
            <!--//app-content-->

            <div class="modal fade" id="SloganModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="staticBackdropLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form @submit.prevent="formAction()">
                                <div class="form-group row mb-2">
                                    <label for="slogan_title" class="col-sm-2 col-form-label fw-bold">Title:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="slogan_title" name="slogan_title" v-model="sloganForm.title"
                                            class="form-control" :class="{ 'is-invalid': sloganForm.errors.has('title') }"
                                            placeholder="Enter slogan title" />
                                        <div v-if="titleErrorMessage" class="invalid-feedback">
                                            {{ titleErrorMessage }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="slogan_image" class="col-sm-2 col-form-label fw-bold">Background:</label>
                                    <div class="col-sm-10">
                                        <div id="editSloganImageView" class="text-center">
                                            <img v-if="sloganImage" :src="sloganImage" height="200" width="200"
                                                class="img-fluid mb-2" alt="Slogan Image" />
                                            <img v-else :src="sloganForm.image + '?rand=' + Math.random()" alt="No Image"
                                                class="img-fluid" height="200" width="200" />
                                        </div>

                                        <input type="file" class="form-control mt-2" name="slogan_image" id="slogan_image"
                                            accept=".png, .webp" @change="onImageChange"
                                            :class="{ 'is-invalid': sloganForm.errors.has('image') }" />
                                        <div v-if="imageErrorMessage" class="invalid-feedback">
                                            {{ imageErrorMessage }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="slogan_description" class="col-sm-2 col-form-label fw-bold">Description:</label>
                                    <div class="col-sm-10">
                                        <textarea rows="5" id="slogan_description" name="slogan_description"
                                            v-model="sloganForm.description" class="form-control h-100" :class="{
                                                'is-invalid': sloganForm.errors.has('description'),
                                            }" placeholder="Enter slogan description"></textarea>
                                        <div v-if="descriptionErrorMessage" class="invalid-feedback">
                                            {{ descriptionErrorMessage }}
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary modal-btn"
                                        :disabled="sloganForm.busy"></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="removeSloganModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                                        <a @click.prevent="confirmDeleteSlogan()" href="#"
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
import { objectToFormData } from "object-to-formdata"; // this package is needed install it to be able to upload image

export default {
    components: {
        Footer,
        CustomForm,
        objectToFormData,
    },
    data() {
        return {
            slogans: {},
            paginate: 10,
            form: {
                search: "",
            },
            sloganForm: new CustomForm({
                title: "",
                description: "",
                image: [],
                _method: null,
            }),
            sloganImage: "",
            titleErrorMessage: "",
            descriptionErrorMessage: "",
            imageErrorMessage: "",
            selectedId: null,
        };
    },

    watch: {
        paginate: function (value) {
            this.loadSlogans();
        },
    },

    methods: {
        async loadSlogans(page = 1, search = "") {
            try {
                const response = await axios.get("/api/slogan?page=" + page + "&paginate=" + this.paginate + "&search=" + search);
                this.slogans = response.data;
            } catch (error) {
                console.error(error);
            }
        },

        search() {
            this.loadSlogans(1, this.form.search);
        },

        async addSlogan() {
            $(".modal-title").text("Add Slogan");
            $(".modal-btn").text("Save");
            $("#editSloganImageView").hide();
            this.sloganForm.reset();
            $("#SloganModal").modal("show");
        },

        async createSlogan() {
            const config = {
                transformRequest: [function (data, headers) { }],
                onUploadProgress: (e) => {
                    console.log(e);
                },
            };

            await this.sloganForm
                .post("/api/slogan", {}, config)
                .then(({ data }) => {
                    //clear input after insert
                    this.sloganForm.title = "";
                    this.sloganForm.description = "";

                    // Reset image input manually
                    const imgInput = document.getElementById("slogan_image");
                    imgInput.value = null;

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

                    $("#SloganModal").modal("hide");

                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        this.titleErrorMessage = errors.title ? errors.title[0] : "";
                        this.imageErrorMessage = errors.image ? errors.image[0] : "";
                        this.descriptionErrorMessage = errors.description ? errors.description[0] : "";
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
                    this.loadSlogans();
                });
        },


        async editSlogan(slogan) {
            $(".modal-title").text("Edit Slogan");
            $(".modal-btn").text("Update");

            this.sloganForm.title = slogan.title
            this.sloganForm.image = slogan.image
            this.sloganForm.description = slogan.description
            this.sloganForm.selectedId = slogan.id

            $('input[type="file"]').val(null);
            $("#SloganModal").modal("show");
            $("#editSloganImageView").show();
            this.sloganImage = null;
        },


        async updateSlogan() {
            if (this.sloganForm.selectedId !== null) {
                let id = this.sloganForm.selectedId;

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

                await this.sloganForm
                    .post(`/api/slogan/${id}`, {}, config)
                    .then((response) => {
                        this.sloganImage = response.data.sloganImg; //prodImg response from server


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


                        $("#SloganModal").modal("hide");

                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const errors = error.response.data.errors;
                            this.titleErrorMessage = errors.title ? errors.title[0] : "";
                            this.imageErrorMessage = errors.image ? errors.image[0] : "";
                            this.descriptionErrorMessage = errors.description ? errors.description[0] : "";
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
                        this.loadSlogans();
                    });
            }
        },


        formAction() {
            if (this.sloganForm.selectedId) {
                this.sloganForm._method = "put";
                this.updateSlogan();
            } else {
                this.sloganForm._method = "post";
                this.createSlogan();
            }
        },

        async deleteSlogan(slogan) {
            this.sloganForm.selectedId = slogan.id;
            $("#removeSloganModal").modal("show");
        },
        async confirmDeleteSlogan() {
            if (this.sloganForm.selectedId !== null) {
                await axios
                    .delete(`/api/slogan/${this.sloganForm.selectedId}`)
                    .then(() => {

                        this.$swal.fire({
                            position: 'top-middle',
                            icon: 'success',
                            title: 'Slogan deleted',
                            showConfirmButton: false,
                            timer: 3000,
                            customClass: {
                                popup: 'my-custom-alert',
                                title: 'my-custom-alert-title',
                                icon: 'my-custom-alert-icon',
                            },
                        });

                    });
                this.loadSlogans();
                $("#removeSloganModal").modal("hide");
            }
        },

        onImageChange(e) {
            const file = e.target.files[0];

            const allowedTypes = ['image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                this.$swal.fire({
                    position: 'top-middle',
                    icon: 'error',
                    title: 'Invalid file type. Please select a PNG or WEBP image.',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'my-custom-alert',
                        title: 'my-custom-alert-title',
                        icon: 'my-custom-alert-icon',
                    },
                });
                return;
            }

            // Create a URL object from the selected file
            const imageUrl = URL.createObjectURL(file);
            this.sloganImage = imageUrl;

            // Set the image URL to the form object
            this.sloganForm.image = file;
        },



    },

    mounted() {
        this.loadSlogans();

    },
};
</script>
  
  
  