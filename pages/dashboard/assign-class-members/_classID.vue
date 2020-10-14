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
                <draggable v-model="teachers" group="members" @start="drag=true" @end="drag=false" :move="handleMove" id="draggable_teachers">
                    <div v-for="teacher in teachers" :key="teacher.id"  :id="'assignclassmembers_teacher_' + teacher.id">{{teacher.name}}</div>
                </draggable>
            </b-card>
        </b-col>
        <b-col lg="4">
            <b-card title="Students">
                <p v-if="this.students.length == 0">Your school has no students</p>
                <draggable v-model="students" group="members" @start="drag=true" @end="drag=false" :move="handleMove" id="draggable_students">
                    <div v-for="student in students" :id="'assignclassmembers_student_' + student.id" :key="student.id">{{student.name}}</div>
                </draggable>
            </b-card>
        </b-col>
        <b-col lg="4">
            <b-card title="Class Members">
                <p v-if="this.classMembers.length == 0">This class has no members</p>
                <draggable v-model="classMembers" group="members" @start="drag=true" @end="drag=false" :move="handleMove" id="draggable_class">
                    <div v-for="member in classMembers" :key="member.id" :id="'assignclassmembers_' + member.type + '_' + member.id">{{member.name}}</div>
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
            if(evt.dragged.id.includes("_student_") && evt.to.id == "draggable_teachers") return false;
            if(evt.dragged.id.includes("_teacher_") && evt.to.id == "draggable_students") return false;

            let type = evt.dragged.id.split("_")[1];
            let uid = evt.dragged.id.split("_")[2];
            let isAdd = (evt.to.id == "draggable_members");

            this.modifyClass(type, isAdd, uid);
        },
        async modifyClass(type, isAdd, uid) {
            let data = {id: uid};
            if(isAdd) {
                await this.$axios.$put("https://mathsunlockedapi.thomas.gg/class/" + this.classID + "/" + type, data, {
                    headers: {"Authorization": localStorage.getItem("authorization")}
                })
                .then((res) => {
                });
            }
            else {
                await this.$axios.$delete("https://mathsunlockedapi.thomas.gg/class/" + this.classID + "/" + type, data, {
                    headers: {"Authorization": localStorage.getItem("authorization")}
                })
                .then((res) => {
                });
            }
        },
        async loadClass() {
            if(this.classLoading == true) return;
            this.classLoading = true;

            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/class/" + this.classID, {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.className = res.name;
                let studentIDs = [];
                let teacherIDs = [];
                for(var i = 0; i < res.students.length; i++) {
                    res.students[i].type = "student";
                    this.classMembers.push(res.students[i]);
                    studentIDs.push(res.students[i].id);
                }
                for(var i = 0; i < res.teachers.length; i++) {
                    res.teachers[i].type = "teacher";
                    this.classMembers.push(res.teachers[i]);
                    teacherIDs.push(res.teachers[i].id);
                }
                
                this.students = this.students.filter((student) => {
                    return studentIDs.includes(student.id) == false;
                })
                this.teachers = this.teachers.filter((teacher) => {
                    return teacherIDs.includes(teacher.id) == false;
                })
            })
            .catch((e) => {
            })
        },
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
