# 2Index.Ninja PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/2indexninja-hub/2index-ninja-sdk.svg?style=flat-square)](https://packagist.org/packages/2indexninja-hub/2index-ninja-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/2indexninja-hub/2index-ninja-sdk.svg?style=flat-square)](https://packagist.org/packages/2indexninja-hub/2index-ninja-sdk)

The official PHP SDK for the [2index.ninja](https://2index.ninja/) API. A modern, fluent, and fully-typed client for seamless integration into your PHP projects.

## Why 2Index.Ninja?

Our service provides powerful tools to manage the indexing of your websites in search engines.

### 🚀 Google Indexing API
Our service supports the **Google Indexing API**, allowing you to quickly and safely manage the indexing of your site's pages in the Google search engine. With our help, you can efficiently increase the number of indexed pages on your site, regardless of their quantity.

### ⚡️ IndexNow
The **IndexNow** protocol works similarly to the Google Indexing API but extends its reach. While the Indexing API is specific to Google, IndexNow works equally well with search engines like **Yandex** and **Bing**. This method is also fast, safe, and efficient.

### 🔗 Backlinks Indexing
External links are one of the most effective ways to boost your site's visibility in search engines. However, not all backlinks are indexed quickly, and sometimes it can take a very long time. Our service can help **speed up the indexing of backlinks**, ensuring your SEO efforts pay off sooner. Remember, the success of indexing also depends on the quality of the donor site where the link is placed.

## SDK Features

*   **A simple, object-oriented interface.**
*   **Strictly-typed models (DTOs)** for all API responses, enabling autocompletion in your IDE and type safety.
*   **Robust error handling** with custom exceptions: `ApiException` and `NetworkException`.
*   **"Batteries-included" with a built-in Guzzle HTTP client** for zero-config, out-of-the-box usage.
*   **Fully documented code** with PHPDoc.

## Installation

You can install the package via Composer:

```bash
composer require 2indexninja-hub/2index-ninja-sdk
```

## Quick Start

To get started, you'll need your `API_TOKEN` from your [2index.ninja account](https://2index.ninja/user/profile#api-token).

```php
<?php

require 'vendor/autoload.php';

use TwoIndexNinja\Sdk\Client;

$accessToken = 'YOUR_API_ACCESS_TOKEN';
$client = new Client($accessToken);
```

## Usage Examples

### Getting Account Information

This is a basic request to check if your token is valid and to get information about your balance and plan.

```php
try {
    $account = $client->getAccount();
    printf(
        "Email: %s, Balance: %.2f, Plan: %s\n",
        $account->email,
        $account->balance,
        $account->tariff
    );
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Working with Projects

**Get a list of your projects:**
```php
$projects = $client->getProjects();

foreach ($projects as $project) {
    printf(
        "Project #%d: %s, Type: %s, Status: %s\n",
        $project->id,
        $project->name,
        $project->type,
        $project->status
    );
}
```

**Create a new indexing project:**
```php
try {
    $message = $client->createIndexingProject(
        name: 'My New Website', 
        website: 'https://example.com',
        indexingSpeed: 100
    );
    echo "Success: $message\n";
} catch (\Exception $e) {
    echo "Error creating project: " . $e->getMessage();
}
```

### Working with Links

**Add links to an existing project:**
```php
$projectId = 123; // Your project's ID
$links = [
    'https://example.com/my-super-page-1',
    'https://example.com/my-super-page-2',
];

try {
    $message = $client->addLinks(
        projectId: $projectId,
        links: $links,
        google: true, // Submit to Google
        yandex: true  // Submit to Yandex
    );
    echo "Success: $message\n";
} catch (\Exception $e) {
    echo "Error adding links: " . $e->getMessage();
}
```

**Add a sitemap:**
```php
$projectId = 123;
$sitemapUrl = 'https://example.com/sitemap.xml';

try {
    $message = $client->addSitemap(
        projectId: $projectId,
        sitemapUrl: $sitemapUrl,
        google: true,
        watch: true // Monitor sitemap for changes
    );
    echo "Success: $message\n";
} catch (\Exception $e) {
    echo "Error adding sitemap: " . $e->getMessage();
}
```

### Error Handling

The SDK uses two exception types for predictable error handling:
- `NetworkException`: For network-related issues (e.g., timeout, connection refused).
- `ApiException`: For errors returned by the API (e.g., invalid data, project not found).

```php
use TwoIndexNinja\Sdk\Exception\ApiException;
use TwoIndexNinja\Sdk\Exception\NetworkException;

$projectId = 123;
$invalidLinks = ['http://invalid-link', 'just-text'];

try {
    $client->addLinks($projectId, $invalidLinks, google: true);
} catch (ApiException $e) {
    // Handle API-level errors
    echo "API Error: " . $e->getMessage() . "\n";
    
    // You can access detailed error information
    if (!empty($e->invalidLinks)) {
        echo "Invalid links found: " . implode(', ', $e->invalidLinks) . "\n";
    }
} catch (NetworkException $e) {
    // Handle network-level errors
    echo "Network Error: " . $e->getMessage() . "\n";
}
```

## Full Demo File

For a complete, ready-to-run example demonstrating all SDK methods, please see the `/examples/basic_usage.php` file in this repository.

## API Reference

*   `getAccount(): Account`
*   `getProjects(): Project[]`
*   `getProject(int $projectId): Project`
*   `createIndexingProject(string $name, ...): string`
*   `createIndexingCheckProject(string $name, ...): string`
*   `clearQueue(int $projectId): string`
*   `addLinks(int $projectId, ...): string`
*   `addLinksSimple(array|string $links, ...): AddedLinksSimpleResponse`
*   `getLinkSources(int $projectId): LinkSource[]`
*   `addSitemap(int $projectId, ...): string`
*   `updateSitemapWatch(int $projectId, int $sitemapId, bool $watch): bool`
*   `deleteSitemap(int $projectId, int $sitemapId): string`

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.