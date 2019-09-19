<template>
    <el-dialog  :close-on-click-modal="false" 
                :title="$t('models.building.delete_building_modal.title')"
                :visible="deleteBuildingVisible"
                width="30%"
                class="delete_building_modal">
            <el-row>
                <el-col :span="24">
                    <p class="description">{{getDelBuildingDescription()}}</p>                    
                </el-col>
            </el-row>
            <el-row v-if="(delBuildingStatus == 0 || delBuildingStatus == 2)">
                <el-col :span="24">
                    <el-switch 
                        :active-text="$t('models.building.delete_building_modal.delete_units')"
                        :inactive-text="$t('models.building.delete_building_modal.dont_delete_units')"
                        v-model="is_units" 
                        class="delete_switch" />
                </el-col>
            </el-row>
            <el-row v-if="(delBuildingStatus == 1 || delBuildingStatus == 2)">
                <el-col :span="24">
                    <el-switch 
                        :active-text="$t('models.building.delete_building_modal.delete_requests')"
                        :inactive-text="$t('models.building.delete_building_modal.dont_delete_requests')"
                        v-model="is_request"
                        class="delete_switch" />
                </el-col>
            </el-row>

            <span class="dialog-footer" slot="footer">
                <el-button @click="close" size="mini">{{$t('models.building.cancel')}}</el-button>
                <el-button @click="deleteSelectedBuilding(is_units, is_request)" size="mini" type="danger">{{$t('models.building.delete')}}</el-button>
            </span>
    </el-dialog>
</template>
<script>
    export default {
        props: {
            deleteBuildingVisible: {
                type: Boolean,
                required: true
            },
            delBuildingStatus: {
                type: Number,
                required: true
            },
            deleteSelectedBuilding: {
                type: Function,
                required: true
            },
            closeModal: {
                type: Function,
                required: true
            }
        },
        data() {
            return {                                
                is_units: false,
                is_request: false,
            }
        },
        methods: {
            getDelBuildingDescription() {
                switch(this.delBuildingStatus) {
                    case 0:
                        return this.$t('models.building.delete_building_modal.description_unit');
                    case 1:
                        return this.$t('models.building.delete_building_modal.description_request');
                    case 2:
                        return this.$t('models.building.delete_building_modal.description_both');
                    default:
                        return "";
                }
            },
            close() {
                this.is_units = false;
                this.is_request = false;
                this.closeModal();
            }            
        },        
    };
</script>
<style lang="scss" scoped>
    .delete_building_modal {        

        .el-row {
            margin: 0 0 22px 0;
        }

        .description {
            margin: 0;
        }        
        
    }
</style>

<style>
    .delete_switch .el-switch__label--left {         
        min-width: 145px;        
    }
</style>