# KaizoKage

Projet Symfony — Gestion d’inventaire de **figurines d’anime** (vitrines & figurines)

---

## Description

**KaizoKage** est une application web destinée aux collectionneur·se·s de **figurines d’anime** (shōnen, seinen, mecha, etc.).
Elle permet de recenser vos figurines et de les organiser par **vitrines** (étagères/expositions), avec une interface sobre, claire et extensible.

* **Figurine** : `nom` (*string*), relation **vitrine** (*ManyToOne*).
* **Vitrine** : `description` (*string/texte*), collection **figurines** (*OneToMany*).

Cette étape utilise **Symfony 6** + **Doctrine ORM**, intègre **Bootstrap** via la distribution *Start Bootstrap – Shop Homepage* et met en place des gabarits **Twig minimalistes**.

---

## Prérequis

* PHP compatible Symfony (≥ **8.1** recommandé)
* **Composer**
* **Symfony CLI** (recommandé)
* Base de données configurée (**SQLite** en dev possible)
* Optionnel : **Migrations/fixtures** si besoin

---

## Structure actuelle (principaux fichiers)

* **Contrôleurs**

  * `src/Controller/HomeController.php` → route **home** (`/`)
  * `src/Controller/FigurineController.php` → **figurine_index** (`/figurines`), **figurine_show** (`/figurines/{id}`)
  * `src/Controller/VitrineController.php` → **vitrine_index** (`/vitrines`), **vitrine_show** (`/vitrines/{id}`)
* **Gabarits Twig**

  * `templates/base.html.twig` (liens CSS/JS du thème via `asset()`, blocs *menu* et *body*)
  * `templates/home/index.html.twig`
  * `templates/figurine/index.html.twig`, `templates/figurine/show.html.twig`
  * `templates/vitrine/index.html.twig`, `templates/vitrine/show.html.twig`
* **Intégration Start Bootstrap**

  * `public/startbootstrap-shop-homepage-gh-pages/` (fichiers `css/styles.css`, `js/scripts.js`, etc.)
  * `config/packages/framework.yaml` → `assets.base_path: '/startbootstrap-shop-homepage-gh-pages'`
* **Menu Bootstrap**

  * `config/packages/bootstrap_menu.yaml` (menu `main`)
  * Bundle : `camurphy/bootstrap-menu-bundle` installé

---

## Installation et exécution

### 1) Dépendances

```bash
composer install
```

### 2) Base de données (développement)

```bash
symfony console doctrine:database:create
symfony console doctrine:schema:create
# ou via migrations :
# symfony console doctrine:migrations:migrate
# (facultatif) charger fixtures :
# symfony console doctrine:fixtures:load
```

### 3) Intégrer Start Bootstrap (conformément au guide)

Depuis la racine du projet :

```bash
cd public
wget https://github.com/startbootstrap/startbootstrap-shop-homepage/archive/gh-pages.zip
unzip gh-pages.zip
# → crée public/startbootstrap-shop-homepage-gh-pages/
```

Configurer Symfony pour pointer les assets du thème :

`config/packages/framework.yaml`

```yaml
framework:
  # ...
  assets:
    base_path: '/startbootstrap-shop-homepage-gh-pages'
```

Grâce à `assets.base_path`, dans `base.html.twig` les appels `{{ asset('css/styles.css') }}` et `{{ asset('js/scripts.js') }}` pointent vers le dossier du thème.

### 4) Menu Bootstrap

Installer le bundle :

```bash
symfony composer require camurphy/bootstrap-menu-bundle
```

Configuration minimale :

`config/packages/bootstrap_menu.yaml`

```yaml
bootstrap_menu:
  menus:
    main:
      items:
        home:      { label: 'Accueil',   route: 'home' }
        figurines: { label: 'Figurines', route: 'figurine_index' }
        vitrines:  { label: 'Vitrines',  route: 'vitrine_index' }
```

### 5) Lancer le serveur

```bash
symfony server:start
# ou
symfony serve -d
```

Ouvrir :

* `/` (Accueil)
* `/figurines`
* `/vitrines`

---

## Étapes réalisées

* [x] Squelette Symfony + Doctrine (SQLite dev)
* [x] Entités : **Figurine** (`nom`, `vitrine`), **Vitrine** (`description`, `figurines`)
* [x] Fixtures de base
* [x] Contrôleurs : **Home / Figurine / Vitrine** avec routes nommées
* [x] Twig : `base.html.twig` + pages **home**, **figurine** (*index/show*), **vitrine** (*index/show*)
* [x] **Bootstrap** via *Start Bootstrap – Shop Homepage* (téléchargé dans `public/...`)
* [x] Configuration assets : `framework.assets.base_path`
* [x] **Menu Bootstrap** (bundle installé + `bootstrap_menu.yaml` minimal)

---

## Dépannage rapide

* **Route introuvable**
  `bin/console debug:router | grep -E 'home|figurine|vitrine'`
* **Classe contrôleur non trouvée**
  Vérifier `namespace App\Controller;` et que le nom de classe = nom du fichier.
* **Template introuvable**
  Vérifier le chemin `templates/...` et l’appel `return $this->render('...')`.
* **Page blanche**
  Contrôler les blocs Twig (`{% block body %}`), vider le cache :
  `bin/console cache:clear`

---

## Auteur

Projet pédagogique développé par **Adam Njeh** — Encadrant : **Olivier Berger**.

---

## Bonus — Améliorations UX

* **Navigation claire**

  * Navbar avec liens explicites **Accueil / Vitrines / Figurin es** (toujours visibles).
  * Mise en évidence de la page active par route.

* **Listes en cartes (Bootstrap 5)**

  * **Vitrines** : grille responsive, badge du **nombre de figurines** par vitrine.
  * **Figurines** : grille responsive avec titres **tronqués** et mise en page propre.

* **Interaction sans backend**

  * **Recherche instantanée côté client** sur la liste des figurines.
  * **Tri côté client** des vitrines (Description A→Z/Z→A, **Nb figurines** ↑/↓).

* **Liens “stretched” & accessibilité**

  * `.stretched-link` pour rendre **toute la carte cliquable** (avec `.card { position: relative }`).
  * Focus rings lisibles, badges et contrastes renforcés.

* **Images placeholders unifiées**

  * Globale Twig `placeholder_img` (`UnderConstruction.jpg`) pour **remplacer toutes les images** tant que les photos réelles ne sont pas prêtes.

* **Finitions visuelles**

  * Légère **élévation au survol** des cartes (ombre + translation).
  * **Reveal on scroll** (IntersectionObserver) pour une apparition fluide des éléments.
  * **Bootstrap Icons** pour des actions explicites (`bi-heart`, `bi-eye`, etc.).
  * Toast helper `showToast('...')` pour un feedback utilisateur rapide.
