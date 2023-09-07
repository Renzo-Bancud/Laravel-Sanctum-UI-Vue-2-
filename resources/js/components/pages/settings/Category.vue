<template>
  <div class="app">
    <div class="app-wrapper">

      <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">

          <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
              <h1 class="app-page-title mb-0">Product Category</h1>
            </div>
            <div class="col-auto">
              <div class="page-utilities">
                <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
                  <div class="col-auto">
                    <div class="table-search-form row gx-1 align-items-center">
                      <div class="col-auto">
                        <input type="text" id="search-orders" name="searchorders" v-model="form.search" @keyup="search"
                          class="form-control search-orders" placeholder="Search">
                      </div>
                      <div class="col-auto d-flex">
                        <select v-model="paginate" class="form-select w-auto">
                          <option value="10">10</option>
                          <option value="20">20</option>
                          <option value="30">30</option>
                        </select>
                      </div>
                    </div>

                  </div><!--//col-->
                  <div class="col-auto d-none">

                    <select class="form-select w-auto">
                      <option selected value="option-1">All</option>
                      <option value="option-2">This week</option>
                      <option value="option-3">This month</option>
                      <option value="option-4">Last 3 months</option>

                    </select>
                  </div>
                  <div class="col-auto">
                    <a href="#" class="btn app-btn-secondary" @click.prevent="addCategory()">
                      Create Category <span class="iconify fs-4" data-icon="ic:outline-library-add"></span>
                    </a>
                  </div>
                </div><!--//row-->
              </div><!--//table-utilities-->
            </div><!--//col-auto-->
          </div><!--//row-->

          <div class="app-card app-card-orders-table shadow-sm mb-4">
            <div class="app-card-body">
              <div class="table-responsive">
                <table class="table-res app-table-hover table-striped mb-0 text-left">
                  <thead>
                    <tr class="text-uppercase">
                      <th class="cell">Name</th>
                      <th class="cell">Created At</th>
                      <th class="cell"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!categories.data || Object.keys(categories.data).length === 0">
                      <td colspan="4" align="middle">There is no categories found</td>
                    </tr>
                    <tr v-else v-for="category in categories.data" :key="category.id">
                      <td class="cell" data-label="Name">{{ category.name }}</td>
                      <td class="cell" data-label="Created At">{{ formattedDateTime(category.created_at) }}</td>
                      <td class="cell">
                        <a class="btn btn-info btn-sm p-1" href="#" @click.prevent="editCategory(category)"><span
                            class="iconify fs-4" data-icon="vaadin:edit"></span> Edit</a>
                        <a class="btn btn-danger btn-sm p-1" href="#"
                        @click.prevent="deleteCategory(category)"><span class="iconify fs-4"
                            data-icon="bi:trash"></span> Delete</a>

                      </td>
                    </tr>
                  </tbody>
                </table>
              </div><!--//table-responsive-->
            </div><!--//app-card-body-->
          </div><!--//app-card-->
          <nav class="app-pagination">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
              <div class="d-flex justify-content-center align-items-center mb-2 mb-md-0">
                <pagination :data="categories" @pagination-change-page="loadCategories"></pagination>
              </div>
              <div class="mb-2 mb-md-0">
                <span class="d-block text-center mb-1">Showing {{ categories.from }} to {{ categories.to }} of {{
                  categories.total }} entries</span>
              </div>
            </div>
          </nav><!--//app-pagination-->

        </div><!--//container-fluid-->
      </div><!--//app-content-->

      <div class="modal fade" id="addCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header border-0">
              <h5 class="modal-title" id="staticBackdropLabel">Add Category</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="createCategory">
                <div class="form-group row">
                  <label for="category_name" class="col-sm-2 col-form-label fw-bold">Name:</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="category_name" v-model="categoryForm.name"
                      :class="{ 'is-invalid': categoryForm.errors.has('name') }" placeholder="Input category name">
                    <div v-if="errorMessage" class="invalid-feedback">
                      {{ errorMessage }}
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                  <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" :disabled="categoryForm.busy">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>


      <div class="modal fade" id="editCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header border-0">
              <h5 class="modal-title" id="staticBackdropLabel">Edit Category</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="updateCategory">
                <div class="form-group row">
                  <label for="category_name" class="col-sm-2 col-form-label fw-bold">Name:</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="category_name" v-model="categoryForm.name"
                      :class="{ 'is-invalid': categoryForm.errors.has('name') }" placeholder="Input category name">
                    <div v-if="errorMessage" class="invalid-feedback">
                      {{ errorMessage }}
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                  <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary" :disabled="categoryForm.busy">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="removeCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header border-0 p-0">
              <button type="button" class="btn-close me-1 mt-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
              <div class="mb-2">
                <span class="iconify h1 fs-1" data-icon="noto-v1:sad-but-relieved-face"></span>
                <h3>Are you sure?</h3>
                <div class="d-flex flex-column gap-1">
                  <i>You won't be able to revert this!</i>
                  <div class="text-center">
                    <a @click.prevent="confirmDeleteCategory()" href="#"
                      class="btn btn-danger btn-sm col-4 text-center mt-2 text-white">Yes, delete it</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <Footer />

    </div><!--//app-wrapper-->
  </div>
