<template>
    <b-card
        header="Edit account details"
        header-tag="header"
    >
    <b-card-body>
        <p>Username:</p>
        <b-input-group class="mt-3">
            <b-form-input id="username" placeholder="Username" type="text" v-model="username" />
        </b-input-group>
        <p>Name:</p>
        <b-input-group class="mt-3">
            <b-form-input id="name" placeholder="Name" type="text" v-model="name" />
        </b-input-group>
        <p>Password:</p>
        <b-input-group class="mt-3">
            <b-form-input id="password" placeholder="Password" type="password" v-model="password" />
        </b-input-group>
        <div class="mt-3"><b-button variant="primary" :disabled="disableSaveBtn()" @click="save()">Save</b-button></div>
    </b-card-body>
    </b-card>
</template>

<script>

export default {
    name: "EditProfileForm",
    components: {
    },
    data() {
        return {
            name: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).name,
            username: JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).user,
            password: null,
            oldPassword: null
        }
    },
    props: {
        oldName: {
            type: String
        },
        oldUsername: {
            type: String
        },
        scope: {
            type: String
        },
        uid: {
            type: Number
        }
    },
    mounted() {
        this.name = this.oldName;
        this.username = this.oldUsername;
    },
    methods: {
        disableSaveBtn() {
            if(this.isSame(this.name, this.oldName) && this.isSame(this.username, this.oldUsername) && this.isSame(this.password, this.oldPassword)) return true;
            return false
        },
        isSame(old, compareTo) {
            if(old == null && compareTo == null) return true;
            if(old != null && compareTo == null) return false;
            if(old.toLowerCase().trim() == compareTo.toLowerCase().trim()) return true;
            return false;
        },
        async save() {
            let data = {};
            if(!this.isSame(this.name, this.oldName)) data["name"] = this.name;
            if(!this.isSame(this.username, this.oldUsername)) data["username"] = this.username;
            if(!this.isSame(this.password, this.oldPassword)) data["password"] = this.password;
            await this.$axios.$patch("https://mathsunlockedapi.thomas.gg/" + this.scope +"s/" + this.uid, data, {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                if(!this.isSame(this.name, this.oldName) || !this.isSame(this.username, this.oldUsername)) {
                    if(this.scope == JSON.parse(atob(localStorage.getItem("authorization").split(".")[1])).scope) {
                        localStorage.removeItem("authorization");
                        $nuxt.$router.push('/login');
                    }
                }
                else $nuxt.$router.push('/dashboard/' + this.scope);
            })
            .catch((e) => {
            })
        }
    }
}
</script>
