# KaizoKage — État d’Implémentation

## 1. Fonctionnalités Déjà Implémentées

### ✓ Authentification & Sécurité
- Système de login fonctionnel (Email + Password)
- Gestion des rôles (ROLE_USER, ROLE_ADMIN)
- Accès restreint pour figurines/vitrines/galeries selon propriétaire
- Protection automatique via `assertFigurineAccess` et `assertGalerieAccess`

### ✓ Gestion des Vitrines
- CRUD complet
- Sécurisation par propriétaire
- Upload d’image de vitrine
- Affichage frontend amélioré (cartes, hover, ratio)
- Page “index” avec tri dynamique
- Page “show” claire et cohérente
- Vitrines des Fixtures enrichies avec images

### ✓ Gestion des Figurines
- CRUD complet
- Upload d’image
- Preview d’image avant enregistrement
- Filtrage automatique : un utilisateur ne voit que ses vitrines et galeries
- Intégration ManyToMany avec galeries
- Cartes optimisées, design cohérent

### ✓ Gestion des Galeries
- CRUD complet
- Accès public/privé conforme
- Accès admin total
- Page show et index respectant le design global
- Ajout de figurines filtrées automatiquement
- Correction du lien d’affichage des figurines
- Sécurisation complète

### ✓ Pages Utilisateurs
- Profil membre : vitrines + galeries
- Bouton “Créer une vitrine” ou “Créer une galerie”
- Navigation propre

### ✓ Frontend Global
- Render Bootstrap Menu fonctionnel
- Toasts animés
- Page d’accueil avec logo

---

## 2. Améliorations Optionnelles Non Encore Réalisées

### ☐ Amélioration avancée de la page Galerie
- Grille visuelle de figurines façon “Pinterest”
- Preview d’image en modal

### ☐ Tableau de bord utilisateur
- Statistiques (nombre de figurines, galeries, etc.)
- Raccourcis visuels comme sur un dashboard

### ☐ Recherche Avancée
- Recherche globale (figurines, vitrines, galeries)
- Autocomplétion

### ☐ Système de favoris réel
- Bouton “heart” stocké dans base
- Liste des favoris par membre

