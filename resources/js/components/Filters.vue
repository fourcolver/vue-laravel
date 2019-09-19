<template>
    <div class="filters" :class="{[layout]: true}">
        <template v-for="field in fields">
            <label v-if="field.title">{{field.title}}</label>
            <component :is="field.type" v-bind="field.props" v-on="field.listeners" v-model.sync="data[field.name]">
                <component v-if="field.children" v-for="child in field.children" :key="child.name" :is="child.type" v-bind="child.props" />
            </component>
        </template>
    </div>
</template>

<script>
    export default {
        name: 'filters',
        props: {
            schema: {
                type: Array,
                required: true
            },
            data: {},
            layout: {
                type: String,
                default: 'column',
                validator: layout => ['row', 'column'].includes(layout)
            }
        },
        methods: {
            reset () {
                this.$emit('update:data', Object.keys(this.data).reduce((acc, curr) => {
                    acc[curr] = null

                    return acc
                }, {}))
            }
        },
        computed: {
            fields () {
                return this.schema.reduce((fields, currField) => {
                    if (currField.require && !this.data[currField.require]) {
                        return fields;
                    }

                    fields.push(currField);

                    return fields;
                }, []);
            }
        },
        watch: {
            data: {
                deep: true,
                handler (filters) {
                    this.$emit('changed', Object.entries(filters).reduce((acc, [key, value]) => {
                        if (value) {
                            acc[key] = value;
                        }

                        return acc;
                    }, {}));
                }
            }
        }
    };
</script>

<style lang="scss">
    .filters {
        color: lighten(#000, 32%);
        &.row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between; 
            > * {
                width: calc(100% * (1/2));
                &:not(:nth-last-child(-n+2)) {
                    margin-bottom: 8px;
                }
            }
            > div {
                justify-content: flex-end;
            }
        }
        &.column {
            display: flex;
            flex-direction: column;
            label {
                margin: 4px 0;
            }
        }
    }
</style>
