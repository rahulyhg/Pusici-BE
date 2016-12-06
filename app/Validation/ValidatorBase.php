<?php
namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;

abstract class ValidatorBase
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
    abstract protected function initRules();

    /**
     * Set user custom error messages
     *
     * @return void
     */
    abstract protected function initMessages();

    /**
     * Assert validation rules.
     *
     * @param array $inputs
     *            The inputs to validate
     * @return boolean True on success; otherwise, false
     */
    public function validate(array $inputs)
    {
        $result = true;

        foreach ($this->rules as $rule => $validator) {
            try {
                // array_get see Laravel Helper Functions (https://laravel.com/docs/5.3/helpers)
                $validator->assert(array_get($inputs, $rule));
            } catch (NestedValidationException $exception) {
                // $newMessages[$rule] = $exception->findMessages($this->messages);
                $newMessages[$rule] = $exception->getMessages();
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
