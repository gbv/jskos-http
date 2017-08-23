<?php declare(strict_types=1);

namespace JSKOS;

const QueryModifiers = [
    "limit", "offset", "properties", "callback",
];

/**
 * JSKOS API backend.
 */
abstract class Service
{
    /**
     * List of supported query parameters.
     * @var array
     */
    protected $supportedParameters = [];

    /**
     * Perform a query.
     *
     * @return Result
     * @throws Error
     */
    abstract public function query(array $request, string $path=''): Result;

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
