<?php declare(strict_types=1);

namespace JSKOS;

/**
 * Query modifiers as defined by JSKOS API.
 */
const QueryModifiers = [
    "properties",
    "expand",
    "page","limit","unique",
    "callback",
];

/**
 * JSKOS API backend class.
 *
 * A Service can be queried with a set of query parameters to return a Page
 * or Error. To actually implement JSKOS API, create a Server that passes HTTP
 * requests to the Service. A Service must implement the query method and possibly 
 * the supportedParameters member variable:
 *
 * @code
 * class MyService extends \JSKOS\Service {
 *
 *     protected $supportedParameters = [...];
 *
 *     public function query($request) {
 *         ...
 *     }
 *
 * }
 * @endcode
 *
 * Each %Service can be configured to support specific query parameters, in
 * addition to the mandatory parameter `uri`. The list of supported parameters
 * can be returned as URI Template.
 *
 * @code
 * $service->supportParameter('notation');
 * $service->uriTemplate(); # '{?uri}{?notation}'
 * @endcode
 *
 * @see Server
 */
abstract class Service
{
    /**
     * List of supported query parameters.
     * @var array
     */
    protected $supportedParameters = [];

    /**
     * List of available types, given by their URIs.
     *
     * If left empty then all types are possible. The query parameter 'type' is
     * added to the list of supported query parameter otherwise, so requests can
     * be checked before perfoming a query and results can be checked for expected
     * types.
     *
     * @var array
     */
    protected $supportedTypes = [];

    /**
     * Create a new service.
     */
    public function __construct()
    {
        $this->supportParameter('uri');
        if (count($this->supportedTypes) and !in_array('type', $this->supportedParameters)) {
            $this->supportParameter('type');
        }
    }

    /**
     * Perform a query.
     *
     * @return Page|Error
     */
    abstract public function query(array $request, string $method='');

    /**
     * Enable support of a query parameter.
     * @param string $name
     */
    public function supportParameter($name)
    {
        if (in_array($name, QueryModifiers)) {
            throw new \DomainException("parameter $name not allowed");
        }
        $this->supportedParameters[$name] = $name;
        asort($this->supportedParameters);
    }

    /**
     * Get a list of supported query parameters.
     * @return array
     */
    public function getSupportedParameters()
    {
        return $this->supportedParameters;
    }

    /**
     * Get a list of supported type URIs.
     * @return array
     */
    public function getSupportedTypes()
    {
        return $this->supportedTypes;
    }

    /**
     * Get a list of query parameters as URI template.
     *
     * @return string
     */
    public function uriTemplate($template='')
    {
        foreach ($this->supportedParameters as $name) {
            $template .= "{?$name}";
        }
        return $template;
    }
}
