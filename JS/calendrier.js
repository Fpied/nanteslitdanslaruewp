document.addEventListener('click', async (event) => {
    const lien = event.target.closest('.calendrier__nav a');
    if (!lien) return;

    event.preventDefault();

    try {
        const reponse = await fetch(lien.href);
        const html = await reponse.text();

        const page = new DOMParser().parseFromString(html, 'text/html');
        const nouveau = page.querySelector('.calendrier');
        const ancien = document.querySelector('.calendrier');

        ancien.replaceWith(nouveau);
        history.pushState(null, '', lien.href);
    } catch (erreur) {
        window.location.href = lien.href;
    }
});

window.addEventListener('popstate', () => {
    window.location.reload();
});