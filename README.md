# KaizoKage

KaizoKage est une application Symfony permettant de gérer une collection de figurines inspirées d’univers d’anime (One Piece, Naruto, Dragon Ball, etc.).  
Chaque utilisateur dispose de **vitrines** (inventaires) où sont rangées ses **figurines**, et peut créer des **galeries** (publiques ou privées) qui regroupent certaines figurines.

L’objectif pédagogique est de couvrir :
- la modélisation de données avec Doctrine (relations 1–N et N–N),
- les opérations CRUD contextualisées,
- l’authentification / autorisations,
- l’upload d’images,
- un frontend cohérent basé sur Bootstrap.

---

## 1. Modèle fonctionnel

### 1.1. Membre (`Member`)
Représente un utilisateur de l’application.

Champs principaux :
- `id`
- `email`
- `password`
- `roles` (tableau, inclut au moins `ROLE_USER`)
- `vitrines` : vitrines dont il est propriétaire
- `galeries` : galeries qu’il a créées

Particularités :
- un membre se connecte avec `email` + `mot de passe`,
- les droits (lecture / écriture) sur vitrines, figurines et galeries dépendent de ce membre et de ses rôles.

### 1.2. Vitrine / Inventaire (`Vitrine`)
Une vitrine (inventaire) est un “espace de rangement” de figurines, appartenant à un **membre**.

Champs principaux :
- `id`
- `description`
- `owner` : membre propriétaire (relation ManyToOne vers `Member`)
- `figurines` : figurines contenues dans cette vitrine (OneToMany)
- `imageFilename` : image associée à la vitrine 

Règles d’accès :
- un membre ne peut voir et modifier **que ses propres vitrines**,
- un administrateur (`ROLE_ADMIN`) peut voir / modifier toutes les vitrines.

### 1.3. Figurine / Objet (`Figurine`)
Une figurine est un objet de collection.

Champs principaux :
- `id`
- `nom`
- `vitrine` : vitrine à laquelle la figurine appartient (ManyToOne)
- `galeries` : galeries dans lesquelles la figurine apparaît (ManyToMany)
- `imageFilename` : fichier image de la figurine

Contraintes :
- chaque figurine appartient à **une seule vitrine**,
- une figurine peut apparaître dans **plusieurs galeries**.

### 1.4. Galerie (`Galerie`)
Une galerie est un regroupement de figurines, appartenant à un membre, et pouvant être :

- **publique** (visible par tous),
- **privée** (visible uniquement par son créateur et l’admin).

Champs principaux :
- `id`
- `description`
- `published` (booléen)
- `creator` : membre créateur
- `figurines` : liste de figurines associées (ManyToMany)

Règles d’accès :
- publique → visible par tous,
- privée → visible par le créateur ou un admin,
- édition / suppression → réservée au créateur ou à l’admin.

---

## 2. Installation locale

### 2.1. Prérequis

- PHP 8.1+  
- Composer  
- Symfony CLI (recommandé)  
- SQLite (par défaut) ou un autre SGBD compatible Doctrine  
- Node / npm (optionnel si tu veux recompiler du front custom)

### 2.2. Cloner le projet

```bash
git clone https://github.com/adamnjeh/KaizoKage.git
cd KaizoKage
```

### 2.3. Installer les dépendances PHP

```bash
composer install
```

### 2.4. Configuration de l’environnement

Créer un fichier `.env.local` à la racine, au minimum pour la base de données, par exemple :

```env
APP_ENV=dev
APP_DEBUG=1
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

Tu peux aussi configurer un autre SGBD (MySQL/PostgreSQL) en adaptant `DATABASE_URL`.

### 2.5. Créer la base de données et appliquer les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 2.6. Charger les données de test (fixtures)

Les fixtures créent :
- plusieurs membres (dont un admin),
- leurs vitrines associées,
- des figurines avec images,
- des galeries publiques et privées.

Commande :

```bash
php bin/console doctrine:fixtures:load
```

Confirmer l’écrasement des données si demandé.

### 2.7. Lancer le serveur Symfony

```bash
symfony serve
```

L’application est alors accessible sur :

- `http://127.0.0.1:8000`

---

## 3. Comptes par défaut

Les fixtures créent au moins les comptes suivants :

### Administrateur

- **Email** : `adam@localhost`  
- **Mot de passe** : `000000`  
- **Rôle** : `ROLE_ADMIN`

### Utilisateurs simples

- **Email** : `slash@localhost` – mot de passe `000000` – `ROLE_USER`  
- **Email** : `nami@localhost` – mot de passe `000000` – `ROLE_USER`

L’admin (`adam@localhost`) peut :
- voir / modifier toutes les vitrines,
- voir / modifier toutes les figurines,
- voir / modifier toutes les galeries, y compris privées.

