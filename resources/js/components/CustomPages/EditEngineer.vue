<template>
    <div>
        <div class="row mt-5">
            <div class="col-6">
                <h3>Performance</h3>
            </div>
            <div class="col-6 text-right">
                <button @click="setNewPerformance()" class="btn btn-primary" data-toggle="modal"
                        data-target="#performanceModal">
                    <span class="ladda-label"><i class="la la-plus"></i> Add Performance</span>
                </button>
            </div>
            <div class="col-12 mt-4">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Level</th>
                        <th>Level Performance</th>
                        <th>Project</th>
                        <th>Individual Performance</th>
                        <th>From Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="performance in performances">
                        <td>{{ performance.level.name }}</td>
                        <td>{{ performance.level.performance }}%</td>
                        <td>
                            <span v-if="performance.project">{{ performance.project.name }}</span>
                            <span v-else>-</span>
                        </td>
                        <td>
                            <span v-if="performance.performance">{{ performance.performance }}%</span>
                            <span v-else>-</span>
                        </td>
                        <td>{{ performance.from_date }}</td>
                        <td>
                            <button @click="setEditPerformance(performance)" data-toggle="modal"
                                    data-target="#performanceModal" class="btn btn-sm btn-link">
                                <i class="la la-edit"></i> Edit
                            </button>
                            <button @click="deletePerformance(performance.id)" class="btn btn-sm btn-link"
                                    data-button-type="delete">
                                <i class="la la-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <h3>History</h3>
            </div>
            <div class="col-12 mt-4">
                <div class="scroll-container">
                    <ul v-if="history_logs.length > 0">
                        <li v-for="log in history_logs">
                            <i>{{ log.label }}</i> <strong>{{ log.value }}</strong> <small>{{ log.created_at }}</small>
                        </li>
                    </ul>
                    <p v-else>No history available for this engineer.</p>
                </div>
            </div>
        </div>

        <div data-backdrop="false" class="modal fade" id="performanceModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="edit_performance.id" class="modal-title" id="exampleModalLabel">Edit Performance</h5>
                        <h5 v-else class="modal-title" id="exampleModalLabel">Add Performance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group col-sm-12">
                            <label>Level</label>
                            <select v-model="edit_performance.level_id" class="form-control" name="level">
                                <option :value="level.id" v-for="level in levels">{{ level.name }}</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Project</label>
                            <select v-model="edit_performance.project_id" class="form-control" name="project">
                                <option :value="project.id" v-for="project in projects">{{ project.name }}</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>Performance</label>
                            <input type="number" class="form-control" v-model="edit_performance.performance" min="0"
                                   max="100"/>
                        </div>
                        <div class="form-group col-sm-12 required">
                            <label>From Date</label>
                            <input type="date" name="date" v-model="edit_performance.from_date" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button @click="savePerformance()" type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from "./../Layout.vue";
import {useNotification} from "@kyvg/vue3-notification";
import errorMessages from "../../helpers";

const {notify} = useNotification()

export default {
    name: "EditEngineer",
    layout: (h, page) => h(Layout, [page]),
    props: {
        levels: Array,
        projects: Array,
        engineer: Object
    },
    data() {
        return {
            edit_performance: null,
            performances: [],
            history_logs: [],
        }
    },
    created() {
        this.getPerformances()
        this.setNewPerformance()
        this.getHistory()
    },
    methods: {
        setNewPerformance() {
            this.edit_performance = {
                id: null,
                level_id: null,
                project_id: null,
                from_date: null,
                performance: null,
            }
        },
        setEditPerformance(performance) {
            this.edit_performance = {
                id: performance.id,
                level_id: performance.level.id,
                project_id: performance.project.id,
                from_date: performance.from_date,
                performance: performance.performance,
            }
        },

        savePerformance() {
            if (this.edit_performance.id) {
                this.updatePerformance()
            } else {
                this.storePerformance()
            }
        },

        storePerformance() {
            axios.post(this.basePath() + 'performances', this.edit_performance).then((response) => {
                this.getPerformances();
                $('#performanceModal').modal('hide');
            }).catch((error) => {
                this.displayError(error);
            });
        },
        updatePerformance() {
            axios.patch(this.basePath() + 'performances/' + this.edit_performance.id, this.edit_performance).then((response) => {
                this.getPerformances();
                $('#performanceModal').modal('hide');
            }).catch((error) => {
                this.displayError(error);
            });
        },
        deletePerformance(id) {
            axios.delete(this.basePath() + 'performances/' + id).then((response) => {
                this.getPerformances()
            }).catch((error) => {
                this.displayError(error)
            });
        },

        getPerformances() {
            axios.get(this.basePath() + 'performances').then((response) => {
                this.performances = response.data.data
            }).catch((error) => {
                this.displayError(error)
            });
        },

        getHistory() {
            axios.get(this.basePath() + 'history').then((response) => {
                this.history_logs = response.data.data
            }).catch((error) => {
                this.displayError(error)
            });
        },

        basePath() {
            return 'engineer/' + this.engineer.id + '/';
        },

        displayError(error) {
            let messages = errorMessages(error.response);
            this.$notify({
                text: messages.join('<br>'),
                type: "error",
            });
        }
    },
}
</script>
