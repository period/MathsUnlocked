<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Hello, {{ name }}</h1>
            <b-card title="Your Students">
                <p v-if="this.students.length == 0">You have no students in your classes</p>
                <b-list-group v-else v-for="student in this.students" :key="student.id">
                    <teacher-student-item :student="student" />
                </b-list-group>
                <n-link to="school/students"><small>Looking for all students in the school?</small></n-link>
            </b-card>
            <br>
            <b-card title="Your Classes">
                <p v-if="this.classes.length == 0">You are not assigned to any classes</p>
                <div v-else>
                    <b-list-group v-for="schoolClass in this.classes" :key="schoolClass.id">
                        <teacher-class-item :schoolclass="schoolClass" />
                    </b-list-group>
                </div>
                <n-link to="school/classes"><small>Looking for all classes in the school?</small></n-link>
            </b-card>
        </b-col>
        <b-col lg="3">
            <n-link to="./edit-profile"><b-button squared block>Edit Profile</b-button></n-link>
            <n-link v-if="isAdministrator" to="./administrator"><b-button class="mt-2" squared block>Administrator Dashboard</b-button></n-link>
        </b-col>
    </b-row>
  </div>
</template>

<script>
import TeacherStudentItem from '../../components/TeacherStudentItem';
import TeacherClassItem from '../../components/TeacherClassItem';

export default {
    name: "TeacherDashboard",
    components: {
        TeacherStudentItem,
        TeacherClassItem
    },
    data() {
        return {
            students: [],
            classes: [],
            name: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).name,
            isAdministrator: false
        }
    },
    mounted() {
        this.checkIfAdministrator();
        this.loadStudents();
        this.loadClasses();
    },
    methods: {
        async checkIfAdministrator() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID"), {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                if(res.owner == JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).user_id) this.isAdministrator = true;
            })
            .catch((e) => {
            })
        },
        async loadClasses() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/teachers/" + localStorage.getItem("userid") + "/classes", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.classes = res;
            })
            .catch((e) => {
            })
        },
        async loadStudents() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/teachers/" + localStorage.getItem("userid") + "/students", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.students = res;
            })
            .catch((e) => {
            })
        },
    }
}
</script>
