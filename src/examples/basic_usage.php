<?php

require 'vendor/autoload.php';

use TwoIndexNinja\Sdk\Client;
use TwoIndexNinja\Sdk\Exception\ApiException;
use TwoIndexNinja\Sdk\Exception\NetworkException;

$accessToken = 'YOUR_API_ACCESS_TOKEN';
$client = new Client($accessToken);

try {
    // 1. Get account info
    echo "Fetching account info...\n";
    $account = $client->getAccount();
    printf(
        "Account: %s, Balance: %.2f, Tariff: %s\n\n",
        $account->email,
        $account->balance,
        $account->tariff
    );

    // 2. Get list of projects
    echo "Fetching projects...\n";
    $projects = $client->getProjects();
    if (empty($projects)) {
        echo "No projects found. Let's create one.\n";
        $message = $client->createIndexingProject('My First API Project', 'https://example.com');
        echo "Success: $message\n";
        $projects = $client->getProjects(); // Refresh list
    }

    foreach ($projects as $project) {
        printf(
            "- Project #%d: %s (%s), Type: %s, Status: %s\n",
            $project->id,
            $project->name,
            $project->website ?? 'N/A',
            $project->type,
            $project->status
        );
    }

    // 3. Add links to the first project
    if (!empty($projects)) {
        $firstProject = $projects[0];
        echo "\nAdding links to project #{$firstProject->id}...\n";

        $linksToAdd = [
            "https://example.com/page-1",
            "https://example.com/page-2",
            "http://invalid-link-format" // This will cause an error
        ];

        try {
            $message = $client->addLinks(
                projectId: $firstProject->id,
                links: $linksToAdd,
                google: true
            );
            echo "Success: $message\n";
        } catch (ApiException $e) {
            echo "API Error while adding links: " . $e->getMessage() . "\n";
            // Check for specific error details
            if (!empty($e->invalidLinks)) {
                echo "Invalid links found: " . implode(', ', $e->invalidLinks) . "\n";
            }
        }
    }

} catch (ApiException $e) {
    // Handle API-level errors (e.g., validation, not found)
    echo "API Error: " . $e->getMessage() . " (HTTP Status: " . $e->getCode() . ")\n";
} catch (NetworkException $e) {
    // Handle network-level errors (e.g., timeout, can't connect)
    echo "Network Error: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    // Handle other unexpected errors
    echo "An unexpected error occurred: " . $e->getMessage() . "\n";
}
