<template>
    <div>
        <div v-if="state != 'GENERATED'">
            <p>QR codes allow for quick and rapid login - simply scan the QR code and you're in!</P>
            <b-button variant="primary" :disabled="state == 'GENERATING'" @click="generate()">Generate</b-button>
        </div>
        <div v-if="state == 'GENERATED'">
            <p>Here's your QR code. Right click on it to save it to your computer to print, or take a photo of it. Make sure not to share your code - it allows access to the account</p>
            <qrcode :value="token" :options="{width:200}" />
        </div>
    </div>
</template>

<script>

export default {
    name: "LoginQRGenerator",
    data() {
        return {
            token: null,
            state: "NOT_GENERATED"
        }
    },
    props: {
        scope: {
            type: String
        },
        uid: {
            type: Number
        }
    },
    methods: {
        async generate() {
            this.state = "GENERATING";
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/" + this.scope +"s/" + this.uid + "/qr", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.state = "GENERATED";
                this.token = res.token;
            })
            .catch((e) => {
                this.state = "NOT_GENERATED";
            })
        }
    }
}
</script>
