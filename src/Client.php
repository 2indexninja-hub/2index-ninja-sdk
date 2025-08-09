<?php

namespace TwoIndexNinja\Sdk;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use TwoIndexNinja\Sdk\Exception\ApiException;
use TwoIndexNinja\Sdk\Exception\NetworkException;
use TwoIndexNinja\Sdk\Model\Account;
use TwoIndexNinja\Sdk\Model\AddedLinksSimpleResponse;
use TwoIndexNinja\Sdk\Model\LinkSource;
use TwoIndexNinja\Sdk\Model\Project;

class Client
{
    private const API_BASE_URL = 'https://2index.ninja/api/v1/';
    private const DEFAULT_USER_AGENT = '2Index-Ninja-PHP-SDK/1.0';

    private GuzzleClient $httpClient;

    /**
     * @param string $accessToken Your API access token.
     * @param array $guzzleOptions Custom options for Guzzle client.
     * @param string $userAgent Custom User-Agent header.
     */
    public function __construct(
        private readonly string $accessToken,
        array $guzzleOptions = [],
        string $userAgent = self::DEFAULT_USER_AGENT
    ) {
        $defaultOptions = [
            'base_uri' => self::API_BASE_URL,
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept' => 'application/json',
                'User-Agent' => $userAgent,
            ],
        ];

