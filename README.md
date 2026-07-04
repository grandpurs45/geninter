# Generateur de fiche d'intervention

Application PHP simple pour creer une fiche de depart manoeuvre pompiers au format A4, inspiree du PDF de reference fourni.

Les champs de l'intervention demarrent vides. Les dates et heures de depart/appel sont remplies automatiquement au moment de la generation de la fiche.

## Utilisation avec XAMPP

1. Placer ce dossier dans `htdocs`.
2. Demarrer Apache dans XAMPP.
3. Ouvrir `http://geninter.local/`.
4. Remplir la fiche puis utiliser `Imprimer / PDF`.

## Utilisation avec Docker

```bash
docker compose up --build
```

Puis ouvrir `http://localhost:8080/`.

## Export PDF

Le bouton `Imprimer / PDF` utilise la fonction d'impression du navigateur. Choisir ensuite `Enregistrer au format PDF` pour generer le fichier final.
