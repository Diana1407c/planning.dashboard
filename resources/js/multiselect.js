import { createApp } from 'vue';
import EngineerMultiselect from './components/Elements/EngineerMultiselect.vue';

const adminApp = createApp({
    components: {
        EngineerMultiselect,
    },
});

adminApp.mount('#backpack-vue');
