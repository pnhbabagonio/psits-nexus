Group 8
-Philip Neel Babagonio
-Ruby Jean Solomon
-Baililian Sali
-Mark Laurence Abdina
-Adrian Michael Demillo


# Project Setup Instructions

To set up and run this project, follow these steps:

## Setup

1. **Clone the repository**
   - If you are using **XAMPP**, clone it inside the `htdocs` folder.
   - If you are using **Laragon**, clone it inside the `www` folder.
   - - If you are using **Herd**, clone it inside the `Herd` folder.
   ```bash
   git clone <repository-url>
   cd RizGroup/app
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment file**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations**
   ```bash
   php artisan migrate
   ```

6. **Install and build frontend assets**
   ```bash
   npm install
   npm run dev
   ```
   > To stop the process, press **Ctrl + C**.

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## Updating Project (When Pulling New Data)

When you pull new changes, run the following as needed:

1. **Update PHP dependencies** (if `composer.json` was changed):
   ```bash
   composer install
   ```

2. **Update Node.js dependencies** (if `package.json` was changed):
   ```bash
   npm install
   ```

3. **Apply database changes** (if migrations were updated):
   ```bash
   php artisan migrate
   ```
