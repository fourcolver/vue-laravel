<template>
    <card shadow="never" :style="{'background-color': color}">
        <i :class="icon" />
        <div class="content">
            <h4>{{ title }}</h4>
            <h3>{{ value }}</h3>
            <p v-if="description">{{ description }}</p>
        </div>
    </card>
</template>

<script>
    import Card from './Card';

    export default {
        name: 'ColoredStatisticsCard',
        components: {
            Card
        },
        props: {
            icon: {
                type: String,
                required: true,
            },
            color: {
                type: String,
                required: true
            },
            title: {
                type: String,
                required: true
            },
            value: {
                type: String,
                required: true
            },
            description: String
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        color: #fff;
        :global(.el-card__body) {
            padding: 1em;
            i {
                margin-right: .25em;
                font-size: 3.5em;
                color: fade-out(#fff, .5);
            }
            p,
            h3,
            h4 {
                margin: 0;
            }
            h4 {
               color: fade-out(#fff, .5); 
            }
            h3 {
                margin: .14em 0;
                font-size: 1.5em;
            }
            p {
               color: fade-out(#fff, .14);  
            }
            display: flex;
            align-items: center;
        }
    }
</style>
