const listeMots = document.querySelector('.mots-animes');

function lancerAnimation(){
    if(!listeMots) return;
    const mots = listeMots.querySelectorAll('li');
    const fenetre = document.querySelector('.animation__fenetre');
    let index = 0;

    function afficherMot() {
        fenetre.textContent = mots[index].textContent;
        index = (index + 1) % mots.length;
    }

    afficherMot();
    setInterval(afficherMot, 3000);

}

lancerAnimation();