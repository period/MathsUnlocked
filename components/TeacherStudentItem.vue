<template>
  <div>
    <b-list-group-item>
        {{ student.name }}
        <b-dropdown class="float-right">
            <template v-slot:button-content>
                <fa :icon="['fas', 'ellipsis-h']" />
            </template>
            <b-dropdown-item @click="assignTask()">Assign task</b-dropdown-item>
            <b-dropdown-item @click="assignClasses()">Assign classes</b-dropdown-item>
            <b-dropdown-item @click="setPassword()">Set password</b-dropdown-item>
            <b-dropdown-item @click="viewAsStudent()">View as student</b-dropdown-item>
        </b-dropdown>
    </b-list-group-item>
  </div>
</template>
<script>
export default {
  name: "TeacherStudentItem",
  props: {
    student: {
      type: Object,
      default: { id: -1, name: "" }
    }
  },
  methods: {
      assignTask() {},
      assignClasses() {},
      setPassword() {},
      viewAsStudent() {}
  }
};
</script>
