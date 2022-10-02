import { Controller } from '@hotwired/stimulus';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller<HTMLElement> {
    static values = {
        url: String
    }
    declare readonly urlValue: string;

    static targets = ['preview', 'form'];
    declare readonly previewTarget: HTMLElement;
    declare readonly formTarget: HTMLFormElement;

    async preview(event: Event) {
        event.preventDefault();

        const res = await fetch(this.urlValue, {
            method: this.formTarget.getAttribute('method') || 'POST',
            headers: {'X-Requested-with': 'XMLHttpRequest'},
            body: new FormData(this.formTarget),
        });


        this.previewTarget.innerHTML = await res.text();
    }
}
