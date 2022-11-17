import { Controller } from '@hotwired/stimulus';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller<HTMLElement> {
    scrollToTopOfElement = false;

    connect() {
        const self = this;
        window.onpopstate = async function () {
            await self.update(window.location.href, self.scrollToTopOfElement);
        };
    }

    async reload(event: CustomEvent) {
        await this.update(event.detail.url, event.detail.scrollToTopOfElement);
        history.pushState({ }, '', event.detail.url);
    }

    async update(url: string, scrollToTopOfElement = false) {
        this.scrollToTopOfElement = scrollToTopOfElement;

        const res = await fetch(url, {
            headers: {'X-Requested-with': 'XMLHttpRequest'},
        });

        if (scrollToTopOfElement) {
            this.element.scrollIntoView();
        }

        this.element.outerHTML = await res.text();
    }
}
