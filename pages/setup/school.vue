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
                header="School Setup"
                header-tag="header"
            >
            <b-card-body>
                <strong>What is your school called?</strong>
                <b-input-group>
                    <b-form-input id="school" placeholder="School name" type="text" v-model="school" />
                </b-input-group>
                <hr>
                <strong>Create a teacher account to administrate this school:</strong>
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
                <small>This account will be used to manage teachers, students and their classes. Don't worry, you'll be able to transfer ownership of the school later.</small>
                <div class="mt-3"><b-button variant="primary" :disabled="disableCreateBtn()" @click="create()">Create school</b-button></div>
                <div class="mt-2"><n-link to="/login">Already have an account?</n-link></div>
            </b-card-body>
            </b-card>
        </b-col>
        <b-col lg="3" />
    </b-row>
  </div>
</template>

<script>

export default {
    name: "SetupSchool",
    components: {
    },
    data() {
        return {
            school: null,
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
            await this.$axios.$put("https://mathsunlockedapi.thomas.gg/school", {name: this.school, administrator: {name: this.name, username: this.username, email: this.email, password: this.password}}, {
                headers: {}
            })
            .then((res) => {
                this.activeRequest = false;
                localStorage.setItem("authorization", res.token);
                this.$toastr(
                    "success",
                    "You will be redirected momentarily to the next step",
                    "Creation successful"
                );
                $nuxt.$router.push('/setup/teachers')
            })
            .catch((e) => {
                this.activeRequest = false;
            })
        },
        disableCreateBtn() {
            if(this.school == null || this.school.length < 3) return true;
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
