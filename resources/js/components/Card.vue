<template>
  <a :href="href" class="w-full">
    <div class="bg-bg shadow-md rounded-lg p-4 w-full hover:bg-gray-300 hover:dark:!bg-gray-600">
      <div class="flex items-center justify-between mb-4">
        <div
          class="bg-primary dark:bg-white w-8 h-8 p-5 lg:w-12 lg:h-12 flex items-center justify-center rounded-full"
        >
          <slot name="icon"></slot>
        </div>
        <h3 class="text-base ml-8 lg:text-lg font-semibold">{{ title }}</h3>
      </div>
      <div class="flex justify-between items-center mb-2">
        <span class="text-gray-600 dark:text-gray-300">{{
          value_one_title
        }}</span>
        <span class="text-fourth font-semibold">{{ listings.value_one }}</span>
      </div>
      <div
        class="flex justify-between items-center mb-2"
        v-if="value_two_title"
      >
        <span class="text-gray-600 dark:text-gray-300">{{
          value_two_title
        }}</span>
        <span class="text-fourth font-semibold">{{ listings.value_two }}</span>
      </div>

      <div v-if="Array.isArray(dynamic_values) && dynamic_values.length > 0">
        <div
          v-for="(item, index) in dynamic_values"
          :key="index"
          class="flex justify-between items-center mb-2"
        >
          <span class="text-gray-600 dark:text-gray-300">{{ item.label }}</span>
          <span class="font-semibold">{{ item.value }}</span>
        </div>
      </div>
    </div>
  </a>
</template>

<script>
import axios from "axios";

export default {
  props: {
    url: {
      type: String,
      required: true,
    },
    sort: {
      type: String,
      required: false,
      default: "",
    },
    title: {
      type: String,
      required: true,
      default: "Title",
    },
    value_one_title: {
      type: String,
      required: false,
    },
    value_two_title: {
      type: String,
      required: false,
    },
    dynamic_values: {
      type: Array,
      required: false,
    },
    href: {
      type: String,
      required: false,
      default: "#",
    },
  },
  data() {
    return {
      listings: [],
    };
  },
  mounted() {
    this.fetchListings();
    setInterval(this.fetchListings, 5 * 60 * 1000); // 5 minutes * 60 seconds * 1000 milliseconds
  },
  methods: {
    fetchListings() {
      let fullUrl = this.url;
      if (this.sort) {
        fullUrl +=
          (fullUrl.includes("?") ? "&" : "?") +
          `sorts%5B0%5D=${encodeURIComponent(this.sort)}`;
      }
      axios
        .get(fullUrl)
        .then((response) => {
          this.listings = response.data.data;
        })
        .catch((error) => {
          console.error("Error fetching listings:", error);
        });
    },
  },
};
</script>
