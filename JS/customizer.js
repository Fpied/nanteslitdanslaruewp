jQuery.wp.wpColorPicker.prototype.options.palettes = ['#000000', "#FFFFFF", "#FB725F", "#659FCC", "#5AB74F"];
wp.customize.bind('ready', ()=> {
    const ranges = document.querySelectorAll('input[type="range"]');

    ranges.forEach((range) => {
        const label = range.labels[0];
        if(!label){
            return;
        }

        const valeur = document.createElement('span');
        label.append(valeur);

        const afficher = () => {
        const actuel = Number(range.value);
        const reglage = range.dataset.customizeSettingLink || '';
        if (reglage.endsWith('_rotation')){
            valeur.textContent = ' (' + actuel + '°)';
            return;
        }
        if (reglage.endsWith('_px')){
            valeur.textContent = ' (' + actuel + 'px)';
            return;
        }
        valeur.textContent = ' (' + actuel + '%)';
    };

        afficher();
        range.addEventListener('input', afficher);
    });

    wp.customize.previewer.bind('nlr-position-titre', (donnees) => {
        const reglageX = wp.customize(donnees.id + '_pos_x');
        const reglageY = wp.customize(donnees.id + '_pos_y');

        if (reglageX && reglageY) {
            reglageX.set(donnees.x);
            reglageY.set(donnees.y);
        }
    });
});