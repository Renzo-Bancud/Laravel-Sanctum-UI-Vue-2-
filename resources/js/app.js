import Vue from 'vue'
import routes from './router/index'
import store from './store/index'
import VueSweetalert2 from 'vue-sweetalert2';
import Pagination from 'laravel-vue-pagination';
import Loader from './components/template/Loader.vue';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import './assets/css/app.css';
import './assets/css/style.css';
require('./bootstrap');


// Fetch the CSRF token from the meta tag and set it as a default header for Axios
// const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
// axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

// Register to access globally
Vue.use(VueSweetalert2);
Vue.component('admin-header', require('./components/template/Header.vue').default);
Vue.component('pagination', Pagination)
Vue.component('Loader', Loader);

// Vue Router Nagivation Guard
function isLoggedIn() {
    return store.getters.getAuthenticated;
}

routes.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (!isLoggedIn()) {
            if (to.name !== 'login') { // check if user is not already on the login page
                this.$store.commit('SET_USER', null);
                next({
                    name: 'login',
                });
            } else {
                next(); // allow user to proceed to login page
            }
        } else {
            next();
        }
    } else if (to.matched.some(record => record.meta.requiresVisitor)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (isLoggedIn()) {
            next(); // Allow authenticated users to proceed
        } else {
            next();
        }
    } else {
        next(); // make sure to always call next()!
    }
});



//check authentication
let auth = localStorage.getItem("auth")

if (auth) {
    store.dispatch('authUser').then(() => {
        const app = new Vue({
            el: '#app',
            router: routes, // includes routers here so that it will read the router folder that has index.js file
            store
        });
    })
} else {
    const app = new Vue({
        el: '#app',
        router: routes,
        store
    });
}


