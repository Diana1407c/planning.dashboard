<template>
    <HistoryGoogleChart :projectTypes="projectTypes" :allProjects="allProjects"></HistoryGoogleChart>
    <ProjectsTypesReport :projectStates="projectStates" :allProjects="allProjects" :allTeams="allTeams"
                         :allEngineers="allEngineers"></ProjectsTypesReport>
</template>

<script>
import HistoryGoogleChart from "../../google-chart/HistoryGoogleChart";
import ProjectsTypesReport from "../../google-chart/ProjectTypesReport.vue";

export default {
    name: "Statistics",
    components: {ProjectsTypesReport, HistoryGoogleChart},
    props: {
        projectStates: Object,
        projectTypes: Object,
        allProjects: Object,
        allTeams: Object,
        allEngineers: Object,
    },
};
</script>
