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
                <draggable v-model="teachers" group="members" @start="drag=true" @end="drag=false" :move="handleMove">
                    <div v-for="teacher in teachers" :key="teacher.id">{{teacher.name}}</div>
                </draggable>
            </b-card>
        </b-col>
        <b-col lg="4">
            <b-card title="Students">
                <p v-if="this.students.length == 0">Your school has no students</p>
                <draggable v-model="students" group="members" @start="drag=true" @end="drag=false" :move="handleMove">
                    <div v-for="student in students" :key="student.id">{{student.name}}</div>
                </draggable>
            </b-card>
        </b-col>
        <b-col lg="4">
            <b-card title="Class Members">
                <p v-if="this.classMembers.length == 0">This class has no members</p>
                <draggable v-model="classMembers" group="members" @start="drag=true" @end="drag=false" :move="handleMove">
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
            classMembers: [],
            classLoading: false
        }
    },
    mounted() {
        this.loadStudents();
        this.loadTeachers();
    },
    methods: {
        handleMove(evt) {
            console.log(evt);
        }
        async loadClass() {
            if(this.classLoading == true) return;
            this.classLoading = true;

            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/class/" + this.classID, {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                let studentIDs = [];
                let teacherIDs = [];
                for(var i = 0; i < res.students.length; i++) {
                    res.students[i].type == "student";
                    this.classMembers.push(res.students[i]);
                    this.studentIDs.push(res.students[i].id);
                }
                for(var i = 0; i < res.teachers.length; i++) {
                    res.teachers[i].type == "teacher";
                    this.classMembers.push(res.teachers[i]);
                    this.teacherIDs.push(res.teachers[i].id);
                }

                this.students = this.students.filter((student) => {
                    return this.studentIDs.includes(student.id) == false;
                })
                this.teachers = this.teachers.filter((teacher) => {
                    return this.teacherIDs.includes(teacher.id) == false;
                })
            })
            .catch((e) => {
            })
        }
        async loadStudents() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/students", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                for(var i = 0; i < res.length; i++) {
                    res[i].type == "student";
                    this.students.push(res[i]);
                }
                this.loadClass();
            })
            .catch((e) => {
            })
        },
        async loadTeachers() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/teachers", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                for(var i = 0; i < res.length; i++) {
                    res[i].type == "teacher";
                    this.teachers.push(res[i]);
                }
                this.loadClass();
            })
            .catch((e) => {
            })
        },
    }
}
</script>
