<template>
    <div>
      <apexchart width="100%" height="350" type="bar" :options="options" :series="series"></apexchart>
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
      options: {
        chart: {
          id: 'vuechart-example'
        },
        xaxis: {
          categories: this.xData
        }
      },
      series: [{
        name: this.$t('models.building.requestStatuses.total'),
        data: this.yData
      }]
    }
  }
}
</script>