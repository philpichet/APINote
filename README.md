# API Note

API Note est une API REST permettant la gestion :
 - D'élève : personne noté lors d'un cours. Il ne dispose pas de caractères unique. 
     Un élève est representé par la ressource `Student`.
     Elle est définie par :
     - `id`: identifiant unique
         - Type : integer
         - Auto-géneré
     - `lastname` : nom de l’élève
         - Type: string
         - Contraintes:
             - Obligatoire
             - Entre 3 et 100 caractères
     - `firstname` : prénom de l’élève
         - Type: string
         - Contraintes :
             - Obligatoire
             - Entre 3 et 100 caractères
     - `birthdate` : date de naissance de l’élève au format YYYY-mm-dd
         - Type : Date
         - Contrainte :
             - Obligatoire
             - Date antérieure à aujourd’hui

 - De note : valeur attribué au travail d'un élève dans un cours
    Une note est représentée par la ressource `Grade`.
    Elle est définie par :
    - `id` : identifiant unique
        - Type : integer
        - Auto-géneré
    - `grade` : valeur de la note
        - Type: integer
        - Contraintes :
            - Obligatoire
            - Entre 0 et 20
    - `matter` : matière notée
        - Type: string
        - Contraintes :
            - Obligatoire
            - Entre 3 et 100 caractères
    - `student` : élève noté
        - Type : Student
        - Contrainte :
            - Obligatoire
            - Relation par l'id de `Student`

## Technologie et Installation

API Note est écrit en PHP 7.4 et utilise le framework Symfony 5.1

L'API nécessite un serveur web ( Apache ou NGinx) ainsi qu'une base de données MySQL.

Le fichier `.env.local`, à créer, permet la configuration de certains élèments.
```
# Parametrage base de données et utilisateur
DATABASE_URL=mysql://db_user:@db_host:db_port/db_name
# Définit l'environnement à prod.
APP_ENV=test
```
Pour l'installation complète, executez les commandes :
```shell script
# Import des librairies de prod via composer
composer install --no-dev --optimize-autoloader
# Création de la base de données si elle n'existe pas 
php bin/console doctrine:database:create
# Création des tables 
php bin/console doctrine:migration:migrate
```

## Prérequis

Url de base : `https://api.monsite.com/`

L'API Note communique en JSON aussi bien en entrée qu'en sortie.

---
**Attention**

Le type MIME `application/x-www-form-urnlencoded` n'est pas supporté.

---

Chaque requête doit contenir les headers :
   - Content-Type : `application/json`
   - Accept: `application/json`


## Points d'entrée
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
```shell script
curl -X POST \
    -H "Accept: application/json" -H "Content-Type: application/json" \
    -d '{"firstname":"Philippe", "lastname": "Pichet", "birthdate": "1987-08-25"}' \
  https://api.monsite.com/api/students
```   
      
### Récupération d'un élève
- URI : `/api/students/{id}`
- Méthode : GET
- Paramètres :
  - `id` : identifiant correspondant à la ressource `Student`
- Requête :
  Le corps de la requête est vide
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
                "matter": "Mathématiques"
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
```shell script
curl -X GET \
    -H "Accept: application/json" -H "Content-Type: application/json" \
  https://api.monsite.com/api/students/1
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
```shell script
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
        "matter": "Mathématiques"
    }
    ```
- Réponses :
    - 201 : Succès
        ```json
        {
            "id": 1,
            "grade": 10,
            "matter": "Mathématiques",
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
```shell script
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
```shell script
curl -X DELETE \
    -H "Accept: application/json" -H "Content-Type: application/json" \
  https://api.monsite.com/api/students/1
```   
### Liste des élèves
- URI : `/api/students`
- Méthode : GET
- Requête :
    Le corps de la requête est vide
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
```shell script
curl -X GET \
    -H "Accept: application/json" -H "Content-Type: application/json" \
  https://api.monsite.com/api/students
```` 