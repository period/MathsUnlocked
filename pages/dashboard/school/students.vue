<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Students in school</h1>
            <b-card>
                <p v-if="this.students.length == 0">Your school has no students</p>
                <b-list-group v-else v-for="student in this.students" :key="student.id">
                    <teacher-student-item :student="student" />
                </b-list-group>
            </b-card>
        </b-col>
        <b-col lg="3">
            <n-link to="../teacher"><b-button squared block>Teacher Dashboard</b-button></n-link>
        </b-col>
    </b-row>
  </div>
</template>

<script>
import TeacherStudentItem from '~/components/TeacherStudentItem';

export default {
    name: "SchoolStudentsList",
    components: {
        TeacherStudentItem,
    },
    data() {
        return {
            students: [],
        }
    },
    mounted() {
        this.loadStudents();
    },
    methods: {
        async loadStudents() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/students", {
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
