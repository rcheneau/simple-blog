import { Controller } from '@hotwired/stimulus';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller {
    async reload(e) {
        const res = await fetch(e.detail.url, {
            headers: {'X-Requested-with': 'XMLHttpRequest'},
        });
        this.element.scrollIntoView();
        this.element.outerHTML = await res.text();
        history.pushState({ }, '', e.detail.url);
    }
}
