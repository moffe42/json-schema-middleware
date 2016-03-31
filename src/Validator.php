<?php
/* vim: set ts=4 sw=4 tw=0 et :*/

namespace Jach\Json;

use Jach\Json\SchemaException;

class Validator
{
    public function __construct($schemaFile)
    {
        $this->schema              = $this->loadSchema($schemaFile);
        $this->jsonSchemaValidator = new \JsonSchema\Validator();
    }

    public function validateObject($object)
    {
        $this->jsonSchemaValidator->check($object, $this->schema);
        if (!$this->jsonSchemaValidator->isValid()) {
            throw new SchemaException($this->formatErrors());
        }
        return true;
    }

    public function validate($input)
    {
        $this->guardUtf8($input);

        $decodedInput = json_decode($input);
        if (is_null($decodedInput)) {
            throw new SchemaException('Input could not be parsed as json', $this->getLastJsonError());
        }

        return $this->validateObject($decodedInput);
    }

    public function getErrors()
    {
        return $this->jsonSchemaValidator->getErrors();
    }

    private function guardUtf8($input)
    {
        if (!mb_check_encoding($input, 'UTF-8')) {
            throw new SchemaException('Input is not valid UTF-8');
        }
    }

    private function formatErrors()
    {
        $msg = "";
        foreach ($this->jsonSchemaValidator->getErrors() as $error) {
            $msg .= ucfirst($error['message']);
            if (!empty($error['property'])) {
                $msg .= " for property " . $error['property'];
            }
            $msg .= ". ";
        }
        return trim($msg);
    }

    private function loadSchema($schemaFile)
    {
        if (!file_exists($schemaFile)) {
            throw new \InvalidArgumentException("Schema file could not be found");
        }

        $schema = json_decode(file_get_contents($schemaFile));

        if ($schema == false) {
            throw new \InvalidArgumentException("Schema file could not be parsed as JSON");
        }

        $retriever   = new UriRetriever(dirname($schemaFile));
        $refResolver = new \JsonSchema\RefResolver($retriever);
        $refResolver->resolve($schema);

        return $schema;
    }

    private function getLastJsonError()
    {
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return '';
        }
    }
}
