@if (session('success') || session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: '{{ session('success') ? 'success' : 'error' }}',
                    title: '{{ session('success') ?? session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
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
