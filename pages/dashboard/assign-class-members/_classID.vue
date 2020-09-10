<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Assigning members to class #{{ classID }} ({{ className }})</h1>
        </b-col>
        <b-col lg="3">
            <n-link to="../teacher"><b-button squared block>Dashboard</b-button></n-link>
        </b-col>
    </b-row>
    <b-row class="mt-2">
        <b-col lg="4">
            <b-card title="Teachers">
                <p v-if="this.teachers.length == 0">Your school has no teachers</p>
                <draggable v-model="teachers" group="members" @start="drag=true" @end="drag=false">
                    <div v-for="teacher in teachers" :key="teacher.id">{{teacher.name}}</div>
                </draggable>
            </b-card>
        </b-col>
        <b-col lg="4">
            <b-card title="Students">
                <p v-if="this.students.length == 0">Your school has no students</p>
                <draggable v-model="students" group="members" @start="drag=true" @end="drag=false">
                    <div v-for="student in students" :key="student.id">{{student.name}}</div>
                </draggable>
            </b-card>
        </b-col>
        <b-col lg="4">
            <b-card title="Class Members">
                <p v-if="this.classMembers.length == 0">This class has no members</p>
                <draggable v-model="classMembers" group="members" @start="drag=true" @end="drag=false">
                    <div v-for="member in classMembers" :key="member.id">{{member.name}}</div>
                </draggable>
            </b-card>
        </b-col>
    </b-row>

  </div>
</template>

<script>
import draggable from 'vuedraggable'
export default {
    name: "AssignClassMembers",
    components: {
        draggable
    },
    data() {
        return {
            classID: parseInt(this.$route.params.classID),
            className: "",
            students: [],
            teachers: [],
            classMembers: []
        }
    },
    mounted() {
        this.loadStudents();
        this.loadTeachers();
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
    }
}
</script>
