<?php
/* vim: set ts=4 sw=4 tw=0 et :*/

namespace Jach\Json\Test;

use Jach\Json\UriRetriever;

class UriRetrieverTest extends \PHPUnit_Framework_TestCase
{
    public function testThrowExceptionIfFileIsNotFound()
    {
        $retriver = new UriRetriever(__DIR__);

        $this->expectException(\ErrorException::class);

        $retriver->retrieve('non-existing.json');
    }

    public function testReturnDecodedJson()
    {
        $retriver = new UriRetriever(__DIR__);

        $decodedJson = $retriver->retrieve('test-schema.json');

        $this->assertInstanceOf('\stdClass', $decodedJson);
    }
}
