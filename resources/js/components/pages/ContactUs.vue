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
                        <h1>Contact Us</h1>

                        <ul style="list-style-type:none;margin-left:-25px;">
                            <li class="mb-2"><b><span class="iconify fs-3" data-icon="carbon:map"></span> Address :</b> {{
                                this.contacts.address }}</li>
                            <li class="mb-2"><b><span class="iconify fs-3" data-icon="tabler:phone"></span> Phone :</b> {{
                                this.contacts.phone }}</li>
                            <li><b><span class="iconify fs-3" data-icon="basil:gmail-outline"></span> Gmail :</b> {{
                                this.contacts.gmail }}</li>
                        </ul>
                    </div>
                    <div class="col-12 col-md-6">
                        <!-- Responsive map container -->
                        <div class="map-container">
                            <div class="mapouter">
                                <div class="gmap_canvas">
                                    <!-- Use a method to dynamically generate the Google Maps URL -->
                                    <iframe class="gmap_iframe" frameborder="0" scrolling="no" marginheight="0"
                                        marginwidth="0" :src="getGoogleMapSrc()"></iframe>
                                </div>
                            </div>
                        </div>
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

        getGoogleMapSrc() {
            const address = this.contacts.address || ''; // Replace with the correct property name if needed
            return `https://maps.google.com/maps?width=600&height=400&hl=en&q=${encodeURIComponent(address)}&t=p&z=14&ie=UTF8&iwloc=B&output=embed`;
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

