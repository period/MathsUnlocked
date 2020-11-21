<template>
  <div>
    <b-list-group-item>
      {{ teacher.name }}
      <b-dropdown class="float-right">
        <template v-slot:button-content>
          <fa :icon="['fas', 'ellipsis-h']" />
        </template>
        <b-dropdown-item :to="'/dashboard/show-teacher-tasks/' + teacher.id">Show tasks by teacher</b-dropdown-item>
        <b-dropdown-item v-b-modal="'modifytecher' + teacher.id">Modify teacher details</b-dropdown-item>
        <b-dropdown-item v-b-modal="'qrcode' + teacher.id">Generate QR Code</b-dropdown-item>
      </b-dropdown>
    </b-list-group-item>
    <b-modal
      :id="'qrcode' + teacher.id"
      title="Generate QR code for teacher"
      ok-only
      ok-title="Close"
    >
      <LoginQRGenerator scope="teacher" :uid="teacher.id" />
    </b-modal>
    <b-modal
      :id="'modifyteacher' + teacher.id"
      title="Modify teacher details"
      ok-only
      ok-title="Close"
    >
      <edit-profile-form :oldName="teacher.name" :oldUsername="teacher.username" scope="teacher" :uid="teacher.id" />
    </b-modal>
  </div>
</template>
<script>
import EditProfileForm from './EditProfileForm';
import LoginQRGenerator from './LoginQRGenerator';

export default {
  name: "TeacherTeacherItem",
  components: {EditProfileForm, LoginQRGenerator},
  props: {
    teacher: {
      type: Object,
      default: { id: -1, name: "" }
    }
  },
  methods: {
  }
};
</script>
