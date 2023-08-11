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
export default {
    name: "GoogleChart",
    components: {
        GChart
    },
    data() {
        return {
            chartType:'LineChart',
            collectionData: [
                ["Week", "PM", "TL", "TW"],
                ['31.07-07.08',  132, 132, 132],
                ['07.08-14.08',  132, 132, 132],
                ['14.08-21.08',  134, 132, 132],
                ['21.08-28.08',  130, 130, 130],
                ['28.08-04.09',  130, 130, 132],
            ],
            chartOptions: {
                title: 'Project History Report',
                dates: [],
                colors: [ 'orange', 'red', 'blue'],
                lineWidth: 5,
                titleTextStyle:{
                    fontSize: 26,
                },
                height: 600,
                vAxis: {
                    title:'Hours(h)',
                    titleTextStyle:{
                        color:'green',
                        bold: 'true',
                        fontSize: 18,
                    },
                },
                hAxis: {
                    title:'Weeks',
                    titleTextStyle:{
                        color:'green',
                        bold: 'true',
                        fontSize: 18,
                    }
                }
            },
        };
    },
};
</script>
