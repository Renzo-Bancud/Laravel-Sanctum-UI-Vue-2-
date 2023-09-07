import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)


//ADMIN SIDE
import Dashboard from '../components/pages/dashboard/Index.vue'
import ProductList from '../components/pages/products/Index.vue'
import OrderList from '../components/pages/orders/Index.vue'
import ProductDeliveries from '../components/pages/product_deliveries/Index.vue'
import Inventory from '../components/pages/inventory/Index.vue'
import Customer from '../components/pages/customer/Index.vue'
import Rating from '../components/pages/ratings/Index.vue'
import Logs from '../components/pages/logs/Index.vue'
import Slogan from '../components/pages/settings/Slogan.vue'
import Settings from '../components/pages/settings/Settings.vue'
import CategoryList from '../components/pages/settings/Category.vue'
import Variations from '../components/pages/settings/Variations.vue'

// Authentication Component
import Login from '../components/pages/auth/Login.vue'
import Signup from '../components/pages/auth/Signup.vue'
import Verification from '../components/pages/auth/Verify.vue';
import ForgotPassword from '../components/pages/auth/ForgotPassword.vue';
import ResetPassword from '../components/pages/auth/ResetPassword.vue';

//CLIENT SIDE
import Home from '../components/pages/Home.vue'
import Sale from '../components/pages/SaleItems.vue'
import ItemCategory from '../components/pages/Categories.vue'
import MyCart from '../components/pages/MyCart.vue'
import CheckoutDetails from '../components/pages/Checkout.vue'
import ItemDetails from '../components/pages/ItemDetails.vue'
import Orders from '../components/pages/Orders.vue'
import OrderDetails from '../components/pages/OrderDetails.vue'
import Purchases from '../components/pages/Purchases.vue'
import AccountDetails from '../components/pages/Myaccount.vue'
import Contact from '../components/pages/ContactUs.vue'
import About from '../components/pages/AboutUs.vue'



const routes = new VueRouter({
    mode: 'history',
    linkExactActiveClass: 'active', //auto set active routes when this is added
    routes: [
        //CLIENT SIDE 
        {
            path: '/',
            component: Home,
            name: 'home',
        },
        {
            path: '/sale-items',
            component: Sale,
            name: 'sale',
        },
        {
            path: '/:slug/category',
            component: ItemCategory,
            name: 'item-category',
        },
        {
            path: '/product/item/:slug',
            component: ItemDetails,
            name: 'item-details',
        },
        {
            path: '/cart',
            component: MyCart,
            name: 'my-cart-list',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/checkout',
            component: CheckoutDetails,
            name: 'checkout-details',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/orders/:orderTrackId',
            component: Orders,
            name: 'orders',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/order-details/:orderId/:slug',
            component: OrderDetails,
            name: 'order-details',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/my-account',
            component: AccountDetails,
            name: 'account-details',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/purchases',
            component: Purchases,
            name: 'purchases',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/contact-us',
            component: Contact,
            name: 'contact',
            meta: {
                requiresVisitor: true,
            }
        },

        {
            path: '/about-us',
            component: About,
            name: 'about',
            meta: {
                requiresVisitor: true,
            }
        },


        //ADMIN SIDE
        {
            path: '/category',
            component: CategoryList,
            name: 'category-list',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/product',
            component: ProductList,
            name: 'product-list',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/orders',
            component: OrderList,
            name: 'client-orders',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/product-deliveries',
            component: ProductDeliveries,
            name: 'product-deliveries',
            meta: {
                requiresAuth: true,
            }
        },
        {
            path: '/inventory',
            component: Inventory,
            name: 'inventory-list',
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/customers',
            component: Customer,
            name: 'customer-list',
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/ratings',
            component: Rating,
            name: 'rating-list',
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/activitylogs',
            component: Logs,
            name: 'logs-list',
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/slogan',
            component: Slogan,
            name: 'slogan-setting',
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/settngs',
            component: Settings,
            name: 'settings',
            meta: {
                requiresAuth: true,
            }
        },


        {
            path: '/variations',
            component: Variations,
            name: 'variation-setting',
            meta: {
                requiresAuth: true,
            }
        },

        {
            path: '/auth/login',
            component: Login,
            name: 'login',
            meta: {
                requiresVisitor: true,
            }
        },
        {
            path: '/auth/signup',
            component: Signup,
            name: 'signup',
            meta: {
                requiresVisitor: true,
            }
        },

        {
            path: '/auth/verify',
            name: 'verify',
            component: Verification,
            meta: { requiresVisitor: true }
        },

        {
            path: '/auth/forgot-password',
            name: 'forgot',
            component: ForgotPassword,
            meta: { requiresVisitor: true }
        },

        {
            path: '/password/reset/:token',
            name: 'reset-password',
            component: ResetPassword,
            meta: { requiresVisitor: true }
        },

        {
            path: '/dashboard',
            component: Dashboard,
            name: 'dashboard',
            meta: {
                requiresAuth: true,
            }
        },

    ]
})


// Scroll to the top after each navigation
routes.afterEach(() => {
    window.scrollTo(0, 0);
});


export default routes; // need to export to be able to use outside