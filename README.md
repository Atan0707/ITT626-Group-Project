# Laravel Project Setup

## Overview
This is a Laravel project that requires a local development environment. Follow the instructions below to set up and run the project.

## Prerequisites
- PHP >= 7.3
- Composer
- MySQL
- Node.js and npm (for frontend dependencies)
- A local server environment (e.g., Laragon, XAMPP, MAMP)

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Atan0707/ITT626-Group-Project.git
   cd repo
   ```

2. **Install PHP Dependencies**
   Make sure you have Composer installed, then run:
   ```bash
   composer install
   ```

3. **Set Up Environment File**
   Copy the example environment file to create your own:
   ```bash
   cp .env.example .env
   ```

   Update the `.env` file with your database and other configurations. Make sure to set the `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` fields according to your local database setup.

4. **Generate Application Key**
   Run the following command to generate the application key:
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations**
   If your project requires a database schema, run the migrations:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Install Frontend Dependencies**
   If your project has frontend dependencies, install them using npm:
   ```bash
   npm install
   ```

7. **Compile Assets**
   Compile your frontend assets:
   ```bash
   npm run dev
   ```

8. **Run the Node.js Server**
   Start the Node.js server:
   ```bash
   cd server
   npx nodemon server.js
   ```
