# language: fr

@feature/create_people
Fonctionnalité: Création d'un people

@POST
Scénario: Créer un people avec les bonnes infos
    Quand       je fais un POST sur /peoples avec le corps contenu dans "add_people.json"
    Alors       le status HTTP devrait être 201
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    {
        "id":"#^\\d+$#",
        "lastname": "NEWPORT",
        "firstname": "Biche",
        "date_of_birth": "1955-02-21",
        "nationality": "américaine"
    }
    """

@POST
Scénario: Créer un people avec des infos caca
    Quand       je fais un POST sur /peoples avec le corps contenu dans "add_people_wrong.json"
    Alors       le status HTTP devrait être 400
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
    [
        "[lastname]: This value should be of type string.",
        "[firstname]: This value should not be blank.",
        "[date_of_birth]: This value is not a valid date.",
        "[nationality]: This value should not be blank."
    ]
    """
