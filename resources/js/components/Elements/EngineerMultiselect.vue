<template>
    <div>
        <label>Members</label>
        <VueMultiselect
            v-model="selected"
            :options="engineers"
            :close-on-select="true"
            :clear-on-select="false"
            placeholder="Select one"
            label="name"
            :multiple="true"
            track-by="name"
            @remove="beforeClose"
        />
        <input type="hidden" name="members" v-model="members">
    </div>
</template>

<script>
import VueMultiselect from 'vue-multiselect'
export default {
    components: { VueMultiselect },
    props: ['engineers', 'selectedProps'],
    data () {
        return {
            selected: [],
            members: null,
            teamLeadId: null
        }
    },
    mounted() {
        const outsideInput = document.querySelector('select[name="team_lead_id"]');
        console.log(outsideInput.value);
        this.teamLeadId = outsideInput.value;

        outsideInput.addEventListener('input', (event) => {
            const newValue = event.target.value;
            const itemToAdd = this.engineers.find((item) => item.id === parseInt(newValue, 10));
            console.log(itemToAdd)
            if (itemToAdd) {
                const exists = this.selected.some((secondItem) => secondItem.id === parseInt(newValue, 10));
                if (!exists) {
                    this.selected.push(itemToAdd);
                    this.members = this.selected.map(obj => obj.id).join('|');
                }
            }

            this.teamLeadId = newValue;
        });

        if(this.selectedProps){
            this.selected = this.selectedProps
        }
    },
    watch:{
        selected(newValue){
            this.members = newValue.map(obj => obj.id).join('|');
        }
    },
    methods: {
        beforeClose(engineer) {
            if (engineer.id === parseInt(this.teamLeadId, 10)) {
                this.selected.push(
                    this.engineers.find((item) => item.id === parseInt(engineer.id, 10))
                );
            }
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
