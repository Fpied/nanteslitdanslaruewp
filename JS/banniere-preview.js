const titre = document.querySelector('.transparencetitle');

if (titre) {
    const hero = titre.closest('.hero');
    let enDeplacement = false;

    titre.style.cursor = 'move';

    titre.addEventListener('pointerdown', (e) => {
        enDeplacement = true;
        titre.setPointerCapture(e.pointerId);
        e.preventDefault();
    });

    titre.addEventListener('pointermove', (e) => {
        if (!enDeplacement) return;

        const cadre = hero.getBoundingClientRect();
        let x = (e.clientX - cadre.left) / cadre.width * 100;
        let y = (e.clientY - cadre.top) / cadre.height * 100;

        x = Math.min(100, Math.max(0, x));
        y = Math.min(100, Math.max(0, y));

        titre.style.left = x + '%';
        titre.style.top = y + '%';
    });

    titre.addEventListener('pointerup', () => {
        if (!enDeplacement) return;
        enDeplacement = false;

        const x = Math.round(parseFloat(titre.style.left));
        const y = Math.round(parseFloat(titre.style.top));
        wp.customize.preview.send('nlr-position-titre', {
            id: titre.dataset.banniere,
            x: x,
            y: y
        });
    });
}