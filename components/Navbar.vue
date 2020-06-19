  
<template>
  <b-navbar toggleable="lg" type="dark" variant="info">
    <b-navbar-brand to="/">Maths Unlocked</b-navbar-brand>
    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav>
      </b-navbar-nav>
      <b-navbar-nav class="ml-auto">
        <b-button to="/login">Login</b-button>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>
<script>
export default {
  name: "Navbar"
};
</script>