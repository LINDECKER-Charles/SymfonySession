## SymfonySession 🚀

### Description

SymfonySession est un projet Symfony complet, conçu pour démontrer un large panel de fonctionnalités liées à la gestion de sessions, la sécurité web, l'organisation de données et la création d'interfaces utilisateur efficaces. Idéal pour les projets de formation ou d'administration, il fournit une base solide et modulaire pour des déploiements personnalisés.

### Fonctionnalités principales

#### 🔹 Front-end / UX

* 🗺️ Barre de navigation responsive et adaptative, facilitant l'accès aux différentes sections du site.
* 🔍 Vue filtrée dynamique permettant de trier et rechercher des données efficacement.
* 🗒️ Système de pagination sur les listes longues.
* 🔒 Bouton permettant d'afficher ou masquer les mots de passe pour une meilleure expérience utilisateur lors de la saisie.
* 📊 Interface claire permettant la visualisation de l'ensemble des données présentes en base.
* 🌈 Utilisation de [Tailwind CSS](https://tailwindcss.com/) pour un design moderne et personnalisable.

#### 🔒 Sécurité / Anti-spam

* ⛓️ Intégration du [Captcha Google v3](https://www.google.com/recaptcha/about/) pour la protection contre les bots automatisés.
* 🧵 Champ honeypot discret pour bloquer les soumissions frauduleuses.
* ❌ Rate limiter configuré pour limiter le nombre de requêtes et empêcher les attaques par force brute.

#### 🕋️ Systèmes intégrés

* 📄 Système de génération automatique de documents PDF depuis les données de la base.
* 🗓️ Intégration du plugin [FullCalendar.js](https://fullcalendar.io/) pour une visualisation interactive des sessions ou événements via un calendrier complet.

#### 👤 Gestion des utilisateurs

* 🛡️ Système de rôles robustes (`ADMIN`, `FORMATEUR`) avec restrictions d'accès aux pages et fonctions sensibles.
* ⛔️ Sécurisation de toutes les routes en fonction des permissions définies.
* 🏢 Interface d'administration pour la création de nouveaux comptes utilisateurs.
* 🔄 Fonctionnalités complètes de gestion (CRUD) pour les entités suivantes :

  * 📖 Programmes de formation
  * 🏫 Sessions (groupes de formation)
  * 📄 Catégories de contenus
  * 🏫 Modules pédagogiques
  * 🎓 Stagiaires inscrits
  * 👥 Utilisateurs

### Installation

1. 📁 Cloner le dépôt :

```bash
git clone https://github.com/LINDECKER-Charles/SymfonySession.git
cd SymfonySession
```

2. 🔧 Installer les dépendances PHP :

```bash
composer install
```

3. 🚀 Lancer le serveur local :

```bash
symfony serve
```

### Prérequis

* ✅ PHP version 8.1 ou supérieure
* 💻 Composer installé sur votre machine
* ⌚ Symfony CLI (facultatif mais recommandé)

### Configuration recommandée

* ⚙️ Configuration de la base de données dans `.env`
* 🔄 Utilisation de Doctrine pour la gestion des entités et migrations
* 🌍 Installation du bundle FullCalendar et DomPDF pour les systèmes associés

### Arborescence du projet

```
SymfonySession/
├── assets/               # Fichiers front-end (JS/CSS)
├── config/               # Fichiers de configuration Symfony
├── migrations/           # Migrations Doctrine
├── public/               # Dossier public (point d'entrée HTTP)
├── src/                  # Code source PHP (contrôleurs, entités, services...)
├── templates/            # Fichiers Twig
├── translations/         # Fichiers de traduction
├── var/                  # Fichiers temporaires (cache, logs...)
├── vendor/               # Librairies installées via Composer
├── .env                  # Configuration d'environnement
├── composer.json         # Dépendances PHP
└── symfony.lock          # Fichier de verrouillage des versions
```

### Crédits

Ce projet a été entièrement conçu, développé et maintenu par [Charles Lindecker](https://www.linkedin.com/in/charles-lindecker/).

Pour toute suggestion, contribution ou fork, n'hésitez pas à explorer le [répertoire GitHub](https://github.com/LINDECKER-Charles/SymfonySession).
