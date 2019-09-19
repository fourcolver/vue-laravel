<template>
    <div class="units-add">
        <heading icon="ti-annoucements" :title="$t('models.post.add')" shadow="heavy" style="margin-bottom: 20px;" />
        <div class="crud-view">
            <card :loading="loading">
                <el-form :model="model" ref="form" label-width="192px" style="max-width: 512px;">

                    <el-form-item>
                        <el-button type="primary" icon="ti-save" @click="submit">
                            {{$t('models.post.save')}}
                        </el-button>
                    </el-form-item>
                </el-form>
            </card>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import PostsMixin from 'mixins/adminPostsMixin';

    export default {
        mixins: [PostsMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card
        }
    }
</script>
