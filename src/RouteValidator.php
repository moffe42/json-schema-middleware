<?php
/* vim: set ts=4 sw=4 tw=0 et :*/

namespace Jach\Json;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Jach\Json\SchemaException;

class RouteValidator
{
    public function __construct($jsonValidator)
    {
        $this->jsonValidator = $jsonValidator;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        try {
            $this->jsonValidator->validate((string)$request->getBody());
        } catch (SchemaException $e) {
            return $response->withStatus(400, 'Invalid JSON');
        }

        return $next($request, $response);
    }
}