---

## 4. Navigation dans l’interface

### 4.1. Barre de navigation principale

En haut de chaque page, la navbar propose :

- **Accueil** → route `home`  
- **Figurines** → route `app_figurine_index`  
- **Vitrines** → route `vitrine_index`  

À droite, selon l’état de connexion :

- Si **non connecté** :
  - bouton **Connexion** → route `app_login`
- Si **connecté** :
  - bouton **Mon espace** → route `app_member_show` (profil du membre connecté)
  - lien **Déconnexion** → route `app_logout`

### 4.2. Page d’accueil (`/`)

- Hero avec logo **KaizoKage**
- Bouton **Voir les vitrines** → `vitrine_index`
- Bouton **Parcourir les figurines** → `app_figurine_index`

### 4.3. Vitrines

#### Liste des vitrines — `/vitrines` (`vitrine_index`)
- Affichage sous forme de cartes avec image, description et nombre de figurines.  
- Tri via un select :
  - Description A→Z / Z→A
  - Plus / moins de figurines
- Si connecté :
  - bouton **Nouvelle vitrine** pour créer une vitrine associée à ton compte.

#### Détails d’une vitrine — `/vitrines/{id}` (`vitrine_show`)
- Détails de la vitrine (description, image, ID)
- Liste des figurines de cette vitrine, sous forme de cartes
- Pour le propriétaire ou l’admin :
  - bouton **Modifier la vitrine**
- Bouton **Retour à la liste**

### 4.4. Figurines

#### Liste des figurines — `/figurine` (`app_figurine_index`)
- Grille de cartes avec :
  - image de la figurine,
  - nom,
  - vitrine associée,
- Champ de recherche en haut pour filtrer **par nom**
- Bouton **Nouvelle figurine** (si connecté)

Chaque carte propose :
- un accès à la fiche figurine,
- un bouton **Modifier** (si autorisé).

#### Détails d’une figurine — `/figurine/{id}` (`app_figurine_show`)
- Image principale
- ID, nom, vitrine associée
- Bouton **Modifier** (si propriétaire ou admin)
- Formulaire de suppression (si autorisé)

#### Création / édition de figurine
- Formulaire avec :
  - nom,
  - vitrine (filtrée selon l’utilisateur),
  - galeries possibles (filtrées selon l’utilisateur),
  - upload de l’image (avec contrainte de format/taille),
- Preview de l’image choisie avant enregistrement.

### 4.5. Galeries

#### Liste des galeries — `/galerie` (`app_galerie_index`)
- Tableau listant :
  - ID
  - Description
  - Statut (Publique / Privée)
- Actions :
  - **Voir** : accessibles à tous (mais filtre d’accès appliqué dans le contrôleur)
  - **Modifier** : visible seulement pour le créateur ou l’admin

#### Détails d’une galerie — `/galerie/{id}` (`app_galerie_show`)
- Description
- Statut (publiée / privée)
- Liste des figurines liées, cliquables.
- Si créateur ou admin :
  - bouton **Modifier**
  - bouton de suppression (formulaire avec CSRF)

#### Détails d’une figurine dans le contexte d’une galerie  
Route : `/galerie/{galerie_id}/figurine/{figurine_id}` (`app_galerie_figurine_show`)

- Affiche la figurine dans le contexte de cette galerie
- Vérifie que la figurine appartient bien à la galerie
- Applique les mêmes règles d’accès (publique / privée, créateur, admin).

### 4.6. Espace Membre — `/member/{id}` (`app_member_show`)
- Informations de base du membre
- Liste de ses vitrines (avec lien “Voir la vitrine”)
- Liste de ses galeries (avec actions “Voir” et “Modifier”, conditionnées par les droits)
- Boutons :
  - **Créer une nouvelle vitrine pour ce membre**
  - **Créer une nouvelle galerie pour ce membre**

---

## 5. Structure des images

Les images utilisées dans l’application sont stockées dans :

- `public/uploads/figurines/` → images des figurines
- `public/uploads/vitrines/` → images des vitrines

Les fixtures utilisent des noms de fichiers prédéfinis (`luffy.jpg`, `naruto.jpg`, etc.).  
Il est possible de les remplacer par de vraies images en conservant les mêmes noms.

---

## 6. Résumé

- **Backend** : modèle Doctrine propre, relations cohérentes (Member/Vitrine/Figurine/Galerie), sécurité appliquée par rôle et par propriétaire.
- **Frontend** : Bootstrap, design cohérent, pages de listing et détail lisibles.
- **Admin** : le compte `adam@localhost` (mot de passe `000000`) dispose de tous les droits pour inspection et tests.

KaizoKage est prêt à être lancé en local pour démonstration, expérimentation ou extension (nouveaux types d’objets, nouveaux univers, etc.).
