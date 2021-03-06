<?php
/*
 * This file is part of the php-mvc-project <https://github.com/php-mvc-project>
 * 
 * Copyright (c) 2018 Aleksey <https://github.com/meet-aleksey>
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpMvc;

/**
 * Provides a way to return an action result with a specific HTTP response status code and description.
 */
class HttpStatusCodeResult implements ActionResult {

    /**
     * Gets the HTTP status code.
     * 
     * @var int
     */
    private $statusCode;

    /**
     * Gets the HTTP status description.
     * 
     * @var string
     */
    private $statusDescription;

    /**
     * Initializes a new instance of HttpStatusCodeResult.
     * 
     * @param int $statusCode The HTTP status code.
     * @param string $statusDescription The HTTP status description.
     */
    public function __construct($statusCode, $statusDescription = null) {
        $this->statusCode = $statusCode;
        $this->statusDescription = $statusDescription;
    }

    /**
     * Executes the action and outputs the result.
     * 
     * @param ActionContext $actionContext The context in which the result is executed.
     * The context information includes information about the action that was executed and request information.
     * 
     * @return void
     */
    public function execute($actionContext) {
        $response = $actionContext->getHttpContext()->getResponse();
        $response->setStatusCode($this->statusCode);
        $response->setStatusDescription($this->statusDescription);
        $response->write($result);
        $response->end();
    }

}