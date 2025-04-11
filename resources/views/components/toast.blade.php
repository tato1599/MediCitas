<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#fff',
        color: '#000',
    });

    document.addEventListener('DOMContentLoaded', function () {
        @if (session()->has('toast'))
            Toast.fire({
                icon: '{{ session('toast')['type'] ?? 'success' }}',
                title: '{{ session('toast')['title'] ?? '' }}',
                text: '{{ session('toast')['message'] ?? '' }}',
            });
        @endif
    });
</script>
