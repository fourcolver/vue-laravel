<template>
    <div class="piechart">
      <el-row type="flex" class="chart-filter">
            <el-col :span="24">                
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
                <apexchart :type="chartType" width='90%' :options="chartOptions" :series="series" />
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
        chartType: 'pie',
        startDate: format(subDays(new Date(), 28), 'DD.MM.YYYY'),
        endDate: format(new Date(), 'DD.MM.YYYY'),
        endDatePickerOptions: {
            disabledDate: this.disabledEndDate
        },
        xData: [],
        yData: []
    }
  },
  computed:{
    series: function(){        
        return this.yData;
    },
    chartOptions: function(){
        return {
          labels: this.xData,
          responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 350
                },
                legend: {
                    show: false
                }
            }
          }],
          legend: {
                show: true,                                
           },
           chart:{
                 toolbar: {
                    show: true,
                 },
                 autoSelected: ''
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
            if(this.type === 'request_by_status'){
                this.chartType = 'pie';
                url = 'admin/chartRequestByStatus';
            }
            else if(this.type === 'request_by_category'){
                this.chartType = 'donut';
                url = 'admin/chartRequestByCategory';
            }
            return axios.get(url,{
            	params: {
                    start_date: that.startDate,
                    end_date: that.endDate                    
                }
            })
            .then(function (response) {
                if(that.type === 'request_by_status'){                    
                    that.yData = response.data.data.data;
                    that.xData = response.data.data.labels.map(function(e){return that.$t('models.request.status.'+e)});
                }
                else if(that.type === 'request_by_category'){                    
                    that.yData = response.data.data.date;
                    that.xData = response.data.data.labels;
                }                
            }).catch(function (error) {
                console.log(error);
            })
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
        }       
    },
    created(){        
        this.fetchData();        
    },
}
</script>
