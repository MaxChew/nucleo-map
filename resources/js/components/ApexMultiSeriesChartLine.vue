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
    <div class="text-xs text-right mt-1">
      <button 
        v-for="(seriesItem, index) in seriesNames" 
        :key="index"
        @click="toggleSeries(index)"
        class="px-2 py-1 mr-1 mb-1 rounded text-xs inline-flex items-center"
        :class="seriesVisibility[index] ? 'bg-gray-200' : 'bg-gray-100 text-gray-500'"
      >
        <span class="w-2 h-2 mr-1 rounded-full" :style="{ backgroundColor: getSeriesColor(index) }"></span>
        {{ seriesItem }}
      </button>
    </div>
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
    seriesNames: {
      type: Array,
      required: true,
      default: () => ['Approved', 'Rejected', 'Pending', 'Expired']
    },
    excludeSeries: {
      type: Array,
      required: false,
      default: () => []
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
    },
    showTotal: {
      type: Boolean,
      required: false,
      default: false
    }
  },
  data() {
    const currentYear = new Date().getFullYear();
    return {
      selectedYear: currentYear,
      availableYears: Array.from({ length: 5 }, (_, i) => currentYear - i),
      seriesVisibility: this.seriesNames.map(() => true),
      series: [],
      colors: [
        '#2E93fA', '#66DA26', '#FFB800', '#FF4560', '#775DD0', 
        '#3f51b5', '#03a9f4', '#4caf50', '#f9ce1d', '#FF9800'
      ],
      chartOptions: {
        chart: {
          id: "multi-series-line-chart",
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
        colors: this.colors,
        stroke: {
          width: 3,
          curve: 'smooth'
        },
        xaxis: {
          categories: []
        },
        legend: {
          show: false
        },
        tooltip: {
          shared: true,
          intersect: false
        },
        markers: {
          size: 4,
          hover: {
            size: 6
          }
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
    },
    filteredSeriesNames() {
      return this.seriesNames.filter(name => !this.excludeSeries.includes(name));
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
    setInterval(() => this.fetchData(), 5 * 60 * 1000); // 5 minutes refresh
  },
  methods: {
    getSeriesColor(index) {
      return this.colors[index % this.colors.length];
    },
    toggleSeries(index) {
      // 切换系列可见性
      this.$set(this.seriesVisibility, index, !this.seriesVisibility[index]);
      
      // 更新图表数据
      this.updateSeriesVisibility();
    },
    updateSeriesVisibility() {
      // 根据可见性更新系列数据
      for (let i = 0; i < this.series.length; i++) {
        if (this.seriesVisibility[i]) {
          this.series[i].visible = true;
        } else {
          this.series[i].visible = false;
        }
      }
      
      // 强制更新图表
      this.$nextTick(() => {
        this.series = [...this.series];
      });
    },
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
            
            // 准备多系列数据
            const newSeries = [];
            
            // 处理每个系列
            this.filteredSeriesNames.forEach((seriesName, index) => {
              // 检查该系列数据是否存在
              if (data[seriesName.toLowerCase()]) {
                const seriesData = sortedMeta.map(month => 
                  data[seriesName.toLowerCase()][month] || 0
                );
                
                newSeries.push({
                  name: seriesName,
                  data: seriesData,
                  visible: this.seriesVisibility[index]
                });
              }
            });
            
            // 添加总数系列（如果需要）
            if (this.showTotal && data.total) {
              const totalData = sortedMeta.map(month => data.total[month] || 0);
              newSeries.push({
                name: 'Total',
                data: totalData,
                visible: this.seriesVisibility[this.filteredSeriesNames.length]
              });
            }
            
            this.series = newSeries;
          } else {
            // 处理其他普通数据
            this.chartOptions.xaxis.categories = meta;
            const newSeries = [];
            
            // 处理每个系列
            this.filteredSeriesNames.forEach((seriesName, index) => {
              if (data[seriesName.toLowerCase()]) {
                const seriesData = meta.map(item => 
                  data[seriesName.toLowerCase()][item] || 0
                );
                
                newSeries.push({
                  name: seriesName,
                  data: seriesData,
                  visible: this.seriesVisibility[index]
                });
              }
            });
            
            this.series = newSeries;
          }

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

<style scoped>
.apexcharts-legend-text {
  margin-left: 8px !important;
}
</style> 