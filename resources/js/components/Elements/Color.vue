<script>
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
                    return 'alert-info'
                case 'warning':
                    return 'alert-warning'
            }
        },
        c_comparisonColor(pm, tl, tw, date) {
            //this.c_isPast(date)
            let percent = this.c_toPercent(pm, tl)
            let status = this.c_percentToStatus(percent)

            return this.statusToCssClass(status)
        },

        c_isPast(date) {
            date = Date.parse(date)
            date.month
        },

        c_toPercent(hour1, hour2) {
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
