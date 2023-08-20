# Laravel Cloudflare

This package provides integration with the Cloudflare API. It currently only supports sending a chat message.

## Installation

You can install this package via Composer using:

```bash
composer require kylewlawrence/laravel-cloudflare-api
```

The facade is automatically installed.

```php
Cloudflare::get('zones', ['per_page' => 100]);
```

## Configuration

To publish the config file to `app/config/cloudflare-laravel.php` run:

```bash
php artisan vendor:publish --provider="KyleWLawrence\Cloudflare\Providers\CloudflareServiceProvider"
```

Set your configuration using **environment variables**, either in your `.env` file or on your server's control panel:

- `CLOUDFLARE_TOKEN`

The API access token. You can create one at: `https://dash.cloudflare.com/profile/api-tokens`

- `CLOUDFLARE_EMAIL`

Set this to your Cloudflare email associated with the API key above.

- `CLOUDFLARE_DRIVER` _(Optional)_

Set this to `null` or `log` to prevent calling the Cloudflare API directly from your environment.

## Contributing

Pull Requests are always welcome here. I'll catch-up and develop the contribution guidelines soon. For the meantime, just open and issue or create a pull request.

## Usage

### Facade

The `Cloudflare` facade acts as a wrapper for an instance of the `Cloudflare\Http\HttpClient` class.

### Dependency injection

If you'd prefer not to use the facade, you can instead inject `KyleWLawrence\Cloudflare\Services\CloudflareService` into your class. You can then use all of the same methods on this object as you would on the facade.

```php
<?php

use KyleWLawrence\Cloudflare\Services\CloudflareService;

class MyClass {

    public function __construct(CloudflareService $cloudflare_service) {
        $this->cloudflare_service = $cloudflare_service;
    }

    public function getZones() {
        $this->cloudflare_service->get('zones', ['per_page' => 100]);
    }

}
```

This package is available under the [MIT license](http://opensource.org/licenses/MIT).
