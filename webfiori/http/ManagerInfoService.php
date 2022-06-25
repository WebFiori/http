<?php

/*
 * The MIT License
 *
 * Copyright 2020 Ibrahim BinAlshikh, WebFiori HTTP.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace webfiori\http;

/**
 * A service which can be used to display information about services manager.
 * 
 * The developer must extend this class and complete the implementation of 
 * the method AbstractWebService::isAuthorized() in order to use it.
 * 
 * @author Ibrahim
 * 
 * @version 1.0
 */
abstract class ManagerInfoService extends AbstractWebService {
    /**
     * Creates new instance of the class.
     * 
     * @since 1.0
     */
    public function __construct() {
        parent::__construct('api-info');
        $this->setDescription('Returns a JSON string that contains all '
                .'needed information about all end points which are registered '
                .'under given manager.');
        $this->addParameter([
            'name' => 'version',
            'type' => 'string',
            'optional' => true,
            'description' => 'Optional parameter. '
                .'If set, the information that will be returned will be specific '
                .'to the given version number.'
        ]);
        $this->addRequestMethod('get');
        $this->addRequestMethod('post');
    }
    /**
     * Sends back JSON response that contains information about the services 
     * at which the manager which is associated with the instance manages.
     * 
     */
    public function processRequest() {
        $this->send('application/json', $this->getManager()->toJSON());
    }
}
