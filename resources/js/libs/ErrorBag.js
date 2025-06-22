import { flatten, unflatten } from 'flat';
import _ from 'lodash';

export default class ErrorBag {
    constructor(errors = {}) {
        this.errors = {};
        this.record(errors);
    }

    has(field) {
        return _.has(this.errors, field);
    }

    any() {
        return Object.keys(this.errors).length > 0;
    }

    get(field) {
        return field ? _.get(this.errors, field) : this.errors;
    }

    first(field) {
        const errors = this.get(field);
        if (!errors) return;
        return errors instanceof Array ? _.first(errors) : _.values(flatten(errors)).join('\n');
    }

    record(errors) {
        this.errors = unflatten(errors, { object: true });
    }

    clear(field) {
        if (field) {
            if (this.has(field)) {
                this.errors = _.omit(this.errors, field);
            }
            return;
        }
        this.errors = {};
    }

    toString() {
        return JSON.stringify(this.errors);
    }

    toListItem(limit) {
        const list = _.values(flatten(this.errors)).slice(0, limit)
            .reduce((listing, error) => {
                return listing + '<li>' + error + '</li>';
            }, '');
        return `<ul class="text-left">${list}</ul>`;
    }
}