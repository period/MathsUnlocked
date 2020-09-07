  
<template>
  <div>
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
    <b-alert show variant="warning" class="text-center" v-if="isVAS">
      <p><strong>You are currently viewing Maths Unlocked from {{ name }}'s perspective.</strong></p>
      <p>If you would like to go back to your teacher account, please <b-link @click="stopViewAsStudent()">click here</b-link></p>
    </b-alert>
  </div>
</template>
<script>
export default {
  name: "Navbar",
  data() {
    return {
      name: null,
      isVAS: localStorage.getItem('view_as_student_teacher_token') != null
    }
  },
  mounted() {
    if(localStorage.getItem("authorization") != null) this.name = JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).name;
    this.isVAS = localStorage.getItem('view_as_student_teacher_token') != null;
  },
  methods: {
    stopViewAsStudent() {
      localStorage.setItem("authorization", localStorage.getItem("view_as_student_teacher_token"));
      localStorage.removeItem("view_as_student_teacher_token");
      this.$forceUpdate();
      $nuxt.$router.push("/dashboard/teacher");
    },
    isLoggedIn() {
      let token = localStorage.getItem("authorization");
      if(token == null) return false;
      if(JSON.parse(atob(token.split(".")[1])).exp < (new Date().getTime()/1000)) return false;
      return true;
    },
    logout() {
      localStorage.removeItem("authorization");
      localStorage.removeItem("view_as_student_teacher_token");
      $nuxt.$router.push("/");
    }
  }
};
</script>