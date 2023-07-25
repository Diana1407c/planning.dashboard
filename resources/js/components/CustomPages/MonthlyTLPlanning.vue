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
            <div class="week-inscription">{{ month_name }} {{ this.filter.year }} - {{ hours_count }} hours</div>
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
                <th class="w-8 vertical-text text-center align-middle heading-tech-total">Total</th>
                <th class="w-8 vertical-text text-center align-middle" v-for="project in projects">{{ project.name }}</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="team in teams" :key="team.id">
                <tr>
                    <td class="vertical-text w-5 text-center align-middle" :rowspan="team.members.length+2">{{ team.name }}</td>
                </tr>
                <tr class="evidence-bg-1">
                    <td class="w-20 align-middle cell-p">{{ team.technology.name }}</td>
                    <td title="Planned monthly by TL / Planned monthly by PM" class="w-8 align-middle text-center cell-p heading-tech-total">
                        <span class="tl-hour-month">{{ table['technologies'][team.technology.id]['total']['planned_tl'] }}</span>
                        <span class="hours-separator">/</span>
                        <span class="pm-hour-month">{{ table['technologies'][team.technology.id]['total']['planned_pm'] }}</span>
                    </td>
                    <td :class="setColorHour(table['technologies'][team.technology.id][project.id]['planned_tl'], table['technologies'][team.technology.id][project.id]['planned_pm'])" title="Planned monthly by TL / Planned monthly by PM" class="w-8 align-middle text-center cell-p" v-for="project in projects">
                        <span class="tl-hour-month">{{ table['technologies'][team.technology.id][project.id]['planned_tl'] }}</span>
                        <span class="hours-separator">/</span>
                        <span class="pm-hour-month">{{ table['technologies'][team.technology.id][project.id]['planned_pm'] }}</span>
                    </td>
                </tr>
                <tr v-for="member in team.members">
                    <td class="w-20 align-middle cell-p">{{ member.name }} <span title="Performance" class="float-right">{{ member.performance }}%</span></td>
                    <td class="w-8 align-middle text-center cell-p heading-tech-total">{{ table['engineers'][member.id]['total'] }}</td>
                    <td class="w-8 align-middle cell-p" v-for="project in projects">
                        <input :disabled="!can_edit" type="number" class="form-control text-center no-arrows" :value="table['engineers'][member.id][project.id]" @blur="plan($event, member.id, project.id)">
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
                <div class="week-inscription">{{ month_name }} {{ this.filter.year }} - {{ hours_count }} hours</div>
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
import errorMessages from "../../helpers";
import Color from "../Elements/Color.vue";

export default {
    name: "MonthlyTLPlanning",
    layout: (h, page) => h(Layout, [page]),
    props: {
        allTeams: Object,
        allProjects: Object
    },
    mixins: [
        Color
    ],
    data(){
        return {
            projects: [],
            teams: [],
            table: [],
            can_edit: true,
            month_name: null,
            filter: {
                team_ids: [],
                project_ids: [],
                month: null,
                year: null
            },
            loaded: false,
            hours_count: 0
        }
    },
    components: {VueMultiselect},
    async mounted() {
        const storedObject = localStorage.getItem("filter-tl-monthly");
        if (storedObject) {
            this.filter = JSON.parse(storedObject);
        }
        await this.setNextMonth()
        await this.getData()
    },
    methods: {
        async getData(){
            localStorage.setItem("filter-tl-monthly", JSON.stringify(this.filter));
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
            await axios.get('tl-planning/monthly', {params: {
                    team_ids: this.filter.team_ids.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    year: this.filter.year,
                    period_number: this.filter.month,
                }}).then(response => {
                this.table = response.data.table;
                this.can_edit = response.data.can_edit
                this.hours_count = response.data.hours_count
            }).catch(() => {});
        },

        plan(event, engineerId, projectId){
            if(!event.target.value){
                event.target.value = this.table['engineers'][engineerId][projectId]
            }

            if(Number(event.target.value) === this.table['engineers'][engineerId][projectId]){
                return;
            }

            axios.post('tl-planning', {
                project_id: projectId,
                engineer_id: engineerId,
                period_type: 'month',
                period_number: this.filter.month,
                year: this.filter.year,
                hours: event.target.value
            }).then((response) => {
                this.table = response.data.table
                this.$notify(response.data.message);
            }).catch((error) => {
                let messages = errorMessages(error.response);
                this.$notify({
                    text: messages.join('<br>'),
                    type: "error",
                });
            });
        },

        async setNextMonth(){
            let now = new Date();
            if (now.getMonth() === 11) {
                this.filter.month = 1
                this.filter.year = now.getFullYear() + 1
            } else {
                this.filter.month = now.getMonth() + 2
                this.filter.year = now.getFullYear()
            }

            await this.setMonthName()
        },

        async setMonthName() {
            let date = new Date(this.filter.year, this.filter.month - 1, 1);
            this.month_name = date.toLocaleString('en-US', { month: 'long' });
        },

        async handleDateInput(nextMonth){
            this.loaded = false;
            if(nextMonth) {
                if(this.filter.month === 12){
                    this.filter.month = 1
                    this.filter.year += 1
                } else {
                    this.filter.month += 1
                }
            } else {
                if(this.filter.month === 1){
                    this.filter.month = 12
                    this.filter.year -= 1
                } else {
                    this.filter.month -= 1
                }
            }

            await this.setMonthName()
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
