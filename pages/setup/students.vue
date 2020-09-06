<template>
  <div class="container">
    <div class="row">
      <h1 class="title">
          Welcome to Maths Unlocked! Let's get your school setup.
      </h1>
    </div>
    <b-row>
        <b-col lg="3" />
        <b-col lg="6">
            <b-card
                header="Student Setup"
                header-tag="header"
            >
            <b-card-body>
                <b-form-select v-model="importMethod">
                    <b-form-select-option value="null" disabled>How would you like to create students?</b-form-select-option>
                    <b-form-select-option value="manual">Manually</b-form-select-option>
                    <b-form-select-option value="csv">CSV Import</b-form-select-option>
                </b-form-select>
                <div v-if="importMethod == 'manual'">
                    <strong>Enter the student's details below:</strong>
                    <b-input-group class="mt-3">
                        <b-form-input id="name" placeholder="First & Last Name" type="text" v-model="name" />
                    </b-input-group>
                    <b-input-group class="mt-3">
                        <b-form-input id="email" placeholder="Email address" type="email" v-model="email" />
                    </b-input-group>
                    <b-button :disabled="disableAddBtn()" variant="primary" @click="add()">Add</b-button>
                </div>
                <div v-if="importMethod == 'csv'">
                    <strong>Select a CSV file:</strong>
                    <p>The CSV file must be comma separated with no headings. Each student should be delimeted by a newline and each column should be delimeted by a comma with no quotes.</p>
                    <p>Format: <code>student full name,student email</code></p>
                    <p>Usernames and passwords will be generated at random</p>
                    <b-form-file accept=".csv" v-model="csvFile" :state="Boolean(csvFile)" placeholder="Select a file" drop-placeholder="Drag and drop a file" />
                    <p>{{ importStatus }}</p>
                    <b-button v-if="Boolean(csvFile)" variant="primary" @click="csvImport()">Load CSV file</b-button>
                </div>
                <hr>
                <div class="mt-3">
                    <strong>Students to be imported ({{this.studentsToImport.length}}):</strong>
                    <b-table responsive striped bordered :items="this.studentsToImport"></b-table>
                </div>
                <div class="mt-3">
                    <b-btn-group>
                        <b-button v-if="importMethod != null" variant="primary" :disabled="this.studentsToImport == 0" @click="create()">Import Students</b-button>
                        <b-button variant="secondary" @click="$nuxt.$router.push('/setup/classes')">Next Step</b-button>
                    </b-btn-group>
                </div>
            </b-card-body>
            </b-card>
        </b-col>
        <b-col lg="3" />
    </b-row>
  </div>
</template>

<script>

export default {
    name: "SetupStudents",
    components: {
    },
    data() {
        return {
            importMethod: null,
            csvFile: null,
            name: null,
            username: null,
            password: null,
            email: null,
            activeRequest: false,
            importStatus: "Waiting for a file to be selected...",
            studentsToImport: []
        }
    },
    methods: {
        async csvImport() {
            let reader = new FileReader();
            reader.onload = e => {
                let lines = e.target.result.split(/\r?\n/);
                for(var i = 0; i < lines.length; i++) {
                    if(lines[i].includes(",") == false) continue;
                    let data = lines[i].split(",");
                    if(data[1].includes("@") == false || data[1].includes(".") == false) continue;
                    if(data[0].includes(" ") == false) continue;
                    if(this.isEmailInBuffer(data[1])) continue;
                    this.studentsToImport.push({name: data[0], email: data[1]})
                }
                this.importStatus = "Found " + this.studentsToImport.length + " students for import. Please verify that the data is correct before importing.";
            }       
            reader.readAsText(this.csvFile);
        },
        async create() {
            this.activeRequest = true;
            await this.$axios.$put("https://mathsunlockedapi.thomas.gg/school/" + localStorage.getItem("schoolID") + "/students", this.studentsToImport, {
                headers: {"Authorization": localStorage.getItem("authorization")}
            })
            .then((res) => {
                this.studentsToImport = [];
                this.activeRequest = false;
                this.$toastr(
                    "success",
                    "Student account(s) created successfully",
                    "Creation successful"
                );
            })
            .catch((e) => {
                this.activeRequest = false;
            })
        },
        isEmailInBuffer(email) {
            for(var i = 0; i < this.studentsToImport.length; i++) if(this.studentsToImport[i].email.toLowerCase() == email.toLowerCase()) return true;
            return false;
        },
        add() {
            if(this.disableAddBtn() == true) return;
            if(this.isEmailInBuffer(this.email)) return;
            this.studentsToImport.push({name: this.name, email: this.email})
        },
        disableAddBtn() {
            if(this.name == null || this.name.length < 5 || this.name.trim().includes(" ") == false) return true;
            if(this.email == null || this.email.length < 5 || this.email.includes("@") == false || this.email.includes(".") == false) return true;
            if(this.activeRequest == true) return true;
            return false;
        }
    }
}
</script>
