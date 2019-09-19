<template>
    <div style="flex-grow: 1;">
        <heading class="custom-heading" icon="ti-home" title="Dashboard" shadow="heavy" />
        <el-row :gutter="20" class="dashboard" style="margin-bottom: 24px;" type="flex">
            <el-col class="dashboard-tabpanel">
                <el-tabs type="border-card">
                    <el-tab-pane :label="$t('menu.requests')">
                        <el-row style="margin-bottom: 24px;" type="flex">
                            <el-col :span="24">
                                <dashboard-statistics-card :totalRequest="totalRequest" :data="reqStatusCount" :avgReqDuration="avgReqDuration"></dashboard-statistics-card>
                            </el-col>
                        </el-row>
                        <el-row style="margin-bottom: 24px;" :gutter="20" type="flex">
                            <el-col :span="24">
                                <el-card class="chart-card" :header="$t('dashboard.requests_by_creation_date')">
                                    <chart-stacked-column type="request_by_creation_date"></chart-stacked-column>
                                </el-card>
                            </el-col>
                         </el-row>
                        <el-row :gutter="20" style="margin-bottom: 24px;" type="flex">
                            <el-col :span="12">
                                <el-card class="chart-card" :header="$t('dashboard.requests_by_status')">
                                    <chart-pie-and-donut type="request_by_status"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                            <el-col :span="12">
                                <el-card class="chart-card" :header="$t('dashboard.requests_by_category')">
                                    <chart-pie-and-donut type="request_by_category"></chart-pie-and-donut>
                                </el-card>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20" style="margin-bottom: 24px;" type="flex">
                            <el-col :span="12">
                                <el-card class="chart-card" :header="$t('dashboard.each_hour_request')">
                                    <chart-heat-map
                                        :xData="chartDataReqByHour.xData"
                                        :yData="chartDataReqByHour.yData">
                                    </chart-heat-map>
                                </el-card>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane :label="$t('menu.buildings')">
                        {{'Second Tab'}}
                    </el-tab-pane>
                    <el-tab-pane :label="$t('menu.news')">
                        {{'Content Third tab'}}
                    </el-tab-pane>
                    <el-tab-pane :label="$t('menu.marketplace')">
                        {{'Fourth Tab'}}
                    </el-tab-pane>
                    <el-tab-pane :label="$t('menu.tenants')">
                        {{'Fourth Tab'}}
                    </el-tab-pane>
                </el-tabs>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import axios from '@/axios';
    import DashboardStatisticsCard from 'components/DashboardStatisticsCard';
    import ChartStackedColumn from 'components/ChartStackedColumn';
    import ChartPieAndDonut from 'components/ChartPieAndDonut';
    import ChartHeatMap from 'components/ChartHeatMap';
    import Heading from 'components/Heading';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import ColoredStatisticsCard from 'components/ColoredStatisticsCard.vue';
    import ProgressStatisticsCard from 'components/ProgressStatisticsCard.vue';
    import CircularProgressStatisticsCard from 'components/CircularProgressStatisticsCard.vue';

    export default {
        name: 'AdminDashboard',
        components: {
            Heading,
            DashboardStatisticsCard,
            ColoredStatisticsCard,
            ProgressStatisticsCard,
            CircularProgressStatisticsCard,
            ChartStackedColumn,
            ChartPieAndDonut,
            ChartHeatMap
        },
        data() {
            return {
                totalRequest: 0,
                avgReqDuration: '',                
                chartDataReqByHour:{
                    xData: [],
                    yData: []
                },
                chartOptionsTotalReqByCreationDate: {},
                reqStatusCount: {},
                statistics: [{
                    icon: 'ti-shopping-cart',
                    color: '#f06292',
                    value: 648,
                    description: 'Requests open'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#26c6da',
                    value: '47.5k',
                    description: 'Requests pending'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#9575cd',
                    value: 764,
                    description: 'Requests done'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#1a237e',
                    value: 256,
                    description: 'Requests archived'
                }],
                liteStatistics: [{
                    icon: 'ti-shopping-cart',
                    color: '#9575cd',
                    value: 764,
                    description: 'Daily earnings'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#1a237e',
                    value: 256,
                    description: 'Products'
                }]
            }
        },
        methods: {
            getReqStatastics() {
                let that = this;

                return axios.get('admin/statistics')
                .then(function (response) {

                    that.reqStatusCount = response.data.data.requests_per_status;

                    that.totalRequest = response.data.data.total_requests;
                    that.avgReqDuration = response.data.data.avg_request_duration;                    
                }).catch(function (error) {
                    console.log(error);
                })
            }
        },
        created(){
            this.getReqStatastics();
        },

    }
