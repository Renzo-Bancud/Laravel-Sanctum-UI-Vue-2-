export default {
    methods: {
        formatPrice(price) {
            const formattedPrice = parseFloat(price);
            if (isNaN(formattedPrice)) {
                return '';
            }
            return formattedPrice.toLocaleString('en-PH', {
                style: 'currency',
                currency: 'PHP',
            });
        },
    },
}