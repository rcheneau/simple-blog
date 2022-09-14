import { Controller } from '@hotwired/stimulus';
import { useDispatch } from 'stimulus-use';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller {
    async refresh(e) {
        const res = await fetch(e.detail.url, {
            headers: {'X-Requested-with': 'XMLHttpRequest'},
        });
        this.element.innerHTML = await res.text();
        history.pushState({ }, '', e.detail.url)
    }
}
