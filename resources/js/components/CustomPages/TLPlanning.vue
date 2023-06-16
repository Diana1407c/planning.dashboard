<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-6">
            <VueMultiselect
                v-model="filter.team_ids"
                :options="allTeams"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select teams"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getData"
                @remove="getData">

                <template v-if="filter.project_ids.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselectTeams" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </VueMultiselect>
        </div>
        <div class="col-6">
            <VueMultiselect
                v-model="filter.project_ids"
                :options="allProjects"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select projects"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getData"
                @remove="getData">

                <template v-if="filter.project_ids.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselectProjects" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </VueMultiselect>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-4 d-flex justify-content-start">
            <button :disabled="!loaded" type="button" class="btn btn-primary date-change-button" @click="handleDateInput(false)"><i class="fa-solid fa-arrow-left"></i> Previous</button>
        </div>
        <div class="col-4 d-flex justify-content-center align-items-center">
            <div class="week-inscription">{{ start_week }} - {{ end_week }}</div>
        </div>
        <div class="col-4 d-flex justify-content-end">
            <button :disabled="!loaded" type="button" class="btn btn-primary date-change-button" @click="handleDateInput(true)">Next <i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="table-responsive">
        <table v-if="loaded" class="table table-striped table-bordered planning-table">
            <thead>
            <tr>
                <th class="w-5 vertical-text text-center align-middle">Team</th>
                <th class="w-20 text-center align-middle">Members</th>
                <th class="w-8 vertical-text text-center align-middle" v-for="project in projects">{{ project.name }}</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="team in teams" :key="team.id">
                <tr>
                    <td class="vertical-text w-5 text-center align-middle" :rowspan="team.members.length+1">{{ team.name }}</td>
                </tr>
                <tr v-for="member in team.members">
                    <td class="w-20 align-middle cell-p">{{ member.name }}</td>
                    <td class="w-8 align-middle cell-p" v-for="project in projects">
                        <input type="number" class="form-control text-center no-arrows" :value="table[member.id][project.id]" @blur="plan($event, member.id, project.id)">
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
    <div v-if="loaded">
        <div class="d-flex box-filter-separator">
            <hr class="col-12 separator-filter">
        </div>
        <div class="row">
            <div class="col-4 d-flex justify-content-start">
                <button type="button" class="btn btn-primary date-change-button" @click="handleDateInput(false)"><i class="fa-solid fa-arrow-left"></i> Previous</button>
            </div>
            <div class="col-4 d-flex justify-content-center align-items-center">
                <div class="week-inscription">{{ start_week }} - {{ end_week }}</div>
            </div>
            <div class="col-4 d-flex justify-content-end">
                <button type="button" class="btn btn-primary date-change-button" @click="handleDateInput(true)">Next <i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>
        <div class="d-flex box-filter-separator">
            <hr class="col-12 separator-filter">
        </div>
    </div>
</template>

<script>
import Layout from "./../Layout.vue";
const { getWeek } = require('date-fns');
import VueMultiselect from 'vue-multiselect';

export default {
    name: "TLPlanning",
    layout: (h, page) => h(Layout, [page]),
    props: {
        allTeams: Object,
        allProjects: Object
    },
    data(){
        return {
            projects: [],
            teams: [],
            table: [],
            start_week: null,
            end_week: null,
            filter: {
                team_ids: [],
                project_ids: [],
                week: null,
                year: null
            },
            loaded: false
        }
    },
    components: {VueMultiselect},
    async mounted() {
        const storedObject = localStorage.getItem("filter-tl");
        if (storedObject) {
            this.filter = JSON.parse(storedObject);
        }
        await this.setNextWeek()
        await this.getData()
    },
    methods: {
        async getData(){
            localStorage.setItem("filter-tl", JSON.stringify(this.filter));
            await this.getProjects()
            await this.getTeams()
            await this.getPlannings()
            this.loaded = true;
        },

        async getProjects(){
            await axios.get('projects', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                }}).then((response) => {
                this.projects = response.data.data
            });
        },

        async getTeams(){
            await axios.get('teams', {params: {
                    team_ids: this.filter.team_ids.map(obj => obj.id),
            }}).then(response => {
                this.teams = response.data.data;
            }).catch(() => {});
        },

        async getPlannings(){
            await axios.get('tl-planning', {params: {
                    team_ids: this.filter.team_ids.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    year: this.filter.year,
                    week: this.filter.week
                }}).then(response => {
                this.table = response.data.table;
            }).catch(() => {});
        },

        plan(event, engineerId, projectId){
            if(!event.target.value){
                event.target.value = this.table[engineerId][projectId]
            }

            if(Number(event.target.value) === this.table[engineerId][projectId]){
                return;
            }

            axios.post('tl-planning', {
                project_id: projectId,
                engineer_id: engineerId,
                week: this.filter.week,
                year: this.filter.year,
                hours: event.target.value
            }).then((response) => {
                this.table[engineerId][projectId] = Number(response.data.hours)
                this.$notify(response.data.message);
            });
        },

        async setNextWeek(){
            let nextWeekStart = new Date();
            nextWeekStart.setDate(nextWeekStart.getDate() + ((8 - nextWeekStart.getDay()) % 7));

            this.filter.week = getWeek(nextWeekStart, { weekStartsOn: 1 })
            this.filter.year = nextWeekStart.getFullYear()
            await this.getWeekRange()
        },

        async  getWeekRange() {
            let startDate = new Date(this.filter.year, 0, 1 + (this.filter.week - 1) * 7);
            let endDate = new Date(startDate.getTime() + 6 * 24 * 60 * 60 * 1000);

            let monday = startDate.getDate() - startDate.getDay() + 1;
            startDate.setDate(monday);

            let sunday = endDate.getDate() - endDate.getDay() + 7;
            endDate.setDate(sunday);


            this.start_week = startDate.toLocaleString('en-US', { month: 'long' })+' '+startDate.getDate();
            if(endDate.getFullYear() !== startDate.getFullYear()){
                this.start_week += ', '+startDate.getFullYear()
            }
            this.end_week = endDate.toLocaleString('en-US', { month: 'long' })+' '+endDate.getDate()+', '+endDate.getFullYear();
        },

        async handleDateInput(nextWeek){
            this.loaded = false;
            if(nextWeek){
                if(this.filter.week === 52){
                    this.filter.week = 1
                    this.filter.year += 1
                } else {
                    this.filter.week += 1
                }
            } else {
                if(this.filter.week === 1){
                    this.filter.week = 52
                    this.filter.year -= 1
                } else {
                    this.filter.week -= 1
                }
            }

            await this.getWeekRange()
            await this.getData()
            this.loaded = true;
        },

        async handleDiselectTeams(){
            this.filter.team_ids = []
            await this.getData()
        },

        async handleDiselectProjects(){
            this.filter.project_ids = []
            await this.getData()
        }
    },
}
</script>
