<template>
  <div>
    <b-list-group-item>
      {{ student.name }}
      <b-dropdown class="float-right">
        <template v-slot:button-content>
          <fa :icon="['fas', 'ellipsis-h']" />
        </template>
        <b-dropdown-item @click="assignTask()">Assign task</b-dropdown-item>
        <b-dropdown-item v-b-modal="'modifystudent' + student.id">Modify student details</b-dropdown-item>
        <b-dropdown-item v-b-modal="'qrcode' + student.id">Generate QR Code</b-dropdown-item>
        <b-dropdown-item v-b-modal="'viewasstudent' + student.id">View as student</b-dropdown-item>
      </b-dropdown>
    </b-list-group-item>
    <b-modal
      :id="'viewasstudent' + student.id"
      title="View as student"
      @ok="viewAsStudent()"
    >
      <p>You will be redirected to the student dashboard and will be logged in as if you were {{ student.name }}.</p>
      <p>This has the same effect as setting the user's password and then logging into their account - except, the password stays the same.</p>
    </b-modal>
    <b-modal
      :id="'qrcode' + student.id"
      title="Generate QR code for student"
      ok-only
      ok-title="Close"
    >
      <LoginQRGenerator scope="student" :uid="student.id" />
    </b-modal>
    <b-modal
      :id="'modifystudent' + student.id"
      title="Modify student details"
      ok-only
      ok-title="Close"
    >
      <edit-profile-form :oldName="student.name" :oldUsername="student.username" scope="student" :uid="student.id" />
    </b-modal>
  </div>
</template>
<script>
import EditProfileForm from './EditProfileForm';
import LoginQRGenerator from './LoginQRGenerator';

export default {
  name: "TeacherStudentItem",
  components: {EditProfileForm, LoginQRGenerator},
  props: {
    student: {
      type: Object,
      default: { id: -1, name: "" }
    }
  },
  methods: {
    assignTask() {
      sessionStorage.setItem("assign_task_students", JSON.stringify([this.student.id]));
      $nuxt.$router.push("./assign-task");
    },
      async viewAsStudent() {
        await this.$axios.$post("https://mathsunlockedapi.thomas.gg/students/" + this.student.id + "/token", {}, {
          headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                localStorage.setItem("view_as_student_teacher_token", localStorage.getItem("authorization"));
                localStorage.setItem("authorization", res.token);
                this.$forceUpdate();
                $nuxt.$router.push("/dashboard/student");
            })
            .catch((e) => {
            })
      }
  }
};
</script>
