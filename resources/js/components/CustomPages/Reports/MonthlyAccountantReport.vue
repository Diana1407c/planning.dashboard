<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-5">
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
        <div class="col-5">
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
        <div class="col-2">
            <button type="button" class="btn btn-primary w-100" @click="exportXls()"><i class="fa-solid fa-download"></i> Export</button>
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
                <th class="w-20 vertical-text text-center align-middle">Engineers</th>
                <th class="vertical-text text-center align-middle">Performance</th>
                <th class="vertical-text text-center align-middle">Team</th>
                <th class="vertical-text text-center align-middle">Technology</th>
                <th class="vertical-text text-center align-middle">Seniority</th>

                <template v-for="state in status_projects">
                    <th v-if="projects[state].length > 0" class="vertical-text text-center align-middle" v-for="project in projects[state]">
                        {{ project.name }}
                    </th>
                    <th class="heading-total vertical-text text-center align-middle">{{ state }}</th>
                </template>
                <th class="heading-total vertical-text text-center align-middle">Total Planned</th>
                <th class="vertical-text text-center align-middle">Unplanned</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="engineer in engineers">
                <td class="align-middle cell-p">{{ engineer.name }} </td>
                <td class="align-middle cell-p">{{ engineer.performance }}%</td>
                <td class="align-middle cell-p">{{ engineer.team.name }}</td>
                <td class="align-middle cell-p">{{ engineer.team.technology }}</td>
                <td class="align-middle cell-p">
                    <span v-if="engineer.level">{{ engineer.level.name }}</span>
                </td>

                <template v-for="state in status_projects">
                    <td v-if="projects[state].length > 0" class="text-center align-middle" v-for="project in projects[state]">
                        <span v-if="table['engineers'][engineer.id][project.id] !==0">
                            {{ table['engineers'][engineer.id][project.id] }}
                        </span>
                    </td>
                    <td class="heading-total text-center align-middle">{{ table['engineers'][engineer.id]['total'][state] }}</td>
                </template>
                <td class="heading-total text-center align-middle">{{ table['engineers'][engineer.id]['total']['all'] }}</td>
                <td class="text-center align-middle">{{ table['engineers'][engineer.id]['total']['unplanned'] }}</td>
            </tr>
            <tr class="heading-total">
                <td colspan="5" class="align-middle cell-p">Totals</td>

                <template v-for="state in status_projects">
                    <td v-if="projects[state].length > 0" class="text-center align-middle" v-for="project in projects[state]">
                        {{ table['projects'][project.id] }}
                    </td>
                    <td class="text-center align-middle">{{ table['projects'][state] }}</td>
                </template>
                <td class="text-center align-middle">{{ table['projects']['all'] }}</td>
                <td class="text-center align-middle">{{ table['projects']['unplanned'] }}</td>
            </tr>
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
import Layout from "./../../Layout.vue";
import VueMultiselect from 'vue-multiselect';
import Color from "../../Elements/Color.vue";

export default {
    name: "MonthlyAccountantReport",
    layout: (h, page) => h(Layout, [page]),
    props: {
        allTeams: Object,
        allProjects: Object,
        status_projects: Array,
    },
    mixins: [
        Color
    ],
    data(){
        return {
            projects: [],
            engineers: [],
            table: [],
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
        const storedObject = localStorage.getItem("filter-ac-monthly");
        if (storedObject) {
            this.filter = JSON.parse(storedObject);
        }
        await this.setNextMonth()
        await this.getData()
    },
    methods: {
        async getData(){
            localStorage.setItem("filter-ac-monthly", JSON.stringify(this.filter));
            await this.getProjects()
            await this.getEngineers()
            await this.getPlannings()
            this.loaded = true;
        },

        async getProjects(){
            await axios.get('projects', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                }}).then((response) => {

                this.status_projects.forEach(element => {
                    this.projects[element] = []
                });
                response.data.data.forEach(project => {
                    this.projects[project.state].push(project)
                })
            });
        },

        async getEngineers(){
            await axios.get('reports/engineers/accountant', {params: {
                    team_ids: this.filter.team_ids.map(obj => obj.id),
            }}).then(response => {
                this.engineers = response.data.data;
            }).catch(() => {});
        },

        async getPlannings(){
            await axios.get('reports/accountant', {params: {
                    team_ids: this.filter.team_ids.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    year: this.filter.year,
                    period_number: this.filter.month,
                }}).then(response => {
                this.table = response.data.table;
                this.hours_count = response.data.hours_count
            }).catch(() => {});
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
        },

        async exportXls(){
            await axios.get('reports/accountant/export', {
                responseType: "blob",
                params: {
                    team_ids: this.filter.team_ids.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    year: this.filter.year,
                    period_number: this.filter.month,
                }
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'monthly_planning.xlsx');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }).catch(() => {})
        }
    },
}
</script>
