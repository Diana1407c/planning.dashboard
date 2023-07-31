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
            <div class="week-inscription">{{ month_name }} {{ this.filter.year }} - {{ hours_count }} hours</div>
        </div>
        <div class="col-4 d-flex justify-content-end">
            <button :disabled="!loaded" type="button" class="btn btn-primary date-change-button" @click="handleDateInput(true)">Next <i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="sticky-table">
        <table v-if="loaded" class="table table-striped table-bordered planning-table">
            <thead class="sticky-top">
            <tr>
                <th class="w-5 vertical-text text-center align-middle">State</th>
                <th class="w-20 text-center align-middle">Projects</th>
                <th class="w-8 vertical-text text-center align-middle heading-tech-total">Total</th>
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
                    <td class="w-20 align-middle cell-p left_sticky">{{ project.name }}</td>
                    <td class="w-8 align-middle cell-p text-center heading-tech-total">{{ table[project.id]['total'] }}</td>
                    <td class="w-8 align-middle cell-p" v-for="technology in technologies">
                        <input :disabled="!can_edit" type="number" class="form-control text-center no-arrows" :value="table[project.id][technology.id]" @blur="plan($event, project.id, technology.id)">
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
import Multiselect from 'vue-multiselect';
import { useNotification } from "@kyvg/vue3-notification";
import errorMessages from "../../helpers";

const { notify } = useNotification()

export default {
    name: "MonthlyPMPlanning",
    layout: (h, page) => h(Layout, [page]),
    props: {
        technologies: Object,
        allProjects: Object
    },
    data(){
        return {
            groupedProjects: [],
            table: [],
            can_edit: true,
            prices: [],
            month_name: null,
            filter: {
                project_ids: [],
                month: null,
                year: null
            },
            loaded: false,
            hours_count: 0,
        }
    },
    components: {Multiselect},
    async mounted() {
        const storedObject = localStorage.getItem("monthly-filter-pm");
        if (storedObject) {
            this.filter = JSON.parse(storedObject);
        }
        await this.setNextMonth()
        await this.getData()
    },
    methods: {
        async getData(){
            localStorage.setItem("monthly-filter-pm", JSON.stringify(this.filter));
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
            await axios.get('pm-planning/monthly', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    year: this.filter.year,
                    period_number: this.filter.month
                }}).then(response => {
                this.table = response.data.table;
                this.can_edit = response.data.can_edit
                this.hours_count = response.data.hours_count
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

        async handleDiselect(){
            this.filter.project_ids = []
            await this.getData()
        },
    },
}
</script>