        $this->httpClient = new GuzzleClient(array_merge_recursive($defaultOptions, $guzzleOptions));
    }

    /**
     * Get account details.
     * @return Account
     * @throws ApiException|NetworkException
     */
    public function getAccount(): Account
    {
        $response = $this->request('GET', 'account');
        return Account::fromArray($response['account']);
    }

    /**
     * Get a list of all projects.
     * @return Project[]
     * @throws ApiException|NetworkException
     */
    public function getProjects(): array
    {
        $response = $this->request('GET', 'project');
        return array_map(fn(array $data) => Project::fromArray($data), $response['projects']);
    }

    /**
     * Get a specific project by its ID.
     * @param int $projectId
     * @return Project
     * @throws ApiException|NetworkException
     */
    public function getProject(int $projectId): Project
    {
        $response = $this->request('GET', "project/{$projectId}");
        return Project::fromArray($response['project']);
    }

    /**
     * Creates a new project for link indexing.
     *
     * @param string $name Project name.
     * @param string $website Website address.
     * @param bool $forExternalLinks Project is for external links.
     * @param int|null $indexingSpeed Indexing speed.
     * @return string Success message.
     * @throws ApiException|NetworkException
     */
    public function createIndexingProject(string $name, string $website, bool $forExternalLinks = false, ?int $indexingSpeed = null): string
    {
        $payload = [
            'name' => $name,
            'website' => $website,
            'for_external_links' => $forExternalLinks,
            'indexing_speed' => $indexingSpeed,
            'type' => 'indexing',
        ];

        $response = $this->request('POST', 'project', ['json' => array_filter($payload)]);
        return $response['message'];
    }

    /**
     * Creates a new project for checking indexing.
     *
     * @param string $name Project name.
     * @param int|null $checkingSpeed Checking speed.
     * @return string Success message.
     * @throws ApiException|NetworkException
     */
    public function createIndexingCheckProject(string $name, ?int $checkingSpeed = null): string
    {
        $payload = [
            'name' => $name,
            'checking_speed' => $checkingSpeed,
            'type' => 'indexing_check',
        ];

        $response = $this->request('POST', 'project', ['json' => array_filter($payload)]);
        return $response['message'];
    }


    /**
     * Clears the indexing queue for a project.
     * @param int $projectId
     * @return string Success message.
     * @throws ApiException|NetworkException
     */
    public function clearQueue(int $projectId): string
    {
        $response = $this->request('POST', "project/{$projectId}/clear_queue");
        return $response['message'];
    }

    /**
     * Adds links to the specified project.
     *
     * @param int $projectId Project ID.
     * @param array|string $links Array of links or string with newlines.
     * @param bool $google Send to Google.
     * @param bool $yandex Send to Yandex.
     * @param bool $bing Send to Bing.
     * @param bool $googleAccessGranted Access to Google account is granted.
     * @return string Success message.
     * @throws ApiException|NetworkException
     */
    public function addLinks(int $projectId, array|string $links, bool $google = false, bool $yandex = false, bool $bing = false, bool $googleAccessGranted = false): string
    {
        $payload = [
            'project_id' => $projectId,
            'links' => $links,
            'google' => $google,
            'yandex' => $yandex,
            'bing' => $bing,
            'google_access_granted' => $googleAccessGranted,
        ];

        $response = $this->request('POST', 'link/add', ['json' => $payload]);
        return $response['message'];
    }

    /**
     * Adds links by project name. If the project does not exist, it will be created.
     *
     * @param array|string $links Array of links or string with newlines.
     * @param string $projectName Project name (defaults to 'default').
     * @param bool $google Send to Google.
     * @param bool $yandex Send to Yandex.
     * @param bool $bing Send to Bing.
     * @param bool $googleAccessGranted Access to Google account is granted.
     * @return AddedLinksSimpleResponse
     * @throws ApiException|NetworkException
     */
    public function addLinksSimple(array|string $links, string $projectName = 'default', bool $google = false, bool $yandex = false, bool $bing = false, bool $googleAccessGranted = false): AddedLinksSimpleResponse
    {
        $payload = [
            'project_name' => $projectName,
            'links' => $links,
            'google' => $google,
            'yandex' => $yandex,
            'bing' => $bing,
            'google_access_granted' => $googleAccessGranted,
        ];

        $response = $this->request('POST', 'link/add_simple', ['json' => $payload]);
        return new AddedLinksSimpleResponse(
            $response['success'],
            $response['message'],
            $response['project_name'],
            $response['project_id']
        );
    }

    /**
     * Get a list of link sources for a project.
     * @param int $projectId
     * @return LinkSource[]
     * @throws ApiException|NetworkException
     */
    public function getLinkSources(int $projectId): array
    {
        $response = $this->request('POST', 'link_sources', ['json' => ['project_id' => $projectId]]);
        return array_map(fn(array $data) => LinkSource::fromArray($data), $response);
    }

    /**
     * Adds a sitemap to the project.
     *
     * @param int $projectId Project ID.
     * @param string $sitemapUrl Sitemap URL.
     * @param bool $google Send to Google.
     * @param bool $yandex Send to Yandex.
     * @param bool $bing Send to Bing.
     * @param bool $googleAccessGranted Access to Google account granted.
     * @param bool $watch Monitor sitemap for changes.
     * @return string Success message.
     * @throws ApiException|NetworkException
     */
    public function addSitemap(int $projectId, string $sitemapUrl, bool $google = false, bool $yandex = false, bool $bing = false, bool $googleAccessGranted = false, bool $watch = false): string
    {
        $payload = [
            'project_id' => $projectId,
            'sitemap' => $sitemapUrl,
            'google' => $google,
            'yandex' => $yandex,
            'bing' => $bing,
            'google_access_granted' => $googleAccessGranted,
            'watch' => $watch,
        ];

        $response = $this->request('POST', 'sitemap/add', ['json' => $payload]);
        return $response['message'];
    }

    /**
     * Update sitemap watch status.
     * @param int $projectId
     * @param int $sitemapId
     * @param bool $watch
     * @return bool
     * @throws ApiException|NetworkException
     */
    public function updateSitemapWatch(int $projectId, int $sitemapId, bool $watch): bool
    {
        $payload = [
            'project_id' => $projectId,
            'sitemap_id' => $sitemapId,
            'watch' => $watch,
        ];
        $response = $this->request('POST', 'sitemap/update_watch', ['json' => $payload]);
        return $response['success'];
    }

    /**
     * Deletes a sitemap from the project.
     * @param int $projectId
     * @param int $sitemapId
     * @return string Success message.
     * @throws ApiException|NetworkException
     */
    public function deleteSitemap(int $projectId, int $sitemapId): string
    {
        $payload = [
            'project_id' => $projectId,
            'sitemap_id' => $sitemapId,
        ];
        $response = $this->request('POST', 'sitemap/delete', ['json' => $payload]);
        return $response['message'];
    }

    /**
     * @throws NetworkException|ApiException
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->httpClient->request($method, $uri, $options);
            $body = (string) $response->getBody();
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            // API может вернуть 200 OK, но с ошибкой внутри JSON
            if (isset($data['success']) && $data['success'] === false) {
                $errorMsg = 'API Error';
                if (!empty($data['errors'])) {
                    // Объединяем вложенные массивы ошибок в одну строку
                    $errorMsg = implode('; ', array_map(fn($e) => is_array($e) ? implode(', ', $e) : $e, $data['errors']));
                } elseif (isset($data['message'])) {
                    $errorMsg = $data['message'];
                }

                throw new ApiException($errorMsg, $response->getStatusCode(), $data['errors'] ?? [], $data['invalid_links'] ?? []);
            }

            return $data;

        } catch (GuzzleException $e) {
            throw new NetworkException($e->getMessage(), $e->getCode(), $e);
        } catch (\JsonException $e) {
            throw new NetworkException('Failed to decode API response: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
