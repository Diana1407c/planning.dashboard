<template>
    <div class="row">
        <div class="col-3">
            <VueDatePicker v-model="filter.date" multi-calendars multi-calendars-solo range
                           @update:model-value="getData"/>
        </div>
        <div class="col-3">
            <multiselect
                v-model="filter.project_types"
                :options="projectTypes"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select types"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getData"
                @remove="getData">
                <template v-if="filter.project_types.length" #beforeList class="multiselect__element">
                    <span @click="handleDiselectTypes" class="multiselect__option diselect_all"><span>Diselect All</span></span>
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
                @select="getData"
                @remove="getData">
                <template v-if="filter.project_ids.length" #beforeList class="multiselect__element">
                    <span @click="handleDiselectProjects" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="col-3">
            <select v-model="filter.period_type" class="form-control" @change="getData">
                <option value="week">Weekly</option>
                <option value="month">Monthly</option>
            </select>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-12">
            <GChart
                type="LineChart"
                :options="options"
                :data="collectionData"
            />
        </div>
    </div>
</template>

<script>
import {GChart} from "vue-google-charts";
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
        projectTypes: Object,
        allProjects: Object,
    },
    data() {
        return {
            chartType: "LineChart",
            collectionData: [
                ["Period", "PM", "TL", "TW"],
            ],
            options: {
                title: 'Projects History Report',
                lineWidth: 2,
                titleTextStyle: {
                    fontSize: 26,
                },
                pointSize: 3,
                height: 600,
                vAxis: {
                    title: 'Hours(h)',
                    titleTextStyle: {
                        color: 'green',
                        bold: 'true',
                        fontSize: 18,
                    },
                },
                hAxis: {
                    title: "Period",
                    titleTextStyle: {
                        color: 'green',
                        bold: 'true',
                        fontSize: 18,
                    },
                },
            },
            filter: {
                project_ids: [],
                project_types: [],
                date: [new Date()],
                period_type: 'week',

            },
            loaded: false,
        };
    },
    async mounted() {
        await this.setFilter()
        await this.getData()
    },
    methods: {
        async handleDiselectProjects() {
            this.filter.project_ids = []
            await this.getData()
        },
        async handleDiselectTypes() {
            this.filter.project_types = []
            await this.getReport()
        },
        async getData() {
            localStorage.setItem("filter-statistics", JSON.stringify(this.filter));
            await this.getProjects()
            await this.getStatistics()
            this.loaded = true;
        },
        async getStatistics() {
            await axios.get('reports/statistics', {
                params: {
                    project_types: this.filter.project_types.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    start_date: this.filter.date[0],
                    end_date: this.filter.date[1],
                    period_type: this.filter.period_type,
                }
            }).then((response) => {
                this.collectionData = response.data
            });
        },
        async getProjects() {
            await axios.get('projects', {
                params: {
                    project_types: this.filter.project_types.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                }
            }).then((response) => {
                this.projects = response.data.data
            });
        },
        async setFilter() {
            const storedObject = localStorage.getItem("filter-statistics");
            if (storedObject) {
                let storageFilter = JSON.parse(storedObject);

                for (const key in this.filter) {
                    if (!storageFilter.hasOwnProperty(key)) {
                        storageFilter[key] = this.filter[key];
                    }
                }

                this.filter = storageFilter;
            }
        },
    },
};
</script>
