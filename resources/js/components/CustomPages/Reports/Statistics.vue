<template>
    <HistoryGoogleChart :projectTypes="projectTypes" :allProjects="allProjects"></HistoryGoogleChart>
    <ProjectsTypesReport :projectStates="projectStates" :allProjects="allProjects" :allTeams="allTeams"
                         :allEngineers="allEngineers"></ProjectsTypesReport>
    <CompanyCapacityHoursReport :allTeams="allTeams" :allEngineers="allEngineers"></CompanyCapacityHoursReport>
</template>

<script>
import HistoryGoogleChart from "../../google-chart/HistoryGoogleChart";
import ProjectsTypesReport from "../../google-chart/ProjectTypesReport.vue";
import CompanyCapacityHoursReport from "../../google-chart/CompanyCapacityHoursReport.vue";

export default {
    name: "Statistics",
    components: {CompanyCapacityHoursReport, ProjectsTypesReport, HistoryGoogleChart},
    props: {
        projectStates: Object,
        projectTypes: Object,
        allProjects: Object,
        allTeams: Object,
        allEngineers: Object,
    },
};
</script>
