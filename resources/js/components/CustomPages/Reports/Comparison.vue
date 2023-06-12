<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-4">
            <VueDatePicker v-model="filter.date">

            </VueDatePicker>
        </div>
        <div class="col-4">
            <multiselect
                v-model="filter.team_ids"
                :options="allTeams"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select teams"
                label="name"
                :multiple="true"
                track-by="name">
                <template v-if="filter.team_ids.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselectTeams" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
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
                track-by="name">
                <template v-if="filter.project_ids.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselectProjects" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
    </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker'
import multiselect from 'vue-multiselect';

export default {
    name: "Comparison",
    props: {
        allTeams: Object,
        allProjects: Object
    },
    data(){
        return {
            projects: [],
            report: [],
            filter: {
                team_ids: [],
                project_ids: [],
                date: null
            },
            loaded: false,
        }
    },
    components: {VueDatePicker, multiselect},
    methods: {
        async handleDiselectProjects(){
            this.filter.project_ids = []
            await this.getData()
        },

        async handleDiselectTeams(){
            this.filter.team_ids = []
            await this.getData()
        },

        async getReport(){
            await axios.get('reports/comparison', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    team_ids: this.filter.team_ids.map(obj => obj.id),
                    start_date: this.filter.date.value[0],
                    end_date: this.filter.date.value[1],
                }}).then((response) => {

                this.report = response.data.report
            });
        },
    }
}
</script>
