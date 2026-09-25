# Heri Brennholz — Boutique en ligne

Boutique e-commerce Laravel pour **Heri Brennholz GmbH**, producteur et vendeur de bois de chauffage (Brennholz), granulés de bois (Pellets), briquettes et bois densifié, basé à Biberist (Soleure, Suisse).

Le site propose la vente en ligne de combustibles bois avec livraison **gratuite en Suisse en 1 à 2 jours ouvrés**.

## Stack technique

- **Backend** : Laravel 13 (PHP 8.3+)
- **Frontend** : Blade + Tailwind CSS 4, Vite
- **Base de données** : MySQL/MariaDB (Eloquent ORM)

## Structure fonctionnelle

- **Catalogue / Shop** — `App\Http\Controllers\ShopController`, `ProductController` : liste, recherche, catégories, fiche produit.
- **Panier** — `App\Http\Controllers\CartController` + `App\Services\Cart` : ajout, mise à jour, suppression, calcul du sous-total (livraison gratuite par défaut).
- **Commande / Checkout** — `App\Http\Controllers\CheckoutController`, `App\Models\Order` : validation de commande, confirmation, e-mails de notification (client + admin).
- **Favoris** — `App\Http\Controllers\WishlistController`.
- **Pages institutionnelles** — `App\Http\Controllers\PageController` : à propos, contact, FAQ, Impressum, Datenschutz (confidentialité), AGB (CGV).

## Installation locale

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed

npm run build   # ou npm run dev en développement
php artisan serve
```

## Configuration importante (`.env`)

- `APP_NAME`, `APP_URL` — identité et URL du site.
- `MAIL_*` — SMTP utilisé pour l'envoi de la confirmation de commande au client et de la notification à l'équipe (`MAIL_ADMIN_ADDRESS`).
- `DB_*` — connexion à la base de données.

⚠️ Ne jamais committer de vrais identifiants (mots de passe SMTP, clés API) dans `.env.example` : ce fichier est un modèle destiné à être partagé publiquement.

## Informations légales de l'entreprise

- **Raison sociale** : Heri Brennholz GmbH
- **Adresse** : Fiderholzstrasse 7, 4562 Biberist, Suisse
- **UID/IDE** : CHE-228.719.493
- **N° registre du commerce** : CH-241.4.020.905-9
- Ces informations sont affichées sur les pages `/impressum`, `/agb` et `/datenschutz`, et doivent être tenues à jour en cas de changement (adresse, dirigeants, forme juridique).

## Politique de livraison

- Livraison **gratuite** vers la Suisse et l'Allemagne, sans montant minimum (`App\Services\Cart::shipping()`).
- Préparation de commande : 0 à 1 jour ouvré.
- Délai de livraison : 2 à 3 jours ouvrés.

Toute modification de cette politique doit être répercutée à la fois dans le code (`App\Services\Cart`) et dans les pages `/agb`, `/faq`, `/impressum` et la bannière du site (`resources/views/layouts/omniva.blade.php`).

## Conformité Google Merchant Center

Le site expose les pages requises pour la validation Merchant Center : informations sur l'entreprise (`/impressum`), politique de retour et de livraison (`/agb`), confidentialité (`/datenschutz`), et coordonnées de contact vérifiables (`/kontakt`).
