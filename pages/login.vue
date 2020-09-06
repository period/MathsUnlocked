<template>
  <div class="container">
    <div class="row">
      <h1 class="title">
          Welcome back!
      </h1>
    </div>
    <b-row>
        <b-col lg="3" />
        <b-col lg="6">
            <b-card
                header="Login"
                header-tag="header"
            >
            <b-card-body>
                <b-input-group>
                   <template v-slot:prepend>
                            <b-dropdown :text="userType" variant="secondary">
                                <b-dropdown-item @click="userType = 'Student'">Student</b-dropdown-item>
                                <b-dropdown-item @click="userType = 'Teacher'">Teacher</b-dropdown-item>
                            </b-dropdown>
                        </template>
                    <b-form-input id="username" placeholder="Username" type="text" v-model="username" />
                </b-input-group>
                <b-input-group class="mt-3">
                    <b-form-input id="password" placeholder="Password" type="password" v-model="password" />
                </b-input-group>
                <div class="mt-3"><b-button variant="primary" :disabled="disableLoginBtn()" @click="login()">Login</b-button></div>
                <div class="mt-2"><n-link to="/password-reset">Forgot your password?</n-link></div>
            </b-card-body>
            </b-card>
        </b-col>
        <b-col lg="3" />
    </b-row>
  </div>
</template>

<script>

export default {
    name: "Login",
    components: {
    },
    data() {
        return {
            userType: "Student/Teacher",
            username: null,
            password: null,
            activeRequest: false
        }
    },
    methods: {
        async login() {
            this.activeRequest = true;
            await this.$axios.$post("https://mathsunlockedapi.thomas.gg/auth/" + this.username, {type: this.userType.toLowerCase(), password: this.password}, {
                headers: {}
            })
            .then((res) => {
                this.activeRequest = false;
                    this.$toastr(
                    "success",
                    "You will be redirected momentarily",
                    "Login successful"
                    );
                    $nuxt.$router.push('/dashboard' + this.userType.toLowerCase())
            })
            .catch((e) => {
                this.activeRequest = false;
            })
        },
        disableLoginBtn() {
            if(this.userType == "Student/Teacher") return true;
            if(this.username == null || this.username.length < 3) return true;
            if(this.password == null || this.password.length < 3) return true;
            if(this.activeRequest == true) return true;
            return false;
        }
    }
}
</script>
