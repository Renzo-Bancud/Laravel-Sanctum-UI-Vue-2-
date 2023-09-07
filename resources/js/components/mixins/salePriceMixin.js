export default {
    methods: {
        salePrice(product) {
            if (product.discount_percentage) {
                const discount = product.price * (product.discount_percentage / 100);
                return (product.price - discount).toFixed(2);
            } else {
                return '--';
            }
        },
    },
}