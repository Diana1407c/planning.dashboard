<script>
export default {
    name: "BasePlanning",
    data() {
        return {
            table: [],
        }
    },
    methods: {
        tValue(keys) {
            return this.tValueByKey(this.table, keys, 0)
        },

        tValueByKey(object, keys, index) {
            if (!object || !object.hasOwnProperty(keys[index])) {
                return 0
            }

            if (keys.length - 1 === index) {
                return object[keys[index]]
            }

            return this.tValueByKey(object[keys[index]], keys, index + 1)
        },

        tValue2(keys) {
            return this.tValueByKey2(this.table, keys, 0)
        },

        tValueByKey2(object, keys, index) {
            console.log(keys, index, keys[index])
            if (!object || !object.hasOwnProperty(keys[index])) {
                return 0
            }

            if (keys.length - 1 === index) {
                return object[keys[index]]
            }

            return this.tValueByKey2(object[keys[index]], keys, index + 1)
        },

        async handleSelectProjects() {
            const plannedProjectsSet = new Set();
            const projectNamesMap = {};
            if (this.filter.team_ids.length > 0) {

                const technologyIds = await this.getTeamsTechnologyIds();

                for (const technologyId of technologyIds) {
                    if (technologyId === 'total') {
                        continue;
                    }

                    if (this.table.hasOwnProperty('technologies') && this.table.technologies.hasOwnProperty('planned_pm')) {
                        for (const projectId in this.table.technologies.planned_pm[technologyId]) {
                            const parsedProjectId = parseInt(projectId, 10);
                            plannedProjectsSet.add(parsedProjectId);
                        }
                    }
                    if (this.table.hasOwnProperty('technologies') && this.table.technologies.hasOwnProperty('planned_tl')) {
                        for (const projectId in this.table.technologies.planned_tl[technologyId]) {
                            const parsedProjectId = parseInt(projectId);
                            plannedProjectsSet.add(parsedProjectId);
                        }
                    }
                }
            } else {
                if (this.table.hasOwnProperty('technologies') && this.table.technologies.hasOwnProperty('planned_pm')) {
                    for (const technologyId in this.table.technologies.planned_pm) {
                        if (technologyId === 'total') {
                            continue;
                        }
                        for (const projectId in this.table.technologies.planned_pm[technologyId]) {
                            const parsedProjectId = parseInt(projectId);
                            plannedProjectsSet.add(parsedProjectId);
                        }
                    }
                }

                if (this.table.hasOwnProperty('technologies') && this.table.technologies.hasOwnProperty('planned_tl')) {
                    for (const technologyId in this.table.technologies.planned_tl) {
                        if (technologyId === 'total') {
                            continue;
                        }
                        for (const projectId in this.table.technologies.planned_tl[technologyId]) {
                            const parsedProjectId = parseInt(projectId);
                            plannedProjectsSet.add(parsedProjectId);
                        }
                    }
                }
            }

            for (const project of this.projects) {
                if (plannedProjectsSet.has(project.id)) {
                    projectNamesMap[project.id] = project.name;
                }
            }

            const plannedProjects = Array.from(plannedProjectsSet).map(projectId => ({
                id: projectId,
                name: projectNamesMap[projectId],
            }));

            this.filter.project_ids = plannedProjects;
            await this.getData();
        },

        async getTeamsTechnologyIds() {
            const technologyIdsSet = new Set();
            try {
                const response = await axios.get('teams', {
                    params: {
                        team_ids: this.filter.team_ids.map(obj => obj.id),
                    }
                });

                for (const team of response.data.data) {
                    for (const technology of team.technologies) {
                        technologyIdsSet.add(technology.id);
                    }
                }
            } catch (error) {
                console.error("Error fetching technology IDs:", error);
            }
            return Array.from(technologyIdsSet);
        }
    },
}
</script>