</script>


<style lang="scss" scoped>
    .custom-heading {
        margin-bottom: 2em;
    }
    .dashboard{
        padding: 5px;
    }
</style>
<style lang="scss">
.el-row.dashboard {
    margin-top: -110px;

    @media screen and (max-width: 1000px) {
        margin-top: 0;
    }
}
.dashboard-tabpanel{
    .el-tabs--border-card > .el-tabs__header .el-tabs__item{
        flex-basis: 0;
        -webkit-box-flex: 1;    
        flex-grow: 1;
        text-align: center;
        color: #495057;    
        cursor: pointer;    
        font-weight:400;    
        -webkit-box-align: center;
        align-items: center;
        text-align: center;
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 0 13px !important;

        &.is-active, &:hover{
            background: #6AC06F;
            //border-radius: 120px;
            border-right-color: none;
            border-left-color: none;
            -ms-flex-positive: 1;
            color: #fff !important;
            transition: background-color .3s ease,color .3s ease !important;
        }

        &:first-child {
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        &:last-child {
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }
    }
    .el-tabs__nav {
        float: none;
        text-align: center;
        border-radius: 120px;
        padding: .75rem;    
        display: flex;
        flex-wrap: wrap;
        width: fit-content;
        margin: 1.5rem 0 1.5em auto;

        @media screen and (max-width: 1000px) {
            margin: 1.5rem auto;
        }
    }
    .el-tabs--border-card{
        background:none;
    }
    .el-tabs--border-card{
        border: none;
        background: none;
        box-shadow: none;
    }
    .el-tabs--border-card > .el-tabs__header{
        border-bottom: none !important;
        background: none !important;    
    }
    .chart-card{
        height: 420px;
    }
}
.chart-card .el-card__body{
    padding: 0 0 0 0;
}
.chart-filter{
    background-color: #fbfbfb; 
    border-bottom: 1px solid #e1e5eb; 
    padding: .5rem .5rem;
}

.dashboard .stackchart .apexcharts-menu-icon{
    margin-top: -170px;
}
.dashboard .stackchart .apexcharts-menu.open{
    margin-top: -79px;
}
.dashboard .piechart .apexcharts-menu-icon{
    margin-top: -170px;
    margin-right: -20px;
}
.dashboard .piechart .apexcharts-menu.open{
    margin-top: -79px;
    margin-right: -20px;
}
.dashboard .box-card-count{
    .el-card__body{
        height: 100%;
    }
    .total-box-card-header{
    clear: both;
    padding: 15px 20px 5px 20px;
    opacity: 0.5;
    text-transform: uppercase;
    text-align: center;
    border-bottom: none;
    box-sizing: border-box;
    font-size: 13px;
  }  
  .total-box-card-body{
    clear: both;
    padding: 5px 20px 15px 20px;
    font-size: 1.6rem;
    font-weight: 700;
    line-height: 1;
    text-align: center;
  }
  .el-divider--horizontal{
    margin: 0 0;
  }
}
.dashboard .box-card{ 
    border: none;
    border-bottom: 4px solid transparent;  
    
    .el-card__header {        
        padding: 20px 20px 0px 20px;       
        opacity: 0.5;     
        text-transform: uppercase;        
        border-bottom: none;
        font-size: 13px;
    }
    .box-card-body{
        display: flex;        
        .box-card-count{
            padding: 8px 20px 12px 20px;
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
            text-align: left;            
            float: left;
        }
        .box-card-progress{            
            float: left;
            margin-left: auto;
            padding: 0 15px 0 0;
            position: relative;
            top: -27px;
            margin-bottom: -27px;

            .el-progress__text {
                font-size: 13px !important;
            }
        }
    }
}  
</style>
