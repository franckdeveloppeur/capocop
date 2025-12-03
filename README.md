#projet capocop shop

ce projet represente le projet e-commerce de capocop pour la ventes des produits .

---

## Project Structure

- **Laravel Backend**: Handles routing, database, and backend logic.
- **Front-End Templates**: Created in Shuffle Editor, located in the `./resources/` directory.
  - `./resources/css/` – Tailwind CSS sources to compile it. You can customize it by editing ./tailwind.config.js
  - `./resources/views/` – Your templates in Blade engine.

> **Note:** Running npm commands will overwrite the `./build/` directory.

---

## Installation & Setup

### 1. Backend (Laravel)

```bash
composer install
php artisan migrate
php artisan key:generate
```

### 2. Frontend (Tailwind CSS & Templates)

```bash
npm install
```

#### Build front-end

```bash
npm run build
```

---

## Running the Application

```bash
php artisan serve
```

---

## Contact

If you encounter any bugs in Shuffle Editor, or have suggestions or questions, feel free to reach out:

support@shuffle.dev

---

## Credits

We use image placeholders from [Unsplash](https://unsplash.com/).
