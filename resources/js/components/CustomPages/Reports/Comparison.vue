<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-3">
            <VueDatePicker v-model="filter.date" multi-calendars multi-calendars-solo range @update:model-value="getReport"/>
        </div>
        <div class="col-2">
            <multiselect
                v-model="filter.project_states"
                :options="projectStates"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select states"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getReport"
                @remove="getReport">
                <template v-if="filter.project_states.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselectStates" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="col-3">
            <multiselect
                v-model="filter.project_ids"
                :options="allProjects"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select projects"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getReport"
                @remove="getReport">
                <template v-if="filter.project_ids.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselectProjects" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="col-2">
            <select v-model="filter.period_type" class="form-control" @change="getReport">
                <option value="week">Weekly</option>
                <option value="month">Monthly</option>
            </select>
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-primary w-100" @click="exportComparison"><i class="fa-solid fa-download"></i> Export</button>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="sticky-table">
        <table v-if="loaded" class="table table-striped table-bordered planning-table">
            <thead class="sticky-top">
            <tr>
                <th colspan="2" rowspan="2" class="w-20 text-center align-middle">Projects</th>
                <template v-for="(date, index) in dates">
                    <th colspan="3" class="date-th text-center align-middle">{{ date }}</th>
                </template>
            </tr>
            <tr>
                <template v-for="date in dates">
                    <th class="plan-type-cell text-center align-middle">PM</th>
                    <th class="plan-type-cell text-center align-middle">TL</th>
                    <th class="plan-type-cell text-center align-middle">TW</th>
                </template>
            </tr>
            </thead>
            <tbody>
            <tr v-for="project in projects">
                <td colspan="2" class="align-middle cell-p">{{ project.name }}</td>
                <template v-for="(date, index) in dates">
                    <td colspan="3" class="hours-compare-td date-th text-center align-middle p-0" @click="openModal(project, index, date)">
                        <div class="d-inline-block hours-pm plan-type-cell text-center align-middle d-table-cell">
                            {{ report[project.id][index]['PM'] }}
                        </div>
                        <div :class="setColorHour(report[project.id][index]['PM'], report[project.id][index]['TL'])" class="d-inline-block hours-tl plan-type-cell text-center align-middle d-table-cell">
                            {{ report[project.id][index]['TL'] }}
                        </div>
                        <div :class="setColorHourDateString(report[project.id][index]['PM'], report[project.id][index]['TM'], index, this.filter.period_type)" class="d-inline-block hours-tm plan-type-cell text-center align-middle d-table-cell">
                            {{ report[project.id][index]['TM'] }}
                        </div>
                    </td>
                </template>
            </tr>
            </tbody>
        </table>
    </div>
    <info-box v-if="detailOpened" :is-open="detailOpened" :project="projectModal" :period_type="filter.period_type" :date="dateModal" :dateIndex="dateIndexModal" :close="closeModal"></info-box>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker'
import multiselect from 'vue-multiselect';
import InfoBox from '../../Elements/InfoBox.vue';
import Color from "../../Elements/Color.vue";
export default {
    name: "Comparison",
    props: {
        allProjects: Object,
        projectStates: Object
    },
    mixins: [Color],
    data(){
        return {
            projects: [],
            dates: [],
            report: [],
            filter: {
                project_ids: [],
                project_states: [],
                date: [new Date()],
                period_type: 'month'
            },
            detailOpened: false,
            projectModal: null,
            dateModal: null,
            dateIndexModal: null,
            loaded: false,
        }
    },
    components: {VueDatePicker, multiselect, InfoBox},
    async mounted() {
        const storedObject = localStorage.getItem("filter-comparison");
        if (storedObject) {
            this.filter = JSON.parse(storedObject);
        }

        await this.getReport()
    },

    methods: {
        openModal(project, dateIndex, date) {
            this.projectModal = project
            this.dateIndexModal = dateIndex
            this.dateModal = date
            this.detailOpened = true;
        },

        closeModal() {
            this.detailOpened = false;
            this.projectModal = null;
            this.dateIndexModal = null;
            this.dateModal = null;
        },

        async handleDiselectProjects(){
            this.filter.project_ids = []
            await this.getReport()
        },

        async handleDiselectStates(){
            this.filter.project_states = []
            await this.getReport()
        },

        async getReport(){
            localStorage.setItem("filter-comparison", JSON.stringify(this.filter));
            await axios.get('reports/comparison', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    project_states: this.filter.project_states.map(obj => obj.id),
                    start_date: this.filter.date[0],
                    end_date: this.filter.date[1],
                    period_type: this.filter.period_type
                }}).then((response) => {
                this.dates = response.data.dates
                this.projects = response.data.projects
                this.report = response.data.report
                this.loaded = true;
            });
        },

        async exportComparison(){
            await axios.get('reports/comparison/export', {
                responseType: "blob",
                params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    project_states: this.filter.project_states.map(obj => obj.id),
                    start_date: this.filter.date[0],
                    end_date: this.filter.date[1],
                    period_type: this.filter.period_type
                }
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'comparison.xlsx');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }).catch(() => {})
        }
    }
}
</script>
