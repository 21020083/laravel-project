<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides default implementation of failedValidation function
 */
trait ApiFailedValidation
{
    use ApiResponse;

    /**
     * @param Validator $validator
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = [];
        $messages = $validator->errors()->getMessages();

        if (!empty($messages)) {
            foreach ($messages as $field => $fieldMessages) {
                if (!empty($fieldMessages[0])) {
                    $errors[$field] = $fieldMessages[0];
                }
            }
        }
        throw new HttpResponseException($this->sendError(null, null, Response::HTTP_UNPROCESSABLE_ENTITY, $errors));
    }
}
