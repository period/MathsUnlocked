<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="3">
            <b-card title="Daily Points" class="text-center">
                <b-card-text>1000</b-card-text>
            </b-card>
            <b-card title="Weekly Points" class="text-center mt-2">
                <b-card-text>1000</b-card-text>
            </b-card>
        </b-col>
        <b-col lg="6">
            <h1 class="text-center">Hello, {{ name }}</h1>
            <b-card title="Assigned Activities">
                You have no assigned activities due 🙌🏻
            </b-card>
            <b-card title="Activities">
            </b-card>
        </b-col>
        <b-col lg="3">
            <b-button squared block>Edit Profile</b-button>
            <b-button squared block>Live Maths</b-button>
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
            name: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).name
        }
    },
    methods: {
    }
}
</script>
