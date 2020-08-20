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
                header="Classes Setup"
                header-tag="header"
            >
            <b-card-body>
                <strong>Enter the class name:</strong>
                <b-input-group class="mt-3">
                    <b-form-input id="name" placeholder="Class name" type="text" v-model="name" />
                    <b-btn @click="add()">Add</b-btn>
                </b-input-group>
                <hr>
                <div v-if="this.classes.length > 0">
                    <div v-for="(className, index) in classes">
                        <setup-wizzard-class :className="className" :index="index" />
                    </div>
                    <hr>
                </div>
                <small>A class is a group of students and teachers which is useful for setting students work in bulk. You'll be able to allocate students and teachers to classes later. If you don't want to create any classes right now, just click on 'Next Step'</small>
                <div class="mt-3">
                    <b-btn-group>
                        <b-button variant="primary" :disabled="this.name.length > 1 && this.name.length < 32" @click="create()">Create classes</b-button>
                        <b-button variant="secondary" @click="$nuxt.$router.push('/setup/done')">Next Step</b-button>
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
import SetupWizzardClass from '../../components/SetupWizzardClass';
export default {
    name: "SetupClasses",
    components: {SetupWizzardClass},
    data() {
        return {
            name: "",
            classes: [],
            activeRequest: false
        }
    },
    mounted() {
        this.$nuxt.$on("setup-class-update", (event) => {
            this.classes[event.index] = event.name;
        })
        this.$nuxt.$on("setup-class-remove", (index) => {
            this.classes.splice(index, 1);
        })
    },
    methods: {
        add() {
            if(this.classes.includes(this.name)) return;
            this.classes.push(this.name);
            this.name = "";
        },
        async create() {
            this.activeRequest = true;
            this.classes = Array.from(new Set(this.classes)); // deduplicate array
            await this.$axios.$put("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/classes", this.classes, {
                headers: {}
            })
            .then((res) => {
                this.activeRequest = false;
                this.classes = [];
                this.$toastr(
                    "success",
                    "Classes created successfully",
                    "Creation successful"
                );
            })
            .catch((e) => {
                this.activeRequest = false;
            })
        }
    }
}
</script>