</template>

<script>
import { Form as CustomForm } from 'vform'
import Footer from '../../template/Footer.vue';


export default {
  components: {
    Footer, CustomForm
  },
  data() {
    return {
      categories: {},
      paginate: 10,
      form: {
        search: '',
      },
      selectedCategories: [],
      selectAll: false,

      categoryForm: new CustomForm({
        name: "",
      }),
      errorMessage: "", // Add this line to store the error message
      selectedId: null,

    }
  },

  watch: {
    paginate: function (value) {
      this.loadCategories();
    },
  },
  methods: {
    async loadCategories(page = 1, search = '') {
      await axios.get('/api/category?page=' + page + '&paginate=' + this.paginate + '&search=' + search).then(response => {
        this.categories = response.data
      })
    },

    formattedDateTime(created_at) {
      const date = new Date(created_at);
      const utcOffset = -480;
      date.setMinutes(date.getMinutes() + utcOffset);

      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        hour12: false,
        timeZone: 'Asia/Manila',
      };

      const formattedDate = date.toLocaleString('en-US', options);

      return formattedDate;
    },
    async addCategory() {
      this.categoryForm.reset()
      $('#addCategoryModal').modal('show')
    },
    async createCategory() {
      await this.categoryForm
        .post("/api/category")
        .then(({ data }) => {
          this.categoryForm.name = '';

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

          this.loadCategories()
          $('#addCategoryModal').modal('hide');

        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.errorMessage = error.response.data.errors.name[0];
          } else {

            this.$swal.fire({
              position: 'top-middle',
              icon: 'error',
              title: "An error occurred. Please try again.",
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

    async editCategory(category) {
      this.categoryForm.name = category.name
      this.categoryForm.selectedId = category.id
      $('#editCategoryModal').modal('show')
    },
    async updateCategory() {
      let id = this.categoryForm.selectedId
      if (id !== null) {
        await this.categoryForm.put(`/api/category/${id}`)
          .then(() => {

            this.$swal.fire({
              position: 'top-middle',
              icon: 'success',
              title: "Category updated",
              showConfirmButton: false,
              timer: 3000,
              customClass: {
                popup: 'my-custom-alert',
                title: 'my-custom-alert-title',
                icon: 'my-custom-alert-icon',
              },
            });


            this.loadCategories()
            $('#editCategoryModal').modal('hide')

          })
          .catch((error) => {
            if (error.response && error.response.status === 422) {
              this.errorMessage = error.response.data.errors.name[0];
            } else {

              this.$swal.fire({
                position: 'top-middle',
                icon: 'error',
                title: "An error occurred. Please try again.",
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


    async deleteCategory(category) {
      this.categoryForm.selectedId = category.id
      $('#removeCategoryModal').modal('show')
    },
    async confirmDeleteCategory() {
      if (this.categoryForm.selectedId !== null) {
        await axios.delete(`/api/category/${this.categoryForm.selectedId}`).then(() => {

          this.$swal.fire({
            position: 'top-middle',
            icon: 'success',
            title: "Category deleted",
            showConfirmButton: false,
            timer: 3000,
            customClass: {
              popup: 'my-custom-alert',
              title: 'my-custom-alert-title',
              icon: 'my-custom-alert-icon',
            },
          });

        })
        this.loadCategories()
        $('#removeCategoryModal').modal('hide')
      }
    },

    search() {
      this.loadCategories(1, this.form.search);
    },

  },
  mounted() {
    this.loadCategories()
  },
};
</script>

<style>
.sr-only {
  display: none;
}
</style >
