import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';

// noinspection JSUnusedGlobalSymbols
export default class extends Controller<HTMLElement> {
    connect() {
        const toastContainer = document.getElementById('toast-container')!;
        if (this.element.parentNode !== toastContainer) {
            toastContainer.appendChild(this.element);
            return;
        }

        const toast = new Toast(this.element);
        toast.show();
    }
}
