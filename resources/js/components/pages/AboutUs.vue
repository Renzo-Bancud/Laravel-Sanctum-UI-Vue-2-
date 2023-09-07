<template>
    <div>
        <Loader v-if="isLoading" />

        <!-- Navigation-->
        <Navbar />

        <div id="carousel" class="carousel slide hero-slides d-none" data-bs-ride="carousel">
            <ol class="carousel-indicators">
                <li v-for="(item, index) in slogan" :key="index" :class="{ 'active': selectedIndex === index }"
                    @click="setActiveIndex(index)" data-bs-target="#carousel" :data-bs-slide-to="index"></li>

            </ol>
            <div class="carousel-inner" role="listbox">
                <div v-for="(item, index) in slogan" :key="index"
                    :class="{ 'carousel-item': true, 'active': selectedIndex === index }"
                    :style="{ 'background-image': `url(${item.image})`, 'background-size': backgroundImageSize }">
                    <div class="container h-100  d-md-block">
                        <div class="row align-items-center h-100">
                            <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                                <div class="caption animated fadeIn">
                                    <h2 class="animated fadeInLeft text-white">{{ item.title }}</h2>
                                    <p class="animated fadeInRight">{{ item.description }}</p>
                                    <router-link :to="{ name: 'sale' }" class="animated fadeInUp btn delicious-btn">Visit on
                                        Sale Items</router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev"
                @click="selectedIndex = (selectedIndex - 1 + slogan.length) % slogan.length">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next"
                @click="selectedIndex = (selectedIndex + 1) % slogan.length">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>



        <section class="py-5">
            <div class="container px-4 px-lg-5">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <img class="logo-icon w-100" :src="require('../../assets/images/logo_admin.png')" alt="logo"
                            style="object-fit:contain" />
                    </div>

                    <div class="col-12 col-md-6">
                        <h1>About Us</h1>
                        <p>{{ this.contacts.about }}</p>
                    </div>
                  
                </div>
            </div>
        </section>


        <Footer />

    </div>
</template>
    
<script>
import { mapState, mapMutations, mapActions } from "vuex";
import Navbar from "../template/shop/Navbar.vue";
import Footer from "../template/shop/Footer.vue";

export default {
    components: {
        Navbar,
        Footer
    },
    data() {
        return {
            contacts: [],
            selectedIndex: 0,
        };
    },

    methods: {

        async loadContacts() {
            try {
                 this.$store.dispatch('showLoader');
                const response = await axios.get("/api/contact-us");
                this.contacts = response.data;
            } catch (error) {
                console.error(error);
            } finally {
                this.$store.dispatch('hideLoader');
            }
        },

        //Slogan Data Store
        ...mapMutations(["setActiveIndex"]),
        ...mapActions(["loadSloganData"]),



    },

    computed: {
        ...mapState(["slogan", "activeIndex"]),
        backgroundImageSize() {
            if (window.innerWidth < 768) {
                return '100% 650px';
            } else {
                return 'cover';
            }
        },
        isLoading() {
            return this.$store.state.isLoading;
        },
    },
    mounted() {
        this.loadContacts();
        this.loadSloganData();
    },

};
</script>


 