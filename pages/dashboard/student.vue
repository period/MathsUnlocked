<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="3">
            <b-card class="text-center">
                <b-card-title>
                    Daily Points <small v-b-tooltip.hover title="Points are earned whenever you answer a question correctly in any activity or during a game of Live Maths"><fa :icon="['fas', 'question-circle']" /></small>
                </b-card-title>
                <b-card-text>{{ this.points[0] }}</b-card-text>
            </b-card>
            <b-card title="Weekly Points" class="text-center mt-2">
                <b-card-text>{{ this.points.reduce((a, b) => a + b, 0) }}</b-card-text>
                <bar-chart v-if="chartData != null" :data="chartData" />
            </b-card>
        </b-col>
        <b-col lg="6">
            <h1 class="text-center">Hello, {{ name }}</h1>
            <b-card title="Assigned Activities">
                <p v-if="this.assigned_activities.length == 0">You have no assigned activities due 🙌🏻</p>
                <div v-else>
                    <task-list :tasks="assigned_activities" />
                </div>
            </b-card>
            <br>
            <b-card title="Activities">
                <div v-if="!disableLiveMaths()">
                    <available-activity-list v-on:activity="createTaskFromActivity" />
                </div>
                <div v-else>
                    <p class="text-danger">You cannot start new activities until you complete the tasks assigned by your teacher.</p>
                </div>
            </b-card>
        </b-col>
        <b-col lg="3">
            <n-link to="./tasks"><b-button squared block>Task List</b-button></n-link>
            <n-link to="./edit-profile"><b-button squared block>Edit Profile</b-button></n-link>
        </b-col>
    </b-row>
  </div>
</template>

<script>
import AvailableActivityList from '~/components/AvailableActivityList';
import TaskList from '~/components/TaskList';
import BarChart from '~/components/charts/BarChart';

export default {
    name: "StudentDashboard",
    components: {
        AvailableActivityList,
        TaskList,
        BarChart
    },
    data() {
        return {
            name: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).name,
            assigned_activities: [],
            activities: [],
            points: [0,0,0,0,0,0,0],
        }
    },
    methods: {
        async loadAssignedActivities() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/students/" + JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).user_id + "/tasks")
            .then((res) => {
                this.assigned_activities = res.filter((task) => { return task.teacher != null && task.completed == null })
            });
        },
        async createTaskFromActivity(activityID) {
            await this.$axios.$put("https://mathsunlockedapi.thomas.gg/students/" + JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).user_id + "/tasks", {
                activity: activityID
            })
            .then((res) => {
                $nuxt.$router.push("/task/" + res.task);
            });
        },
        getDateWithOffset(offset) {
            return ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"][new Date(new Date().getTime() - (offset*86400000)).getDay()];
        },
        async loadPoints() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/students/" + JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).user_id)
            .then((res) => {
                this.points = res.points;
                this.chartData = {
                    labels: [this.getDateWithOffset(0), this.getDateWithOffset(1), this.getDateWithOffset(2), this.getDateWithOffset(3), this.getDateWithOffset(4), this.getDateWithOffset(5), this.getDateWithOffset(6)],
                    datasets: [{
                        label: "Points earned",
                        data: res.points
                    }]
                }
            });
        },
        disableLiveMaths() {
            if(this.assigned_activities.length > 0) return true;
            return false;
        }
    },
    mounted() {
        this.loadAssignedActivities();
        this.loadPoints();
    }
}
</script>
