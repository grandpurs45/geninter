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

## Production Docker avec Traefik

1. Copier `.env.example` vers `.env`.
2. Modifier `APP_HOST` avec le nom de domaine public.
3. Verifier que le reseau Docker externe de Traefik existe, par defaut `traefik`.
4. Lancer :

```bash
docker compose -f docker-compose.prod.yml --env-file .env up -d --build
```

Le conteneur expose Apache en interne sur le port `80`. Traefik route le trafic HTTPS via les labels du service.

## Export PDF

Le bouton `Imprimer / PDF` utilise la fonction d'impression du navigateur. Choisir ensuite `Enregistrer au format PDF` pour generer le fichier final.
