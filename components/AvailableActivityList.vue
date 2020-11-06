<template>
    <div>
        <b-list-group>
            <b-list-group-item v-for="(category, categoryID) in categories" :key="categoryID">
                <p><strong>{{ category.name }}</strong> <b-button v-b-toggle="'availableactivitylistcategory' + categoryID" variant="secondary" class="float-right"><fa :icon="['fas', 'caret-down']" /></b-button></p>
                <b-collapse :id="'availableactivitylistcategory' + categoryID">
                    <b-list-group>
                        <b-list-group-item v-for="activity in category.activities" :key="'activity' + activity.id">
                            <p>{{ activity.name }} <b-button variant="secondary" class="float-right" @click="selectActivity(activity.id)"><fa :icon="['fas', 'caret-right']" /></b-button></p>
                        </b-list-group-item>
                    </b-list-group>
                </b-collapse>
            </b-list-group-item>
        </b-list-group>
    </div>
</template>
<script>
export default {
    name: "AvailableActivityList",
    components: {},
    data() {
        return {
            categories: {}
        }
    },
    methods: {
        selectActivity(id) {
            this.$emit("activity", id);
        }
    },
    async mounted() {
        await this.$axios.$get("https://mathsunlockedapi.thomas.gg/activities", {
            headers: {"Authorization": localStorage.getItem("authorization")}
        })
        .then((res) => {
            this.categories = res;
        });
    }
}
</script>