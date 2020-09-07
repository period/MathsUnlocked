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
            </b-card>
        </b-col>
        <b-col lg="6">
            <h1 class="text-center">Hello, {{ name }}</h1>
            <b-card title="Assigned Activities">
                <p v-if="this.assigned_activities.length == 0">You have no assigned activities due 🙌🏻</p>
                <div v-else></div>
            </b-card>
            <b-card title="Activities">
                <p v-if="this.activities.length == 0">There are no activities available</p>
                <div v-else></div>
            </b-card>
        </b-col>
        <b-col lg="3">
            <n-link to="./edit-profile"><b-button squared block>Edit Profile</b-button></n-link>
            <b-button squared block :disabled="disableLiveMaths()">Live Maths</b-button>
        </b-col>
    </b-row>
  </div>
</template>

<script>

export default {
    name: "StudentDashboard",
    components: {
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
        disableLiveMaths() {
            if(this.assigned_activities.length > 0) return true;
            return false;
        }
    }
}
</script>
