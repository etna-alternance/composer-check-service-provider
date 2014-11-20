# language: fr

@feature/check
Fonctionnalité: Requêtes sur la route check d'une API

@GET
Scénario: Faire une requête GET sur la route check
    Quand je fais un GET sur /check
    Alors le status HTTP devrait être 200
    Et    je devrais avoir un résultat d'API en JSON
    Et    le résultat devrait être identique au fichier "check_result.json"

@POST
Scénario: Faire une requête POST sur la route check
    Quand je fais un POST sur /check
    Alors le status HTTP devrait être 200
    Et    je devrais avoir un résultat d'API en JSON
    Et    le résultat devrait être identique au fichier "check_result.json"

@PUT
Scénario: Faire une requête PUT sur la route check
    Quand je fais un PUT sur /check
    Alors le status HTTP devrait être 200
    Et    je devrais avoir un résultat d'API en JSON
    Et    le résultat devrait être identique au fichier "check_result.json"

@DELETE
Scénario: Faire une requête DELETE sur la route check
    Quand je fais un DELETE sur /check
    Alors le status HTTP devrait être 200
    Et    je devrais avoir un résultat d'API en JSON
    Et    le résultat devrait être identique au fichier "check_result.json"
