Événements
==========

Système de gestion des évenements de Sciencespo

Le système est une web-app PHP/HTML/CSS/JAVASCRIPT.

ISOTOPE Centré cf : http://jsfiddle.net/desandro/P6JGY/24/

FLECHES CSS : http://jsfiddle.net/vZfeV/

###Pour le site

Les classes existantes :

- classe_core.php (la classe qui permet de d'amorcer l'outil)

- classe_fonctions.php (la classes statique avec les fonctions génériques)

- classe_organisme.php (la classe de gestion des organismes que j'avais fait)

- classe_simpleimage.php (une classe de gestion d'images)

- classe_spuser.php (la classe de gestion des utilisateurs)

- **classe_default (une classe d'exemple par défaut)**

Les classes à créer (à affiner) :

- classe_evenement -> pour la gestion des événements

- classe_session -> pour la gestion des sessions d'événements

- classe_billet -> pour la gestion de la création des billets (cf https://github.com/tschoffelen/PHP-PKPass + http://www.tcpdf.org)




En général les classes sont là pour la partie logique + algorithme, la partie structure est gérée dans le dossier 
`structure/`

Le fichier `config-sample.php` contient les exemples des variables à déclarer, le fichier `config.php` contient les variables réellement utilisées (mais il n'est pas synchronisé sur github).

Le système de template fonctionne avec la fonction `ob_content` qui permet de garder en cache le résultat d'un include (cf **classe_default.php**).


Pour les filtres isotopes avec checkbox :
- http://jsfiddle.net/bj5WG/
