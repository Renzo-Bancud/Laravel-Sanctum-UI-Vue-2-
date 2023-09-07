<template>
  <div class="app">
    <div class="app-wrapper">
      <div class="app-content pt-3 p-md-3 p-lg-4">
        <div class="container-xl">
          <div class="row g-3 mb-4 align-items-center justify-content-between">
            <div class="col-auto">
              <h1 class="app-page-title mb-0">Products</h1>
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

                  <div class="col-auto">
                    <a href="#" class="btn app-btn-secondary" @click.prevent="addProduct()">
                      Create Product
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
                      <th class="cell">Main Image</th>
                      <th class="cell">Title</th>
                      <th class="cell">Category</th>
                      <th class="cell">Price</th>
                      <th class="cell">Discount %</th>
                      <th class="cell">Sale Price</th>
                      <th class="cell">Fee</th>
                      <th class="cell">Description</th>
                      <th class="cell">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!products.data || Object.keys(products.data).length === 0">
                      <td colspan="9" align="middle">
                        There is no product found
                      </td>
                    </tr>
                    <tr v-else v-for="product in products.data" :key="product.id">

                      <td class="cell" data-label="Main Image">
                        <img v-if="product.main_picture" :src="product.main_picture" alt="Product Image"
                          style="object-fit:contain;height:80px;width:80px;" />
                        <img v-else :src="require('/storage/img-icon/no-image-icon.png')" alt="No Image" height="80"
                          width="80" />
                      </td>

                      <td class="cell" data-label="Title">
                        {{ product.title }}
                      </td>

                    
                      <td class="cell" data-label="Category">
                        <span v-if="product.category">
                          {{ product.category.name }}</span>
                        <span v-else> {{ product.category_id }} </span>
                      </td>
                      <td class="cell" data-label="Price">
                        {{ product.price }}
                      </td>

                      <td class="cell" data-label="Discount Percentage">
                        {{ product.discount_percentage == null ? '--' : product.discount_percentage + '%' }}
                      </td>

                      <td class="cell" data-label="Sale Price">
                        {{ salePrice(product) }}
                      </td>

                      <td class="cell" data-label="Fee">
                        {{ product.shipping_fee == null ? '--' : product.shipping_fee }}
                      </td>
                      <td class="cell" data-label="Description">
                        {{
                          product.description.length > 20
                          ? product.description.substring(0, 20) + "..."
                          : product.description
                        }}
                      </td>
                      <td class="cell" data-label="Action">
                        <a class="link-secondary mx-1" href="#" @click.prevent="viewProduct(product)"><span
                            class="iconify fs-4" data-icon="ph:eye"></span></a>
                        <a class="link-info" href="#" @click.prevent="editProduct(product)"><span class="iconify fs-4"
                            data-icon="vaadin:edit"></span></a>
                        <a class="link-danger" href="#" @click.prevent="deleteProduct(product)"><span class="iconify fs-4"
                            data-icon="bi:trash"></span></a>
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
                <pagination :data="products" @pagination-change-page="loadProducts"></pagination>
              </div>
              <div class="mb-2 mb-md-0">
                <span class="d-block text-center mb-1">Showing {{ products.from }} to {{ products.to }} of
                  {{ products.total }} entries</span>
              </div>
            </div>
          </nav>
          <!--//app-pagination-->
        </div>
        <!--//container-fluid-->
      </div>
      <!--//app-content-->

      <div class="modal fade" id="ProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header border-0">
              <h5 class="modal-title" id="staticBackdropLabel"></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" @click="ResetForm()"
                aria-label="Close"></button>
            </div>
            <div class="modal-body add-edit px-4">
              <form @submit.prevent="formAction()">

                <div class="row">
                  <div class="col-5 px-4">
                    <div class="form-group row mb-2">
                      <label for="product_title" class="col-form-label fw-bold">Title:</label>
                      <input type="text" id="product_title" name="product_title" v-model="productForm.title"
                        class="form-control"
                        :class="{ 'is-invalid': productForm.errors.has('title') || titleErrorMessage }"
                        placeholder="Enter product title" />
                      <div v-if="titleErrorMessage" class="invalid-feedback d-block">
                        {{ titleErrorMessage }}
                      </div>
                    </div>

                    <div class="form-group row mb-2">
                      <label for="product_category" class="col-form-label fw-bold">Category:</label>
                      <select name="category" id="product_category" class="form-control" v-model="productForm.category"
                        :class="{
                          'is-invalid': productForm.errors.has('category') || categoryErrorMessage
                        }">
                        <option style="display: none" value="" selected>
                          -- Choose Category --
                        </option>
                        <option :value="category.id" v-for="category in categories" :key="category.id">
                          {{ category.name }}
                        </option>
                      </select>
                      <div v-if="categoryErrorMessage" class="invalid-feedback d-block">
                        {{ categoryErrorMessage }}
                      </div>
                    </div>


                    <div class="form-group row mb-2">
                      <label for="product_price" class="col-form-label fw-bold">Price:</label>
                      <input type="number" id="product_price" name="product_price" v-model="productForm.price"
                        class="form-control"
                        :class="{ 'is-invalid': productForm.errors.has('price') || priceErrorMessage }"
                        placeholder="Enter product price" />
                      <div v-if="priceErrorMessage" class="invalid-feedback d-block">
                        {{ priceErrorMessage }}
                      </div>
                    </div>

                    <div class="form-group row mb-2">
                      <label for="product_price" class="col-form-label fw-bold">Discount %:</label>
                      <input type="number" id="discount_percentage" name="discount_percentage"
                        v-model="productForm.percentage" class="form-control" placeholder="Enter discount percentage" />
                      <small class="mt-2 text-muted"><i>Leave it empty if item is not on sale</i></small>
                    </div>

                    <div class="form-group row mb-2">
                      <label for="product_price" class="col-form-label fw-bold">Shipping Fee:</label>
                      <input type="number" id="product_price" name="product_price" v-model="productForm.fee"
                        class="form-control" :class="{ 'is-invalid': productForm.errors.has('fee') || feeErrorMessage }"
                        placeholder="Enter shipping fee" />
                      <div v-if="feeErrorMessage" class="invalid-feedback d-block">
                        {{ feeErrorMessage }}
                      </div>
                    </div>

                    <div class="form-group row">
                      <label for="product_description" class="col-form-label fw-bold">Description:</label>

                      <textarea rows="5" id="product_description" name="product_description"
                        v-model="productForm.description" class="form-control" :class="{
                          'is-invalid': productForm.errors.has('description') || descriptionErrorMessage
                        }" placeholder="Enter product description"></textarea>
                      <div v-if="descriptionErrorMessage" class="invalid-feedback d-block">
                        {{ descriptionErrorMessage }}
                      </div>
                    </div>


                  </div>

                  <div class="col-7 px-3">

                    <div class="form-group row">
                      <label for="product_category" class="col-form-label fw-bold">Multiple Upload:</label>


                      <label for="chooseFile" class="border rounded border-gray-200 py-2 px-3 cursor-pointer">Choose
                        multiple files...</label>
                      <input type="file" accept="image/*" multiple="multiple" @change="onImageChange" id="chooseFile">

                      <div class="image-preview border border-gray-200 mt-1 p-2 add-image">
                        <h5 class="text-muted">Preview here</h5>

                        <div v-if="previews.length">
                          <ul class="list-inline">
                            <li class="list-inline-item" v-for="(item, index) in previews" :key="index">
                              <div class="d-flex flex-column">
                                <img :src="item" style="object-fit: cover; width: 100px; height: 100px;">
                                <button class="btn btn-outline-danger btn-sm mt-1 hover-pulse"
                                  @click.prevent="removeImage(index)">
                                  <span class="iconify fs-4" data-icon="iconamoon:sign-times-thin"></span>&nbsp;Remove
                                </button>


                                <label class="mt-2">
                                  <input type="checkbox" v-model="productForm.mainPicture" :value="index"> Use as main
                                  picture
                                </label>

                              </div>
                            </li>
                          </ul>
                        </div>


                        <small class="text-muted mt-3 edit-message"><i>The image will be updated if a new image is
                            provided in
                            the input file.</i></small>

                        <div v-if="imageErrorMessage" class="invalid-feedback d-block">
                          {{ imageErrorMessage }}
                        </div>

                      </div>
                    </div>



                    <div class="form-group row mt-2">
                      <label for="variations" class="col-form-label fw-bold">Variations:</label>
                      <div class="row">
                        <template v-if="variations.length === 0">
                          <p class="text-muted">No variation found!</p>
                        </template>
                        <template v-else>
                          <div class="table-responsive d-md-none">
                            <table class="table table-bordered">
                              <tbody>
                                <tr v-for="variation in variations" :key="variation.id">
                                  <template>
                                    <td class="font-weight-bold" style="font-size: 12px;">{{ variation.variation_name }}
                                    </td>
                                    <td>
                                      <div class="d-flex flex-wrap">
                                        <div v-for="option in variation.options" :key="option.id"
                                          class="form-check col-md-3">
                                          <input type="checkbox" :id="`${variation.id}_${option.id}`" :value="option.id"
                                            :checked="isOptionSelected(variation.id, option.id)"
                                            @change="updateSelectedOptions(variation.id, option.id)"
                                            class="form-check-input" />
                                          <label :for="`${variation.id}_${option.id}`" class="form-check-label"
                                            style="font-size: 11px;">{{ option.value }}</label>
                                        </div>
                                      </div>
                                    </td>
                                  </template>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                          <div class="table-responsive d-none d-md-block p-0">
                            <table class="table table-bordered">
                              <tbody>
                                <tr v-for="variation in variations" :key="variation.id">
                                  <template>
                                    <td class="font-weight-bold" style="font-size: 10px;">{{ variation.variation_name }}</td>
                                    <td style="min-width: 365px; width: auto!important;">
                                      <div class="d-flex flex-wrap">
                                        <div v-for="option in variation.options" :key="option.id"
                                          class="form-check col-md-3">
                                          <input type="checkbox" :id="`${variation.id}_${option.id}`" :value="option.id"
                                            :checked="isOptionSelected(variation.id, option.id)"
                                            @change="updateSelectedOptions(variation.id, option.id)"
                                            class="form-check-input" />
                                          <label :for="`${variation.id}_${option.id}`" class="form-check-label" style="font-size: 11px;">{{
                                            option.value }}</label>
                                        </div>
                                      </div>
                                    </td>
                                  </template>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </template>
                      </div>
                    </div>

                  </div>


                </div>


                <div class="d-flex justify-content-end mt-2 px-1">
                  <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">
                    Close
                  </button>
                  <button type="submit" class="btn btn-primary modal-btn" :disabled="productForm.busy"></button>
                </div>
              </form>
            </div>
            <div class="modal-body view-details">
              <div class="container my-5">
                <div class="row">
                  <div class="col-md-5">
                    <div class="main-img">

                      <img class="img-fluid" :src="productDetails.mainPicture" alt="Product"
                        v-if="productDetails.mainPicture" />
                      <img class="img-fluid" v-else :src="require('/storage/img-icon/no-image-icon.png')"
                        alt="No Image" />

                      <div class="row my-3 previews">
                        <div class="col-md-3" v-for="(image, index) in productDetails.multiplePicture.split(',')"
                          :key="index">
                          <img class="w-100" :src="image" alt="Product Image">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-7">
                    <div class="main-description px-2">
                      <div class="category text-bold">
                        {{ productDetails.category }}
                      </div>
                      <div class="product-title text-bold my-3">
                        {{ productDetails.title }}
                      </div>


                      <div class="price-area my-4">
                        <p class="old-price mb-1" v-if="productDetails.onSale != null"><del><span
                              class="old-price-discount text-danger"><span class="iconify" data-icon="pepicons-pop:peso"
                                style="position:relative;top:-1px;"></span>{{
                                  productDetails.price }}</span></del> <span class="old-price-discount text-danger">({{
    productDetails.discount }}%
                            off)</span></p>

                        <p class="new-price text-bold mb-1" v-if="productDetails.onSale"><span class="iconify"
                            data-icon="pepicons-pop:peso" style="position:relative;top:-5px;"></span>{{
                              productDetails.salePrice }}</p>

                        <p class="new-price text-bold mb-1" v-else><span class="iconify" data-icon="pepicons-pop:peso"
                            style="position:relative;top:-5px;"></span>{{ productDetails.price }}</p>

                        <p class="old-price mb-1">Shipping Fee: <span class="old-price-discount text-danger"><span
                              class="iconify" data-icon="pepicons-pop:peso" style="position:relative;top:-1px;"></span>{{
                                productDetails.shippingFee }}</span></p>
                      </div>

                    </div>

                    <div class="product-details my-4">
                      <p class="details-title text-color mb-1">Product Details</p>
                      <p class="description">{{ productDetails.description }} </p>
                    </div>

                    <div class="row questions bg-light p-3 d-none">
                      <div class="col-md-1 icon">
                        <i class="fa-brands fa-rocketchat questions-icon"></i>
                      </div>
                      <div class="col-md-11 text">
                        Have a question about our products at E-Store? Feel free to contact our representatives via live
                        chat or email.
                      </div>
                    </div>


                  </div>
                </div>
              </div>


            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="removeProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                    <a @click.prevent="confirmDeleteProduct()" href="#"
                      class="btn btn-danger btn-sm col-4 text-center mt-2 text-white">Yes, delete it</a>
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
import { objectToFormData } from "object-to-formdata";
import salePriceMixin from '../../mixins/salePriceMixin';

