<?php

namespace Caghetti\FFTTApi\Service;

use Caghetti\FFTTApi\Exception\InternalServerErrorException;
use Caghetti\FFTTApi\Exception\InvalidRequestException;
use Caghetti\FFTTApi\Exception\InvalidResponseException;

interface FFTTClientInterface
{
    /**
     * @param array<string, string> $params
     *
     * @return array<mixed>
     *
     * @throws InvalidRequestException
     * @throws InvalidResponseException
     * @throws InternalServerErrorException
     * @param string $path
     * @param string|null $queryParameter
     */
    public function get($path, $params = [], $queryParameter = null): array;
}
