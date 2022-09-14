import { Controller } from '@hotwired/stimulus';
import { useDispatch } from 'stimulus-use';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller {
    connect() {
        useDispatch(this);
    }

    goTo(e) {
        e.preventDefault();
        this.dispatch('goTo', {
            url: e.currentTarget.href,
        })
    }
}
