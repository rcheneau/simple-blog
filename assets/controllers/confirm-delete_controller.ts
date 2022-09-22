import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller<HTMLElement> {
    static targets = ['modal', 'msg'];
    declare readonly modalTarget: HTMLElement;
    declare readonly msgTarget: HTMLElement;

    private modal: Modal | null = null;
    private href: string | null = null;
    private csrfToken: string | null = null;

    confirmDelete(event: Event) {
        event.preventDefault();

        if (!(event.currentTarget instanceof HTMLElement)) {
            return;
        }

        this.msgTarget.innerText = event.currentTarget.dataset.msg || '';
        this.csrfToken = event.currentTarget.dataset.csrfToken || '';

        this.modal = new Modal(this.modalTarget);
        this.modal.show();

        this.href = event.currentTarget.getAttribute('href');
    }

    async delete() {
        if (!this.modal || !this.href){
            return;
        }

        this.modal.hide();
        const res = await fetch(this.href, {
            method: 'post',
            headers: {'X-Requested-with': 'XMLHttpRequest', 'X-CSRF-Token': this.csrfToken || ''},
        });

        if (res.status !== 204) {
            console.error(res.statusText);
        }

        this.dispatch('reload', {
            detail: { url: window.location.href },
            prefix: '',
        });
    }
}