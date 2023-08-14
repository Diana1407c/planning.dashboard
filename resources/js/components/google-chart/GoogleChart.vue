<template>
    <div class="row">
        <div class="col-6">
            <GChart
                :type="chartType"
                :options="chartOptions"
                :data="collectionData"
            />
        </div>
    </div>
</template>

<script>
import { GChart } from "vue-google-charts";
import { history } from './../../history';
export default {
    name: "GoogleChart",
    components: {
        GChart
    },
    mixins: [history],
};
</script>
