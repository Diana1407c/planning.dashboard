<template>
    <div class="filter-box d-flex pt-1 pb-2 mb-2 border-1">
        <div class="col-4 filter-planning-wrapper">
            <VueDatePicker v-model="rawDateRange" week-picker @update:model-value="getData"  />
        </div>
        <div class="col-4 filter-planning-wrapper">
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
                @remove="getData"
            />
        </div>
        <div class="col-4 filter-planning-wrapper">
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
                @remove="getData"
            />
        </div>
    </div>
    <table v-if="loaded" class="table table-striped table-bordered planning-table">
        <thead>
        <tr>
            <th class="header-team-name vertical-text text-center">
                <div class="d-flex justify-content-center align-items-center">
                    Team
                </div>
            </th>
            <th class="header-member-name text-center">Members</th>
            <th class="header-project-name vertical-text text-center" v-for="project in projects">
                <div class="d-flex justify-content-center align-items-center">
                    {{ project.name }}
                </div>
            </th>
        </tr>
        </thead>
        <tbody>
        <template v-for="team in teams" :key="team.id">
            <tr>
                <td class="vertical-text cell-team-name" :rowspan="team.members.length+1">
                    <div class="d-flex justify-content-center align-items-center">
                        {{ team.name }}
                    </div>
                </td>
            </tr>
            <tr v-for="member in team.members">
                <td class="cell-member-name">{{ member.name }}</td>
                <td class="cell-project-name" v-for="project in projects">
                    <input type="text" class="form-control" :value="table[member.id][project.id]" @input="plan($event, member.id, project.id)">
                </td>
            </tr>
        </template>
        </tbody>
    </table>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker';
const { getWeek } = require('date-fns');
import VueMultiselect from 'vue-multiselect';

export default {
    name: "Example",
    data(){
        return {
            allProjects: [],
            allTeams: [],
            projects: [],
            teams: [],
            table: [],
            rawDateRange: [
                this.getNextWeekStart(),
                this.getNextWeekEnd()
            ],
            filter: {
                team_ids: [],
                project_ids: [],
                week: null,
                year: null
            },
            tableLoaded: false,
            teamsLoaded: false,
            projectsLoaded: false
        }
    },
    components: {VueDatePicker, VueMultiselect},
    computed:{
        loaded(){
            return this.teamsLoaded && this.projectsLoaded && this.tableLoaded
        }
    },
    mounted() {
        this.getAllProjects()
        this.getAllTeams()
        this.getData()
    },
    methods: {
        getData(){
            this.handleDateInput()
            this.getProjects()
            this.getTeams()
            this.getPlannings()
        },

        getAllProjects(){
            axios.get('projects/all').then((response) => {
                this.allProjects = response.data.data
            });
        },

        getAllTeams(){
            axios.get('teams/all').then(response => {
                this.allTeams = response.data.data;
            });
        },

        getProjects(){
            this.projectsLoaded = false
            axios.get('projects', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                }}).then((response) => {
                this.projects = response.data.data
                this.projectsLoaded = true
            });
        },

        getTeams(){
            this.teamsLoaded = false
            axios.get('teams', {params: {
                    team_ids: this.filter.team_ids.map(obj => obj.id),
            }}).then(response => {
                this.teams = response.data.data;
                this.teamsLoaded = true
            }).catch(error => {
                console.error(error);
            });
        },

        getPlannings(){
            this.tableLoaded = false
            axios.get('tl-planning', {params: {
                    team_ids: this.filter.team_ids.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    year: this.filter.year,
                    week: this.filter.week
                }}).then(response => {
                this.table = response.data.table;
                this.tableLoaded = true
            }).catch(error => {
                console.error(error);
            });
        },

        plan(event, engineerId, projectId){
            axios.post('tl-planning', {
                project_id: projectId,
                engineer_id: engineerId,
                week: this.filter.week,
                year: this.filter.year,
                hours: event.target.value
            }).then((response) => {

            });
        },

        getNextWeekStart(){
            let nextWeekStart = new Date();
            nextWeekStart.setDate(nextWeekStart.getDate() + ((8 - nextWeekStart.getDay()) % 7));
            return nextWeekStart
        },

        getNextWeekEnd() {
            let nextWeekStart = this.getNextWeekStart();
            let nextWeekEnd = new Date(nextWeekStart);
            nextWeekEnd.setDate(nextWeekEnd.getDate() + 6);
            return nextWeekEnd;
        },

        handleDateInput(){
            this.filter.week = getWeek(this.rawDateRange[0], { weekStartsOn: 1 })
            this.filter.year = new Date(this.rawDateRange[0]).getFullYear()
        }
    },
}
</script>
