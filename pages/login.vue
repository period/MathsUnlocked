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
                <div class="mt-3"><b-button variant="primary" v-b-modal="'qrlogin'">QR Code</b-button> <b-button variant="primary" :disabled="disableLoginBtn()" @click="login()">Login</b-button></div>
                <div class="mt-2"><n-link to="/password-reset">Forgot your password?</n-link></div>
            </b-card-body>
            </b-card>
        </b-col>
        <b-col lg="3" />
    </b-row>
    <b-modal title="QR Code Login" id="qrlogin">
        <qrcode-stream @decode="loginQR"></qrcode-stream>
    </b-modal>
  </div>
</template>

<script>
import { QrcodeStream } from 'vue-qrcode-reader';
export default {
    name: "Login",
    components: {
        QrcodeStream
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
        handleLoginResponse(res) {
            this.activeRequest = false;
            localStorage.setItem("authorization", res.token);
            localStorage.setItem("userid", JSON.parse(atob(res.token.split(".")[1])).user_id);
            localStorage.setItem("schoolID", res.school);
            this.$toastr(
                "success",
                "You will be redirected momentarily",
                "Login successful"
            );
            $nuxt.$router.push('/dashboard/' + JSON.parse(atob(res.token.split(".")[1])).scope)
            $nuxt.$emit("navbar_update");
        },
        async loginQR(jwt) {
            this.activeRequest = true;
            await this.$axios.$post("https://mathsunlockedapi.thomas.gg/auth/qr", {use_qr: true, qr: jwt}, {
                headers: {}
            })
            .then((res) => {
                this.handleLoginResponse(res);
            })
            .catch((e) => {
                this.activeRequest = false;
            })
        },
        async login() {
            this.activeRequest = true;
            await this.$axios.$post("https://mathsunlockedapi.thomas.gg/auth/" + this.username, {type: this.userType.toLowerCase(), password: this.password}, {
                headers: {}
            })
            .then((res) => {
                this.handleLoginResponse(res);
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
