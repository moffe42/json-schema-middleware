<?php
/* vim: set ts=4 sw=4 tw=0 et :*/

namespace Jach\Json;

class UriRetriever
{
    public function __construct($base)
    {
        $this->base = $base;
    }

    public function retrieve($uri)
    {
        $path = "{$this->base}/$uri";
        if (!file_exists($path)) {
            throw new \ErrorException("Schema not found at '{$path}'");
        }

        return json_decode(file_get_contents($path));
    }
}
