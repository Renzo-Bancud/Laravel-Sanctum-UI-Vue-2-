// mixins/cartCalculations.js  mixins you can reuse them in multiple components. 
export default {
  methods: {
    calculateSubtotal() {
      let subtotal = 0;
      for (let item of this.Items) {
        if (item.selected) {
          if (item.isOnSale === null) {
            subtotal += item.price * this.getCartItemQuantity(item);
          } else {
            subtotal += this.salePrice(item) * this.getCartItemQuantity(item);
          }
        }
      }

      return subtotal;

    },

    calculateShippingFee() {
      let shippingFee = 0;
      for (let item of this.Items) {
        if (item.selected) {
          shippingFee += item.shipping_fee;
        }
      }
      return shippingFee;
    },
    
    calculateTotal() {
      let subtotal = 0;
      let shippingFee = 0;
      for (let item of this.Items) {
        if (item.selected) {
          if (item.isOnSale === null) {
            subtotal += item.price * item.qty_cart;
          } else {
            subtotal += this.salePrice(item) * item.qty_cart;
          }
          shippingFee += item.shipping_fee;
        }
      }
      
      return subtotal + shippingFee;
    }


  }
}
