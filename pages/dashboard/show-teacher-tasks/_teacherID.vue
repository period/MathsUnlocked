<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Tasks assigned by teacher #{{ teacherID}}</h1>
            <b-card>
                <p v-if="this.tasks.length == 0">This teacher has assigned no tasks</p>
                <b-list-group v-else v-for="task in this.tasks" :key="task.id">
                    <teacher-task-item :task="task" />
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
    name: "ShowTeacherTasks",
    components: {
        TeacherTaskItem
    },
    data() {
        return {
            teacherID: parseInt(this.$route.params.teacherID),
            tasks: []
        }
    },
    mounted() {
        this.loadTasks();
    },
    methods: {
        async loadTasks() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/teachers/" + this.teacherID + "/tasks", {
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
