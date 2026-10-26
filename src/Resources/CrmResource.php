<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/crm` — ficha del cliente (todos los planes): campos
 * definidos, dueño, empresa, etapa, línea de tiempo, empresas y duplicados.
 * Mismos nombres de método que `CrmResource` del SDK de JS.
 */
final class CrmResource extends Resource
{
    // ── Configuración ──

    /** @return mixed */
    public function getSettings(?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, '/configuracion');
    }

    /**
     * @param array<string,mixed> $input diasParaInactivo?, diasSinActividad?,
     *   asignacionDeDueno? ('NINGUNO'|'ROTATIVO'|'PRIMERO_QUE_RESPONDE'), asignarEntre? (userIds), orgId?
     * @return mixed
     */
    public function updateSettings(array $input)
    {
        return $this->conCuerpo('PUT', '/configuracion', $input);
    }

    // ── Campos del cliente ──

    /** @return mixed */
    public function listFields(?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, '/campos');
    }

    /** Atributos sueltos que ya usan los contactos y aún no son un campo. @return mixed */
    public function suggestedFields(?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, '/campos/sugeridos');
    }

    /**
     * @param array<string,mixed> $input etiqueta, clave?, tipo?, opciones?, obligatorio?, enTabla?, enFicha?, orden?, orgId?
     * @return mixed
     */
    public function createField(array $input)
    {
        return $this->conCuerpo('POST', '/campos', $input);
    }

    /** @param array<string,mixed> $input etiqueta?, tipo?, opciones?, obligatorio?, enTabla?, enFicha?, orden?, orgId? @return mixed */
    public function updateField(string $id, array $input)
    {
        return $this->conCuerpo('PATCH', "/campos/{$id}", $input);
    }

    /** Archiva el campo; los valores de los contactos se conservan. @return mixed */
    public function archiveField(string $id, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/campos/{$id}");
    }

    /** Junta claves repetidas («Numero», «Número» → «telefono»). @param array<string,mixed> $input de (string[]), a, orgId? @return mixed */
    public function mergeFieldKeys(array $input)
    {
        return $this->conCuerpo('POST', '/campos/unificar', $input);
    }

    // ── Clientes ──

    /**
     * @param array<string,mixed> $input duenoUserId? (null = sin dueño), empresaId? (null = sin empresa),
     *   etapa? (PROSPECTO|CLIENTE|RECURRENTE|INACTIVO), campos? (se mezclan; null o '' borra la clave), orgId?
     * @return mixed
     */
    public function updateClient(string $contactId, array $input)
    {
        return $this->conCuerpo('PATCH', "/clientes/{$contactId}", $input);
    }

    /** @param array<string,mixed> $input contactIds, duenoUserId?, empresaId?, etapa?, orgId? @return mixed */
    public function bulkUpdateClients(array $input)
    {
        return $this->conCuerpo('POST', '/clientes/masivo', $input);
    }

    /** @return mixed */
    public function getProfile(string $contactId, ?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, "/clientes/{$contactId}/ficha");
    }

    /** @param array<string,mixed> $query antes?, limite?, orgId? @return mixed */
    public function getTimeline(string $contactId, array $query = [])
    {
        $orgId = $query['orgId'] ?? null;
        unset($query['orgId']);
        return $this->pedir('GET', $orgId, "/clientes/{$contactId}/linea-de-tiempo", ['query' => $query]);
    }

    // ── Empresas ──

    /** @param array<string,mixed> $query q?, duenoUserId?, page?, pageSize?, orgId? @return mixed */
    public function listCompanies(array $query = [])
    {
        $orgId = $query['orgId'] ?? null;
        unset($query['orgId']);
        return $this->pedir('GET', $orgId, '/empresas', ['query' => $query]);
    }

    /** @return mixed */
    public function getCompany(string $id, ?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, "/empresas/{$id}");
    }

    /**
     * @param array<string,mixed> $input nombre, nit?, sector?, ciudad?, sitioWeb?, telefono?, correo?,
     *   duenoUserId?, notas?, atributos?, orgId?
     * @return mixed
     */
    public function createCompany(array $input)
    {
        return $this->conCuerpo('POST', '/empresas', $input);
    }

    /** @param array<string,mixed> $input mismos campos que createCompany, todos opcionales @return mixed */
    public function updateCompany(string $id, array $input)
    {
        return $this->conCuerpo('PATCH', "/empresas/{$id}", $input);
    }

    /** @return mixed */
    public function deleteCompany(string $id, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/empresas/{$id}");
    }

    /** Datos del NIT en la DIAN para prellenar (y si ya existe la empresa). @return mixed */
    public function lookupNit(string $nit, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, '/empresas/consultar-nit', ['body' => ['nit' => $nit]]);
    }

    /** @param string[] $contactIds @return mixed */
    public function addCompanyContacts(string $id, array $contactIds, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/empresas/{$id}/contactos", ['body' => ['contactIds' => $contactIds]]);
    }

    /** @param string[] $contactIds @return mixed */
    public function removeCompanyContacts(string $id, array $contactIds, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/empresas/{$id}/contactos/quitar", ['body' => ['contactIds' => $contactIds]]);
    }

    // ── Duplicados ──

    /** @return mixed */
    public function listDuplicates(?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, '/duplicados');
    }

    /** Pasa todo del duplicado al principal y borra el duplicado. @return mixed */
    public function mergeContacts(string $principalId, string $duplicadoId, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, '/duplicados/fusionar', [
            'body' => ['principalId' => $principalId, 'duplicadoId' => $duplicadoId],
        ]);
    }

    /** Etapas, recompra, gasto promedio, los que más compran y nuevos por mes. @return mixed */
    public function customersReport(?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, '/reportes/clientes');
    }

    /**
     * @param array<string,mixed> $input cuerpo + orgId?
     * @return mixed
     */
    private function conCuerpo(string $method, string $ruta, array $input)
    {
        $orgId = $input['orgId'] ?? null;
        unset($input['orgId']);
        return $this->pedir($method, $orgId, $ruta, ['body' => $input]);
    }

    /**
     * @param array<string,mixed> $opts
     * @return mixed
     */
    private function pedir(string $method, ?string $orgId, string $ruta, array $opts = [])
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request($method, "/organizations/{$orgId}/crm{$ruta}", $opts);
    }
}
