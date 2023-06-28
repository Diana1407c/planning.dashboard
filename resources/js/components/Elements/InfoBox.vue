<template>
    <Modal
        v-model:visible="isOpen"
        :title="title"
    >
        <div class="modal-compare-element">
            <div v-if="stacks.length" class="col-12 p-0 d-flex align-items-center flex-column">
                <h4>Project Manager Planning</h4>
                <table class="table table-striped table-bordered planning-table">
                    <thead>
                    <tr>
                        <th class="w-8 text-center align-middle">Cost (€)</th>
                        <th class="w-10 text-center align-middle" v-for="stack in stacks">{{ stack.name }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr >
                        <td class="w-10 align-middle text-center cell-p">{{ cost }}</td>
                        <td class="w-10 align-middle text-center cell-p" v-for="stack in stacks">{{ pmPlanning[stack.id] }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="col-12 p-0 d-flex align-items-center flex-column">
                <h4 class="m-0">No plannings from PMs</h4>
            </div>
        </div>
        <div class="modal-compare-element">
            <div v-if="teams.length" class="col-12 p-0 d-flex align-items-center flex-column">
                <h4>Team Lead Planning</h4>
                <table class="table table-striped table-bordered planning-table">
                    <thead>
                    <tr>
                        <th class="w-5 vertical-text text-center align-middle">Team</th>
                        <th class="w-15 text-center align-middle">Members</th>
                        <th class="w-8 text-center align-middle">Planned</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-for="team in teams" :key="team.id">
                        <tr>
                            <td class="w-5 vertical-text text-center align-middle" :rowspan="team.members.length+1">{{ team.name }}</td>
                        </tr>
                        <tr v-for="member in team.members" >
                            <template v-if="tlPlanning[member.id]">
                                <td class="w-15 align-middle cell-p">{{ member.name }}</td>
                                <td class="w-8 align-middle text-center cell-p">{{ tlPlanning[member.id] }}</td>
                            </template>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
            <div v-else class="col-12 p-0 d-flex align-items-center flex-column">
                <h4 class="m-0">No plannings from TLs</h4>
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
        isOpen: {
            type: Boolean,
            default: false
        },
        close: Function
    },
    data() {
        return {
            year: this.date ? this.dateIndex.split("_")[0] : null,
            week: this.date ? this.dateIndex.split("_")[1] : null,

            teams: [],
            tlPlanning: [],
            stacks: [],
            cost: null,
            pmPlanning: [],
            loadingDetails: true
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
                    this.year = this.date ? this.dateIndex.split("_")[0] : null;
                    this.week = this.date ? this.dateIndex.split("_")[1] : null;
                    await this.getInfo()
                }
            },
            deep: true
        },
        date: {
            async handler(newValue) {
                if(newValue && !this.loadingDetails){
                    this.year = this.date ? this.dateIndex.split("_")[0] : null;
                    this.week = this.date ? this.dateIndex.split("_")[1] : null;
                    await this.getInfo()
                }
            }
        }
    },
    methods: {
        async getInfo(){
            this.loadingDetails = true;
            axios.get('reports/comparison/detail/'+this.project.id, {params: {
                    week: this.week,
                    year: this.year,
                }}).then((response) => {
                this.teams = response.data.teams
                this.tlPlanning = response.data.tl_planning
                this.stacks = response.data.stacks
                this.pmPlanning = response.data.pm_planning
                this.cost = response.data.cost
                this.loadingDetails = false;
            }).catch(() => {
                this.loadingDetails = false;
            })
        }
    }
}
</script>


