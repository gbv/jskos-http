<?php declare(strict_types = 1);

namespace JSKOS;

/**
 * This subclass of JSKOS\Service contains service configuration to
 * to configure an URISpaceService among other settings.
 */
abstract class ConfiguredService extends Service
{
    protected $config = [];

    private $uriSpaceService;

    public function __construct(array $config=[])
    {
        $this->configure($config);
    }

    public function configure(array $config)
    {
        $this->config = $config;
        $this->uriSpaceService = isset($config['_uriSpace'])
            ? new URISpaceService($config['_uriSpace']) : null;
    }

    public function queryURISpace(array $query=[], string $path=''): Result
    {
        return $this->uriSpaceService 
            ? $this->uriSpaceService->query($query, $path) 
            : new Result();
    }
}
