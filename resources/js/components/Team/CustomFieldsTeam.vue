<template>
    <div>
        <div class="mb-3">
            <label>Members</label>
            <VueMultiselect
                v-model="m_selected"
                :options="engineers"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select engineers"
                label="name"
                :multiple="true"
                track-by="name"
            />
            <input v-for="member_id in members_ids" type="hidden" name="members[]" :value="member_id">
        </div>
        <div class="mb-3">
            <label>Technologies</label>
            <VueMultiselect
                v-model="t_selected"
                :options="technologies"
                :close-on-select="true"
                :clear-on-select="false"
                placeholder="Select technologies"
                label="name"
                :multiple="true"
                track-by="name"
            />
            <input type="hidden" v-for="technology in technologies_ids" name="technologies[]" :value="technology">
        </div>
    </div>
</template>

<script>
import VueMultiselect from 'vue-multiselect'
export default {
    name: 'CustomFieldsTeam',
    components: { VueMultiselect },
    props: {
        engineers: Object,
        technologies: Object,
        current_technologies: Array,
        current_members: Array,
    },
    data () {
        return {
            m_selected: [],
            t_selected: [],
            technologies_ids: [],
            members_ids: [],
        }
    },
    watch:{
        t_selected(newValue){
            this.technologies_ids = newValue.map(obj => obj.id);
        },
        m_selected(newValue){
            this.members_ids = newValue.map(obj => obj.id);
        }
    },
    mounted() {
        this.setSelected()
    },

    methods: {
        setSelected() {
            if (this.current_members) {
                this.m_selected = this.current_members
            }
            if (this.current_technologies) {
                this.t_selected = this.current_technologies
            }
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
