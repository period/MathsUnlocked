  
<template>
  <b-navbar toggleable="lg" type="dark" variant="info">
    <b-navbar-brand to="/">Maths Unlocked</b-navbar-brand>
    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav>
      </b-navbar-nav>
      <b-navbar-nav class="ml-auto">
        <b-button to="/login" v-if="!isLoggedIn()">Login</b-button>
        <b-button @click="logout()" v-else>Logout</b-button>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>
<script>
export default {
  name: "Navbar",
  methods: {
    isLoggedIn() {
      let token = localStorage.getItem("authorization");
      if(token == null) return false;
      if(JSON.parse(atob(token.split(".")[1])).exp < (new Date().getTime()/1000)) return false;
      return true;
    },
    logout() {
      localStorage.removeItem("authorization");
      $nuxt.$router.push("/");
    }
  }
};
</script>