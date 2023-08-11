<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-4">
            <VueDatePicker v-model="filter.date" multi-calendars multi-calendars-solo range @update:model-value="getReport"/>
        </div>
        <div class="col-4">
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
                    <span @click="handleDiselect" class="multiselect__option diselect_all"><span>Diselect All</span></span>
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
            <button type="button" class="btn btn-primary w-100" @click="filterChart"><i class="fa-solid fa-filter"></i> Filter Chart </button>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div>
        <GoogleChart></GoogleChart>
    </div>
    <info-box v-if="detailOpened" :is-open="detailOpened" :project="projectModal" :period_type="filter.period_type" :date="dateModal" :dateIndex="dateIndexModal" :close="closeModal"></info-box>
</template>

<script>
import GoogleChart from "../../google-chart/GoogleChart.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import InfoBox from "../../Elements/InfoBox.vue";
import multiselect from "vue-multiselect";

export default {
    name: "ProjectHistoryReport",
    props: {
        allProjects: Object
    },
    data(){
        return {
            projects: [],
            dates: [],
            report: [],
            filter: {
                project_ids: [],
                date: [new Date()],
                period_type: 'week'
            },
            detailOpened: false,
            projectModal: null,
            dateModal: null,
            dateIndexModal: null,
            loaded: false,
        }
    },
    components: {multiselect, InfoBox, VueDatePicker, GoogleChart },
    async mounted() {
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

        async handleDiselect() {
            this.filter.project_ids = []
            await this.getReport()
        },

        async getReport() {
            await axios.get('reports/history', {params: {
                project_ids: this.filter.project_ids.map(obj => obj.id),
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
    }
};
</script>
