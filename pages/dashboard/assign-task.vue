<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Assigning task to {{ students.length }} student<span v-if="students.length > 1">s</span></h1>
        </b-col>
        <b-col lg="3">
            <n-link to="./teacher"><b-button squared block>Dashboard</b-button></n-link>
        </b-col>
    </b-row>
    <hr>
    <div v-if="step == 1">
        <h3>Please select an activity for the task</h3>
        <available-activity-list v-on:activity="selectedActivity" />
    </div>
    <div v-if="step == 2">
        <h3>Additional task details</h3>
        <b-form-checkbox
            v-model="set_due_date"
        >
            I want to set a due date for this task
        </b-form-checkbox>
        <div v-if="set_due_date">
            <b-form-datepicker value-as-date :min="new Date()" v-model="due_date"></b-form-datepicker>
        </div>
        <br>
        <p><strong>Additional remarks:</strong></p>
        <textarea rows=2 v-model="remarks" class="form-control" />
        <p class="text-danger" v-if="remarks != null && remarks.length != 0 && (remarks.length > 2048 || !remarks.match(/^[-\w\s]+$/))" ><strong>Remarks must not exceed 2048 characters and must be alphanumeric.</strong></p>
        <hr>
        <b-button variant="primary" @click="createTask">Create Task</b-button>
    </div>
    <div v-if="step == 3">
        <h3>Task creation in progress</h3>
        <p>Please do not cloes the tab whilst we are assigning the task. You can view the current progress below.</p>
        <b-progress :max="this.students.length">
            <b-progress-bar :value="this.students.length-this.students_todo.length">
                <span>{{ this.students.length-this.students_todo.length }} to go</span>
            </b-progress-bar>
        </b-progress>
    </div>
    <div v-if="step == 4">
        <h3>Your task is now live!</h3>
    </div>
</div>
</template>
<script>
import AvailableActivityList from "~/components/AvailableActivityList";
export default {
    name: "AssignTask",
    components: {AvailableActivityList},
    data() {
        return {
            students: [],
            selected_activity: -1,
            step: 1,
            students_todo: [],
            set_due_date: false,
            due_date: null,
            remarks: null
        }
    },
    mounted() {
        this.students = JSON.parse(sessionStorage.getItem("assign_task_students"));
    },
    methods: {
        async allocateTaskToStudent() {
            let student = this.students_todo.shift();
            let data = {activity: this.selected_activity};
            if(this.set_due_date == true) data.due = Math.round(this.due_date.getTime()/1000);
            if(this.remarks != null && this.remarks.length > 0) data.remarks = this.remarks;
            await this.$axios.$put("https://mathsunlockedapi.thomas.gg/students/" + student + "/tasks", data)
            .then((res) => {
                if(this.students_todo.length > 0) this.allocateTaskToStudent();
                else this.step++;
            });
        },
        createTask() {
            this.step++;
            this.students_todo = this.students.slice(0);
            this.allocateTaskToStudent();
        },
        selectedActivity(activityID) {
            this.selected_activity = activityID;
            this.step++;
        }
    }
}
</script>