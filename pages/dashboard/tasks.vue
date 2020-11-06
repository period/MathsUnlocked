<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Task List</h1>
            <p v-if="this.tasks.length == 0">No tasks found</p>
            <div v-else>
                <task-list :tasks="tasks" />
            </div>
        </b-col>
        <b-col lg="3">
            <n-link to="./dashboard/student"><b-button squared block>Dashboard</b-button></n-link>
        </b-col>
    </b-row>
  </div>
</template>
<script>
import TaskList from "~/components/TaskList";
export default {
    name: "Tasks",
    components: {TaskList},
    data() {
        return {
            tasks: []
        }
    },
    async mounted() {
        await this.$axios.$get("https://mathsunlockedapi.thomas.gg/students/" + JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).user_id + "/tasks")
        .then((res) => {
            this.tasks = res;
        });
    }
}
</script>