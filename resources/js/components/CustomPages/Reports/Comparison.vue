<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-6">
            <VueDatePicker v-model="filter.date" multi-calendars multi-calendars-solo range @update:model-value="getReport"/>
        </div>
        <div class="col-6">
            <multiselect
                v-model="filter.project_ids"
                :options="allProjects"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select teams"
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
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="table-responsive">
        <table v-if="loaded" class="table table-striped table-bordered planning-table">
            <thead>
            <tr>
                <th colspan="2" rowspan="2" class="w-20 text-center align-middle">Projects</th>
                <template v-for="(date, index) in dates">
                    <th colspan="2" class="date-th text-center align-middle">{{ date }}</th>
                </template>
            </tr>
            <tr>
                <template v-for="date in dates">
                    <th class="plan-type-cell text-center align-middle">PM</th>
                    <th class="plan-type-cell text-center align-middle">TL</th>
                </template>
            </tr>
            </thead>
            <tbody>
            <tr v-for="project in projects">
                <td colspan="2" class="align-middle cell-p">{{ project.name }}</td>

                <template v-for="(date, index) in dates">
                    <td colspan="2" class="hours-compare-td date-th text-center align-middle p-0">
                        <div class="d-inline-block hours-pm plan-type-cell text-center align-middle cell-p">{{ report[project.id][index]['PM'] }}</div>
                        <div class="d-inline-block hours-tl plan-type-cell text-center align-middle cell-p">{{ report[project.id][index]['TL'] }}</div>
                        <div class="info-box">
                            <div class="info-arrow-wrapper">
                                <div class="info-arrow"></div>
                            </div>
                            <div class="info-box-content d-flex">
                                <div class="row">
                                    <div class="col-6">
                                        Open
                                    </div>
                                    <div class="col-6">
                                        Info
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </template>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker'
import multiselect from 'vue-multiselect';

export default {
    name: "Comparison",
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
                date: [new Date()]
            },
            loaded: false,
        }
    },
    components: {VueDatePicker, multiselect},
    async mounted() {
        await this.getReport();

        const cells = document.querySelectorAll('.hours-compare-td');

        cells.forEach((cell) => {
            cell.addEventListener('click', () => {
                cells.forEach((c) => {
                    if (c !== cell) {
                        c.classList.remove('active');
                    }
                });
                cell.classList.toggle('active');
            });
        });
    },
    methods: {
        async handleDiselect(){
            this.filter.project_ids = []
            await this.getReport()
        },

        async getReport(){
            await axios.get('reports/comparison', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    start_date: this.filter.date[0],
                    end_date: this.filter.date[1],
                }}).then((response) => {
                this.dates = response.data.dates
                this.projects = response.data.projects
                this.report = response.data.report
                this.loaded = true;
            });
        },
    }
}
</script>
