import { Controller } from '@hotwired/stimulus';
import { useDispatch } from 'stimulus-use';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller {
    connect() {
        useDispatch(this, {eventPrefix: false});
    }

    goTo(e) {
        e.preventDefault();
        this.dispatch('reload', {
            url: e.currentTarget.href,
        });
    }
}
