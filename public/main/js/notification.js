var alert = Swal.mixin({
    toast: true,
    icon: '',
    title: '',
    position: 'top-right',
    showConfirmButton: false,
    timer: 3500,
    allowEscapeKey: true,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});
