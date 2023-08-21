<template>
    <div class="row">
        <div class="col-4">
            <VueDatePicker  v-model="filter.date"  multi-calendars multi-calendars-solo range @update:model-value="getData"/>
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
                @select="getData"
                @remove="getData">
                <template v-if="filter.project_ids.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselect" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="col-4">
            <select v-model="filter.period_type" class="form-control" @change="getData">
                <option value="week">Weekly</option>
                <option value="month">Monthly</option>
            </select>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div v-if="filter.project_ids.length"  class="col-8">
        <GChart
            type="LineChart"
            :options="options"
            :data="collectionData"
        />
    </div>
</template>

<script>
import { GChart } from "vue-google-charts";
import VueDatePicker from "@vuepic/vue-datepicker";
import multiselect from "vue-multiselect";
export default {
    name: 'HistoryGoogleChart',
    components: {
        multiselect,
        VueDatePicker,
        GChart,
    },
    props: {
        allProjects: Object,
    },
    data() {
        return {
            chartType:"LineChart",
            collectionData: [
                ["Period", "PM", "TL", "TW"],
            ],
            options: {
                title: 'Project History Report',
                lineWidth: 2,
                titleTextStyle:{
                    fontSize: 26,
                },
                pointSize: 3,
                height: 600,
                vAxis: {
                    title:'Hours(h)',
                    titleTextStyle:{
                        color:'green',
                        bold: 'true',
                        fontSize: 18,
                    },
                },
                hAxis: {
                    title: "Period",
                    titleTextStyle:{
                        color:'green',
                        bold: 'true',
                        fontSize: 18,
                    },
                },
            },
            filter: {
                project_ids: [],
                date: [new Date()],
                period_type: 'week'
            },
            loaded: false,
        };
    },
    async mounted() {
        await this.getData()
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
        async handleDiselect(){
            this.filter.project_ids = []
            await this.getData()
        },
        async getData(){
            await axios.get('reports/statistics', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    start_date: this.filter.date[0],
                    end_date: this.filter.date[1],
                    period_type: this.filter.period_type
                }}).then((response) => {
                this.collectionData = response.data
            });
        },
    }
};
</script>
