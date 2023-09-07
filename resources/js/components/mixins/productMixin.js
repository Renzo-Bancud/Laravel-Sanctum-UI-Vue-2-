export default {
    data() {
        return {
            product: [],
        };
    },
    methods: {
        async loadProductData() {
            let slug = this.$route.params.slug;
            let { data } = await axios.get("/api/product/item/" + slug);
            this.product = data;
            this.loadRelatedProduct(this.product.category_id, this.product.slug);
        },

        async loadRelatedProduct(category_id, slug) {
            try {
                let { data } = await axios.get(
                    "/api/product/get-related-product/" + category_id + "/" + slug
                );
                this.relatedProducts = data;
            } catch (error) {
                // Handle the error if the API call fails
                console.error(error);
            }
        },
        // Other shared methods
    },
};