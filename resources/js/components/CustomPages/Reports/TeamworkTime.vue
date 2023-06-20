<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="row">
        <div class="pb-1 col-md-4 col-sm-6 col-12">
            <multiselect
                v-model="filter.stacks"
                :options="allStacks"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select stacks"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getLogging"
                @remove="getLogging"
                :disabled="filter.engineers.length || filter.technologies.length">
                <template v-if="filter.stacks.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselect('stacks')" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="pb-1 col-md-4 col-sm-6 col-12">
            <multiselect
                v-model="filter.technologies"
                :options="techOptions"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select technologies"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getLogging"
                @remove="getLogging"
                :disabled="filter.engineers.length">
                <template v-if="filter.technologies.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselect('technologies')" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="pb-1 col-md-4 col-sm-6 col-12">
            <multiselect
                v-model="filter.engineers"
                :options="engineersOptions"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select engineers"
                label="name"
                :multiple="true"
                track-by="name"
                @select="getLogging"
                @remove="getLogging">
                <template v-if="filter.engineers.length" #beforeList class="multiselect__element" >
                    <span @click="handleDiselect('engineers')" class="multiselect__option diselect_all"><span>Diselect All</span></span>
                </template>
            </multiselect>
        </div>
        <div class="pb-1 col-md-6 col-sm-6 col-12">
            <select v-model="filter.type" class="form-control" @change="getLogging">
                <option :value="null">All hours</option>
                <option :value="'billable'" selected>billable</option>
                <option :value="'non_billable'" selected>Non-billable</option>
            </select>
        </div>
        <div class="pb-1 col-md-6 col-sm-12 col-12">
            <VueDatePicker v-model="filter.dates" multi-calendars range :format="rangeDisplay" @update:model-value="getLogging"/>
        </div>
    </div>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
    <div class="table-responsive">
        <table v-if="loaded" class="table table-striped table-bordered planning-table">
            <thead>
            <tr>
                <th class="w-5 vertical-text text-center align-middle">Stack</th>
                <th class="w-5 vertical-text text-center align-middle">Technology</th>
                <th class="w-15 text-center align-middle">Engineer</th>
                <th v-for="date in dates" class="text-center align-middle">{{ date.formatted }}</th>
                <th class="w-15 text-center align-middle">Totals</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(stackGroup, stackId) in headingGroup" :key="stackId">
                <tr>
                    <td class="w-5 vertical-text text-center align-middle" :rowspan="rowspanStackTotal(stackGroup)">{{ stack(stackId) }}</td>
                </tr>
                <template v-for="(tech, techId) in stackGroup" :key="techId">
                    <tr>
                        <td class="w-5 vertical-text text-center align-middle" :rowspan="rowspanTechTotal(tech)">{{ technology(techId) }}</td>
                    </tr>
                    <tr v-for="(engineer, engineerId) in tech" :key="engineerId">
                        <td>{{ engineer }}</td>
                        <td v-for="date in dates" :class="'engineer-time-'+engineerId" class="w-15 text-center align-middle">{{ logging?.[date.index]?.[stackId]?.[techId]?.[engineerId] || 0 }}</td>
                        <td class="w-15 text-center align-middle grand-totals-cell" v-html="grandTotals['engineers'][engineerId]"></td>
                    </tr>
                    <tr>
                        <td class="text-center align-middle heading-tech-total">Total</td>
                        <td v-for="date in dates"  :class="'total-tech-'+techId" class="text-center align-middle cell-tech-total">{{ logging?.[date.index]?.[stackId]?.[techId]?.['total_tech'] || 0 }}</td>
                        <td class="w-15 text-center align-middle grand-totals-cell" v-html="grandTotals['tech'][techId]"></td>
                    </tr>
                </template>
                <tr>
                    <td class="text-center align-middle heading-stack-total" colspan="2">Total</td>
                    <td v-for="date in dates" :class="'total-stack-'+stackId" class="text-center align-middle cell-stack-total">{{ logging?.[date.index]?.[stackId]?.['total_stack'] || 0 }}</td>
                    <td class="w-15 text-center align-middle grand-totals-cell" v-html="grandTotals['stack'][stackId]"></td>
                </tr>
            </template>
            <tr class="total-row">
                <td class="text-center align-middle heading-total" colspan="3">Total</td>
                <td v-for="date in dates" class="text-center align-middle cell-total">{{ logging?.[date.index]?.['total_month'] || 0 }}</td>
                <td class="w-15 text-center align-middle grand-totals-cell" v-html="grandTotals['total']"></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

