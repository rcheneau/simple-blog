import { Controller } from '@hotwired/stimulus';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller<HTMLElement> {
    async reload(event: CustomEvent) {
        const res = await fetch(event.detail.url, {
            headers: {'X-Requested-with': 'XMLHttpRequest'},
        });

        console.log(event.detail.scrollToTopOfElement)
        if (event.detail.scrollToTopOfElement === true) {
            this.element.scrollIntoView();
        }

        this.element.outerHTML = await res.text();
        history.pushState({ }, '', event.detail.url);
    }
}
