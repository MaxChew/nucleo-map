import { computed } from 'vue';
import _ from 'lodash';
export default {
    data() {
        return {
        }
    },
    computed: {
        countrysLabel() {
            if (!this.$.isEmpty(this.filters["countrys"])) {
                const selected = this.filters["countrys"].slice(0);
                return selected.length > 1
                    ? selected.length + " Countries"
                    : selected
                          .map((countrys) => this.countrys[countrys])
                          .join(", ");
            }
        },
        filterParams() {
            const filterParams = _.omitBy(this.filters, value => !value);

            if (filterParams.keyword) {
                filterParams.keyword = '~' + filterParams.keyword
            }
            if (!_.isEmpty(this.filters.status)) {
                filterParams.status = this.filters.status
            }
            return filterParams
        }
    },
    methods: {
        resetSummary(summary) {
            console.log('summary', summary);
            this.$root.selectedRow = []
            if (summary != null) {
                this.$root.summary = summary
            }
        },
        applyCountrysFilter() {
            Vue.set(this.filters, "countrys", this.currentCountrys.slice(0));
            this.$refs.listing.fetchData(true);
        },
        clearCountrysFilter() {
            this.currentCountrys = [];
            this.applyCountrysFilter();
        },
        resetFilters() {
            this.filters = _.pick(this.filters, ['status','years','countrys'])
            this.$refs.listing.fetchData(true)
        },
        filterListing(e, vm) {
            vm.loading = false
            e.preventDefault()
            this.$refs.listing.fetchData(true)
        }
    },
}