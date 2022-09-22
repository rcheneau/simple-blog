export enum Type {
    info = 'bg-primary',
    error = 'bg-danger',
}

class ToastManager {
    private toastContainer: HTMLElement;
    private template: Document;

    public constructor() {
        this.toastContainer = document.getElementById('toast-container')!;
        const html = this.toastContainer.dataset.toastTpl!;
        this.template = new DOMParser().parseFromString(html, 'text/html')
    }

    public show(content = '', type = Type.info) {
        this.template.getElementsByClassName('toast')[0].classList.add(type);
        this.template.getElementsByClassName('toast-body')[0].textContent = content;
        this.toastContainer.insertAdjacentElement("beforeend", this.template.body);
    }
}

export default ToastManager;