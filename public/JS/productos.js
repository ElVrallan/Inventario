document.addEventListener('DOMContentLoaded', () => {
    let page = 2;
    let loading = false;
    let finished = false;

    const container = document.getElementById('productos-container');
    const loader = document.getElementById('loader');
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');
    let previewBox;

    // Crear contenedor para preview dinámico
    previewBox = document.createElement('div');
    previewBox.id = "search-preview";
    previewBox.className = "bg-white border rounded shadow mt-1 absolute w-full z-50";
    previewBox.style.display = "none";
    searchInput.parentNode.appendChild(previewBox);

    // Buscar dinámico
    searchInput.addEventListener('input', () => {
        const q = searchInput.value.trim();
        if (!q) {
            previewBox.style.display = "none";
            return;
        }
        axios.get(`/productos/search/preview?q=${q}`)
            .then(res => {
                previewBox.innerHTML = res.data;
                previewBox.style.display = "block";
            })
            .catch(() => {
                previewBox.innerHTML = "<p class='p-2 text-gray-500'>Error buscando productos.</p>";
                previewBox.style.display = "block";
            });
    });

    // Buscar completo (click botón o enter)
    searchButton.addEventListener('click', () => {
        const q = searchInput.value.trim();
        if (q) window.location.href = `/productos/search?q=${q}`;
    });
    searchInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchButton.click();
        }
    });

    // Infinite scroll
    window.addEventListener('scroll', () => {
        if (finished || loading) return;
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
            loading = true;
            loader.style.display = 'block';
            
            axios.get(`${window.location.pathname}?page=${page}&q=${searchInput.value}`)
                .then(res => {
                    if (!res.data.trim()) {
                        finished = true;
                    } else {
                        container.insertAdjacentHTML('beforeend', res.data);
                        page++;
                    }
                    loading = false;
                    loader.style.display = 'none';
                })
                .catch(err => {
                    console.error(err);
                    loading = false;
                    loader.style.display = 'none';
                });
        }
    });

    // SweetAlert para eliminar
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
