<?php

namespace App\Services;

use Carbon\Carbon;
use Elasticsearch\Client;
use Illuminate\Support\Str;

class ElasticSearchService
{
    public $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search($query)
    {
        return $this->client->search($query);
    }

    public function getSingle($index, $orderBy, $order, $filter)
    {
        $query = [
            'index' => $index,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                    ],
                ],
                'size' => 1, // Fetch all matching records
                'sort' => [[$orderBy => ['order' => $order]]],
            ],
        ];

        if ($filter) {
            $query['body']['query']['bool']['must'] = $filter;
        }

        // Elasticsearch search and handle errors
        try {
            $response = $this->search($query);
            // Check if hits are found and if not, return an empty collection
            $data = isset($response['hits']['hits']) ? collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']) : collect();
        } catch (\Exception $e) {
            // In case of error, initialize heartRates as an empty collection
            $data = collect();
        }

        if (count($data) == 0) {
            return null;
        }
        return $data[0];
    }

    public function getList($index, $orderBy, $order, $filter, $limit)
    {
        $query = [
            'index' => $index,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                    ],
                ],
                'size' => $limit, // Fetch all matching records
                'sort' => [[$orderBy => ['order' => $order]]],
            ],
        ];

        if ($filter) {
            $query['body']['query']['bool']['must'] = $filter;
        }

        // Elasticsearch search and handle errors
        try {
            $response = $this->search($query);
            // Check if hits are found and if not, return an empty collection
            $data = isset($response['hits']['hits']) ? collect($response['hits']['hits'])->map(fn($hit) => $hit['_source']) : collect();
        } catch (\Exception $e) {
            // In case of error, initialize heartRates as an empty collection
            $data = collect();
        }

        return $data;
    }
    /**
     * Save a search term.
     */
    public function save($request, $table)
    {
        $data = [
            'id' => (string) Str::uuid(),
        ];

        $data = array_merge($data, $request);

        if (isset($data['recorded_at'])) {
            $data['recorded_at'] = Carbon::parse($data['recorded_at'])->toIso8601String();
        }

        if (isset($data['device_address'])) {
            $data['device_address'] = (string) $data['device_address'];
        }

        $response = $this->saveDocument($table, $data);

        return $response;
    }

    public function deleteSearch($id)
    {
        $response = $this->deleteDocument('recent_searches', $id);

        return $response;
    }


    public function deleteSearchByKeywordAndCustomerId($keyword, $userCustomerId): array
    {
        $response = $this->client->deleteByQuery([
            'index' => 'recent_searches',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['keyword.keyword' => $keyword]], // Match the keyword
                            ['term' => ['user_customer_id' => $userCustomerId]], // Match the user_customer_id
                        ],
                    ],
                ],
            ],
        ]);

        return $response->asArray();
    }

    public function getRecentSearch($types, $userCustomerId)
    {
        $query = [
            'query' => [
                'bool' => [
                    'must' => array_filter([
                        $types ? ['terms' => ['type.keyword' => (array) $types]] : null, // Support array of types
                        $userCustomerId ? ['term' => ['user_customer_id' => $userCustomerId]] : null,
                    ]),
                ],
            ],
            'sort' => [
                'created_at' => ['order' => 'desc'],
            ],
        ];

        $response = $this->searchDocuments('recent_searches', $query);

        // Extract the source data
        $recentSearches = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

        // Remove duplicate keywords and keep the most recent entry
        $uniqueSearches = [];
        foreach ($recentSearches as $search) {
            if (!isset($uniqueSearches[$search['keyword']])) {
                $uniqueSearches[$search['keyword']] = $search;
            }
        }

        // Return unique searches as a list
        return array_values($uniqueSearches);
    }


    public function getPopularSearch($types)
    {
        if (!$types) {
            return response()->json(['error' => 'Types are required'], 400);
        }

        // Query to aggregate keywords by frequency
        $query = [
            'size' => 0, // Don't return individual documents
            'query' => [
                'bool' => [
                    'filter' => [
                        ['terms' => ['type.keyword' => (array) $types]], // Support array of types
                    ],
                ],
            ],
            'aggs' => [
                'popular_keywords' => [
                    'terms' => [
                        'field' => 'keyword.keyword',
                        'size' => 10, // Limit to top 10 keywords
                        'order' => ['_count' => 'desc'], // Sort by count
                    ],
                ],
            ],
        ];

        $response = $this->searchDocuments('recent_searches', $query);

        // Extract aggregation results
        $popularSearches = array_map(function ($bucket) use ($types) {
            return [
                'id' => uniqid(), // Generate a temporary ID for frontend
                'keyword' => $bucket['key'],
                'type' => implode(', ', (array) $types), // Combine types for display
                'count' => $bucket['doc_count'], // Include count for reference
            ];
        }, $response['aggregations']['popular_keywords']['buckets']);

        // Filter out duplicates by keywords
        $distinctSearches = [];
        foreach ($popularSearches as $search) {
            if (!isset($distinctSearches[$search['keyword']])) {
                $distinctSearches[$search['keyword']] = $search;
            }
        }

        // Return distinct searches as a list
        return array_values($distinctSearches);
    }


    /**
     * Create or update a document in Elasticsearch.
     *
     * @param string $index
     * @param string $id
     * @param array $data
     * @return array
     */
    // public function saveDocument(string $index, array $data): array
    // {
    //     $response = $this->client->index([
    //         'index' => $index,
    //         'id' => $data['id'], // Unique ID for the document
    //         'body' => $data,
    //     ]);

    //     // Convert the response to an array
    //     return $response->asArray();
    // }

    public function saveDocument(string $index, array $data): array
    {
        $response = $this->client->index([
            'index' => $index,
            'id' => $data['id'],
            'body' => $data
        ]);

        return $response;
    }




    /**
     * Get a document by ID.
     *
     * @param string $index
     * @param string $id
     * @return array
     */
    public function getDocument(string $index, string $id): array
    {
        $response = $this->client->get([
            'index' => $index,
            'id' => $id,
        ]);

        // Convert the response to an array
        return $response->asArray();
    }

    /**
     * Search for documents in Elasticsearch.
     *
     * @param string $index
     * @param array $query
     * @return array
     */
    public function searchDocuments(string $index, array $query): array
    {
        $response = $this->client->search([
            'index' => $index,
            'body' => $query,
        ]);

        // Convert the response to an array
        return $response->asArray();
    }

    /**
     * Delete a document by ID.
     *
     * @param string $index
     * @param string $id
     * @return array
     */
    public function deleteDocument(string $index, string $id): array
    {
        $response = $this->client->delete([
            'index' => $index,
            'id' => $id,
        ]);

        // Convert the response to an array
        return $response->asArray();
    }

    /**
     * Delete an index.
     *
     * @param string $index
     * @return array
     */
    public function deleteIndex(string $index): array
    {
        $response = $this->client->indices()->delete(['index' => $index]);
        // Convert the response to an array
        return $response->asArray();
    }

    /**
     * Create an index with mappings.
     *
     * @param string $index
     * @param array $mappings
     * @return array
     */
    public function createIndex(string $index, array $mappings): array
    {
        $response = $this->client->indices()->create([
            'index' => $index,
            'body' => $mappings,
        ]);

        // Convert the response to an array
        return $response->asArray();
    }

    /**
     * Check if an index exists.
     *
     * @param string $index
     * @return bool
     */
    public function indexExists(string $index): bool
    {
        $response = $this->client->indices()->exists(['index' => $index]);

        // Convert the response to an array
        return $response->asBool();
    }
}