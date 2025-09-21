document.addEventListener('DOMContentLoaded', () => {
    let page = 2;
    let loading = false;
    let finished = false;

    const container = document.getElementById('productos-container');
    const loader = document.getElementById('loader');
    const searchInput = document.getElementById('search-input');

    // Infinite scroll
    window.addEventListener('scroll', () => {
        if (finished || loading) return;
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
            loading = true;
            loader.style.display = 'block';
            axios.get(`{{ route('productos.index') }}?page=${page}&q=${searchInput.value}`)
                .then(res => {
                    if (!res.data.trim()) finished = true;
                    else container.insertAdjacentHTML('beforeend', res.data);
                    page++;
                    loading = false;
                    loader.style.display = 'none';
                })
                .catch(err => { loading = false; loader.style.display = 'none'; console.error(err); });
        }
    });

    // Búsqueda en tiempo real
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            page = 1;
            finished = false;
            axios.get(`{{ route('productos.index') }}?q=${searchInput.value}`)
                .then(res => {
                    container.innerHTML = res.data;
                    page = 2;
                });
        }, 300);
    });

    // SweetAlert para eliminar productos
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.classList.contains('delete-form')) {
            e.preventDefault();
            Swal.fire({
                title: '¿Seguro que deseas eliminar este producto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed) form.submit();
            });
        }
    });
});
