import {Controller} from '@hotwired/stimulus';
import {Modal} from 'bootstrap';
import {useDispatch} from "stimulus-use";

// noinspection JSUnusedGlobalSymbols
export default class extends Controller {
    static targets = ['modal', 'msg'];
    modal = null;
    href = null;
    csrfToken = null;

    connect() {
        useDispatch(this);
    }

    confirmDelete(event) {
        event.preventDefault();

        this.msgTarget.innerText = event.currentTarget.dataset.msg;
        this.csrfToken = event.currentTarget.dataset.csrfToken;

        this.modal = new Modal(this.modalTarget);
        this.modal.show();

        this.href = event.currentTarget.getAttribute('href');
    }

    async delete() {
        this.modal.hide();
        console.log(this.href)
        const res = await fetch(this.href, {
            method: 'post',
            headers: {'X-Requested-with': 'XMLHttpRequest', 'X-CSRF-Token': this.csrfToken},
        });

        if (res.status === 204) {
            this.dispatch('deleted', {
                url: window.location.href,
            });
        }
        console.error(res.statusText)
    }
}