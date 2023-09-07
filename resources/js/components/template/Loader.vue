<template>
    <!--====== PRELOADER PART START ======-->
    <div class="preloader" :class="{ 'show-loader': isLoading }">
      <div class="loader">
        <div class="spinner">
                <div class="spinner-container">
                    <div class="spinner-rotator">
                        <div class="spinner-left">
                            <div class="spinner-circle"></div>
                        </div>
                        <div class="spinner-right">
                            <div class="spinner-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
      </div>
    </div>
    <!--====== PRELOADER PART ENDS ======-->
  </template>
  
  <script>
  export default {
    computed: {
      isLoading() {
        return this.$store.state.isLoading;
      },
    },
  };
  </script>
  
  <style>
  /* Add this style to the component */
  .preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Add a semi-transparent background color for the overlay effect */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999; /* Make sure it is on top of other elements */
    display: none; /* Hide the preloader by default */
  }
  
  .show-loader {
    display: flex; /* Show the preloader when isLoading is true */
  }
  </style>
  