import multiselect from "vue-multiselect";
import VueDatePicker from "@vuepic/vue-datepicker";

export default {
    name: 'TeamworkTime',
    components: {VueDatePicker, multiselect},
    props: {
        allStacks: Object,
        allTechnologies: Object,
        allEngineers: Object
    },
    data(){
        return {
            dates: [],
            headingGroup: [],
            logging: [],
            grandTotals: [],
            loaded: false,
            filter: {
                engineers: [],
                stacks: [],
                technologies: [],
                dates: [
                    new Date((new Date).getFullYear(), (new Date).getMonth(), 2),
                    new Date((new Date).getFullYear(), (new Date).getMonth() + 1, 2)
                ],
                type: null,
            }
        }
    },
    computed: {
        techOptions(){
            if (this.filter.stacks.length === 0) {
                return this.allTechnologies;
            } else {
                const selectedIds = this.filter.stacks.map(ob => ob.id);
                return this.allTechnologies.filter(ob => selectedIds.includes(ob.stack_id));
            }
        },
        engineersOptions(){
            let engineersOptions = this.allEngineers
            if (this.filter.stacks.length !== 0) {
                const selectedIds = this.filter.stacks.map(ob => ob.id);
                engineersOptions = engineersOptions.filter(ob => selectedIds.includes(ob.stack_id));
            }

            if (this.filter.technologies.length !== 0) {
                const selectedIds = this.filter.technologies.map(ob => ob.id);
                engineersOptions = engineersOptions.filter(ob => selectedIds.includes(ob.technology_id));
            }

            return engineersOptions;
        }
    },
    async mounted() {
        await this.getLogging();
    },
    methods: {
        async getLogging(){
            await axios.get('reports/teamwork-time', {params: {
                    engineer_ids: this.filter.engineers.map(obj => obj.id),
                    stack_ids: this.filter.stacks.map(obj => obj.id),
                    technology_ids: this.filter.technologies.map(obj => obj.id),
                    start_date: this.filter.dates?.[0] || null,
                    end_date: this.filter.dates?.[1] || null,
                    type: this.filter.type
                }}).then((response) => {
                this.headingGroup = response.data.headingGroup
                this.dates = response.data.dates
                this.logging = response.data.logging
                this.grandTotals = response.data.grandTotals
                this.loaded = true;
            }).catch(() => {})
        },

        async handleDiselect(filterValue){
            this.filter[filterValue] = [];
            await this.getLogging();
        },

        technology(techId) {
            return this.allTechnologies.find((obj) => obj.id === Number(techId)).name;
        },

        stack(stackId) {
            return this.allStacks.find((obj) => obj.id === Number(stackId)).name;
        },

        rowspanStackTotal(stack) {
            let count = 0;
            for (const techId in stack) {
                count += Object.keys(stack[techId]).length;
            }
            return count + 2 + Object.keys(stack).length * 2;
        },

        rowspanTechTotal(tech) {
            return Object.keys(tech).length + 2;
        },

        rangeDisplay(dates){
            let startDate = dates[0]
            let endDate = dates[1]

            const start_month = startDate.toLocaleString('default', { month: 'long' });
            const start_year = startDate.getFullYear();

            const end_month = endDate.toLocaleString('default', { month: 'long' });
            const end_year = endDate.getFullYear();

            if(start_year !== end_year){
                return `${start_month} ${start_year} - ${end_month} ${end_year}`;
            }

            if(start_month !== end_month){
                return `${start_month} - ${end_month} ${end_year}`;
            }

            return `${start_month} ${start_year}`;
        }
    }
}
</script>
