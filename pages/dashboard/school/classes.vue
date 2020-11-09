<template>
  <div class="container">
    <b-row class="mt-2">
        <b-col lg="9">
            <h1 class="text-center">Classes in school</h1>
            <b-card>
                <p v-if="this.classes.length == 0">Your school has no classes</p>
                <b-list-group v-else v-for="school_class in this.classes" :key="school_class.id">
                    <teacher-class-item :schoolclass="school_class" />
                </b-list-group>
            </b-card>
        </b-col>
        <b-col lg="3">
            <n-link to="../teacher"><b-button squared block>Teacher Dashboard</b-button></n-link>
        </b-col>
    </b-row>
  </div>
</template>

<script>
import TeacherClassItem from '~/components/TeacherClassItem';

export default {
    name: "SchoolClassesList",
    components: {
        TeacherClassItem,
    },
    data() {
        return {
            classes: [],
        }
    },
    mounted() {
        this.loadClasses();
    },
    methods: {
        async loadClasses() {
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/classes", {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.classes = res;
            })
            .catch((e) => {
            })
        },
    }
}
</script>
