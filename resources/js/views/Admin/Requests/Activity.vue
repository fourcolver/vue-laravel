<template>
    <div class="activity">
        <heading :title="$t('pages.request_activities.title')" icon="ti-announcement" shadow="heavy" />
        <el-row :gutter="20" class="crud-view">
            <el-col :md="16">
                <el-card class="mt15">
                    <audit type="request"/>
                </el-card>
            </el-col>
            <el-col :md="8">
                <el-card class="mt15">
                    <!-- <filters ref="filters" :data.sync="filters.data" :schema="filters.schema" @changed="filtersChanged"/> -->
                </el-card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Audit from 'components/Audit'

    export default {
        components: {
            Heading,
            Audit
        }
    };
</script>

