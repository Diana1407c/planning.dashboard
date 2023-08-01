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
        }
    },
}
</script>
