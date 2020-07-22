# API Note

API Note est une API permettant la gestion :
 - D'élève
 - De note

## Technologie

API Note est écrit en PHP 7.4 et utilise le framework Symfony 5.1

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


 ## Les ressources

 ## &Eacute;lève

Un élève est representé par la ressource `Student`.
Elle est défini par :
- id: identifiant unique
    - Type : integer
    - Auto-géneré
- lastname : nom de l’élève
    - Type: string
    - Contraintes:
        - Obligatoire
        - Entre 3 et 100 caractères
- firstname : prénom de l’élève
    - Type: string
    - Contraintes :
        - Obligatoire
        - Entre 3 et 100 caractères
- birthdate : date de naissance de l’élève
    - Type : Date
    - Contrainte :
        - Obligatoire
        - Date antérieure à aujourd’hui

### Actions
 - Ajout : ajout d'un élève
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
                "birthdate": "1987-08-25"
            }
            ```
        - 400 : Données invalides ou manquantes
            ```json
            {
                "errors": {
                  "firstname": "This value should not be null",
                  "birthdate": "This value should be less than Jul 22, 2020, 12:00 AM"
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
 - Modification : modification d'un élève
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
                  "birthdate": "This value should be less than Jul 22, 2020, 12:00 AM"
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
                  "resource": "Student has not been updated"
                }
            }
            ```

 - Suppression : suppression d'un élève ainsi que de ses notes
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

 ## Note

Une note est représentée par la ressource `Grade`.
Elle est défini par :
- id : identifiant unique
    - Type : integer
    - Auto-géneré
- grade : valeur de la note
    - Type: integer
    - Contraintes :
        - Obligatoire
        - Entre 0 et 20
- matter : matière notée
    - Type: string
    - Contraintes :
        - Obligatoire
        - Entre 3 et 100 caractères
- student : élève noté
    - Type : Student
    - Contrainte :
        - Obligatoire
        - Relation par l'id de `Student`

### Actions
 - Ajout : ajout d'une note à un élève
    - URI : `/api/students/{id}`
    - Méthode : POST
    - Paramètres :
        - `id` : identifiant correspondant à la ressource `Student`
    - Requête :
        ```json
        {
            "grade": 10,
            "matter": "Mathématique"
        }
        ```
    - Réponses :
        - 201 : Succès
            ```json
            {
                "id": 1,
                "grade": 10,
                "matter": "Mathématique",
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
 - Moyenne d'un élève : Retourne la moyenne d'un élève
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
                "average": 5
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
- Moyenne global : Retourne la moyenne de la classe
    - URI : `/api/grades/average`
    - Méthode : GET
    - Requête :
        Le corps de la requête est vide
    - Réponses :
        - 200 : Succès
            ```json
            {
                "average": 5
            }
            ```