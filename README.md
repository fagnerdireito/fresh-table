# Laravel Artisan Command: FreshTable

This custom Laravel Artisan command (`php artisan fresh-table`) allows developers to refresh a specific database table without resetting the entire database. The command interacts with the user to select a migration from the `migrations` table and performs the following steps:

## Features
- **Lists all available migrations** from the `migrations` table.
- **Prompts the user to select a migration** for refresh.
- **Asks for confirmation** before proceeding.
- **Disables foreign key checks** to prevent integrity constraint errors.
- **Drops the selected table** from the database if it exists.
- **Removes the migration record** from the `migrations` table.
- **Re-runs `php artisan migrate`** to recreate the table with its updated schema.

## Installation
1. Add the command to your Laravel application by creating a new Artisan command:
   ```bash
   php artisan make:command FreshTable
   ```
2. Replace the generated command file content with the implementation provided in this repository.
3. Register the command if necessary (usually not required for Laravel 8+).

## Usage
Run the following command in the terminal:

```bash
php artisan fresh-table
```

You will be prompted to select a migration from the list and confirm the action. If confirmed, the command will handle the entire refresh process, ensuring the table is updated with the latest schema.

## Why Use FreshTable?
- **More efficient than `migrate:fresh`**, which resets all tables.
- **Ideal for schema updates** on individual tables without affecting other data.
- **Automates the refresh process**, saving time and reducing manual errors.

## License
This project is open-source and available under the [MIT License](LICENSE).
