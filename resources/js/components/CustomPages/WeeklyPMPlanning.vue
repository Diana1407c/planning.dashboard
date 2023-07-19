<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-12">
            <multiselect
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
                    <span @click="handleDiselect" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
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
    <div></div>
    <table v-if="loaded" class="table table-striped table-bordered planning-table">
        <thead>
        <tr>
            <th class="w-5 vertical-text text-center align-middle">State</th>
            <th class="w-20 text-center align-middle">Projects</th>
            <th class="w-8 vertical-text text-center align-middle" v-for="technology in technologies">{{ technology.name }}</th>
        </tr>
        </thead>
        <tbody>
        <template v-for="(projects, state) in groupedProjects" :key="state">
            <tr>
                <td class="vertical-text w-5" :rowspan="projects.length+1">
                    <div class="d-flex justify-content-center align-items-center">
                        {{ state }}
                    </div>
                </td>
            </tr>
            <tr v-for="project in projects">
                <td class="w-20 align-middle cell-p">{{ project.name }}</td>
                <td class="w-8 align-middle cell-p" v-for="technology in technologies">
                    <input type="number" class="form-control text-center no-arrows" :value="table[project.id][technology.id]" @blur="plan($event, project.id, technology.id)">
                </td>
            </tr>
        </template>
        </tbody>
    </table>
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
import Multiselect from 'vue-multiselect';
const { getWeek } = require('date-fns');
import { useNotification } from "@kyvg/vue3-notification";

const { notify } = useNotification()

export default {
    name: "WeeklyPMPlanning",
    layout: (h, page) => h(Layout, [page]),
    props: {
        technologies: Object,
        allProjects: Object
    },
    data(){
        return {
            groupedProjects: [],
            table: [],
            start_week: null,
            end_week: null,
            filter: {
                project_ids: [],
                week: null,
                year: null
            },
            loaded: false,
        }
    },
    components: {Multiselect},
    async mounted() {
        const storedObject = localStorage.getItem("filter-pm-weekly");
        if (storedObject) {
            this.filter = JSON.parse(storedObject);
        }
        await this.setNextWeek()
        await this.getData()
    },
    methods: {
        async getData(){
            localStorage.setItem("filter-pm-weekly", JSON.stringify(this.filter));
            await this.getProjects()
            await this.getPlannings()
            this.loaded = true;
        },

        async getProjects(){
            await axios.get('projects', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                }}).then((response) => {

                this.groupedProjects = response.data.data.reduce((result, item) => {
                    if (!result[item.state]) {
                        result[item.state] = [];
                    }
                    result[item.state].push(item);
                    return result;
                }, {});
            });
        },

        async getPlannings(){
            await axios.get('pm-planning/weekly', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    year: this.filter.year,
                    period_number: this.filter.week
                }}).then(response => {
                this.table = response.data.table;
            }).catch(() => {});
        },

        plan(event, projectId, technologyId){
            if(!event.target.value){
                event.target.value = this.table[projectId][technologyId]
                return;
            }

            if(Number(event.target.value) === this.table[projectId][technologyId]){
                return;
            }

            axios.post('pm-planning', {
                project_id: projectId,
                technology_id: technologyId,
                period_type: 'week',
                period_number: this.filter.week,
                year: this.filter.year,
                hours: event.target.value
            }).then((response) => {
                this.table = response.data.table;
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

        async getWeekRange() {
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
        },

        async handleDiselect(){
            this.filter.project_ids = []
            await this.getData()
        },
    },
}
</script>
