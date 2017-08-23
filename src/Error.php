<?php declare(strict_types=1);

namespace JSKOS;

use JSKOS\PrettyJsonSerializable;

/**
 * A JSKOS API Error response.
 *
 * @see https://gbv.github.io/jskos-api/jskos-api.html#error-responses
 */
class Error extends \Error implements \JsonSerializable
{
    protected $error;
    protected $description;
    protected $uri;

    /**
     * Create a JSKOS API error.
     *
     * @param integer $code HTTP status code
     * @param string  $message
     * @param string  $description
     * @param string  $uri
     */
    public function __construct(int $code, string $message=null, string $description=null, string $uri=null)
    {
        $this->code        = $code;
        $this->message     = $message;
        $this->description = $description;
        $this->uri         = $uri;
    }


    /**
     * Only include non-null fields in JSON.
     */
    public function jsonSerialize()
    {
        $json = [];

        foreach (['code', 'message', 'description', 'uri'] as $field) {
            if ($this->$field !== null) {
                $json[$field] = $this->$field;
            }
        }

        return $json;
    }
}
