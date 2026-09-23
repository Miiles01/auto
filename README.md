# PC Auto — site web

Site statique (HTML, CSS, JavaScript) pour PC Auto, Granby et Sainte-Eulalie. Aucune compilation : les fichiers se déploient tels quels sur Hostinger (Custom PHP/HTML).

## Pages
- `index.html` : accueil (héros, catégories, inventaire en vedette, services, inspection, financement, formulaire, marques, succursales)
- `inventaire.html` : inventaire complet avec recherche, filtres et tri (liens partageables : `?type=vus`, `?succursale=Granby`, `?prix=8000`, `?q=kia`)
- `vehicule.html?v=<slug>` : fiche véhicule (galerie, caractéristiques, options, calculateur de paiements)

## Administration (démo)
`/admin` ouvre la connexion (identifiants préremplis : `admin@pcauto.ca` / `demo-pcauto`), puis le panneau :
- **Tableau de bord** : véhicules en ligne, valeur de l'inventaire, prix moyen, demandes, activité, fiches à compléter.
- **Inventaire** : prix modifiables directement dans le tableau, statut (en ligne, réservé, vendu, brouillon), vedette, recherche, filtres, tri et actions groupées.
- **Ajouter / modifier** : fiche complète, photos (compressées, réordonnables), options, aperçu en direct.
- **Demandes** : formulaire du site et boutons « Réserver un essai routier », avec suivi et notes.
- **Réglages** : véhicules en préparation, taux du calculateur, export JSON, réinitialisation.

Démo sans serveur : tout est enregistré dans le navigateur (`localStorage`, clé `pc-admin-v1`) via `assets/js/store.js`, puis appliqué au site public. Pour une gestion partagée, brancher une base de données (PHP + SQLite sur Hostinger) et une vraie authentification.

## Mettre l'inventaire à jour
Tout l'inventaire est dans `assets/js/data.js`. Chaque véhicule a son prix, kilométrage, options et la liste de ses photos (`assets/img/autos/` en 940 px et `assets/img/autos/thumb/` en 720 px, format WebP). Pour retirer un véhicule vendu, supprimez son bloc ; pour en ajouter un, copiez un bloc existant.

Après une modification de CSS ou JS, augmentez le numéro `?v=` dans les trois pages HTML pour forcer les navigateurs à recharger les fichiers.

## Vidéo de l'accueil
`assets/video/hero.mp4` (1280×720, 25 s, sans son) est un montage des photos du terrain. Pour utiliser une vraie vidéo du concessionnaire, remplacez ce fichier (MP4 H.264, idéalement moins de 5 Mo) et `assets/video/hero-poster.webp` (image affichée pendant le chargement et en mode « mouvement réduit »).

## Témoignages (exemples)
Le composant `assets/js/temoignages.js` s'affiche sur l'accueil et en bas de l'inventaire (conteneur `data-temoignages`). **Les six témoignages sont des exemples de démonstration** (photos d'illustration dans `assets/img/avatars/`) : avant la mise en ligne publique, les remplacer par de vrais avis clients, avec leur accord.

## Formulaire
Le formulaire ouvre WhatsApp (450 378-0888) avec le message prérempli : aucun serveur requis.

## Déploiement
La branche `deploy` est synchronisée avec Hostinger. Travailler sur `main`, puis fusionner dans `deploy`.
