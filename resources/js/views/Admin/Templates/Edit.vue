<template>
    <div class="templates-edit">
        <heading :title="$t('models.template.edit')" icon="ti-user" shadow="heavy" style="margin-bottom: 20px;">
            <edit-actions :queryParams="{tab: 'templates'}" :saveAction="submit" route="adminSettings"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-width="100px" ref="form">
                <el-col :md="12">
                    <card :loading="loading">
                        <el-form-item :label="$t('models.template.name')" :rules="validationRules.name" prop="name">
                            <el-input autocomplete="off" type="text"
                                      v-model="model.name"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-radio-group v-model="language">
                                <el-radio-button label="fr">
                                    <span class="flag-icon flag-icon-fr"></span> {{$t('languages.fr')}}
                                </el-radio-button>
                                <el-radio-button label="de">
                                    <span class="flag-icon flag-icon-de"></span> {{$t('languages.de')}}
                                </el-radio-button>
                                <el-radio-button label="en">
                                    <span class="flag-icon flag-icon-us"></span> {{$t('languages.en')}}
                                </el-radio-button>
                                <el-radio-button label="it">
                                    <span class="flag-icon flag-icon-it"></span> {{$t('languages.it')}}
                                </el-radio-button>
                            </el-radio-group>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.subject')" :prop="`translations.${language}.subject`"
                                      :rules="validationRules.subject">
                            <el-input @blur="setLastFocusedElement($event)" autocomplete="off"
                                      type="text"
                                      v-model="model.translations[language].subject"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.body')" :prop="`translations.${language}.body`"
                                      :rules="validationRules.body">
                            <quill-editor @blur="setLastFocusedElement($event)"
                                          ref="quillEditor"
                                          v-model="model.translations[language].body"
                            >
                            </quill-editor>
                        </el-form-item>
                    </card>
                </el-col>
                <el-col :md="12">
                    <card :loading="loading">

                        <el-form-item :label="$t('models.template.category')" :rules="validationRules.category"
                                      prop="category_id">
                            <el-select :placeholder="$t('models.template.placeholders.category')"
                                       @change="setSelectedCategory"
                                       v-model="model.category_id"
                            >
                                <el-option-group
                                    :key="group.id"
                                    :label="group.name"
                                    v-for="group in categories">
                                    <el-option
                                        :key="category.id"
                                        :label="category.name"
                                        :value="category.id"
                                        v-for="category in group.categories">
                                    </el-option>
                                </el-option-group>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.tags')" v-if="!_.isEmpty(selectedCategory)">
                            <el-button :key="tag" @click="insertTag(tag)" size="mini" type="info"
                                       v-for="tag in categoryTags">{{tag}}
                            </el-button>
                        </el-form-item>
                    </card>
                </el-col>
            </el-form>
        </el-row>
    </div>
</template>

<script>
    // require styles
    import 'quill/dist/quill.core.css'
    import 'quill/dist/quill.snow.css'
    import 'quill/dist/quill.bubble.css'

    import {quillEditor} from 'vue-quill-editor'
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import EditActions from 'components/EditViewActions';
    import TemplatesMixin from 'mixins/adminTemplatesMixin';

    export default {
        mixins: [TemplatesMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            EditActions,
            quillEditor
        }
    }
</script>

<style>
    .ql-container.ql-snow, .ql-editor {
        min-height: 250px;
    }
</style>
