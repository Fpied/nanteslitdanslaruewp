const { addFilter } = wp.hooks;
const { createElement: el, Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, ColorPalette, RangeControl } = wp.components; // NOUVEAU : RangeControl
const { createHigherOrderComponent } = wp.compose;

const blocsCibles = ['core/video', 'core/embed'];

const couleursAsso = [
    { name: 'Noir', color: '#000000' },
    { name: 'Blanc', color: '#FFFFFF' },
    { name: 'Rouge', color: '#FB725F' },
    { name: 'Bleu', color: '#659FCC' },
    { name: 'Vert', color: '#5AB74F' },
];

// Étape 1 : les attributs
addFilter('blocks.registerBlockType', 'nlr/ombre-attribut', (settings, name) => {
    if (!blocsCibles.includes(name)) {
        return settings;
    }
    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            ombreCouleur: { type: 'string', default: '' },
            ombreTaille: { type: 'number', default: 15 }, // NOUVEAU
            ombreArrondi: { type: 'number', default: 20 },
        },
    };
});

// Étape 2 : le panneau
const avecReglageOmbre = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        if (!blocsCibles.includes(props.name)) {
            return el(BlockEdit, props);
        }
        return el(Fragment, null,
            el(BlockEdit, props),
            el(InspectorControls, null,
                el(PanelBody, { title: "Ombre de la vidéo" },
                    el(ColorPalette, {
                        colors: couleursAsso,
                        value: props.attributes.ombreCouleur,
                        onChange: (couleur) => props.setAttributes({ ombreCouleur: couleur || '' }),
                    }),
                    // NOUVEAU : le curseur de taille
                    el(RangeControl, {
                        label: "Taille de l'ombre (px)",
                        value: props.attributes.ombreTaille,
                        onChange: (taille) => props.setAttributes({ ombreTaille: taille }),
                        min: 0,
                        max: 50,
                    }),
                    el(RangeControl, {
                        label: "Arrondi des coins (px)",
                        value: props.attributes.ombreArrondi,
                        onChange: (arrondi) => props.setAttributes({ ombreArrondi: arrondi }),
                        min: 0,
                        max: 100,
                    })

                )
            )
        );
    };
}, 'avecReglageOmbre');

addFilter('editor.BlockEdit', 'nlr/ombre-panneau', avecReglageOmbre);