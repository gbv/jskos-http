<?php declare(strict_types=1);
  
namespace JSKOS;
  
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use InvalidArgumentException;

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
		$this->httpClient     = $client ?: HttpClientDiscovery::find();
		$this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
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

        $result = new Result();
        if ($response->hasHeader('X-Total-Count')) {
            $result->setTotalCount(intval($response->getHeaderLine('X-Total-Count')));
        } else {
            $result->unsetTotalCount();
        }

        foreach ($data as $n => $resource) {
            $class = Resource::guessClassFromTypes($resource['type'] ?? []) 
                ?? Concept::class;
            try {
                # TODO: enable strict parsing?
                $result->append(new $class($data[$n]));
            } catch(InvalidArgumentException $e) {
                throw new Error(502, 'JSON response is no valid JSKOS');
            }
        }

        return $result;
	}
}
