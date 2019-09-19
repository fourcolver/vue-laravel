<template>
    <div class="services-add">
        <heading :title="$t('models.service.add_title')" icon="ti-user" shadow="heavy">
            <add-actions :saveAction="submit" route="adminServices" editRoute="adminServicesEdit"/>
        </heading>
        <div class="crud-view">
            <card :loading="loading">
                <el-form :model="model" :rules="validationRules" label-width="192px" ref="form" style="max-width: 512px;">
                    <el-form-item :label="$t('models.service.category')" :rules="validationRules.category" prop="category">
                        <el-select :placeholder="$t('models.service.placeholders.category')" style="display: block;"
                                   v-model="model.category">
                            <el-option
                                :key="category"
                                :label="$t(`models.service.${category}`)"
                                :value="category"
                                v-for="category in serviceCategories">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="$t('models.user.name')" :rules="validationRules.name" prop="name">
                        <el-input type="text" v-model="model.name"/>
                    </el-form-item>
                    <el-form-item :label="$t('models.user.profile_image')">
                        <cropper :resize="false" :viewportType="'square'" @cropped="cropped"/>
                    </el-form-item>
                    <el-form-item :label="$t('models.user.email')" :rules="validationRules.email" prop="email">
                        <el-input type="email" v-model="model.email"/>
                    </el-form-item>
                    <el-form-item :label="$t('password')" :rules="validationRules.password" autocomplete="off"
                                  prop="user.password">
                        <el-input type="password" v-model="model.user.password"/>
                    </el-form-item>
                    <el-form-item :label="$t('confirm_password')" :rules="validationRules.password_confirmation"
                                  prop="user.password_confirmation">
                        <el-input type="password" v-model="model.user.password_confirmation"/>
                    </el-form-item>
                    <el-form-item :label="$t('models.user.phone')" prop="phone">
                        <el-input type="text" v-model="model.phone"/>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.street')" :rules="validationRules.street" prop="address.street"
                                  style="max-width: 512px;">
                        <el-input type="text" v-model="model.address.street"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.street_nr')" :rules="validationRules.street_nr"
                                  prop="address.street_nr" style="max-width: 512px;">
                        <el-input type="text" v-model="model.address.street_nr"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.zip')" :rules="validationRules.zip" prop="address.zip"
                                  style="max-width: 512px;">
                        <el-input type="text" v-model="model.address.zip"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.city')" :rules="validationRules.city" prop="address.city"
                                  style="max-width: 512px;">
                        <el-input type="text" v-model="model.address.city"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.state.label')" :rules="validationRules.state_id"
                                  prop="address.state_id" style="max-width: 512px;">
                        <el-select :placeholder="$t('models.address.state.label')" style="display: block"
                                   v-model="model.address.state_id">
                            <el-option :key="state.id" :label="state.name" :value="state.id"
                                       v-for="state in states"></el-option>
                        </el-select>
                    </el-form-item>

                </el-form>
            </card>
        </div>

    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import ServicesMixin from 'mixins/adminServicesMixin';
    import Cropper from 'components/Cropper';
    import AddActions from 'components/EditViewActions';


    export default {
        name: 'AdminServicesAdd',
        mixins: [ServicesMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            AddActions
        }
    }
</script>

<style lang="scss" scoped>
    .services-add {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>

