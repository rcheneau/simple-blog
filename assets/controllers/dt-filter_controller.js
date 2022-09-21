import { Controller } from '@hotwired/stimulus';
import { useDispatch, useDebounce } from 'stimulus-use';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller {
    static debounces = ['textSearch']

    connect() {
        useDispatch(this, {eventPrefix: false});
        useDebounce(this, {wait: 500});
    }

    textSearch(e) {
        const url = new URLSearchParams(e.target.dataset.params);
        url.set(`search[${e.target.dataset.paramName}]`, e.target.value)
        this.dispatch('reload', {
            url: `${e.target.dataset.pathInfo}?${url.toString()}`,
        })
    }
}
