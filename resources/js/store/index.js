import Vue from 'vue'
import Vuex from 'vuex' //npm install vuex@3.6.2 for Vue version 2
import router from '../router/index'

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        message: 'Welcome',
        user: {},
        authenticated: false,
        //LOAD MYCART STATE
        carts: [],
        variationOptions: [],
        myItems: [], // total count cart
        cartItemCount: 0,
        remainingItems: 0,
        //END

        //SELECTED SLUG
        selectedSlug: null,
        selectedProductId: null,
        //END

        //SLOGAN
        slogan: [],
        activeIndex: 0, // set active carousel
        //END
        isLoading: false,

        selectedCategoryData: null,
        selectedCategorySlug: null,
    },
    getters: {
        getMessage(state) {
            return state.message
        },
        getUser(state) {
            return state.user
        },
        getAuthenticated(state) {
            return state.authenticated
        }
    },
    mutations: {
        SET_USER(state, data) {
            state.user = data;
        },
        SET_MESSAGE(state, message) {  // add mutation to set message
            state.message = message;
        },
        SET_AUTHENTICATED(state, data) {
            state.authenticated = data;
        },
        //SELECTED SLUG
        setSelectedSlug(state, slug) {
            state.selectedSlug = slug;
        },
        setSelectedProductId(state, productId) {
            state.selectedProductId = productId;
        },
        //END

        //LOAD MYCART MUTATION
        SET_CARTS(state, carts) { // Add mutation to update carts
            state.carts = carts;
        },
        SET_VARIATION_OPTION(state, variationOptions) {
            state.variationOptions = variationOptions;
        },
        SET_ITEMS(state, items) {
            state.myItems = items;
            state.cartItemCount = items.reduce((total, item) => total + item.qty_cart, 0);
        },
        SET_REMAINING_ITEMS(state, remainingItems) { // Add mutation to update remainingItems
            state.remainingItems = remainingItems;
        },
        //END

        //SLOGAN
        setSlogan(state, sloganData) {
            state.slogan = sloganData;
        },
        setActiveIndex(state, index) {
            state.activeIndex = index;
        },
        //END

        SET_LOADING(state, isLoading) {
            state.isLoading = isLoading;
        },

        setSelectedCategoryData(state, data) {
            state.selectedCategoryData = data;
        },
        setSelectedCategorySlug(state, slug) {
            state.selectedCategorySlug = slug;
        },

    },
    actions: {
        authUser({ commit, dispatch }) {
            return axios.get('/api/user')
                .then((response) => {

                    commit('SET_AUTHENTICATED', true)
                    commit('SET_USER', response.data)
                    localStorage.setItem('auth', true)


                    if (router.currentRoute.name !== null) {
                        return router.push({ name: 'dashboard' })
                    };

                })
                .catch(() => {
                    commit('SET_AUTHENTICATED', false);
                    commit('SET_USER', null);
                    localStorage.removeItem('auth');
                    if (router.currentRoute.name !== 'login') {
                        return router.push({ name: 'login' })
                    };
                });
        },

        async loadMyCart({ commit }, params) {
            try {
                let { data } = await axios.get("/api/load-my-cart", {
                    params: params,
                });

                commit('SET_ITEMS', data.MyCartitems);
                commit('SET_VARIATION_OPTION', data.getVariationOptions);
                commit('SET_REMAINING_ITEMS', data.remainingItems); // Update remainingItems in the state
                commit('SET_CARTS', data.your_cart); // Update carts in the state
            } catch (error) {
                if (error.response && error.response.status === 401) {
                    // console.log("Unauthorized access");
                }
            }
        },

        removeCartItem({ commit }, item) {
            try {
                // Perform the server-side deletion
                return axios.post(`/api/remove-order-item/${item.item_id}`);
            } catch (error) {
                console.error(error);
            }
        },

        removePendingOrderItem({ commit }, item) {
            try {
                // Perform the server-side deletion
                return axios.post(`/api/remove-pending-order-item/${item.order_id}`);
            } catch (error) {
                console.error(error);
            }
        },

        async loadSelectedCategory({ commit }, slug) {
            try {
                commit('setSelectedCategorySlug', slug); // Store the selected category slug
                // const response = await axios.get(`/api/load-category-products/${slug}`);
                // commit('setSelectedCategoryData', response.data);
            } catch (error) {
                console.error(error);
            }
        },


        setSelectedSlug({ commit }, slug) {
            commit('setSelectedSlug', slug);
        },

        async loadSloganData({ commit }) {
            try {
                let { data } = await axios.get("/api/load-slogan");
                commit("setSlogan", data); // Call the mutation to update the slogan array in the store
            } catch (error) {
                console.log(error);
            }
        },

        showLoader({ commit }) {
            commit('SET_LOADING', true);
        },
        hideLoader({ commit }) {
            // Introduce a 500ms delay before hiding the loader
            setTimeout(() => {
                commit('SET_LOADING', false);
            }, 900);
        },

    }
});


export default store
