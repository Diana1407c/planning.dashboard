<script>
import Layout from "./../Layout.vue";
import Multiselect from 'vue-multiselect';
import { useNotification } from "@kyvg/vue3-notification";
import errorMessages from "../../helpers";

const { notify } = useNotification()

export default {
    name: "BasePlanning",
    layout: (h, page) => h(Layout, [page]),
    data(){
        return {
            table: [],
            filter: {},
            loaded: false,
            can_edit: true,
            filter_name: 'planning-filter'
        }
    },
    components: {Multiselect},
    async mounted() {

        await this.setNextMonth()
        await this.getData()
    },
    methods: {
        setFilter() {
            const storedObject = localStorage.getItem(this.filter_name);
            if (storedObject) {
                this.filter = JSON.parse(storedObject);
            }
        },

        async getPlannings(filter) {
            await axios.get('pm-planning/monthly', {params: {
                    project_ids: this.filter.project_ids.map(obj => obj.id),
                    year: this.filter.year,
                    period_number: this.filter.month
                }}).then(response => {
                this.table = response.data.table;
            }).catch(() => {});
        },

        plan(event, projectId, technologyId){
            if(!event.target.value){
                event.target.value = this.table[projectId][technologyId]
                return;
            }

            if(Number(event.target.value) === this.table[projectId][technologyId]){
                return;
            }

            axios.post('pm-planning', {
                project_id: projectId,
                technology_id: technologyId,
                period_type: 'month',
                period_number: this.filter.month,
                year: this.filter.year,
                hours: event.target.value
            }).then((response) => {
                this.table = response.data.table
                this.$notify(response.data.message);
            }).catch((error) => {
                let messages = errorMessages(error.response);
                this.$notify({
                    text: messages.join('<br>'),
                    type: "error",
                });
            });
        },

        async setNextMonth(){
            let now = new Date();
            if (now.getMonth() === 11) {
                this.filter.month = 1
                this.filter.year = now.getFullYear() + 1
            } else {
                this.filter.month = now.getMonth() + 2
                this.filter.year = now.getFullYear()
            }

            await this.setMonthName()
        },

        async setMonthName() {
            let date = new Date(this.filter.year, this.filter.month - 1, 1);
            this.month_name = date.toLocaleString('en-US', { month: 'long' });
        },

        async handleDateInput(nextMonth){
            this.loaded = false;
            if(nextMonth) {
                if(this.filter.month === 12){
                    this.filter.month = 1
                    this.filter.year += 1
                } else {
                    this.filter.month += 1
                }
            } else {
                if(this.filter.month === 1){
                    this.filter.month = 12
                    this.filter.year -= 1
                } else {
                    this.filter.month -= 1
                }
            }

            await this.setMonthName()
            await this.getData()
            this.loaded = true;
        },

        async handleDiselect(){
            this.filter.project_ids = []
            await this.getData()
        },
    },
}
</script>
