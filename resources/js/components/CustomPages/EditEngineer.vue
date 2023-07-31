<template>
    <div>
        <div class="row mt-5">
            <div class="col-6">
                <h3>Levels</h3>
            </div>
            <div class="col-6 text-right">
                <button @click="setNewLevel()" class="btn btn-primary" data-toggle="modal" data-target="#levelModal">
                    <span class="ladda-label"><i class="la la-plus"></i> Add Level</span>
                </button>
            </div>
            <div class="col-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th>From Date</th>
                        <th>Level</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="level in engineer_levels">
                        <td>{{ level.from_date }}</td>
                        <td>{{ level.level_name }}</td>
                        <td>
                            <button @click="setEditLevel(level)" data-toggle="modal" data-target="#levelModal" class="btn btn-sm btn-link">
                                <i class="la la-edit"></i> Edit
                            </button>
                            <button @click="deleteLevel(level.id)" class="btn btn-sm btn-link" data-button-type="delete">
                                <i class="la la-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-6">
                <h3>Performance</h3>
            </div>
            <div class="col-6 text-right">
                <button @click="setNewPerformance()" class="btn btn-primary" data-toggle="modal" data-target="#performanceModal">
                    <span class="ladda-label"><i class="la la-plus"></i> Add Performance</span>
                </button>
            </div>
            <div class="col-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th>From Date</th>
                        <th>Performance</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="performance in performances">
                        <td>{{ performance.from_date }}</td>
                        <td>{{ performance.performance }}%</td>
                        <td>
                            <button @click="setEditPerformance(performance)" data-toggle="modal" data-target="#performanceModal" class="btn btn-sm btn-link">
                                <i class="la la-edit"></i> Edit
                            </button>
                            <button @click="deletePerformance(performance.id)" class="btn btn-sm btn-link" data-button-type="delete">
                                <i class="la la-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div data-backdrop="false" class="modal fade" id="levelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="edit_level.id" class="modal-title" id="exampleModalLabel">Edit Level</h5>
                        <h5 v-else class="modal-title" id="exampleModalLabel">Add Level</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group col-sm-12">
                            <label>Level</label>
                            <select v-model="edit_level.level_id" class="form-control" name="level">
                                <option :value="level.id" v-for="level in levels">{{ level.name }}</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-12 required">
                            <label>From Date</label>
                            <input type="date" name="date" v-model="edit_level.from_date" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button @click="saveLevel()" type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div data-backdrop="false" class="modal fade" id="performanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <label>Performance</label>
                            <input type="number" class="form-control" v-model="edit_performance.performance" min="1" max="100" />
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
import { useNotification } from "@kyvg/vue3-notification";
import errorMessages from "../../helpers";

const { notify } = useNotification()

export default {
    name: "EditEngineer",
    layout: (h, page) => h(Layout, [page]),
    props: {
        levels: Array,
        engineer: Object
    },
    data() {
        return {
            edit_level: null,
            edit_performance: null,
            performances: [],
            engineer_levels: [],
        }
    },
    created() {
        this.getLevels()
        this.getPerformances()
        this.setNewLevel()
        this.setNewPerformance()
    },
    methods: {
        setNewLevel() {
            this.edit_level = {
                id: null,
                from_date: null,
                level_id: null,
            }
        },
        setEditLevel(engineer_level) {
            this.edit_level = {
                id: engineer_level.id,
                from_date: engineer_level.from_date,
                level_id: engineer_level.level_id,
            }
        },

        setNewPerformance() {
            this.edit_performance = {
                id: null,
                from_date: null,
                performance: null,
            }
        },
        setEditPerformance(performance) {
            this.edit_performance = {
                id: performance.id,
                from_date: performance.from_date,
                performance: performance.performance,
            }
        },

        saveLevel() {
            if (this.edit_level.id) {
                this.updateLevel()
            } else {
                this.storeLevel()
            }
        },

        storeLevel() {
            axios.post(this.basePath() + 'levels', this.edit_level).then((response) => {
                this.getLevels()
                $('#levelModal').modal('hide')
            }).catch((error) => {
                this.displayError(error)
            });
        },
        updateLevel() {
            axios.patch(this.basePath() + 'levels/' + this.edit_level.id, this.edit_level).then((response) => {
                this.getLevels()
                $('#levelModal').modal('hide')
            }).catch((error) => {
                this.displayError(error)
            });
        },
        deleteLevel(id) {
            axios.delete(this.basePath() + 'levels/' + id).then((response) => {
                this.getLevels()
            }).catch((error) => {
                this.displayError(error)
            });
        },

        getLevels() {
            axios.get(this.basePath() + 'levels').then((response) => {
                this.engineer_levels = response.data.data
            }).catch((error) => {
                this.displayError(error)
            });
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
                this.getPerformances()
                $('#performanceModal').modal('hide')
            }).catch((error) => {
                this.displayError(error)
            });
        },
        updatePerformance() {
            axios.patch(this.basePath() + 'performances/' + this.edit_performance.id, this.edit_performance).then((response) => {
                this.getPerformances()
                $('#performanceModal').modal('hide')
            }).catch((error) => {
                this.displayError(error)
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
