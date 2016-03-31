<?php
/* vim: set ts=4 sw=4 tw=0 et :*/

namespace Jach\Json\Test;

use Jach\Json\SchemaException;
use Jach\Json\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testWeDoNotThrowOnValidJson()
    {
        $input = '{"test":"lol"}';
        $validator = new Validator(__DIR__.'/test-schema.json');

        $this->assertTrue($validator->validate($input));
    }

    public function testWeDoNotThrowOnValidObject()
    {
        $input = new \stdClass;
        $input->test = "lol";
        $validator = new Validator(__DIR__.'/test-schema.json');

        $this->assertTrue($validator->validateObject($input));
    }

    public function testWeThrowOnInvalidUtf8String()
    {
        $input = "{\"test\": \"\xe6ble\"}";
        $validator = new Validator(__DIR__.'/test-schema.json');

        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Input is not valid UTF-8');

        $validator->validate($input);
    }

    public function testWeDoNotThrowOnValidUtf8String()
    {
        $input = "{\"test\": \"\xe2\x98\x81\"}";
        $validator = new Validator(__DIR__.'/test-schema.json');

        $this->assertTrue($validator->validate($input));
    }

    public function testWeThrowOnInputWhichDoesNotMatchSchema()
    {
        $input = '{"nottest":"lol"}';
        $validator = new Validator(__DIR__.'/test-schema.json');

        $this->expectException(SchemaException::class);

        $validator->validate($input);
    }

    public function testWeThrowOnInputWithWrongType()
    {
        $input = '{"test":1}';
        $validator = new Validator(__DIR__.'/test-schema.json');

        $this->expectException(SchemaException::class);

        $validator->validate($input);
    }

    public function testWeThrowOnInvalidInput()
    {
        $input = 'weoij0923r';
        $validator = new Validator(__DIR__.'/test-schema.json');

        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Input could not be parsed as json');

        $validator->validate($input);
    }

    public function testWeThrowOnSchemaNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Schema file could not be found');

        $validator = new Validator("oijwefoijwefoijwef");
    }

    public function testWeThrowOnInvalidSchema()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Schema file could not be parsed as JSON');

        $validator = new Validator(__DIR__.'/test-invalid-schema.json');
    }
}
