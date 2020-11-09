<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Hello, {{ name }}</h1>
            <b-card title="Classes">
                <p v-if="this.classes.length == 0">Your school has no classes</p>
                <b-list-group v-else v-for="schoolclass in this.classes" :key="schoolclass.id">
                    <teacher-class-item :schoolclass="schoolclass" />
                </b-list-group>
                <b-button variant="primary" class="mt-2" v-b-modal="'createclass'">Create Class</b-button>
            </b-card>
            <b-card title="Teachers">
                <p v-if="this.teachers.length == 0">Your school has no teachers</p>
                <b-list-group v-else v-for="teacher in this.teachers" :key="teacher.id">
                    <teacher-teacher-item :teacher="teacher" />
                </b-list-group>
            </b-card>
        </b-col>
        <b-col lg="3">
            <n-link to="./edit-profile"><b-button squared block>Edit Profile</b-button></n-link>
            <b-button squared block class="mt-2"  v-b-modal="'editschool'">Edit School</b-button>
            <n-link to="./school/students"><b-button squared block class="mt-2">Student List</b-button></n-link>
            <n-link to="./teacher"><b-button squared block class="mt-2">Teacher Dashboard</b-button></n-link>
            <n-link to="../setup/teachers"><b-button squared block class="mt-2">Setup Wizard</b-button></n-link>
        </b-col>
    </b-row>
    <b-modal
      id="createclass"
      title="Create class"
      @ok="createClass()"
      ok-title="Create"
    >
        <b-form-input id="name" placeholder="Class name" type="text" v-model="newClassName" />
    </b-modal>
    <b-modal
      id="editschool"
      title="Edit school"
      @ok="editSchool()"
      ok-title="Save"
    >
        <b-form-input id="name" placeholder="School name" type="text" v-model="newSchoolName" />
    </b-modal>
  </div>
</template>

<script>
import TeacherClassItem from '../../components/TeacherClassItem';
import TeacherTeacherItem from '~/components/TeacherTeacherItem';
export default {
    name: "AdministratorDashboard",
    components: {
        TeacherClassItem,
        TeacherTeacherItem
    },
    data() {
        return {
            teachers: [],
            classes: [],
            name: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).name,
            newClassName: null,
            newSchoolName: null
        }
    },
    mounted() {
        this.loadClasses();
        this.loadTeachers();
    },
    methods: {
        async editSchool() {
            await this.$axios.$patch("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID"), {name: this.newSchoolName}, {
                headers: {"Authorization": localStorage.getItem("authorization")}
            }).then((res) => {
                this.$toastr(
                    "success",
                    "School modified successfully",
                    "Modification successful"
                );
            })
            .catch((e) => {
            })
        },
        async loadClasses() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/classes", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.classes = res;
            })
            .catch((e) => {
            })
        },
        async loadTeachers() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/teachers", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.teachers = res;
            })
            .catch((e) => {
            })
        },
        async createClass() {
            await this.$axios.$put("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/classes", [this.newClassName], {
                headers: {"Authorization": localStorage.getItem("authorization")}
            }).then((res) => {
                if(res.length == 1) {
                    this.classes.push({id: res[0].id, name: res[0].name});
                    this.$toastr(
                        "success",
                        "Classes created successfully",
                        "Creation successful"
                    );
                }
            })
            .catch((e) => {
            })
        }
    }
}
</script>
