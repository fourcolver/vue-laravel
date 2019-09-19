<template>
    <div class="stackchart">
        <el-row type="flex" class="chart-filter">
            <el-col :span="24">
                <el-radio-group v-model="period">                
                    <el-radio-button label="day">{{$t('timestamps.days')}}</el-radio-button>
                    <el-radio-button label="month">{{$t('timestamps.months')}}</el-radio-button>
                    <el-radio-button label="week">{{$t('timestamps.weeks')}}</el-radio-button>
                    <el-radio-button label="year">{{$t('timestamps.years')}}</el-radio-button>
                </el-radio-group>
                <el-date-picker
                    v-model="startDate"
                    type="date"
                    format="dd.MM.yyyy"
                    value-format="dd.MM.yyyy"
                >
                </el-date-picker>
                <el-date-picker
                    v-model="endDate"
                    type="date"
                    format="dd.MM.yyyy"
                    value-format="dd.MM.yyyy"
                    :picker-options="endDatePickerOptions"
                >
                </el-date-picker>
            </el-col>
        </el-row>    
        <el-row style="margin-bottom: 24px;" type="flex">
            <el-col :span="24">
                <apexchart ref="stackColumnChart" width="100%" height="310" type="bar" :options="chartOptions" :series="series"></apexchart>
            </el-col>
        </el-row>        
    </div>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'
import {format, subDays, isBefore, isAfter, parse} from 'date-fns'
import axios from '@/axios';

export default {  
  components: {'apexchart': VueApexCharts},
  props: {
            type: {
                type: String,
                required: true
            }            
    },  
    data() {
        return {        
            period: 'day',
            startDate: format(subDays(new Date(), 28), 'DD.MM.YYYY'),
            endDate: format(new Date(), 'DD.MM.YYYY'),
            endDatePickerOptions: {
            	disabledDate: this.disabledEndDate
            },
            xData: [],
            yData: []
        }
    },    
    computed: {    
        series: function(){  
            return this.yData;
        },
        chartOptions: function(){
          return {  
            chart: {
              stacked: true, 
              toolbar: {
                show: true,
                tools: {
            download: true,
            selection: true,
            zoom: false,
            zoomin: false,
            zoomout: false,
            pan: false,
            reset: false            
          },
              },  
            },
            responsive: [{
              breakpoint: 480,
              options: {
                legend: {
                  position: 'bottom',
                  offsetX: 0,
                  offsetY: 0
                }
              }
            }],
            plotOptions: {
              bar: {
                horizontal: false,
              },
            },

            xaxis: {
              categories: this.xData,
            },
            legend: {
              position: 'bottom',              
              horizontalAlign: 'center'              
            },
            fill: {
              opacity: 1
            },
            dataLabels:{
                enabled: false,
            }
          }
        }        
      },
    methods: {
      disabledEndDate(date){
        var parsed_start_date = (this.startDate) ? this.startDate.split(".") : [];
        if((parsed_start_date[0] !== undefined) && (parsed_start_date[1] !== undefined) && (parsed_start_date[0] !== undefined)){
                return isBefore(date, new Date(parsed_start_date[2], parsed_start_date[1] - 1, parsed_start_date[0]))
        }
        return false;            
      },
      fetchData(){
            let that = this;                                               
            let url = '';						
            if(this.type === 'request_by_creation_date'){
                    url = 'admin/chartRequestByCreationDate';
            }
            return axios.get(url,{
            	params: {
                    start_date: that.startDate,
                    end_date: that.endDate,
                    period: that.period
                }
            })
            .then(function (response) {
                that.yData = response.data.data.requests_per_day_ydata;
                that.xData = response.data.data.requests_per_day_xdata;

            }).catch(function (error) {
                console.log(error);
            })
        }
    },
    created(){
        if(this.type === 'request_by_creation_date'){
            this.fetchData();
        }
    },
    watch:{
        startDate: function (val) {
            var parsed_end_date = (this.endDate) ? this.endDate.split(".") : [];
            var parsed_start_date = (val) ? val.split(".") : [];
            if((parsed_end_date[2] !== undefined) && (parsed_start_date[2] !== undefined)){        				
                if(isAfter(new Date(parsed_start_date[2], parsed_start_date[1] - 1, parsed_start_date[0]), new Date(parsed_end_date[2], parsed_end_date[1] - 1, parsed_end_date[0]))){
                    this.endDate = val;
                }
            }
            this.fetchData();
        },
        endDate: function (val) {
            this.fetchData();
        },
        period: function (val) {
            this.fetchData();
        }
    }
}
</script>
