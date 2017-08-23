<?php declare(strict_types = 1);

namespace JSKOS;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Http\Message\ResponseFactory;
use Http\Discovery\MessageFactoryDiscovery;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * A JSKOS Server.
 */
class Server implements \Psr\Log\LoggerAwareInterface
{
    protected $service;
    protected $responseFactory;
    protected $logger;

    public function __construct(
        Service $service, 
        ResponseFactory $responseFactory=null,
        LoggerInterface $logger=null 
    )
    {
        $this->service = $service;
        $this->responseFactory = $responseFactory ?: MessageFactoryDiscovery::find();
        $this->logger = $logger ?: new NullLogger();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function parseRequest(RequestInterface $request): array
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        $query = [];
        parse_str($uri->getQuery(), $query);

        $callback = null;
        if (isset($query['callback'])) {
            if (preg_match('/^[$A-Z_][0-9A-Z_$.]*$/i', $query['callback'])) {
                $callback = $query['callback'];
            }
            unset($query['callback']);
        }

        # TODO: get language parameter from headers

        return [$query, $path, $callback];
    }

    public function query(RequestInterface $request): ResponseInterface
    {    
        $method = $request->getMethod();

        $result = null;
        $callback = null;

        if ($method == 'GET' || $method == 'HEAD') {
            list ($query, $path, $callback) = $this->parseRequest($request);

            # TODO: detect conflicting parameters?
            # if (isset($params['uri']) and isset($params['search'])) {
            #   $error = new Error(422, 'request_error', 'Conflicting request parameters uri & search');
            # }

            try {
                $result = $this->service->query($query, $path);
            } catch(Error $error) {
                $result = $error;
            }
            # TODO: catch other kinds of errors:
            # } catch (\Exception $e) {
            # $this->logger->error('Service Exception', ['exception' => $e]);
            # $error = new Error(500, 'Internal server error');
        } elseif ($request->getMethod() == 'OPTIONS') {            
            return $this->optionsResponse();
        } else {
            $result = new Error(405, 'Method not allowed');
        }


        if ($result instanceof Result) {
            $code = 200;
            $headers = [
                'Access-Control-Allow-Origin' => '*',
                'Content-Type' => 'application/json; charset=UTF-8',
                'X-Total-Count' => $result->getTotalCount()
            ];
        } else {
            $code = $result->code;
            $headers = [
                'Access-Control-Allow-Origin' => '*',
                'Content-Type' => 'application/json; charset=UTF-8',
            ];
        }

        $body = $result->json();
        $headers['Content-Length'] = strlen($body);
        if ($method == 'HEAD') {
            $body = '';
        }

        if ($callback) {
            $body = "/**/$callback($body);";
            $headers['Content-Type'] = 'application/javascript; charset=UTF-8';
        }

        return $this->responseFactory->createResponse($code, null, $headers, $body);
    }


    public function optionsResponse()
    {
        $headers = [
            'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
        ];

        # TODO:
        # if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) &&
        #    $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'GET') {
        #    $response->headers['Access-Control-Allow-Origin'] = '*';
        #    $response->headers['Acess-Control-Expose-Headers'] = 'Link, X-Total-Count';

        return $this->responseFactory->createResponse(200, null, $headers, '');
    }

    /**
     * TODO: Extract requested languages(s) from request.
    public function extractRequestLanguage($params)
    {
        $language = null;

        # get query modifier: language
        if (isset($params['language'])) {
            $language = $params['language'];
            unset($params['language']);
            # TODO: parse language
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            # parse accept-language-header
            preg_match_all(
                '/([a-z]+(?:-[a-z]+)?)\s*(?:;\s*q\s*=\s*(1|0?\.[0-9]+))?/i',
                $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                $match);
            if (count($match[1])) {
                foreach ($match[1] as $i => $l) {
                    if (isset($match[2][$i]) && $match[2][$i] != '') {
                        $langs[strtolower($l)] = (float) $match[2][$i];
                    } else {
                        $langs[strtolower($l)] = 1;
                    }
                }
                arsort($langs, SORT_NUMERIC);
                reset($langs);
                $language = key($langs); # most wanted language
            }
        }
        
        return $language;
    }
*/

	/**
	 * Utility function to emit a Response without additional framework.
	 */
    public static function sendResponse(ResponseInterface $response) 
    {
		$code = $response->getStatusCode();
		$reason = $response->getReasonPhrase();
		header(
			sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $code, $reason),
			true, $code 
		);

		foreach ($response->getHeaders() as $header => $values) {
			foreach ($values as $value) {
				header("$header: $value", false);
			}
		}

		echo $response->getBody();
	}
}
