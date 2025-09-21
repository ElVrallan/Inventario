document.addEventListener('DOMContentLoaded', function () {
    // --- Carrusel ---
    const mainImg = document.querySelector('.showproducto-main-img');
    const galleryImgs = document.querySelectorAll('.showproducto-gallery img');
    const lightbox = document.getElementById('showproducto-lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const prevBtn = document.getElementById('prev-image');
    const nextBtn = document.getElementById('next-image');

    const images = [mainImg.src, ...Array.from(galleryImgs).map(img => img.src)];
    let currentIndex = 0;

    function showLightbox(index) {
        currentIndex = index;
        lightboxImg.src = images[currentIndex];
        lightbox.classList.add('active');
    }

    function hideLightbox() {
        lightbox.classList.remove('active');
    }

    function showNext() {
        currentIndex = (currentIndex + 1) % images.length;
        lightboxImg.src = images[currentIndex];
    }

    function showPrev() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        lightboxImg.src = images[currentIndex];
    }

    mainImg.style.cursor = 'pointer';
    mainImg.addEventListener('click', () => showLightbox(0));
    galleryImgs.forEach((img, idx) => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', () => showLightbox(idx + 1));
    });

    lightbox.addEventListener('click', (e) => {
        if(e.target === lightboxImg) return;
        hideLightbox();
    });

    nextBtn.addEventListener('click', (e) => { e.stopPropagation(); showNext(); });
    prevBtn.addEventListener('click', (e) => { e.stopPropagation(); showPrev(); });

    document.addEventListener('keydown', (e) => {
        if(!lightbox.classList.contains('active')) return;
        if(e.key === 'ArrowRight') showNext();
        if(e.key === 'ArrowLeft') showPrev();
        if(e.key === 'Escape') hideLightbox();
    });

    // --- SweetAlert para venta ---
    const venderForm = document.getElementById('vender-form');
    if(venderForm){
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
