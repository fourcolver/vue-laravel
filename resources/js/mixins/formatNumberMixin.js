export default {
    filters: {
        formatNumber (number) {
            return (number || '').toLocaleString()
        }
    }
}