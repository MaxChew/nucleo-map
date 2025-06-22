<template>
  <div>
    <div class="mb-3 flex justify-between items-center">
      <h4 class="font-medium">{{ displayTitle }}</h4>
      <select v-model="selectedYear" @change="fetchData" class="py-1 px-2 border border-gray-300 rounded text-sm">
        <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
      </select>
    </div>
    <apexchart type="line" class="w-full h-auto" 
      :options="chartOptions" :series="series">
    </apexchart>
  </div>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";

export default {
  components: {
    apexchart: VueApexCharts,
  },
  props: {
    url: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true,
      default: 'Title'
    },
    seriesName: {
      type: String,
      required: true,
      default: 'seriesName'
    },
    noResults: {
      type: String,
      required: false,
      default: 'No results available.'
    },
    showTools: {
      type: Boolean,
      required: false,
      default: true
    }
  },
  data() {
    const currentYear = new Date().getFullYear();
    return {
      selectedYear: currentYear,
      availableYears: Array.from({ length: 5 }, (_, i) => currentYear - i),
      series: [
        {
          name: this.seriesName,
          data: []
        }
      ],
      chartOptions: {
        chart: {
          id: "vuechart-line-example",
          toolbar: {
            show: this.showTools,
            tools: {
              download: true,
              selection: true,
              zoom: true,
              zoomin: true,
              zoomout: true,
              pan: true,
              reset: true,
              customIcons: []
            },
            export: {
              csv: {
                filename: undefined,
                columnDelimiter: ',',
                headerCategory: 'category',
                headerValue: 'value'
              },
              svg: {
                filename: undefined,
              },
              png: {
                filename: undefined,
              }
            },
            autoSelected: 'zoom',
            position: 'top',
            offsetX: 0,
            offsetY: 0
          }
        },
        xaxis: {
          categories: []
        },
        noData: {
          text: this.noResults
        }
      }
    };
  },
  computed: {
    displayTitle() {
      // 替换标题中的年份为选择的年份
      return this.title.replace(/\d{4}/, this.selectedYear);
    }
  },
  watch: {
    showTools: {
      handler(newValue) {
        this.chartOptions.chart.toolbar.show = newValue;
        // 强制重新渲染图表
        this.$nextTick(() => {
          this.chartOptions = { ...this.chartOptions };
        });
      },
      immediate: true
    }
  },
  mounted() {
    this.fetchData();
    setInterval(() => this.fetchData(), 5 * 60 * 1000); // 5 minutes * 60 seconds * 1000 milliseconds
  },
  methods: {
    fetchData() {
      // 构建包含年份参数的URL
      const urlWithParams = new URL(this.url, window.location.origin);
      urlWithParams.searchParams.set('year', this.selectedYear);
      
      axios.get(urlWithParams.toString())
        .then(response => {
          const data = response.data.data;
          const meta = response.data.meta;
          
          // 检查是否为月份数据（12个月）
          const isMonthData = meta.length === 12 && meta.includes('Jan') && meta.includes('Dec');
          
          if (isMonthData) {
            // 正确排序月份顺序
            const monthOrder = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            // 限制只显示到当前月份
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth(); // 0-11
            const currentYear = currentDate.getFullYear();
            
            // 如果所选年份是当前年份，只显示到当前月份
            let maxMonthIndex = 11; // 默认显示全年
            if (parseInt(this.selectedYear) === currentYear) {
              maxMonthIndex = currentMonth;
            } else if (parseInt(this.selectedYear) > currentYear) {
              // 未来年份不显示任何月份数据
              maxMonthIndex = -1;
            }
            
            // 过滤月份和对应数据
            const sortedMeta = monthOrder.filter((_, index) => index <= maxMonthIndex && meta.includes(monthOrder[index]));
            this.chartOptions.xaxis.categories = sortedMeta;
            
            // 按已排序月份重新组织数据
            const seriesData = sortedMeta.map(month => data[month] || 0);
            this.series[0].data = seriesData;
          } else {
            // 其他普通数据
            this.chartOptions.xaxis.categories = meta;
            const seriesData = meta.map(item => data[item] || 0);
            this.series[0].data = seriesData;
          }
          
          this.series[0].name = this.seriesName;

          // 强制重新渲染图表
          this.$nextTick(() => {
            this.chartOptions = { ...this.chartOptions };
            this.series = [...this.series];
          });
        })
        .catch(error => {
          console.error("Error fetching data: ", error);
        });
    }
  }
};
</script>