<?php

namespace Lamudi\UseCaseBundle\Request\Processor;

use Lamudi\UseCaseBundle\Request\Request;

interface InputProcessorInterface
{
    /**
     * Initializes a use case request based on the input data received. Additional options may help
     * determine the way to initialize the use case request object.
     *
     * @param Request $request The use case request object to be initialized.
     * @param mixed $input Any object that contains input data.
     * @param array $options An array of options used to create the request object.
     */
    public function initializeRequest($request, $input, $options = []);
}
