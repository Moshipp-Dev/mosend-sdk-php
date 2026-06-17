<?php

declare(strict_types=1);

namespace Mosend\Resources;

use Mosend\Exception\MosendValidationException;
use Mosend\Http\HttpClient;

/** Base de todos los resource clients: guarda el HTTP client y el orgId default. */
abstract class Resource
{
    /** @var HttpClient */
    protected $http;
    /** @var string|null */
    protected $defaultOrgId;

    public function __construct(HttpClient $http, ?string $defaultOrgId = null)
    {
        $this->http = $http;
        $this->defaultOrgId = $defaultOrgId;
    }

    /** Resuelve el orgId del parámetro o del default del cliente. */
    protected function requireOrgId(?string $orgId): string
    {
        $id = $orgId ?? $this->defaultOrgId;
        if ($id === null || $id === '') {
            throw new MosendValidationException(
                'orgId es requerido: pasalo por llamada o seteá orgId al construir MosendClient.'
            );
        }
        return $id;
    }
}
