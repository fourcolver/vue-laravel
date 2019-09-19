<template>
    <div class="templates-add">
        <heading :title="$t('models.template.add')" icon="ti-user" shadow="heavy" style="margin-bottom: 20px;"/>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-width="100px" ref="form">
                <el-col :md="16">
                    <card :loading="loading">
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
                        <el-form-item :label="$t('models.template.name')" :rules="validationRules.name" prop="name">
                            <el-input autocomplete="off" type="text"
                                      v-model="model.translations[language].name"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.subject')" :rules="validationRules.subject"
                                      prop="subject">
                            <el-input autocomplete="off" type="text"
                                      v-model="model.translations[language].subject"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.template.body')" :rules="validationRules.body" prop="body">
                            <quill-editor ref="quillEditor"
                                          v-model="model.translations[language].body"
                            >
                            </quill-editor>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="submit" icon="ti-save" type="primary">
                                {{$t('models.user.save')}}
                            </el-button>
                        </el-form-item>
                    </card>
                </el-col>
                <el-col :md="8">
                    <card :loading="loading">
                        <el-form-item :label="$t('models.template.category')" :rules="validationRules.category"
                                      prop="category_id">
                            <el-select :placeholder="$t('models.template.placeholders.category')"
                                       @change="setSelectedCategory"
                                       class="custom-select"
                                       v-model="model.category_id">
                                <el-option
                                    :key="category.id"
                                    :label="category.name"
                                    :value="category.id"
                                    v-for="category in categories">
                                </el-option>
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
    import TemplatesMixin from 'mixins/adminTemplatesMixin';

    export default {
        mixins: [TemplatesMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            quillEditor
        }
    }
</script>
<style>
    .ql-container.ql-snow, .ql-editor {
        min-height: 250px;
    }
</style>
