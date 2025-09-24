document.addEventListener('DOMContentLoaded', () => {
    let page = 2;
    let loading = false;
    let finished = false;

    const container = document.getElementById('productos-container');
    const loader = document.getElementById('loader');
    const searchInput = document.getElementById('search-input');
    const resultadosContainer = document.getElementById('resultados-container');
    const searchBtn = document.getElementById('search-btn');

    // ---------- Infinite scroll (carga más productos en la grilla) ----------
    window.addEventListener('scroll', () => {
        if (finished || loading) return;
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
            loading = true;
            loader.style.display = 'block';

            axios.get(`${window.location.pathname}?page=${page}&q=${encodeURIComponent(searchInput.value)}`)
                .then(res => {
                    if (!res.data.trim()) {
                        finished = true;
                    } else {
                        // Insertar al final del grid
                        container.insertAdjacentHTML('beforeend', res.data);
                        page++;
                    }
                })
                .catch(err => console.error(err))
                .finally(() => {
                    loading = false;
                    loader.style.display = 'none';
                });
        }
    });

    // ---------- SweetAlert delegada para formularios de eliminación ----------
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.classList && form.classList.contains('delete-form')) {
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

    // ---------- Búsqueda rápida (resultados relevantes arriba del grid) ----------
    const doQuickSearch = (query) => {
        if (!resultadosContainer) return;
        if (!query || query.length < 2) {
            resultadosContainer.innerHTML = '';
            resultadosContainer.classList.add('hidden');
            return;
        }

        axios.get('/productos/buscar', { params: { q: query } })
            .then(response => {
                const html = response.data || '';
                if (html.trim()) {
                    resultadosContainer.innerHTML = html;
                } else {
                    resultadosContainer.innerHTML = `<p class="text-gray-500">No se encontraron productos relevantes.</p>`;
                }
                resultadosContainer.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                resultadosContainer.innerHTML = `<p class="text-red-500">Error buscando productos.</p>`;
                resultadosContainer.classList.remove('hidden');
            });
    };

    // Debounce para la búsqueda (teclado)
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            clearTimeout(debounceTimer);

            // Si presionó Enter: disparar búsqueda inmediatamente (no navegar)
            if (e.key === 'Enter') {
                e.preventDefault();
                const q = searchInput.value.trim();
                doQuickSearch(q);
                return;
            }

            const q = searchInput.value.trim();
            debounceTimer = setTimeout(() => doQuickSearch(q), 400);
        });
    }

    // Click en botón de búsqueda (dispara búsqueda inmediata)
    if (searchBtn) {
        searchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const q = (searchInput ? searchInput.value.trim() : '');
            doQuickSearch(q);
        });
    }

    // Si el usuario borra el input, ocultar resultados y resetear estado de paginación
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            if (!searchInput.value.trim()) {
                resultadosContainer.innerHTML = '';
                resultadosContainer.classList.add('hidden');
                // resetear paginación para scroll si hacía falta
                page = 2;
                finished = false;
            }
        });
    }
});
