import { Controller } from '@hotwired/stimulus';
import { useDebounce } from 'stimulus-use';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller<HTMLElement> {
    static debounces = ['textSearch'];

    connect() {
        useDebounce(this, {wait: 500});
    }

    textSearch(event: Event) {
        if (!(event.target instanceof HTMLInputElement)) {
            throw new Error('Event\'s current target must be link.');
        }

        const url = new URLSearchParams(event.target.dataset.params);
        url.set(`search[${event.target.dataset.paramName}]`, event.target.value)

        this.dispatch('reload', {
            detail: {  url: `${event.target.dataset.pathInfo}?${url.toString()}` },
            prefix: '',
        });
    }
}
