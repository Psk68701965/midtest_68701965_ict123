<template>
 <div>
  
<Navbar />

  <router-view/>

  <Footer />

  </div>


</template> 


<script>
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';

import "bootstrap/dist/css/bootstrap.min.css";
import * as bootstrap from "bootstrap"; 
window.bootstrap = bootstrap; 
export default {
name: "App",
 components: { Navbar, Footer}


};

</script>


