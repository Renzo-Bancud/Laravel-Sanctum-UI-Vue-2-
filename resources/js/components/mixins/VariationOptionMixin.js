export default {
    methods: {
        getVariationOptionsByItem(item) {
            const variationOptionIds = item.variation_option_id.split(',').map(Number);
            return this.$store.state.variationOptions.filter((option) =>
                variationOptionIds.includes(option.id)
            );
        },
    },
}