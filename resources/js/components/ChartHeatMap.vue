<template>
    <div>
      <apexchart type=heatmap width=400 :options="chartOptions" :series="series" />
    </div>
</template>
<script>
import VueApexCharts from 'vue-apexcharts'

export default {
    components: {'apexchart': VueApexCharts},
    props: {
            xData: {
                type: Array,
                required: true
            },
            yData: {
                type: Array,
                required: true
            }
    },  
    data() {
        return {
            series: [
                {
                  name: "Series 1",
                  data: [{
                    x: 'W1',
                    y: 22
                  }, {
                    x: 'W2',
                    y: 29
                  }, {
                    x: 'W3',
                    y: 13
                  }, {
                    x: 'W4',
                    y: 32
                  }]
                },
                {
                  name: "Series 2",
                  data: [{
                    x: 'W1',
                    y: 43
                  }, {
                    x: 'W2',
                    y: 43
                  }, {
                    x: 'W3',
                    y: 43
                  }, {
                    x: 'W4',
                    y: 43
                  }]
                }
            ],
            chartOptions: {
                dataLabels: {
                  enabled: false
                },
                colors: ["#008FFB"]            
            }            
        }
    }
}
</script>