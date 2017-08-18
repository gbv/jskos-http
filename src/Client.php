<?php declare(strict_types=1);
  
namespace JSKOS;
  
use Http\Client\Common\HttpMethodsClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;

/**
 * JSKOS API Client
 */
class Client extends Service
{
	protected $baseURL;
	protected $httpClient;
	protected $methods;

	public function __construct(string $url, array $methods=null, HttpMethodsClient $client=null) {
		$this->baseURL = $url;
		$this->methods = $methods ?? [
			'top', 'broader', 'narrower', 'descendants', 'ancestors'
		];
		$this->httpClient = $client ?: new HttpMethodsClient(
			HttpClientDiscovery::find(),
			MessageFactoryDiscovery::find()
		);		
	}

	public function query(array $query, string $method='') {
		$query = array_intersect_key(
			$query,
			array_flip(['uri','id','notation','type','limit','offset','properties'])
		);

		$url = $this->baseURL;
        
        if ($method) {
            if (in_array($this->methods, $method)) {
    			$url .= "/$method";
            } else {
                return new Page();
            }
        }

		if (count($query)) {
			$url .= '?' . http_build_query($query);
		}

		$response = $this->httpClient->get($url);

		# TOOD: catch error, broken JSON etc.
		$json = $response->getBody()->getContents();
        $data = json_decode($json, true);

		# TODO: add total, offset, limit of page
        return new Page($data ?? []);
	}
}
