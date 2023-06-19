<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="col-6">
            <multiselect
                v-model="filter.projects"
                :options="allProjects"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select Projects"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getEngineers"
                @remove="getEngineers">
                <template v-if="filter.projects.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselectProjects" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="col-6 pb-1">
            <multiselect
                v-model="filter.teams"
                :options="allTeams"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select teams"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getEngineers"
                @remove="getEngineers">
                <template v-if="filter.teams.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselectTeams" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="col-3">
            <select v-model="filter.withPlanning" class="form-control" @change="getEngineers">
                <option :value="null" selected>All</option>
                <option :value="'without'" selected>No planning</option>
                <option :value="'with'" selected>With planning</option>
            </select>
        </div>
        <div class="col-3">
            <VueDatePicker v-model="filter.date" :disabled="!filter.withPlanning" multi-calendars multi-calendars-solo range @update:model-value="getEngineers"/>
        </div>
        <div class="col-3">
            <input v-model="filter.min_hours" :disabled="filter.withPlanning !== 'with'" class="form-control" placeholder="min planned hours" @blur="getEngineers">
        </div>
        <div class="col-3">
            <input v-model="filter.max_hours" :disabled="filter.withPlanning !== 'with'" class="form-control" placeholder="max planned hours" @blur="getEngineers">
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <table class="table table-striped table-bordered planning-table">
        <thead>
        <tr>
            <th class="text-center align-middle">Name</th>
            <th class="text-center align-middle">email</th>
            <th class="text-center align-middle">Username</th>
            <th class="text-center align-middle">Team</th>
            <th v-if="lastWithPlanning !== 'without'" class="text-center align-middle">Plannings</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="engineer in engineers">
            <td class="align-middle cell-p">{{ engineer.name }}</td>
            <td class="align-middle cell-p">{{ engineer.email }}</td>
            <td class="align-middle cell-p">{{ engineer.username }}</td>
            <td class="align-middle cell-p">{{ engineer.team_name }}</td>
            <td v-if="lastWithPlanning !== 'without'" class="align-middle text-center cell-p">
                <div v-if="engineer.plannings.total > 0" class="tooltip-wrapper">{{ engineer.plannings.total }}
                    <div  class="tooltip-content">
                        <div v-for="planning in engineer.plannings.details" class="d-flex justify-content-between">
                            <div class="no-wrap ms-auto">{{planning.project}}</div>
                            <div class="no-wrap ms-auto">{{planning.hours}}</div>
                        </div>
                    </div>
                </div>
                <div v-else>-</div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
import multiselect from "vue-multiselect";
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
    name: "Engineers",
    components: {VueDatePicker, multiselect},
    props: {
        allTeams: Object,
        allProjects: Object
    },
    data() {
        return {
            filter: {
                projects: [],
                teams: [],
                date: null,
                withPlanning: 'without',
                min_hours: null,
                max_hours: null
            },
            engineers: [],
            lastWithPlanning: 'without',
        }
    },
    async mounted() {
          await this.getEngineers();
    },
    methods: {
        async handleDiselectProjects(){
            this.filter.projects = []
            await this.getEngineers()
        },

        async handleDiselectTeams(){
            this.filter.teams = []
            await this.getEngineers()
        },

        async getEngineers(){
            await axios.get('reports/engineers', {params: {
                    team_ids: this.filter.teams.map(obj => obj.id),
                    project_ids: this.filter.projects.map(obj => obj.id),
                    start_date: this.filter.date ? this.filter.date[0] : null,
                    end_date: this.filter.date ? this.filter.date[1] : null,
                    with_planning: this.filter.withPlanning,
                    min_hours: this.filter.min_hours,
                    max_hours: this.filter.max_hours
                }}).then((response) => {
                    this.lastWithPlanning = this.filter.withPlanning
                    this.engineers = response.data.data
            }).catch(() => {})
        }
    }
}
</script>
