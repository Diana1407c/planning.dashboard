<script>
const { getWeek } = require('date-fns');
export default {
    name: 'Color',
    methods: {
        statusToCssClass(status) {
            switch (status) {
                case 'success':
                    return 'alert-success'
                case 'error':
                    return 'alert-error'
                case 'info':
                    return 'alert-ok'
                case 'warning':
                    return 'alert-warning'
            }
        },

        setColorHour(hour1, hour2) {
            if (hour1 === 0 && hour2 === 0) {
                return ''
            }

            let percent = this.c_toPercent(hour1, hour2)
            let status = this.c_percentToStatus(percent)

            return this.statusToCssClass(status)
        },

        setColorHourDateString(hour1, hour2, date_string, period_type) {
            let date_data = date_string.split('_')
            let year = parseInt(date_data[0])
            let period_number = parseInt(date_data[1])

            if (this.c_periodIsPast(year, period_type, period_number)) {
                return this.setColorHour(hour1, hour2)
            }

            return ''
        },

        setColorHourWeek(hour1, hour2, year, week) {
            if (this.c_weekIsPast(parseInt(year), parseInt(week))) {
                return this.setColorHour(hour1, hour2)
            }

            return ''
        },

        c_periodIsPast(year, period_type, period_number) {
            if (period_number === 'month') {
                return this.c_monthIsPast(year, period_number)
            }
            return this.c_weekIsPast(year, period_number)
        },

        c_weekIsPast(year, week) {
            if (week === 0) {
                week = 51
                year -= 1
            } else {
                week -=1
            }

            let currentDate = new Date();
            let currentWeek = getWeek(currentDate)

            if (currentDate.getFullYear() === year) {
                return week <= currentWeek
            } else {
                return year < currentDate.getFullYear()
            }
        },

        c_monthIsPast(year, month) {
            if (month === 0) {
                month = 11
                year -= 1
            } else {
                month -= 1
            }

            let currentDate = new Date();
            if (currentDate.getFullYear() === year) {
                return month <= currentDate.getMonth()
            } else {
                return year < currentDate.getFullYear()
            }
        },

        c_toPercent(hour1, hour2) {
            hour1 = parseFloat(hour1)
            hour2 = parseFloat(hour2)
            let denominator = hour2
            let numerator = hour1

            if (hour1 > hour2) {
                denominator = hour1
                numerator = hour2
            }

            return (numerator / denominator) * 100
        },

        c_percentToStatus(percent) {
            if (percent >= 95) {
                return 'success'
            } else if (percent > 70) {
                return 'info'
            }else if (percent > 50) {
                return 'warning'
            } else {
                return 'error'
            }
        }
    }
}

</script>
