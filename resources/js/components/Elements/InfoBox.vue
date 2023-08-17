<template>
    <Modal v-model:visible="isOpen" :title="title" :onclose="onClose()" :okButton="this.refreshButton">
        <div v-if="!loadingDetails" class="modal-compare-element">
            <div v-if="technologies.length" class="col-12 p-0 d-flex align-items-center flex-column">
                <h4>Project Manager Planning</h4>
                <table class="table table-striped table-bordered planning-table">
                    <thead>
                    <tr>
                        <th class="w-10 text-center align-middle">Technology</th>
                        <th class="w-10 text-center align-middle">Planned</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="technology in technologies">
                        <td class="w-10 align-middle text-center cell-p">{{ technology.name }}</td>
                        <td class="w-10 align-middle text-center cell-p">{{ hours['pm'][technology.id] }}</td>
                    </tr>
                    <tr>
                        <td class="w-10 align-middle text-center cell-p">Total</td>
                        <td class="w-10 align-middle text-center cell-p">{{ hours['pm']['total'] }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="col-12 p-0 d-flex align-items-center flex-column">
                <h4 class="m-0">No plannings from PMs</h4>
            </div>
        </div>
        <div v-if="!loadingDetails" class="modal-compare-element">
            <div v-if="teams.length" class="col-12 p-0 d-flex align-items-center flex-column">
                <h4>Team Lead Planning</h4>
                <table class="table table-striped table-bordered planning-table">
                    <thead>
                    <tr>
                        <th class="w-5 vertical-text text-center align-middle">Team</th>
                        <th class="w-5 vertical-text text-center align-middle">Technology</th>
                        <th class="w-15 text-center align-middle">Members</th>
                        <th class="w-8 text-center align-middle">Planned</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="team in teams" :key="team.id">
                        <tr>
                            <td class="w-5 vertical-text text-center align-middle" :rowspan="team.members.length+1">{{ team.name }}</td>
                            <td :title="titleTechnologies(team.technologies)" class="w-5 vertical-text text-center align-middle" :rowspan="team.members.length+1">
                                {{ technologyList(team.technologies) }}
                            </td>
                        </tr>
                        <tr v-for="member in team.members" >
                            <template v-if="hours['tl'][member.id]">
                                <td class="w-15 align-middle cell-p">{{ member.name }}</td>
                                <td class="w-8 align-middle text-center cell-p">
                                    <span v-if="hours['tl'][member.id]">{{ hours['tl'][member.id] }}</span>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <tr>
                        <td colspan="3" class="w-15 align-middle cell-p">Total</td>
                        <td class="w-8 align-middle text-center cell-p">
                            {{ hours['tl']['total'] }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="col-12 p-0 d-flex align-items-center flex-column">
                <h4 class="m-0">No plannings from TLs</h4>
            </div>
        </div>
        <div v-if="!loadingDetails" class="modal-compare-element">
            <div v-if="tw_teams.length" class="col-12 p-0 d-flex align-items-center flex-column">
                <h4>Teamwork Hours</h4>
                <table class="table table-striped table-bordered planning-table">
                    <thead>
                    <tr>
                        <th class="w-5 vertical-text text-center align-middle">Team</th>
                        <th class="w-5 vertical-text text-center align-middle">Technology</th>
                        <th class="w-15 text-center align-middle">Members</th>
                        <th class="w-8 text-center align-middle">Billable</th>
                        <th class="w-8 text-center align-middle">Non Billable</th>
                        <th class="w-8 text-center align-middle">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="team in tw_teams" :key="team.id">
                        <tr>
                            <td class="w-5 vertical-text text-center align-middle" :rowspan="team.members.length+1">{{ team.name }}</td>
                            <td :title="titleTechnologies(team.technologies)" class="w-5 vertical-text text-center align-middle" :rowspan="team.members.length+1">
                                {{ technologyList(team.technologies) }}
                            </td>
                        </tr>
                        <tr v-for="member in team.members" >
                            <template v-if="hours['tw'][member.id]['total']">
                                <td class="w-15 align-middle cell-p">{{ member.name }}</td>
                                <td class="w-8 align-middle text-center cell-p">
                                    <span v-if="hours['tw'][member.id]['billable']">
                                        {{ hours['tw'][member.id]['billable'] }}
                                    </span>
                                </td>
                                <td class="w-8 align-middle text-center cell-p">
                                    <span v-if="hours['tw'][member.id]['no_billable']">
                                        {{ hours['tw'][member.id]['no_billable'] }}
                                    </span>
                                </td>
                                <td class="w-8 align-middle text-center cell-p">
                                    <span v-if="hours['tw'][member.id]['total']">
                                        {{ hours['tw'][member.id]['total'] }}
                                    </span>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <tr>
                        <td :colspan="3" class="w-15 align-middle cell-p">Total</td>
                        <td class="w-8 align-middle text-center cell-p">
                            {{ hours['tw']['total']['billable'] }}
                        </td>
                        <td class="w-8 align-middle text-center cell-p">
                            {{ hours['tw']['total']['no_billable'] }}
                        </td>
                        <td class="w-8 align-middle text-center cell-p">
                            {{ hours['tw']['total']['total'] }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="col-12 p-0 d-flex align-items-center flex-column">
                <h4 class="m-0">No hours from Teamwork</h4>
            </div>
        </div>
    </Modal>
</template>

<script>
import { Modal } from 'usemodal-vue3';

export default {
    name: "InfoBox",
    props: {
        project: {
            type: Number,
            default: null
        },
        dateIndex: {
            type: String,
            default: null
        },
        date: {
            type: String,
            default: null
        },
        period_type: {
            type: String,
            default: null
        },
        isOpen: {
            type: Boolean,
            default: false
        },
        close: Function
    },
    data() {
        return {
            year: this.dateIndex ? this.dateIndex.split("_")[0] : null,
            period_number: this.dateIndex ? this.dateIndex.split("_")[1] : null,
            teams: [],
            tw_teams: [],
            hours: [],
            technologies: [],
            loadingDetails: true,

            refreshButton: {
                text: 'Refresh',
                onclick: () => {
                    this.getInfo()
                },
                loading: false
            }
        }
    },
    computed:{
        title(){
            return this.project.name+' for '+this.date
        }
    },
    components: {Modal},
    async mounted() {
        await this.getInfo()
    },
    watch: {
        project: {
            async handler(newValue) {
                if(newValue && !this.loadingDetails){
                    this.year = this.dateIndex ? this.dateIndex.split("_")[0] : null;
                    this.period_number = this.dateIndex ? this.dateIndex.split("_")[1] : null;
                    await this.getInfo()
                }
            },
            deep: true
        },
        date: {
            async handler(newValue) {
                if(newValue && !this.loadingDetails){
                    this.year = this.dateIndex ? this.dateIndex.split("_")[0] : null;
                    this.period_number = this.dateIndex ? this.dateIndex.split("_")[1] : null;
                    await this.getInfo()
                }
            }
        }
    },
    methods: {
        onClose() {
            if (!this.isOpen) {
                this.close();
            }
        },
        async getInfo(){
            this.loadingDetails = true;
            axios.get('reports/comparison/detail/'+this.project.id, {params: {
                    period_number: this.period_number,
                    year: this.year,
                    period_type: this.period_type,
                }}).then((response) => {
                this.teams = response.data.teams
                this.tw_teams = response.data.tw_teams
                this.hours = response.data.hours
                this.technologies = response.data.technologies
                this.loadingDetails = false;
            }).catch(() => {
                this.loadingDetails = false;
            })
        },

        titleTechnologies(technologies) {
            if (technologies.length) {
                const namesArray = technologies.map(obj => obj.name);
                return namesArray.join(', ');
            }

            return '';
        },

        technologyList(technologies) {
            if (technologies.length === 0) {
                return ''
            }

            if (technologies.length <= 2) {
                return this.titleTechnologies(technologies)
            }

            return technologies[0]['name'] + ', ' + technologies[1]['name'] + '...';
        }
    }
}
</script>


