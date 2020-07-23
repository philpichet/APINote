# API Note

API Note est une API REST permettant la gestion :
 - D'élève : personne notée lors d'un cours. Plusieurs élèves peuvent partager le même nom, prénom et/ou date de naissance.<br>
Un élève est representé par la ressource `Student`. <br>
Elle est définie par :
     - `id`: identifiant unique
         - Type : integer
         - Auto-géneré
     - `lastname` : nom de l’élève
         - Type : string
         - Contraintes :
             - Obligatoire
             - Entre 3 et 100 caractères
     - `firstname` : prénom de l’élève
         - Type : string
         - Contraintes :
             - Obligatoire
             - Entre 3 et 100 caractères
     - `birthdate` : date de naissance de l’élève au format YYYY-MM-DD
         - Type : Date
         - Contrainte :
             - Obligatoire
             - Date antérieure à aujourd’hui

 - De note : valeur attribuée au travail d'un élève dans un cours. <br>
 Une note est représentée par la ressource `Grade`. <br>
 Elle est définie par :
    - `id` : identifiant unique
        - Type : integer
        - Auto-géneré
    - `grade` : valeur de la note
        - Type : integer
        - Contraintes :
            - Obligatoire
            - Entre 0 et 20
    - `course` : matière notée
        - Type : string
        - Contraintes :
            - Obligatoire
            - Entre 3 et 100 caractères
    - `student` : élève noté
        - Type : Student
        - Contrainte :
            - Obligatoire
            - Relation par l'id de `Student`

La moyenne est calculée en base et arrondie à deux chiffres après la virgule.

## Technologie et Installation

API Note est écrit en PHP 7.4 et utilise le framework Symfony 5.1

L'API nécessite un serveur web (Apache ou NGinx) ainsi qu'une base de données MySQL.

Le fichier `.env.local`, à créer, permet la configuration de certains éléments :

```sh
# Paramétrage base de données et utilisateur
DATABASE_URL=mysql://db_user:@db_host:db_port/db_name
# Définit l'environnement à prod.
APP_ENV=prod
```

Pour l'installation compléte, éxecutez les commandes :

```sh
# Import des librairies de prod via composer
composer install --no-dev --optimize-autoloader
# Création de la base de données si elle n'existe pas 
php bin/console doctrine:database:create
# Création des tables 
php bin/console doctrine:migration:migrate
```

### Test et fixtures
L'API comporte des tests fonctionnels.

Pour permettre la gestion des données stocké en base lors des tests, il est prévu l'insertion de données à l'aide du bundle [DoctrineFixtureBundle](https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html).
Cela créera un `Student` ainsi que 10 `Grade` dont la note va de 1 à 10.

Pour configurer la base de donnée de test, il est nécessaire de renseigner la clé `DATABASE_URL` dans le fichier `.env.local.test`

```sh 
# Remplissage de la base de données 
php bin/console doctrine:fixtures:load
# excecution des tests
php bin/phpunit
```

## Sécurité

L'API ne contient pas de système d'authentification.

Dans une utilisation publique, un système d'authentification soit avec utilisateur/mot de passe soit par bearer authentication serait nécessaire.

Afin de réduire l'exposition d'id, un champ slug pourrait être ajouté à `Student` comment identifiant unique utilisé dans les URI.

## Prérequis

Url de base : `https://api.monsite.com/`

Chaque requête doit contenir les headers :
   - Content-Type : `application/json` ou `application/x-www-form-urlencoded`
   - Accept: `application/json`

## Points d'entrée

### Liste des élèves et moyenne de la classe
- URI : `/api/students`
- Méthode : GET
- Réponses :
    - 200 : Succès
        ```json
        {
            "average": 5,
            "students": [
                   {
                      "id": 1,
                      "firstname": "Philippe",
                      "lastname": "Pichet",
                      "birthdate": "1987-08-25"
                  }
            ]
        }
        ```
      
##### Exemple cURL
```sh
curl -X GET \
    -H "Accept: application/json" \
  https://api.monsite.com/api/students
```` 

### Récupération d'un élève, ses notes et sa moyenne
- URI : `/api/students/{id}`
- Méthode : GET
- Paramètres :
  - `id` : identifiant correspondant à la ressource `Student`
- Réponses :
  - 200 : Succès
      ```json
      {
          "id": 1,
          "firstname": "Philippe",
          "lastname": "Pichet",
          "birthdate": "1987-08-25",
          "average": 5,
          "grades": [
            {
                "grade": 5,
                "course": "Mathématiques"
            }
          ]
      }
      ```
  - 404 : ressource `Student` introuvable
      ```json
      {
          "errors": {
            "resource": "Student not found"
          }
      }
      ```
#### Exemple cURL
```sh
curl -X GET \
    -H "Accept: application/json" \
  https://api.monsite.com/api/students/1
