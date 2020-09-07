<template>
  <div class="container">
   <b-row class="mt-5">
        <b-col lg="3" />
        <b-col lg="6">
            <edit-profile-form :oldName="this.oldName" :oldUsername="this.oldUsername" :scope="this.scope" :uid="this.uid" />
            <b-card
                header="QR Login"
                header-tag="header"
            >
                <b-card-body>
                    <LoginQRGenerator  :scope="this.scope" :uid="this.uid" />
                </b-card-body>
            </b-card>
        </b-col>
        <b-col lg="3" />
    </b-row>
  </div>
</template>

<script>
import EditProfileForm from '../../components/EditProfileForm';
import LoginQRGenerator from '../../components/LoginQRGenerator';

export default {
    name: "EditProfile",
    components: {
        EditProfileForm,
        LoginQRGenerator
    },
    data() {
        return {
            oldName: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).name,
            oldUsername: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).user,
            uid: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).user_id,
            scope: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).scope
        }
    }
}
</script>
