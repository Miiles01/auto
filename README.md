# PC Auto — site web

Site statique (HTML, CSS, JavaScript) pour PC Auto, Granby et Sainte-Eulalie. Aucune compilation : les fichiers se déploient tels quels sur Hostinger (Custom PHP/HTML).

## Pages
- `index.html` : accueil (héros, catégories, inventaire en vedette, services, inspection, financement, formulaire, marques, succursales)
- `inventaire.html` : inventaire complet avec recherche, filtres et tri (liens partageables : `?type=vus`, `?succursale=Granby`, `?prix=8000`, `?q=kia`)
- `vehicule.html?v=<slug>` : fiche véhicule (galerie, caractéristiques, options, calculateur de paiements)

## Mettre l'inventaire à jour
Tout l'inventaire est dans `assets/js/data.js`. Chaque véhicule a son prix, kilométrage, options et la liste de ses photos (`assets/img/autos/` en 940 px et `assets/img/autos/thumb/` en 720 px, format WebP). Pour retirer un véhicule vendu, supprimez son bloc ; pour en ajouter un, copiez un bloc existant.

Après une modification de CSS ou JS, augmentez le numéro `?v=` dans les trois pages HTML pour forcer les navigateurs à recharger les fichiers.

## Formulaire
Le formulaire ouvre WhatsApp (450 378-0888) avec le message prérempli : aucun serveur requis.

## Déploiement
La branche `deploy` est synchronisée avec Hostinger. Travailler sur `main`, puis fusionner dans `deploy`.
