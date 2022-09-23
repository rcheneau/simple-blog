import { Controller } from '@hotwired/stimulus';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller<HTMLElement> {
    goTo(event: Event) {
        event.preventDefault();

        if (!(event.currentTarget instanceof HTMLAnchorElement)) {
            throw new Error('Event\'s current target must be link.');
        }

        this.dispatch('reload', {
            detail: { url: event.currentTarget.href, scrollToTopOfElement: true },
            prefix: '',
        });
    }
}
