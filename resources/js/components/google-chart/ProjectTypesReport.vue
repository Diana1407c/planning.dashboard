<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="pb-1 col-md-4 col-sm-6 col-12">
            <VueDatePicker v-model="filter.date" multi-calendars multi-calendars-solo range
                           @update:model-value="getData"/>
        </div>
        <div class="pb-1 col-md-4 col-sm-6 col-12">
            <select v-model="filter.period_type" class="form-control" @change="getData">
                <option value="week">Weekly</option>
                <option value="month">Monthly</option>
            </select>
        </div>
        <div class="pb-1 col-md-4 col-sm-6 col-12">
            <multiselect
                v-model="filter.project_states"
                :options="projectStates"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select states"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getData"
                @remove="getData">
                <template v-if="filter.project_states.length" #beforeList class="multiselect__element">
                    <span @click="handleDiselect('project_states')" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="pb-1 col-md-4 col-sm-6 col-12">
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
                    <span @click="handleDiselect('project_ids')" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="pb-1 col-md-4 col-sm-6 col-12">
            <multiselect
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
                <template v-if="filter.team_ids.length" #beforeList class="multiselect__element">
                    <span @click="handleDiselect('team_ids')"
                          class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="pb-1 col-md-4 col-sm-6 col-12">
            <multiselect
                v-model="filter.engineer_ids"
                :options="allEngineers"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select engineers"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getData"
                @remove="getData">
                <template v-if="filter.engineer_ids.length" #beforeList class="multiselect__element">
                    <span @click="handleDiselect('engineer_ids')" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-12">
            <GChart
                :type="chartType"
                :options="options"
                :data="pieData"
            />
        </div>
    </div>
</template>

<script>
import {GChart} from "vue-google-charts";
import multiselect from "vue-multiselect";
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
    name: "ProjectsTypesReport",
    components: {
        VueDatePicker,
        multiselect,
        GChart
    },
    props: {
        projectStates: Object,
        allProjects: Object,
        allTeams: Object,
        allEngineers: Object,
    },
    data() {
        const endDate = new Date();
        const startDate = new Date();
        startDate.setMonth(startDate.getMonth() - 3);
        return {
            chartType: "PieChart",
            pieData: [
                ["Project Type", "Hours"],
            ],
            options: {
                title: 'Project Types Report',
                titleTextStyle: {
                    fontSize: 26,
                },
                pieSliceText: 'percentage',
                height: 500,
                chartArea: {
                    height: '85%'
                },
                is3D: 'true',
            },
            filter: {
                project_states: [],
                project_types: [],
                project_ids: [],
                team_ids: [],
                engineer_ids: [],
                date: [startDate, endDate],
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
        async handleDiselect(filterValue) {
            this.filter[filterValue] = [];
            await this.getReport();
        },

        async getData() {
            localStorage.setItem("filter-pie", JSON.stringify(this.filter));
            await this.getProjects()
            await this.getPieChart()
            this.loaded = true;
        },

        async getProjects() {
            await axios.get('projects', {
                params: {
                    project_states: this.filter.project_states.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                }
            }).then((response) => {
                this.projects = response.data.data
            });
        },

        async getPieChart() {
            await axios.get('reports/pie', {
                params: {
                    start_date: this.filter.date ? this.filter.date[0] : null,
                    end_date: this.filter.date ? this.filter.date[1] : null,
                    period_type: this.filter.period_type,
                    project_states: this.filter.project_states.map(obj => obj.id),
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    team_ids: this.filter.team_ids.map(obj => obj.id),
                    engineer_ids: this.filter.engineer_ids.map(obj => obj.id),
                }
            }).then((response) => {
                console.log(response.data);
                this.pieData = response.data;
            });
        },

        async setFilter() {
            const storedObject = localStorage.getItem("filter-pie");
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
    }
};
</script>
