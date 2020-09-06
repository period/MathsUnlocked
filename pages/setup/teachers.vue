<template>
  <div class="container">
    <div class="row">
      <h1 class="title">
          Welcome to Maths Unlocked! Let's get your school setup.
      </h1>
    </div>
    <b-row>
        <b-col lg="3" />
        <b-col lg="6">
            <b-card
                header="Teacher Setup"
                header-tag="header"
            >
            <b-card-body>
                <strong>Enter the teacher's details below:</strong>
                <b-input-group class="mt-3">
                    <b-form-input id="name" placeholder="First & Last Name" type="text" v-model="name" />
                </b-input-group>
                <b-input-group class="mt-3">
                    <b-form-input id="username" placeholder="Username" type="text" v-model="username" />
                </b-input-group>
                <b-input-group class="mt-3">
                    <b-form-input id="email" placeholder="Email address" type="email" v-model="email" />
                </b-input-group>
                <b-input-group class="mt-3">
                    <b-form-input id="password" placeholder="Password" type="password" v-model="password" />
                </b-input-group>
                <small>Teachers have access to setting work for students as well as the capability of resetting a student's password. If you don't want to create any teachers right now, just click on 'Next Step'</small>
                <div class="mt-3">
                    <b-btn-group>
                        <b-button variant="primary" :disabled="disableCreateBtn()" @click="create()">Create teacher</b-button>
                        <b-button variant="secondary" @click="$nuxt.$router.push('/setup/students')">Next Step</b-button>
                    </b-btn-group>
                </div>
            </b-card-body>
            </b-card>
        </b-col>
        <b-col lg="3" />
    </b-row>
  </div>
</template>

<script>

export default {
    name: "SetupTeachers",
    components: {
    },
    data() {
        return {
            name: null,
            username: null,
            password: null,
            email: null,
            activeRequest: false
        }
    },
    methods: {
        async create() {
            this.activeRequest = true;
            await this.$axios.$put("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/teachers", {name: this.name, username: this.username, email: this.email, password: this.password}, {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.activeRequest = false;
                this.name = null;
                this.email = null;
                this.username = null;
                this.password = null;
                this.$toastr(
                    "success",
                    "Teacher account created successfully",
                    "Creation successful"
                );
            })
            .catch((e) => {
                this.activeRequest = false;
            })
        },
        disableCreateBtn() {
            if(this.name == null || this.name.length < 5 || this.name.trim().includes(" ") == false) return true;
            if(this.email == null || this.email.length < 5 || this.email.includes("@") == false || this.email.includes(".") == false) return true;
            if(this.username == null || this.username.length < 3) return true;
            if(this.password == null || this.password.length < 3) return true;
            if(this.activeRequest == true) return true;
            return false;
        }
    }
}
</script>
