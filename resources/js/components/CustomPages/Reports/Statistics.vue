<template>
    <div class="d-flex box-filter-separator">
        <hr class="col-12 separator-filter">
    </div>
            <HistoryGoogleChart :allProjects="allProjects" ></HistoryGoogleChart>
</template>

<script>
import HistoryGoogleChart from "../../google-chart/HistoryGoogleChart";
export default {
    name: "Statistics",
    components: { HistoryGoogleChart },
    props: {
        allProjects: Object
    },
};
</script>
