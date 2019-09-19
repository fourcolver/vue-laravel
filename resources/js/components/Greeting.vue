<template>
    <div class="greeting">
        {{ greeting }}
    </div>
</template>

<script>
    export default {
        computed: {
            greeting () {
                const split_afternoon = 12
                const split_evening = 17
                const currentHour = (new Date()).getHours()

                let g

                if (currentHour >= split_afternoon && currentHour <= split_evening)
                    g = 'Good afternoon'
                else if (currentHour >= split_afternoon)
                    g = 'Good evening'
                else
                    g = 'Good morning'

                return g + ' ' + this.$store.getters.loggedInUser.name
            }
        }
    }
</script>