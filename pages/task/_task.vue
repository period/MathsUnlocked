<template>
    <div class="container">
        <div class="text-center" v-if="!loaded">
            <h1>Task loading, please wait...</h1>
        </div>
        <div class="row" v-if="!started && loaded">
            <div class="col-12 text-center">
                <h1>{{ task.activity }}</h1>
                <div class="offset-md-3 col-6">
                    <ul>
                        <li v-if="task.notes != null"><strong>Your teacher has left the following notes: </strong> {{ task.notes }}</li>
                        <li><strong>Due date: </strong> <span v-if="task.due != null">{{ task.due }}</span><span v-else>No due date set</span></li>
                        <li><strong>Completed at: </strong> <span v-if="task.completed != null">{{ task.completed }}</span><span v-else class="text-danger">You have not checked this task out yet</span></li>

                    </ul>
                </div>
                <b-button block variant="primary" @click="started = true">Start</b-button>
            </div>
        </div>
        <div class="row" v-if="started">
            <div class="col-12 text-center">
                <h1>{{ task.activity }}</h1>
            </div>
            <div class="col-3">
                <h3>Questions</h3>
                <b-list-group>
                    <div v-for="question in task.questions">
                        <b-list-group-item button :variant="getVariant(question)" @click="current_question = question.id">Question #{{ question.position }}</b-list-group-item>
                    </div>
                </b-list-group>
                <div v-if="task.completed == null">
                    <h3>Checkout</h3>
                    <p>In order to have your questions marked and scores submitted, you need to checkout. Only checkout once you have finished all the questions!</p>
                    <b-button block variant="primary" @click="checkout()" :disabled="!canCheckout()">Checkout</b-button>
                </div>
                <div v-else>
                    <p><strong>Score: {{ task.questions.filter((question) => { return question.data.submitted_answer == question.data.answer.value}).length }} out of {{ task.questions.length }} </strong></p>
                    <p>This task has been checked out! No further modifications can be made.</p>
                </div>
            </div>
            <div class="col-9">
                <div v-for="question in task.questions">
                    <div v-if="current_question == question.id">
                        <h1>Question #{{question.position}}</h1>
                        <h3>{{ question.data.question.title }}</h3>
                        <b-input-group>
                            <b-form-input type="number" :disabled="task.completed != null" v-model="answers[question.id]" />
                            <b-input-group-append v-if="task.completed == null">
                                <b-button variant="outline-primary" @click="saveAnswer(question.id)">Save</b-button>
                            </b-input-group-append>
                        </b-input-group>
                        <p v-if="question.data.answer != null && question.data.answer.value != null">The correct answer is <strong>{{ question.data.answer.value }}</strong></p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "Task",
    components: {},
    data() {
        return {
            task: {questions: []},
            answers: {},
            started: false,
            loaded: false,
            current_question: 0,
        }
    },
    mounted() {
        this.loadTask();
    },
    methods: {
        getVariant(question) {
            if(this.current_question == question.id) return "primary";
            if(question.data.submitted_answer != null && question.data.answer != null) {
                if(question.data.submitted_answer == question.data.answer.value) return "success";
                else return "danger";
            }
            if(question.data.submitted_answer == null) return "";
            else return "secondary"
        },
        canCheckout() {
            let answeredQuestions = 0;
            for(var i = 0; i < this.task.questions.length; i++) {
                if(this.answers[this.task.questions[i].id] != null) answeredQuestions++;
            }
            return (answeredQuestions == this.task.questions.length)
        },
        async checkout() {
            await this.$axios.$post("https://mathsunlockedapi.thomas.gg/tasks/" + parseInt(this.$route.params.task), {}, {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.loadTask();
            })
            .catch((err) => {
            });
        },
        async saveAnswer(questionID) {
            await this.$axios.$post("https://mathsunlockedapi.thomas.gg/tasks/questions/" + questionID, {answer: this.answers[questionID]}, {headers: {"Authorization": localStorage.getItem("authorization")}})
            .then((res) => {
                this.$toastr("success", "Your answer has been saved!");
            })
            .catch((err) => {
                this.$toastr("error", "Unable to save answer");
            })
        },
        async loadTask() {
            this.loaded = false;
            await this.$axios.$get("https://mathsunlockedapi.thomas.gg/tasks/" + parseInt(this.$route.params.task), {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.task = res;
                for(var i = 0; i < res.questions.length; i++) {
                    if(res.questions[i].data.submitted_answer != null) this.answers[res.questions[i].id] = res.questions[i].data.submitted_answer;
                }
                this.current_question = res.questions[0].id;
                this.loaded = true;
            })
            .catch((err) => {

            });
        }
    }
}
</script>