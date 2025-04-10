@if (session()->has('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: '{{ session('toast')['type'] ?? 'success' }}',
                title: '{{ session('toast')['title'] ?? '' }}',
                text: '{{ session('toast')['message'] ?? '' }}',
            });
        });
    </script>
@endif
