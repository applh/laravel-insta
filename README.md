# Laravel Insta

## Objectifs

```

L'objectif : créer un système de récupération automatique des posts d'une page Instagram. 

Consignes : 
- Récupérer les derniers posts (donc quelques posts pas tous ceux de la page) et les afficher sur une page. 
- Cette récupération doit prendre en compte l'utilisation en production (ex : si un nouveau post apparaît sur la page Insta, qu'il soit pris en compte sur le système de récupération et non juste fait par rapport à une liste de posts figée à un instant T). 
- Nous nous concentrons ici essentiellement surtout sur l'aspect back-end.
- Il n'est évidemment pas nécessaire que ce soit une page Instagram personnelle.

```

## Laravel Breeze

Pour ce projet, on s'appuie sur Laravel Breeze comme starter kit.
* Laravel Breaze est un starter kit pour Laravel.
* Laravel Breeze inclut les fonctionnalités suivantes :
  * authentification
  * email verification
  * password reset
  * session management
  * API support via Laravel Sanctum
  * Tailwind CSS
  * Vue.js
* Ainsi, toute la partie Gestion des utilisateurs est déjà implémentée.
  * register: création de compte utilisateur
  * login+logout: connexion utilisateur
  * dashboard: espaces utilisateur
  * ...


```bash

# install laravel
laravel new laravel-insta

cd laravel-insta

# create SQLite database
touch database/database.sqlite
php artisan migrate

php artisan serve

```

### Breeze install options

```

 ┌ Would you like to install a starter kit? ────────────────────┐
 │ Laravel Breeze                                               │
 └──────────────────────────────────────────────────────────────┘

 ┌ Which Breeze stack would you like to install? ───────────────┐
 │ Blade                                                        │
 └──────────────────────────────────────────────────────────────┘

 ┌ Would you like dark mode support? ───────────────────────────┐
 │ No                                                           │
 └──────────────────────────────────────────────────────────────┘

 ┌ Which testing framework do you prefer? ──────────────────────┐
 │ PHPUnit                                                      │
 └──────────────────────────────────────────────────────────────┘

 ┌ Would you like to initialize a Git repository? ──────────────┐
 │ Yes                                                          │
 └──────────────────────────────────────────────────────────────┘
 
 ...

 ┌ Which database will your application use? ───────────────────┐
 │ SQLite                                                       │
 └──────────────────────────────────────────────────────────────┘

 ...

```

## Controller: InstaController

```bash
php artisan make:controller InstaController
```

### add routes and views

* route: insta_home
  * url: /
  * controller: InstaController::home()
  * view: insta_home.blade.php

* route: insta_user
  * url: /user/{name}
  * controller: InstaController::user()
  * view: insta_user.blade.php

* route: dashboard
  * url: /dashboard
  * controller: InstaController::dashboard()
  * view: dashboard.blade.php

Add routes in  
* routes/web.php

Add views in
* resources/views/insta_home.blade.php
* resources/views/insta_user.blade.php
* (breeze) resources/views/dashboard.blade.php