``` 

### Ajout d'un élève
- URI : `/api/students`
- Méthode : POST
- Requête :
    ```json
    {
        "firstname": "Philippe",
        "lastname": "Pichet",
        "birthdate": "1987-08-25"
    }
    ```
- Réponses :
    - 201 : Succès
        ```json
        {
            "id": 1,
            "firstname": "Philippe",
            "lastname": "Pichet",
            "birthdate": "1987-08-25",
            "grades": []
        }
        ```
    - 400 : Données invalides ou manquantes
        ```json
        {
            "errors": {
              "firstname": "This value should not be null",
              "birthdate": "This value should be less than Jul 22, 2020"
            }
        }
        ```
    - 503 : Erreur lors de la création
        ```json
        {
            "errors": {
              "resource": "Student has not been created"
            }
        }
        ```
#### Exemple cURL
```sh
curl -X POST \
    -H "Accept: application/json" -H "Content-Type: application/json" \
    -d '{"firstname":"Philippe", "lastname": "Pichet", "birthdate": "1987-08-25"}' \
  https://api.monsite.com/api/students
```   
      
### Modification d'un élève
- URI : `/api/students/{id}`
- Méthode : PUT
- Paramètre :
    - `id` : identifiant de la ressource `Student`
- Requête : Elle doit contenir tous les éléments constituant la ressource
    ```json
    {
        "firstname": "Philippe",
        "lastname": "Pichet",
        "birthdate": "1987-08-25"
    }
    ```
- Réponses :
    - 200 : Succès
        ```json
        {
            "id": 1,
            "firstname": "Philippe",
            "lastname": "Pichet",
            "birthdate": "1987-08-25",
            "grades": []
        }
        ```
    - 400 : Données invalides ou manquantes
        ```json
        {
            "errors": {
              "firstname": "This value should not be null",
              "birthdate": "This value should be less than Jul 22, 2020"
            }
        }
        ```
    - 404 : ressource `Student` introuvable (id inexistant)
        ```json
        {
            "errors": {
              "resource": "Student not found"
            }
        }
        ```
    - 503 : Erreur lors de la mise à jour
        ```json
        {
            "errors": {
              "resource": "Student has not been updated"
            }
        }
        ```

#### Exemple cURL
```sh
curl -X PUT \
    -H "Accept: application/json" -H "Content-Type: application/json" \
    -d '{"firstname":"Philippe", "lastname": "Pichet", "birthdate": "1987-08-25"}' \
  https://api.monsite.com/api/students/1
```     
### Ajout d'une note à un élève
- URI : `/api/students/{id}/grades`
- Méthode : POST
- Paramètres :
    - `id` : identifiant correspondant à la ressource `Student`
- Requête :
    ```json
    {
        "grade": 10,
        "course": "Mathématiques"
    }
    ```
- Réponses :
    - 201 : Succès
        ```json
        {
            "id": 1,
            "grade": 10,
            "course": "Mathématiques",
            "student": {
                "id": 1,
                "firstname": "Philippe",
                "lastname": "Pichet",
                "birthdate": "1987-08-25"
            }
        }
        ```
    - 400 : Données invalides ou manquantes
        ```json
        {
            "errors": {
              "grade": "This value should not be null"
            }
        }
        ```
    - 404 : ressource `Student` introuvable
        ```json
        {
            "errors": {
              "resource": "Student not found"
            }
        }
        ```
    - 503 : Erreur lors de la création
        ```json
        {
            "errors": {
              "resource": "Student has not been created"
            }
        }
        ```
#### Exemple cURL
```sh
curl -X POST \
    -H "Accept: application/json" -H "Content-Type: application/json" \
    -d '{"grade":10, "matter"}' \
  https://api.monsite.com/api/students
``` 

### Suppression d'un élève ainsi que de toutes ses notes
- URI : `/api/students/{id}`
- Méthode : DELETE
- Paramètre :
    - `id` : identifiant de la ressource `Student`
- Requête : Elle ne contient pas de corps
- Réponses :
    - 204 : Succès
        Elle ne contient pas de corps
    - 404 : ressource `Student` introuvable
        ```json
        {
            "errors": {
              "resource": "Student not found"
            }
        }
        ```
    - 503 : Erreur lors de la suppression
        ```json
        {
            "errors": {
              "resource": "Student has not been deleted"
            }
        }
        ```
#### Exemple cURL
```sh
curl -X DELETE \
    -H "Accept: application/json" \
  https://api.monsite.com/api/students/1
```   