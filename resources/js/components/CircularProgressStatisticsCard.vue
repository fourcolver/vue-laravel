<template>
    <card>
        <h3>{{ title }}</h3>
        <el-progress 
            type="circle" 
            :percentage="percentage"
            :width="width"
            :color="color" />
    </card>
</template>

<script>
    import Card from './Card';

    export default {
        name: 'CircularProgressStatisticsCard',
        components: {
            Card
        },
        props: {
            width: {
                type: Number,
                default: 96
            },
            percentage: {
                type: Number,
                required: true
            },
            title: {
                type: String,
                required: true
            },
            color: String
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        :global(.el-card__body) {
            padding: 1em;
            text-align: center;
            h3 {
                margin: 0;
                margin-bottom: .5em;
            }
        }
    }
</style>
