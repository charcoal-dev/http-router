<?php
/*
 * This file is a part of "charcoal-dev/http-router" package.
 * https://github.com/charcoal-dev/http-router
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/http-router/blob/main/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Http\Router\Controllers\Response;

use Charcoal\Http\Commons\WritableHeaders;
use Charcoal\Http\Router\Exception\ResponseDispatchedException;

/**
 * Class AbstractControllerResponse
 * @package Charcoal\Http\Router\Controllers\Response
 */
abstract class AbstractControllerResponse
{
    public readonly int $createdOn;

    /**
     * @param int $statusCode
     * @param WritableHeaders $headers
     */
    public function __construct(
        protected int          $statusCode = 200,
        public WritableHeaders $headers = new WritableHeaders()
    )
    {
        $this->createdOn = time();
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setHeader(string $key, string $value): static
    {
        $this->headers->set($key, $value);
        return $this;
    }

    /**
     * @return void
     */
    abstract protected function beforeSendResponseHook(): void;

    /**
     * @return void
     */
    abstract protected function sendBody(): void;

    /**
     * @return never
     * @throws ResponseDispatchedException
     */
    public function send(): never
    {
        $this->beforeSendResponseHook();

        // HTTP Response Code
        http_response_code($this->statusCode);

        // Headers
        if ($this->headers->count()) {
            foreach ($this->headers->toArray() as $key => $val) {
                header(sprintf('%s: %s', $key, $val));
            }
        }

        $this->sendBody();

        throw new ResponseDispatchedException();
    }
}