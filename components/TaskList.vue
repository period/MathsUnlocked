<template>
    <b-list-group>
        <b-list-group-item v-for="task in tasks" :key="'task'+task.id">
            <p><strong>{{ task.activity }}</strong> <n-link :to="'/task/' + task.id"><b-button variant="secondary" class="float-right"><fa :icon="['fas', 'caret-right']" /></b-button></n-link></p>
            <small>Set at {{ new Date(task.created*1000).toString() }}<span v-if="task.due != null"> / Due at {{ new Date(task.due*1000).toString() }}</span></small>
        </b-list-group-item>
    </b-list-group>
</template>
<script>
export default {
    name: "TaskList",
    components: {},
    props: {
        tasks: {
            type: Array,
            default: []
        }
    }
}
</script>