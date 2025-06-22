<template>
  <div>
      <slot 
          :collection="collection" 
          :page="page" 
          :lastPage="lastPage" 
          :fetchData="fetchData"
          :loading="loading">
      </slot>
  </div>
</template>

<script>
import { ref, computed, watch, nextTick } from 'vue';

export default {
  props: {
    modelValue: {
      type: Array,
      default: () => []
    },
    url: String,
    accumulate: Boolean,
    initialPayload: Object,
    initialSorts: {
      type: Array,
      default: () => []
    },
    initialPage: {
      type: Number,
      default: 1
    },
    initialPerPage: Number,
    initialPerPageOptions: Array,
    primaryKey: {
      type: String,
      default: "id"
    },
    config: {
      type: Object,
      default: () => ({})
    },
    params: {
      type: Object,
      default: () => ({})
    }
  },
  emits: ['update:modelValue', 'update:page', 'update:last-page', 'loaded', 'failed', 'input'],
  setup(props, { emit }) {
      const sorts = ref(props.initialSorts);
      const page = ref(props.initialPage || 1);
      const perPage = ref(props.initialPerPage);
      const perPageOptions = ref(props.initialPerPageOptions);
      const collection = ref([]);
      const from = ref(null);
      const to = ref(null);
      const total = ref(null);
      const lastPage = ref(null);
      const meta = ref(null);
      const error = ref(null);
      const errorDetail = ref(null);
      const loading = ref(false);
      const selected = computed({
      get: () => props.modelValue,
      set: (value) => emit('update:modelValue', value)
      });

      watch(() => props.url, () => {
          fetchData(true);
      });

      watch(selected, (newSelected) => {
          emit('input', newSelected);
      });

      watch(page, (newPage) => {
      console.log('Page changed to:', newPage);
      emit('update:page', newPage);
      });

      watch(() => props.page, (newPage) => {
      console.log('Page prop changed:', newPage);
      setPage(newPage);
      });

      watch(() => page.value, (newPage) => {
      console.log('Page updated:', newPage);
      emit('update:page', newPage);
      });

      watch(() => lastPage.value, (newLastPage) => {
      console.log('Last page updated:', newLastPage);
      emit('update:last-page', newLastPage);
      });

      const showingAll = computed(() => perPage.value === false);

      const perPageOptionsMapped = computed(() => {
      return perPageOptions.value.reduce(
          (mapped, option) => {
          mapped[option] = option === false ? "All" : option;
          return mapped;
          },
          perPage.value ? { [perPage.value]: perPage.value } : {}
      );
      });

      const sortStatus = computed(() => {
      return (sorts.value || []).reduce((result, sort) => {
          const [field, direction = "asc"] = sort.split(":");
          result[field] = direction;
          return result;
      }, {});
      });

      const pagingInfo = computed(() => {
      if (loading.value) return "Loading...";
      if (!from.value) {
          return '<span class="text-danger">No result</span>';
      }
      return `Showing ${from.value} to ${to.value}${total.value ? ` of ${total.value}` : ""}`;
      });

      const ajaxOptions = computed(() => {
      console.log("Props params:", props.params);
      return {
          params: {
          page: page.value,
          per_page: perPage.value,
          sorts: sorts.value,
          ...props.params
          },
          headers: { "Cache-Control": "no-cache" },
          ...props.config
      };
      });

      const listKeys = computed(() => collection.value.map(item => item[props.primaryKey]));

      const listSelected = computed(() => {
      if (!listKeys.value || listKeys.value.length < 1) return false;
      return listKeys.value.filter(isSelected).length === listKeys.value.length;
      });

      function setPage(newPage = 1) {
          console.log('1Listing: setPage called with', newPage);
      }

      function nextPage() {
          page.value++;
          console.log("nextPage", page.value);
          fetchData();
      }

      function clearSort() {
      sorts.value = [];
      fetchData();
      }

      function setSort(field, direction, reset = true) {
      direction = direction || sortStatus.value[field] === "asc" ? "desc" : "asc";
      if (reset) clearSort();
      else unsetSort(field);
      sorts.value.push(`${field}:${direction}`);
      fetchData();
      }

      function unsetSort(field) {
      sorts.value = sorts.value.filter(sort => {
          const [sortField] = sort.split(":");
          return sortField !== field;
      });
      fetchData();
      }

      async function fetchData(resetPage = false, newpage = 1) {
          page.value = newpage;
          if (!props.url) return;
          if (resetPage) page.value = 1;
          loading.value = true;
          
          // 添加日志记录请求的参数
          console.log("Sending request with params:", ajaxOptions.value.params);
          
          try {
              const { data: payload } = await axios.get(props.url, ajaxOptions.value);
              console.log("Data", payload)
              if (resetPage && props.accumulate) collection.value = [];
              extractPayload(payload);
              loading.value = false;
              emit("loaded", payload);
          } catch (error) {
              const { message, detail } = error;
              error.value = message || "Unknown Error Occurred";
              errorDetail.value = detail;
              loading.value = false;
              emit("failed", error.value);
          }
      }

      function extractPayload(payload) {
          const { data, links, meta: payloadMeta } = payload;

          if (props.accumulate) {
              collection.value.push(...data);
          } else {
              collection.value = data;
          }
          meta.value = payloadMeta;
          if (payloadMeta && payloadMeta.current_page !== undefined) {
              // with paging
              perPage.value = perPage.value || payloadMeta.per_page;
              page.value = page.value || payloadMeta.current_page;
              lastPage.value = payloadMeta.last_page || (links.next ? payloadMeta.current_page + 1 : payloadMeta.current_page);
              from.value = payloadMeta.from;
              to.value = payloadMeta.to;
              total.value = payloadMeta.total;
          } else {
              // show all
              perPage.value = 0;
              page.value = 1;
              lastPage.value = 1;
              from.value = 1;
              to.value = data.length;
              total.value = data.length;
          }

          page.value = payloadMeta.current_page || 1;
          lastPage.value = payloadMeta.last_page || 1;
          emit('update:page', page.value);
          emit('update:last-page', lastPage.value);  // 使用 kebab-case
          console.log('Extracted page:', page.value, 'lastPage:', lastPage.value);
      }

      function dismissError() {
      error.value = null;
      errorDetail.value = null;
      }

      function isSelected(key) {
      return selected.value.indexOf(key) >= 0;
      }

      function toggleSelection(key, state) {
      if (state === undefined) state = !isSelected(key);
      const index = selected.value.indexOf(key);
      const newSelected = [...selected.value];
      if (index >= 0) newSelected.splice(index, 1);
      if (state) newSelected.push(key);
      selected.value = newSelected;
      }

      function toggleListSelection(state) {
      if (state === undefined) state = !listSelected.value;
      const newSelected = selected.value.filter(selected => {
          return listKeys.value.indexOf(selected) < 0;
      });
      if (state) newSelected.push(...listKeys.value);
      selected.value = newSelected;
      }

      function onPageUpdate(newPage) {
          console.log("onPageUpdate", newPage);
          page.value = newPage;
      }

      function onLastPageUpdate(newLastPage) {
          lastPage.value = newLastPage;
      }

    // 初始化
    if (props.initialPayload) {
      extractPayload(props.initialPayload);
    } else {
      fetchData();
    }

    // 添加一个方法，确保加载状态可以被外部直接控制
    function setLoading(value) {
      console.log("直接设置loading状态:", value);
      loading.value = value;
    }

    return {
      collection,
      sortStatus,
      isSelected,
      toggleSelection,
      from,
      primaryKey: props.primaryKey,
      loading,
      error,
      errorDetail,
      dismissError,
      perPage,
      perPageOptionsMapped,
      fetchData,
      page,
      lastPage,
      setPage,
      toggleListSelection,
      listSelected,
      showingAll,
      pagingInfo,
      nextPage,
      clearSort,
      setSort,
      unsetSort,
      onPageUpdate,
      onLastPageUpdate,
      setLoading,
    };
  },
  methods: {
      
  }
};
</script>