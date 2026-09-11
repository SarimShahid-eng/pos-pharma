@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json($errors->first()),
                    showConfirmButton: false,
                    // timer: 3000,
                    timerProgressBar: true,
                      didOpen: (toast) => {
                        toast.parentNode.style.zIndex = 99999;
                    },
                    customClass: {
                        popup: 'z-[99999]'
                    }
                });
            }, 200);
        });
    </script>
@endif
