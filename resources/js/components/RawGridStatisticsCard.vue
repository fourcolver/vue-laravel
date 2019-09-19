<template>
    <card>
        <el-row>
            <el-col :span="cols" v-for="(item, index) in data" :key="index">
                    <i :class="item.icon" :style="{'color': item.color}" />
                    <h3 :style="{'color': item.color}">
                        {{ item.value }}
                        <small>{{ item.description }}</small>
                    </h3>
            </el-col>
        </el-row>
    </card>
</template>

<script>
    import Card from './Card';

    export default {
        name: 'RawGridStatisticsCard',
        components: {
            Card
        },
        props: {
            data: {
                type: Array,
                required: true
            },
            cols: {
                type: Number,
                default: 12
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        :global(.el-card__body) {
            padding: 0;
            .el-row {
                margin: -1px;
                margin-bottom: -2px;
                margin-right: -2px;
                border: 1px darken(#fff, 4%) solid;
                .el-col {
                    position: relative;
                    border-right: 1px darken(#fff, 4%) solid;
                    border-bottom: 1px darken(#fff, 4%) solid;
                    padding: 1em;
                    text-align: center;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    i {
                        border-radius: 50%;
                        line-height: 40px;
                        margin-bottom: 1em;
                        text-align: center;
                        background-color: darken(#fff, 4%);
                        width: 40px;
                        height: 40px;
                    }
                    h3 {
                        margin: 0;
                        font-size: 1.8em;
                        small {
                            font-size: 40%;
                            font-weight: normal;
                            display: block;
                            text-transform: uppercase;
                            color: darken(#fff, 24%);
                        }
                    }
                }
            }
        }
    }
</style>
