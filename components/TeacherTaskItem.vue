<template>
  <div>
    <b-list-group-item>
      <b-row>
        <b-col lg="12">
          <h5><strong>{{ task.activity }}</strong></h5>
          <b-dropdown class="float-right">
            <template v-slot:button-content>
              <fa :icon="['fas', 'ellipsis-h']" />
            </template>
            <b-dropdown-item @click="viewTask()">View task</b-dropdown-item>
            <b-dropdown-item v-if="task.completed == null" v-b-modal="'deletetask' + task.id">Delete task</b-dropdown-item>
          </b-dropdown>
        </b-col>
      </b-row>
      <b-row>
        <b-col sm="4">
          <span v-if="task.teacher.id != null"><strong>Assigned by:</strong> {{ task.teacher.name }}</span>
          <span v-else>Task initiated by student</span>
          <span v-if="show_student_names"><strong>Assigned to:</strong> {{ task.student.name }}</span>
        </b-col>
        <b-col sm="4">
          <span v-if="task.due != null"><strong>Due:</strong> {{ new Date(task.due*1000).toDateString()}}</span>
          <span v-if="task.completed != null"><strong>Completed:</strong> {{ new Date(task.completed*1000).toDateString()}}</span>
        </b-col>
        <b-col sm="4">
          <span v-if="task.due != null">
            <span v-if="task.completed == null && new Date().getTime() < (task.due*1000)"><b-badge pill variant="info">Outstanding</b-badge></span>
            <span v-if="task.completed == null && new Date().getTime() >= (task.due*1000)"><b-badge pill variant="danger">Overdue</b-badge></span>
            <span v-if="task.completed != null && task.completed > task.due"><b-badge pill variant="warning">Completed (Late)</b-badge></span>
            <span v-if="task.completed != null && task.completed <= task.due"><b-badge pill variant="success">Completed (On-time)</b-badge></span>
          </span>
        </b-col>
      </b-row>
    </b-list-group-item>
    <b-modal
      :id="'deletetask' + task.id"
      title="Delete task"
      @ok="deleteTask()"
    >
      <p>Are you sure you want to delete this task?</p>
    </b-modal>
  </div>
</template>
<script>
export default {
  name: "TeacherTaskItem",
  components: {},
  props: {
    task: {
      type: Object,
      default: { id: -1, name: "", teacher: {id: -1, name: ""}, student: {id: -1, name: ""}}
    },
    show_student_names: {
      type: Boolean,
      default: true
    }
  },
  methods: {
    viewTask() {
      $nuxt.$router.push("/task/" + this.task.id);
    },
      async deleteTask() {
        await this.$axios.$delete("https://mathsunlockedapi.thomas.gg/tasks/" + this.task.id, {}, {
          headers: {"Authorization": localStorage.getItem("authorization")}
          })
          .then((res) => {
            location.reload();
          })
          .catch((e) => {
          })
      }
  }
};
</script>
