(function() {
    const input = document.getElementById('galeria-input');
    const preview = document.getElementById('galeria-preview');
    const principalInput = document.getElementById('galeria_principal');
    const form = input ? input.closest('form') : null;
    /** @type {File[]} */
    let selectedFiles = [];

    function rebuildInputFiles() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }

    function rebuildPreview() {
        preview.innerHTML = '';
        selectedFiles.forEach((file, idx) => {
            const card = document.createElement('div');
            card.className = 'border rounded p-2 flex flex-col items-center gap-2 bg-blue-50';

            const topRow = document.createElement('div');
            topRow.className = 'w-full flex items-center justify-between';
            const label = document.createElement('label');
            label.className = 'text-xs text-blue-700 font-medium';
            label.textContent = `Nuevo ${idx + 1}`;
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.textContent = '✕';
            removeBtn.title = 'Eliminar de la selección';
            removeBtn.style = 'font-size:12px;color:#b91c1c;background:#fee2e2;border:1px solid #fecaca;border-radius:4px;padding:0 6px;';
            removeBtn.addEventListener('click', () => {
                selectedFiles.splice(idx, 1);
                rebuildInputFiles();
                rebuildPreview();
            });
            topRow.appendChild(label);
            topRow.appendChild(removeBtn);
            card.appendChild(topRow);

            // preview
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'w-24 h-24 object-cover rounded';
                img.src = URL.createObjectURL(file);
                card.appendChild(img);
            } else if (file.type === 'video/mp4') {
                const vid = document.createElement('video');
                vid.className = 'w-24 h-24 object-cover rounded';
                vid.src = URL.createObjectURL(file);
                vid.controls = true;
                card.appendChild(vid);
            }

            const newLabel = document.createElement('div');
            newLabel.className = 'text-xs text-blue-600';
            newLabel.textContent = 'Se agregará a la galería';
            card.appendChild(newLabel);

            preview.appendChild(card);
        });
    }

    input?.addEventListener('change', function(e) {
        const files = Array.from(e.target.files || []);
        if (!files.length) return;
        input.value = '';
        selectedFiles.push(...files);
        rebuildInputFiles();
        rebuildPreview();
    });

    form?.addEventListener('submit', function() {
        rebuildInputFiles();
    });
})();

// Eliminar imágenes existentes con confirmación
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('delete-image-btn')) {
        e.preventDefault();
        e.stopPropagation();
        
        const imageId = e.target.dataset.imageId;
        const card = e.target.closest('[data-image-id="' + imageId + '"]');
        const deleteBtn = e.target;
        
        console.log('Eliminando imagen ID:', imageId);
        console.log('Card encontrada:', card);
        
        if (!card) {
            console.error('No se encontró la card para la imagen:', imageId);
            return;
        }
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta imagen se eliminará permanentemente del servidor y no se podrá recuperar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Deshabilitar el botón y mostrar loading
                deleteBtn.disabled = true;
                deleteBtn.textContent = 'Eliminando...';
                deleteBtn.style.opacity = '0.5';
                
                // Enviar petición AJAX para eliminar
                // Usar la configuración inyectada por la vista (window.productosEdit)
                    const base = window.productosEdit && window.productosEdit.deleteUrlBase ? window.productosEdit.deleteUrlBase : '/productos/images';
                    const token = window.productosEdit && window.productosEdit.csrfToken ? window.productosEdit.csrfToken : null;
                    const url = base.replace(/\/$/, '') + '/' + imageId;

                    fetch(url, {
                        method: 'DELETE',
                        headers: Object.assign({
                            'Accept': 'application/json'
                        }, token ? {'X-CSRF-TOKEN': token} : {}),
                        credentials: 'same-origin'
                    })
                    .then(async response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            // Try to parse JSON error, fallback to statusText
                            let err = { message: response.statusText };
                            try { err = await response.json(); } catch (e) { /* ignore */ }
                            throw err;
                        }
                        return response.json();
                    })
                    .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Eliminar inmediatamente del DOM sin animación para asegurar que desaparezca
                        // Eliminar cualquier elemento con data-image-id
                        const cardSelector = '[data-image-id="' + data.imagen_id + '"]';
                        const nodes = document.querySelectorAll(cardSelector);
                        nodes.forEach(n => n.remove());

                        // Eliminar cualquier radio que pudiera apuntar a esta imagen
                        const radios = document.querySelectorAll('input[name="galeria_principal_radio"][value="' + data.imagen_id + '"]');
                        radios.forEach(r => {
                            const parentLabel = r.closest('label');
                            if (parentLabel) parentLabel.remove();
                            else r.remove();
                        });

                        // Si el servidor indica que esa imagen era la principal, limpiar el hidden
                        const principalHidden = document.getElementById('galeria_principal');
                        if (data.removed_principal && principalHidden) {
                            principalHidden.value = '';
                        }

                        // Además, si existe una card marcada con data-type="principal" y la ruta coincide, eliminarla
                        if (data.ruta) {
                            const galeriaExistente = document.getElementById('galeria-existente');
                            if (galeriaExistente) {
                                const principalCard = galeriaExistente.querySelector('[data-type="principal"]');
                                if (principalCard) {
                                    // comparar por imagen src que contenga la ruta
                                    const imgs = principalCard.querySelectorAll('img, video');
                                    imgs.forEach(el => {
                                        const src = (el.tagName.toLowerCase() === 'img') ? el.getAttribute('src') : el.getAttribute('src');
                                        if (src && src.indexOf(data.ruta) !== -1) {
                                            principalCard.remove();
                                        }
                                    });
                                }
                            }
                        }
                        
                        // Verificar si quedan imágenes en la galería
                        const galeriaExistente = document.getElementById('galeria-existente');
                        const remainingImages = galeriaExistente ? galeriaExistente.querySelectorAll('[data-image-id]') : [];
                        
                        // Si no hay imagen principal tampoco, mostrar mensaje de galería vacía
                        const principalExists = galeriaExistente ? galeriaExistente.querySelector('[data-type="principal"]') : null;
                        
                        if (remainingImages.length === 0 && !principalExists) {
                            const existingMsg = galeriaExistente.querySelector('.text-gray-500');
                            if (!existingMsg && galeriaExistente) {
                                const noImagesMsg = document.createElement('p');
                                noImagesMsg.className = 'text-sm text-gray-500 col-span-full';
                                noImagesMsg.textContent = 'No hay archivos en la galería.';
                                galeriaExistente.appendChild(noImagesMsg);
                            }
                        }
                        
                        Swal.fire({
                            title: '¡Eliminada!',
                            text: 'La imagen ha sido eliminada correctamente.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        console.error('Error del servidor:', data.message);
                        // Restaurar botón en caso de error
                        deleteBtn.disabled = false;
                        deleteBtn.textContent = 'Eliminar';
                        deleteBtn.style.opacity = '1';
                        
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'No se pudo eliminar la imagen.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error completo:', error);
                    
                    // Restaurar botón en caso de error
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = 'Eliminar';
                    deleteBtn.style.opacity = '1';
                    
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al eliminar la imagen. Por favor, intenta de nuevo.',
                        icon: 'error'
                    });
                });
            }
        });
    }
});
