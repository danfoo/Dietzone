# Food Surveys Photo Uploads

Ce répertoire contient les photos des repas téléchargées par les patients dans le cadre des enquêtes alimentaires.

## Structure

- Photos originales: `meal_*.jpg|png|gif`
- Miniatures: `thumb_meal_*.jpg|png|gif`

## Sécurité

- Taille maximale: 5MB par photo
- Types autorisés: JPEG, PNG, GIF
- Validation du type MIME
- Protection contre directory traversal

## Permissions

Les permissions recommandées pour ce répertoire sont `0755` pour permettre l'upload par le serveur web.
