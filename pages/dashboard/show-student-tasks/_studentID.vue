<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Tasks assigned to student #{{ studentID}}</h1>
            <b-card>
                <p v-if="this.tasks.length == 0">This student has no tasks</p>
                <b-list-group v-else v-for="task in this.tasks" :key="task.id">
                    <teacher-task-item :task="task" :show_student_names="false" />
                </b-list-group>
            </b-card>
        </b-col>
        <b-col lg="3">
            <n-link to="../teacher"><b-button squared block>Dashboard</b-button></n-link>
        </b-col>
    </b-row>
  </div>
</template>

<script>
import TeacherTaskItem from "~/components/TeacherTaskItem.vue";
export default {
    name: "ShowStudentTasks",
    components: {
        TeacherTaskItem
    },
    data() {
        return {
            studentID: parseInt(this.$route.params.studentID),
            tasks: []
        }
    },
    mounted() {
        this.loadTasks();
    },
    methods: {
        async loadTasks() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/students/" + this.studentID + "/tasks", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.tasks = res;

            })
            .catch((e) => {
            })
        }
    }
}
</script>
