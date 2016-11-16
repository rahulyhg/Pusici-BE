<?php

namespace App\Validators;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as V;

class UserValidator
{
    /**
     * List of constraints
     *
     * @var array
     */
    protected $rules = [];

    /**
     * List of customized messages
     *
     * @var array
     */
    protected $messages = [];

    /**
     * List of returned errors in case of a failing assertion
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Just another constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->initRules();
        $this->initMessages();
    }

    /**
     * Set the user subscription constraints
     *
     * @return void
     */
    public function initRules()
    {
        $this->rules['first_name'] = V::alpha()->noWhitespace()->length(1, 20)->setName('First name');
        $this->rules['last_name'] = V::alpha()->noWhitespace()->length(1, 20)->setName('Last name');
        $this->rules['email'] = V::email();
        $this->rules['password'] =
            V::regex('/(?=(.*[a-z]){2})(?=(.*[A-Z]){2})(?=(.*\d){2})(?=(.*[~!@#$%^&*?.]){2})/')
            ->noWhitespace()
            ->length(10, 20)->setName('Password');
    }

    /**
     * Set user custom error messages
     *
     * @return void
     */
    public function initMessages()
    {
        $this->messages = [
            'alpha'         => '{{name}} must only contain alphabetic characters.',
            'noWhitespace'  => '{{name}} must not contain white spaces.',
            'length'        => '{{name}} length must be between {{minValue}} and {{maxValue}}.',
            'email'         => 'Please make sure you typed a correct email address.',
            'regex'         => '{{name}} must contain at least 2 lower-case letters, 2 upper-case letters, 2 digits and 2 special characters.',
        ];
    }

    /**
     * Assert validation rules.
     *
     * @param array $inputs
     *   The inputs to validate.
     * @return boolean
     *   True on success; otherwise, false.
     */
    public function validate(array $inputs)
    {
        $result = true;
        
        foreach ($this->rules as $rule => $validator) {
            try {

                $validator->assert(array_get($inputs, $rule));

            } catch(NestedValidationException $exception) {
                $newMessages[$rule] = $exception->findMessages($this->messages);
                //$newMessages[$rule] = $exception->getMessages();
                $this->errors = array_merge($this->errors, $newMessages);
                $result = false;
            }
        }

        return $result;
    }

    public function errors()
    {
        return $this->errors;
    }
}
