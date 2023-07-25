import { createApp } from 'vue';
import CustomFieldsTeam from './components/Team/CustomFieldsTeam.vue';

const adminApp = createApp({
    components: {
        CustomFieldsTeam
    },
});

adminApp.mount('#backpack-vue');
