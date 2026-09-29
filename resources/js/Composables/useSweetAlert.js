const getPrimaryColor = () => {
    const value = getComputedStyle(document.documentElement)
        .getPropertyValue('--color-primary-600')
        .trim();

    return value ? `rgb(${value})` : '#9333ea';
};

const getPrimaryHover = () => {
    const value = getComputedStyle(document.documentElement)
        .getPropertyValue('--color-primary-700')
        .trim();

    return value ? `rgb(${value})` : '#7e22ce';
};

let swalModulePromise = null;

const loadSweetAlert = async () => {
    if (!swalModulePromise) {
        swalModulePromise = Promise.all([
            import('sweetalert2'),
            import('sweetalert2/dist/sweetalert2.min.css'),
        ]).then(([mod]) => mod.default);
    }

    return swalModulePromise;
};

const baseModal = () => ({
    buttonsStyling: false,
    reverseButtons: true,
    focusConfirm: false,
    allowOutsideClick: false,
    customClass: {
        popup: 'app-swal-popup',
        title: 'app-swal-title',
        htmlContainer: 'app-swal-text',
        icon: 'app-swal-icon',
        actions: 'app-swal-actions',
        confirmButton: 'app-swal-confirm',
        cancelButton: 'app-swal-cancel',
        denyButton: 'app-swal-cancel',
        closeButton: 'app-swal-close',
    },
    didOpen: (popup) => {
        popup.style.setProperty('--swal-primary', getPrimaryColor());
        popup.style.setProperty('--swal-primary-hover', getPrimaryHover());
    },
});

const createToast = (Swal) =>
    Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3200,
        timerProgressBar: true,
        showCloseButton: true,
        customClass: {
            popup: 'app-swal-toast',
            title: 'app-swal-toast-title',
            htmlContainer: 'app-swal-toast-text',
            timerProgressBar: 'app-swal-toast-timer',
        },
        didOpen: (toastEl) => {
            toastEl.style.setProperty('--swal-primary', getPrimaryColor());
            toastEl.onmouseenter = Swal.stopTimer;
            toastEl.onmouseleave = Swal.resumeTimer;
        },
    });

export function useSweetAlert() {
    const toast = async (title, text = '', icon = 'success') => {
        const Swal = await loadSweetAlert();
        return createToast(Swal).fire({
            icon,
            title,
            text: text || undefined,
        });
    };

    const success = (title, text = '') => toast(title, text, 'success');

    const info = (title, text = '') => toast(title, text, 'info');

    const error = async (title = 'Something went wrong', text = 'Please try again or contact support if it continues.') => {
        const Swal = await loadSweetAlert();
        return Swal.fire({
            ...baseModal(),
            icon: 'error',
            title,
            text,
            confirmButtonText: 'Got it',
            showCloseButton: true,
        });
    };

    const confirm = async ({
        title = 'Are you sure?',
        text = '',
        icon = 'question',
        confirmButtonText = 'Yes, continue',
        cancelButtonText = 'Cancel',
        danger = false,
    } = {}) => {
        const Swal = await loadSweetAlert();
        const modal = baseModal();
        const result = await Swal.fire({
            ...modal,
            icon,
            title,
            text: text || undefined,
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonText,
            cancelButtonText,
            customClass: {
                ...modal.customClass,
                confirmButton: danger ? 'app-swal-confirm app-swal-confirm-danger' : 'app-swal-confirm',
            },
        });

        return result.isConfirmed;
    };

    return {
        toast,
        success,
        info,
        error,
        confirm,
    };
}
