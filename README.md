# PC Auto — maquette HTML/CSS pour WordPress

Version **sans JavaScript** du site, préparée pour être transférée dans un thème WordPress. La version animée (GSAP, préchargeur, panneau d'administration de démo) est conservée dans la branche `version-animada`.

## Contenu
- `index.html` : accueil
- `inventaire.html` : liste des véhicules (filtres = maquette statique, à brancher sur un plugin de filtres)
- `vehicule-<slug>.html` : une fiche par véhicule (modèle pour le gabarit « single » d'un type de contenu *Véhicule*)
- `assets/css/styles.css` : seule feuille de style
- `assets/img`, `assets/video` : médias

## Retiré par rapport à la version animée
Tout le JavaScript (`assets/js`), le panneau `/admin`, le préchargeur, la transition entre pages, le défilement doux, le curseur « Voir », le lecteur YouTube du héros (remplacé par `hero.mp4` en lecture automatique), la bascule CAD/USD, le calculateur de paiements, la visionneuse de photos, ainsi que les balises `canonical`, `og:*` et JSON-LD (à laisser à Yoast / Rank Math).

## Interactions restantes (CSS seulement)
Menu mobile (`:target`), survol des catégories et des services, points d'inspection, carrousel de témoignages (défilement horizontal), défilement des marques.

## À faire côté WordPress
- Véhicules : type de contenu personnalisé (ACF / JetEngine) ; les cartes `.car-card` et la fiche `vehicule-*.html` servent de gabarits.
- Formulaire : remplacer le `<form>` par Contact Form 7 ou WPForms.
- Filtres de l'inventaire : plugin (JetSmartFilters, FacetWP…).
- Les 6 témoignages sont des exemples : les remplacer par de vrais avis.
