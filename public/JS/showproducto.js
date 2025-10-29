document.addEventListener('DOMContentLoaded', function () {
    // --- Elements (defensivo) ---
    const mainImg = document.querySelector('.showproducto-main-img');
    const galleryImgs = document.querySelectorAll('.showproducto-gallery img');
    const lightbox = document.getElementById('showproducto-lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const prevBtn = document.getElementById('prev-image');
    const nextBtn = document.getElementById('next-image');

    // Build images array only with existing srcs
    const images = [];
    if (mainImg && mainImg.src) images.push(mainImg.src);
    if (galleryImgs && galleryImgs.length) {
        Array.from(galleryImgs).forEach(img => {
            if (img && img.src) images.push(img.src);
        });
    }

    let currentIndex = 0;

    function showLightbox(index) {
        if (!lightbox || !lightboxImg || images.length === 0) return;
        currentIndex = (index >= 0 && index < images.length) ? index : 0;
        lightboxImg.src = images[currentIndex];
        lightbox.classList.add('active');
    }

    function hideLightbox() {
        if (!lightbox) return;
        lightbox.classList.remove('active');
    }

    function showNext() {
        if (images.length === 0) return;
        currentIndex = (currentIndex + 1) % images.length;
        if (lightboxImg) lightboxImg.src = images[currentIndex];
    }

    function showPrev() {
        if (images.length === 0) return;
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        if (lightboxImg) lightboxImg.src = images[currentIndex];
    }

    // Attach listeners only if relevant elements exist
    if (mainImg) {
        mainImg.style.cursor = 'pointer';
        mainImg.addEventListener('click', () => showLightbox(0));
    }

    if (galleryImgs && galleryImgs.length) {
        galleryImgs.forEach((img, idx) => {
            img.style.cursor = 'pointer';
            img.addEventListener('click', () => showLightbox(idx + (mainImg ? 1 : 0)));
        });
    }

    if (lightbox) {
        lightbox.addEventListener('click', (e) => {
            // close when clicking on overlay (not the image)
            if (e.target === lightbox) hideLightbox();
        });
    }

    if (nextBtn) nextBtn.addEventListener('click', (e) => { e.stopPropagation(); showNext(); });
    if (prevBtn) prevBtn.addEventListener('click', (e) => { e.stopPropagation(); showPrev(); });

    document.addEventListener('keydown', (e) => {
        if (!lightbox || !lightbox.classList.contains('active')) return;
        if (e.key === 'ArrowRight') showNext();
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'Escape') hideLightbox();
    });

    // --- SweetAlert para venta ---
    const venderForm = document.getElementById('vender-form');
    if (venderForm){
        venderForm.addEventListener('submit', function(e){
            e.preventDefault();
            Swal.fire({
                title: '¿Continuar con la venta?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, vender',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed){
                    this.submit();
                }
            });
        });
    }

    // --- SweetAlert para eliminar (solo admin) ---
    const eliminarForm = document.getElementById('eliminar-form');
    if(eliminarForm){
        eliminarForm.addEventListener('submit', function(e){
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
                if(result.isConfirmed){
                    this.submit();
                }
            });
        });
    }
});