export default {
  mixins: [salePriceMixin],
  components: {
    Footer,
    CustomForm,
    objectToFormData,
  },
  data() {
    return {
      products: {},
      paginate: 10,
      form: {
        search: "",
      },
      productForm: new CustomForm({
        title: "",
        category: "",
        images: [],
        mainPicture: [],
        selectedVariations: [],
        price: "",
        percentage: "",
        fee: "",
        description: "",
        _method: null,
      }),
      productDetails: {
        mainPicture: '',
        multiplePicture: '',
        title: '',
        category: '',
        price: '',
        discount: '',
        onSale: '',
        salePrice: '',
        shippingFee: '',
        description: '',
        id: '',
      },
      previews: [],
      categories: [],
      variations: [],
      productImage: "",
      titleErrorMessage: "",
      categoryErrorMessage: "",
      imageErrorMessage: "",
      priceErrorMessage: "",
      feeErrorMessage: "",
      descriptionErrorMessage: "",
      selectedId: null,
    };
  },

  watch: {
    paginate: function (value) {
      this.loadProducts();
    },
  },

  methods: {
    async loadProducts(page = 1, search = "") {
      await axios
        .get("/api/product?page=" + page + "&paginate=" + this.paginate + "&search=" + search)
        .then((response) => {
          this.products = response.data;
          //console.log(this.products)
        });
    },

    search() {
      this.loadProducts(1, this.form.search);
    },

    ResetForm() {
      this.productForm.reset();
    },

    async addProduct() {
      $(".modal-title").text("Add Product");
      $(".modal-btn").text("Save");
      $('.add-edit').show();
      $('.view-details').hide();
      this.productForm.reset();
      $('.edit-message').hide();
      $("#ProductModal").modal("show");
    },

    async createProduct() {
      const formData = new FormData();
      formData.append('title', this.productForm.title);
      formData.append('price', this.productForm.price);
      formData.append('discount', this.productForm.percentage);
      formData.append('fee', this.productForm.fee);
      formData.append('description', this.productForm.description);
      formData.append('category', this.productForm.category);
      formData.append('selectedVariations', JSON.stringify(this.productForm.selectedVariations));

      //console.log(JSON.stringify(this.productForm.selectedVariations)); 

      // Append each image and its corresponding index to FormData
      for (let i = 0; i < this.productForm.images.length; i++) {
        formData.append(`images[${i}]`, this.productForm.images[i]);
        if (this.productForm.mainPicture.includes(i)) {
          formData.append('main_picture_index', i);
        }
      }

      const config = {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (e) => {
          // Do whatever you want with the progress event
          //console.log(e);
        },
      };

      await axios
        .post('/api/product', formData, config)
        .then(({ data }) => {
          // Clear input after insert
          this.productForm.title = "";
          this.productForm.price = "";
          this.productForm.description = "";
          this.productForm.images = []; // Clear uploaded images
          this.productForm.mainPicture = ""; // Clear selected main picture

          // Reset image input manually
          const imgInput = document.getElementById('chooseFile');
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

          this.loadProducts();
          this.previews = [];
          $('#ProductModal').modal('hide');
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            this.titleErrorMessage = errors.title ? errors.title[0] : '';
            this.categoryErrorMessage = errors.category ? errors.category[0] : '';
            this.imageErrorMessage = errors['main_picture_index'] ? errors['main_picture_index'][0] : "";
            this.priceErrorMessage = errors.price ? errors.price[0] : '';
            this.feeErrorMessage = errors.fee ? errors.fee[0] : '';
            this.descriptionErrorMessage = errors.description ? errors.description[0] : '';
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
        });
    },

    async viewProduct(product) {
      $(".modal-title").text("Product Overview");
      $(".modal-btn").hide();
      $('.edit-message').hide();
      $('.add-edit').hide();
      $('.view-details').show();

      this.productDetails = {
        mainPicture: product.main_picture,
        multiplePicture: product.image, //this is comma delimited how can i display the image 
        title: product.title,
        category: product.category.name,
        price: product.price,
        discount: product.discount_percentage,
        onSale: product.isOnSale,
        salePrice: this.salePrice(product),
        shippingFee: product.shipping_fee,
        description: product.description,
        id: product.id,
      };

      $("#ProductModal").modal("show");
    },



    async editProduct(product) {
      $(".modal-title").text("Edit Product");
      $(".modal-btn").text("Update");
      $(".modal-btn").show();
      $('.add-edit').show();
      $('.view-details').hide();
      this.productForm.title = product.title;
      this.productForm.category = product.category_id;
      this.productForm.price = product.price;
      this.productForm.percentage = product.discount_percentage;
      this.productForm.fee = product.shipping_fee;
      this.productForm.description = product.description;
      this.productForm.selectedId = product.id;

      // Clear uploaded images
      this.productForm.images = [];

      // Clear existing selectedVariations
      this.productForm.selectedVariations = [];

      // Check if the product has variations before populating the variation options
      if (Array.isArray(product.variations)) {
        // Get the selected variation_option_ids from the product_variations table
        product.variations.forEach((variation) => {
          if (variation.pivot) {
            const optionIds = variation.pivot.variation_option_id
              .split(',') // Convert the comma-separated string to an array of option_ids
              .map(optionId => parseInt(optionId)); // Convert the option_ids to integers

            this.productForm.selectedVariations.push({
              variation_id: variation.id,
              option_ids: optionIds
            });
          }
        });
      }

      $('.edit-message').show();
      $("#ProductModal").modal("show");
    },

    async updateProduct() {
      if (this.productForm.selectedId !== null) {
        let id = this.productForm.selectedId;

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


        await this.productForm
          .post(`/api/product/${id}`, {}, config)
          .then((response) => {

            // Update the productForm's selectedVariations with the new data
            this.productForm.selectedVariations = response.data.variations;


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

            this.loadProducts();
            this.previews = [];
            $("#ProductModal").modal("hide");

          })
          .catch((error) => {
            if (error.response && error.response.status === 422) {
              const errors = error.response.data.errors;
              this.titleErrorMessage = errors.title ? errors.title[0] : "";
              this.categoryErrorMessage = errors.category_id ? errors.category_id[0] : "";
              this.imageErrorMessage = errors['images.0'] ? errors['images.0'][0] : "";
              this.priceErrorMessage = errors.price ? errors.price[0] : "";
              this.feeErrorMessage = errors.fee ? errors.fee[0] : "";
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
          });
      }
    },

    formAction() {
      if (this.productForm.selectedId) {
        this.productForm._method = "put";
        this.updateProduct();
      } else {
        this.productForm._method = "post";
        this.createProduct();
      }
    },

    async deleteProduct(product) {
      this.productForm.selectedId = product.id;
      $("#removeProductModal").modal("show");
    },
    async confirmDeleteProduct() {
      if (this.productForm.selectedId !== null) {
        await axios
          .delete(`/api/product/${this.productForm.selectedId}`)
          .then(() => {

            this.$swal.fire({
              position: 'top-middle',
              icon: 'success',
              title: 'Product deleted',
              showConfirmButton: false,
              timer: 3000,
              customClass: {
                popup: 'my-custom-alert',
                title: 'my-custom-alert-title',
                icon: 'my-custom-alert-icon',
              },
            });

          });
        this.loadProducts();
        $("#removeProductModal").modal("hide");
      }
    },


    loadCategories() {
      axios.get("/api/category-selection").then((response) => {
        this.categories = response.data;
      });
    },

    async loadVariations() {
      await axios.get("/api/variation-selection").then((response) => {
        this.variations = response.data;
      });
    },


    onImageChange(event) {
      let input = event.target;
      let count = input.files.length;
      let index = 0;

      if (input.files) {
        while (count--) {
          let reader = new FileReader();

          reader.onload = (e) => {
            this.previews.push(e.target.result);
          };

          this.productForm.images.push(input.files[index]);
          reader.readAsDataURL(input.files[index]);
          index++;
        }
      }
    },

    removeImage(index) {
      if (this.productForm.images[index] && this.productForm.images[index].length > 0) {
        this.productForm.images.splice(index, 1);
      }

      if (this.previews[index] && this.previews[index].length > 0) {
        this.previews.splice(index, 1);
      }
    },

    isOptionSelected(variationId, optionId) {
      const selectedVariation = this.productForm.selectedVariations.find(
        (variation) => variation.variation_id === variationId
      );
      return selectedVariation ? selectedVariation.option_ids.includes(optionId) : false;
    },

    updateSelectedOptions(variationId, optionId) {

      const existingVariationIndex = this.productForm.selectedVariations.findIndex(
        (variation) => variation.variation_id === variationId
      );

      if (existingVariationIndex !== -1) {
        const selectedOptionIds = this.productForm.selectedVariations[existingVariationIndex].option_ids;
        const optionIndex = selectedOptionIds.indexOf(optionId);

        if (optionIndex !== -1) {
          selectedOptionIds.splice(optionIndex, 1); // Uncheck the option if it was checked
        } else {
          selectedOptionIds.push(optionId); // Check the option if it was unchecked
        }
      } else {
        // If the variation doesn't exist in selectedVariations array, add it with the selected option
        this.productForm.selectedVariations.push({
          variation_id: variationId,
          option_ids: [optionId],
        });
      }
    }

  },

  mounted() {
    this.loadProducts();
    this.loadCategories();
    this.loadVariations();
  },
};
</script>

<style scoped>
.image-preview {
  width: 100%;
}

input[type="file"] {
  display: none;
}</style>

