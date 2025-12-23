# Application Bancaire Console en PHP OOP – PDO

## Description

Ce projet est une application bancaire en mode console développée en PHP, destinée à l’apprentissage de la Programmation Orientée Objet (POO) et de la manipulation des bases de données avec PDO.

L’application permet de gérer des clients, des comptes bancaires et des transactions de manière structurée, sécurisée et conforme aux bonnes pratiques du développement back-end.

Le projet met l’accent sur l’architecture logicielle, la séparation des responsabilités, la persistance des données et la modélisation UML.

---

## Contexte du projet

Vous êtes développeur back-end au sein d’une entreprise spécialisée dans les solutions digitales financières.

Votre mission consiste à concevoir et développer une application bancaire en mode console destinée à un établissement financier.

Cette application permet de gérer les clients, les comptes bancaires et les transactions de manière fiable et maintenable, tout en respectant les standards professionnels du développement back-end.

---

## Objectifs pédagogiques

- Maîtriser la programmation orientée objet en PHP
- Concevoir une architecture claire et maintenable
- Manipuler une base de données relationnelle avec PDO
- Implémenter un CRUD complet et sécurisé
- Gérer la logique métier bancaire
- Respecter les normes PSR-4 et PSR-12
- Comprendre et appliquer la modélisation UML

---

## Entités métier

- Client
- Compte (classe abstraite)
  - CompteCourant
  - CompteEpargne
- Transaction

Toutes les données sont persistées dans une base de données relationnelle et manipulées exclusivement via PDO avec des requêtes préparées.

---

## Fonctionnalités

### Gestion des clients
- Création, modification et suppression de clients
- Consultation de la liste des clients
- Vérification de l’unicité de l’email

### Gestion des comptes
- Création de comptes bancaires
- Association d’un compte à un client
- Gestion de plusieurs types de comptes via l’héritage
- Suppression d’un compte uniquement si le solde est nul

### Gestion des transactions
- Dépôt sur un compte bancaire
- Retrait avec règles métier selon le type de compte
- Consultation de l’historique des transactions
- Utilisation des transactions PDO (commit / rollback)

---

## User Stories (Scrum)

### Gestion des clients
- US01 : Créer un client
- US02 : Consulter la liste des clients
- US03 : Consulter les détails d’un client
- US04 : Modifier un client
- US05 : Supprimer un client sans compte bancaire

### Gestion des comptes
- US06 : Créer un compte bancaire pour un client
- US07 : Choisir le type de compte
- US08 : Consulter tous les comptes
- US09 : Consulter les comptes d’un client
- US10 : Supprimer un compte bancaire avec solde nul

### Gestion des transactions
- US11 : Effectuer un dépôt
- US12 : Effectuer un retrait selon les règles métier
- US13 : Consulter l’historique des transactions d’un compte

---

## Contraintes techniques

### Programmation orientée objet
- Classes et objets
- Constructeurs
- Encapsulation (private, protected)
- Getters et setters avec validation
- Héritage et polymorphisme
- Classes abstraites

### Architecture et qualité
- Type hinting
- Exceptions personnalisées
- CRUD via PDO avec requêtes préparées
- Gestion des transactions PDO
- Séparation des responsabilités : (Bonus)
  - Entités
  - Repositories (accès aux données)
  - Services (logique métier)

### Normes (Bonus)
- PSR-4 (autoloading)
- PSR-12 (style de code)

---

## UML – Modélisation obligatoire

### Diagramme de classes
Le diagramme doit représenter :
- Client
- Compte (abstraite)
- CompteCourant
- CompteEpargne
- Transaction

Relations attendues :
- Un client possède plusieurs comptes
- Un compte possède plusieurs transactions
- Héritage entre Compte et ses sous-classes

### Diagramme de cas d’utilisation
Acteur principal : Utilisateur bancaire

Cas d’utilisation :
- Gérer les clients
- Gérer les comptes
- Effectuer un dépôt
- Effectuer un retrait
- Consulter l’historique des transactions



