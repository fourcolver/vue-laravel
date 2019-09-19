<template>
    <card>
        <div class="content">
            <h3 :style="{ color }">{{ value }}</h3>
            <h5>
                {{ title }}
                <small>{{ description }}</small>
            </h5>
        </div>
        <el-progress :percentage="70" :color="color"></el-progress>
    </card>
</template>

<script>
    import Card from './Card';

    export default {
        name: 'ProgressStatisticsCard',
        components: {
            Card
        },
        props: {
            value: {
                type: String,
                required: true
            },
            title: {
                type: String,
                required: true
            },
            description: {
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
            .content {
                display: flex;
                align-items: center;
                h3, h5 {
                    margin: 0;
                }
                h3 {
                    font-size: 3.2em;
                    margin-right: .25em;
                }
                h5 {
                    font-size: 1em;
                    font-weight: normal;
                    color: darken(#fff, 40%);
                    small {
                        display: block;
                        color: #000;
                    }
                }
            }
        }
    }
</style>
