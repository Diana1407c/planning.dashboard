<template>
    <div>
        <label>Technologies</label>
        <VueMultiselect
            v-model="selected"
            :options="technologies"
            :close-on-select="true"
            :clear-on-select="false"
            placeholder="Select one"
            label="name"
            :multiple="true"
            track-by="name"
        />
        <label>Technologies2</label>
        <VueMultiselect
            v-model="selected"
            :options="technologies"
            :close-on-select="true"
            :clear-on-select="false"
            placeholder="Select one"
            label="name"
            :multiple="true"
            track-by="name"
        />
        <input type="hidden" name="technologies" v-model="technologies_ids">
    </div>
</template>

<script>
import VueMultiselect from 'vue-multiselect'
export default {
    components: { VueMultiselect },
    props: ['technologies', 'selectedProps'],
    data () {
        return {
            selected: [],
            technologies_ids: [],
        }
    },
    watch:{
        selected(newValue){
            this.technologies_ids = newValue.map(obj => obj.id);
        }
    },
    mounted() {
        if(this.selectedProps){
            this.selected = this.selectedProps
        }
    },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
