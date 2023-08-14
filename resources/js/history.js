import axios from 'axios';

export const history = {
    data() {
        return {
            chartType:'LineChart',
            collectionData: [
                ["Week", "PM", "TL", "TW"],
                ['31.07-07.08',  132, 132, 130],
                ['07.08-14.08',  132, 132, 132],
                ['14.08-21.08',  134, 132, 132],
                ['21.08-28.08',  130, 130, 130],
                ['28.08-04.09',  130, 130, 132],
            ],
            chartOptions: {
                title: 'Project History Report',
                dates: [],
                colors: [ 'red', 'orange', 'blue'],
                lineWidth: 3,
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
                },
                historyData: null,
                hours:[],
            },
        };
    },
};
