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
                <n-link :to="getStudentsURL()"><small>Looking for all students in the school?</small></n-link>
            </b-card>
            <b-card title="Your Classes">
                <p v-if="this.classes.length == 0">You are not assigned to any classes</p>
                <div v-else></div>
                <n-link :to="getClassesURL()"><small>Looking for all classes in the school?</small></n-link>
            </b-card>
        </b-col>
        <b-col lg="3">
            <n-link to="./edit-profile"><b-button squared block>Edit Profile</b-button></n-link>
        </b-col>
    </b-row>
  </div>
</template>

<script>
import TeacherStudentItem from '../../components/TeacherStudentItem';
export default {
    name: "TeacherDashboard",
    components: {
        TeacherStudentItem
    },
    data() {
        return {
            students: [],
            classes: [],
            name: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).name,
        }
    },
    mounted() {
        this.loadStudents();
    },
    methods: {
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
        getStudentsURL() {
            return "/school/" + localStorage.getItem("schoolID") + "/students";
        },
        getClassesURL() {
            return "/school/" + localStorage.getItem("schoolID") + "/classes";
        },
    }
}
</script>
