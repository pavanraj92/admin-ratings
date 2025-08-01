# admin-ratings

This package allows you to manage product ratings and reviews submitted by users in the admin panel.

## Features

- View product ratings and reviews
- Filter ratings by user, product, or status
- Sort ratings by various columns (e.g. user, product, rating)
- View star rating display for each review
- Soft delete and restore ratings

## Usage

1. **View**: See a paginated list of ratings with user and product information.
2. **Filter**: Filter ratings by user name, product name, or status (e.g. Approved/Pending).
3. **Sort**: Sort ratings by rating value, creation date, etc.
4. **Restore/Delete**: Soft delete or restore individual ratings if needed.

## Example Endpoints

| Method | Endpoint       | Description         |
|--------|----------------|---------------------|
| GET    | `/ratings`     | List all ratings    |
| GET    | `/ratings/{id}`| Get rating details  |
| DELETE | `/ratings/{id}`| Delete a rating     |

## Requirements

- PHP 8.2+
- Laravel Framework

## Update `composer.json` file

Add the following to your `composer.json` to use the package from a local path:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/pavanraj92/admin-ratings.git"
    }
]
```

## Installation

```bash
composer require admin/ratings:@dev
```

## Usage

1. Publish the configuration and migration files:
    ```bash    
    php artisan ratings:publish --force

    composer dump-autoload

    php artisan migrate
    ```
2. Access the Rating manager from your admin dashboard.


## Customization

You can customize views, routes, and permissions by editing the package's configuration file.


## License

This package is open-sourced software licensed under the Dotsquares.write code in the readme.md file regarding to the admin/ratings manager