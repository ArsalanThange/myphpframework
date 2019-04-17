<?php

namespace Core;

class Request
{
    /**
     * Errors that are generated during request parsing and validation.
     *
     * @var array
     */
    public $errors = [];

    /**
     * Get incoming request params and send it for construction.
     *
     * @param array $request    Incoming $_REQUEST parameter
     * @return void
     */
    public function __construct($request)
    {
        $this->makeRequest($request);

        //Helper function
        sanitizeArray($this);
    }

    /**
     * Converting incoming request objects/arrays into Request objects.
     *
     * @param array|object $request    Incoming $_REQUEST parameter
     * @return void
     */
    protected function makeRequest($request)
    {
        foreach ($request as $key => $value) {

            if (is_object($value) || is_array($value)) {
                $this->$key = $this->makeRequest($value);
            } else {
                $this->$key = $value;
            }

        }
    }

    /**
     * Validate incoming request against defined validation parameters.
     *
     * @param array $fields    Contains fields against the validations to be executed on the specified field.
     * @return array
     */
    public function validate($fields = [])
    {
        foreach ($fields as $field => $validations) {

            foreach ($validations as $validation => $value) {

                switch ($validation) {
                    case 'required':
                        if ($value) {
                            if (!isset($this->$field) || $this->$field == '') {
                                $this->errors[] = $field . ' is required';
                            }
                        }
                        break;
                    case 'min_length':
                        if (isset($this->$field) && strlen($this->$field) < $value) {
                            $this->errors[] = $field . ' cannot be less than ' . $value . ' characters';
                        }
                        break;
                    case 'max_length':
                        if (isset($this->$field) && strlen($this->$field) > $value) {
                            $this->errors[] = $field . ' cannot be greater than ' . $value . ' characters';
                        }
                        break;
                    default:
                        break;
                }
            }

        }

        return $this->errors;
    }
}
