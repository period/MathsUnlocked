<template>
  <div>
    <b-list-group-item>
      {{ schoolclass.name }}
      <b-dropdown class="float-right">
        <template v-slot:button-content>
          <fa :icon="['fas', 'ellipsis-h']" />
        </template>
        <b-dropdown-item @click="assignTask">Assign task</b-dropdown-item>
        <b-dropdown-item v-b-modal="'renameclass' + schoolclass.id">Rename class</b-dropdown-item>
        <b-dropdown-item :to="'./assign-class-members/' + schoolclass.id">Modify class members</b-dropdown-item>
        <b-dropdown-item v-b-modal="'deleteclass' + schoolclass.id">Delete class</b-dropdown-item>
      </b-dropdown>
    </b-list-group-item>
    <b-modal
      :id="'deleteclass' + schoolclass.id"
      title="Delete class"
      @ok="deleteClass()"
      ok-title="Delete"
    >
      <p>Are you sure you want to delete this class? There's no way to recover the class once deleted. Any activities that are currently assigned to a class will remain assigned to the students.</p>
    </b-modal>
    <b-modal
      :id="'renameclass' + schoolclass.id"
      title="Rename class"
      @ok="renameClass()"
      ok-title="Rename"
    >
      <b-form-input v-model="newClassName" placeholder="New class name" />
    </b-modal>
  </div>
</template>
<script>
export default {
  name: "TeacherClassItem",
  components: {},
  props: {
    schoolclass: {
      type: Object,
      default: { id: -1, name: "" }
    }
  },
  data() {
    return {
      newClassName: this.schoolclass.name
    }
  },
  methods: {
      async assignTask() {
        await this.$axios.$get("https://mathsunlockedapi.thomas.gg/class/" + this.schoolclass.id, {
              headers: {"Authorization": localStorage.getItem("authorization")}
          })
          .then((res) => {
            let studentList = [];
            for(var i = 0; i < res.students.length; i++) studentList.push(res.students[i].id);
            sessionStorage.setItem("assign_task_students", JSON.stringify(studentList));
            $nuxt.$router.push("./assign-task");
          })
          .catch((e) => {
          })
      },
      async renameClass() {
          await this.$axios.$patch("https://mathsunlockedapi.thomas.gg/class/" + this.schoolclass.id, {name: this.newClassName}, {
              headers: {"Authorization": localStorage.getItem("authorization")}
          })
          .then((res) => {
            this.schoolclass.name = this.newClassName;

          })
          .catch((e) => {
          })
      },
      async deleteClass() {
          await this.$axios.$delete("https://mathsunlockedapi.thomas.gg/class/" + this.schoolclass.id, {
              headers: {"Authorization": localStorage.getItem("authorization")}
          })
          .then((res) => {
            window.location.reload(true)
          })
          .catch((e) => {
          })
      }
  }
};
</script>
