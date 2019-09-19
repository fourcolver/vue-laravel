import { distanceInWordsToNow } from 'date-fns'

export default {
    methods: {
        ago(date) {
            if (!date) return

            return distanceInWordsToNow(date, {
                includeSeconds: true,
                addSuffix: 'ago'
            })
        }
    }
}