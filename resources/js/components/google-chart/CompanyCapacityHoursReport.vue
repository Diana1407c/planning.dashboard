<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="pb-1 col-md-3 col-sm-6 col-12">
            <VueDatePicker v-model="filter.date" multi-calendars multi-calendars-solo range
                           @update:model-value="getData"/>
        </div>
        <div class="pb-1 col-md-3 col-sm-6 col-12">
            <select v-model="filter.period_type" class="form-control" @change="getData">
                <option value="week">Weekly</option>
                <option value="month">Monthly</option>
            </select>
        </div>
        <div class="pb-1 col-md-3 col-sm-6 col-12">
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
        <div class="pb-1 col-md-3 col-sm-6 col-12">
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
    name: "CompanyCapacityHoursReport",
    components: {
        VueDatePicker,
        multiselect,
        GChart
    },
    props: {
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
                ["Project", "Capacity Hours"],
            ],
            options: {
                title: 'Company Capacity Hours Report',
                titleTextStyle: {
                    fontSize: 26,
                },
                pieSliceText: 'percentage',
                height: 700,
                chartArea: {
                    height: '85%'
                },
                is3D: 'true',
            },
            filter: {
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
            localStorage.setItem("filter-capacity", JSON.stringify(this.filter));
            await this.getPieChart()
            this.loaded = true;
        },
        async getPieChart() {
            await axios.get('reports/capacity', {
                params: {
                    start_date: this.filter.date ? this.filter.date[0] : null,
                    end_date: this.filter.date ? this.filter.date[1] : null,
                    period_type: this.filter.period_type,
                    team_ids: this.filter.team_ids.map(obj => obj.id),
                    engineer_ids: this.filter.engineer_ids.map(obj => obj.id),
                }
            }).then((response) => {
                console.log(response.data);
                this.pieData = response.data;
            });
        },

        async setFilter() {
            const storedObject = localStorage.getItem("filter-capacity");
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
