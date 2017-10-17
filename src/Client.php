<?php declare(strict_types=1);
  
namespace JSKOS;
  
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;

/**
 * JSKOS API Client
 */
class Client extends Service
{
    protected $baseUrl;
	protected $httpClient;
    protected $requestFactory;

    public function __construct(
        string $baseUrl,
        HttpClient $client=null, 
        RequestFactory $requestFactory=null
    )
    {
		$this->baseUrl        = $baseUrl;
		$this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
		$this->httpClient     = $client ?: HttpClientDiscovery::find();
	}

	public function query(array $query=[], string $path=''): Result {
		$url = $this->baseUrl . $path;
                
		if (count($query)) {
			$url .= '?' . http_build_query($query);
		}

        $request = $this->requestFactory->createRequest('GET', $url, []);
		$response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() != 200) {
            throw new Error(502, 'Unsuccessful HTTP response');
        }

        $body = (string)$response->getBody();
        if (!preg_match('/\s*\[/m', $body)) {
            throw new Error(502, 'Failed to parse JSON array');
        }

        $data = json_decode($body, true);        
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new Error(502, 'Failed to parse JSON', json_last_error_msg());
        }

        foreach ($data as $n => $resource) {
            $class = Resource::guessClassFromTypes($resource['type'] ?? []) 
                ?? Concept::class;
            try {
                # TODO: enable strict parsing?
                $data[$n] = new $class($data[$n]);
            } catch(InvalidArgumentException $e) {
                throw new Error(502, 'JSON response is no valid JSKOS');
            }
        }

		# TODO: add total, offset, limit of page
        return new Result($data ?? []);
	}
}
