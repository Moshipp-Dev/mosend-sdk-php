<?php

declare(strict_types=1);

namespace Mosend\Resources;

/**
 * `/organizations/{orgId}/crm` — CRM avanzado (función de plan `crm_avanzado`):
 * embudos, oportunidades, cadencias de seguimiento y automatizaciones.
 * Mismos nombres de método que `DealsResource` del SDK de JS.
 */
final class DealsResource extends Resource
{
    // ── Embudos ──

    /** @return mixed */
    public function listPipelines(?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, '/embudos');
    }

    /** @param array<string,mixed> $input nombre, porDefecto?, automatico?, motivosDePerdida?, conEtapasPorDefecto?, orgId? @return mixed */
    public function createPipeline(array $input)
    {
        return $this->conCuerpo('POST', '/embudos', $input);
    }

    /** @param array<string,mixed> $input nombre?, porDefecto?, automatico?, motivosDePerdida?, orden?, orgId? @return mixed */
    public function updatePipeline(string $id, array $input)
    {
        return $this->conCuerpo('PATCH', "/embudos/{$id}", $input);
    }

    /** @return mixed */
    public function archivePipeline(string $id, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/embudos/{$id}");
    }

    /**
     * @param array<string,mixed> $input nombre, probabilidad?, tipo? ('ABIERTA'|'GANADA'|'PERDIDA'),
     *   clave? (NUEVO|CONTACTADO|REUNION|COTIZADO|NEGOCIACION|null), color? (#RRGGBB), orden?, orgId?
     * @return mixed
     */
    public function createStage(string $pipelineId, array $input)
    {
        return $this->conCuerpo('POST', "/embudos/{$pipelineId}/etapas", $input);
    }

    /** @param string[] $etapaIds orden nuevo de todas las etapas del embudo @return mixed */
    public function reorderStages(string $pipelineId, array $etapaIds, ?string $orgId = null)
    {
        return $this->pedir('PUT', $orgId, "/embudos/{$pipelineId}/etapas/orden", ['body' => ['etapaIds' => $etapaIds]]);
    }

    /** @param array<string,mixed> $input nombre?, probabilidad?, tipo?, clave?, color?, orgId? @return mixed */
    public function updateStage(string $stageId, array $input)
    {
        return $this->conCuerpo('PATCH', "/etapas/{$stageId}", $input);
    }

    /** Si la etapa tiene oportunidades, `$moverA` dice a qué etapa pasan. @return mixed */
    public function deleteStage(string $stageId, ?string $moverA = null, ?string $orgId = null)
    {
        $query = $moverA !== null ? ['moverA' => $moverA] : [];
        return $this->pedir('DELETE', $orgId, "/etapas/{$stageId}", ['query' => $query]);
    }

    // ── Oportunidades ──

    /** @param array<string,mixed> $query embudoId?, duenoUserId?, origen?, q?, diasCerradas?, orgId? @return mixed */
    public function board(array $query = [])
    {
        $orgId = $query['orgId'] ?? null;
        unset($query['orgId']);
        return $this->pedir('GET', $orgId, '/oportunidades/tablero', ['query' => $query]);
    }

    /**
     * @param array<string,mixed> $query embudoId?, estado? ('ABIERTA'|'GANADA'|'PERDIDA'), contactId?, empresaId?,
     *   duenoUserId?, origen?, q?, page?, pageSize?, orgId?
     * @return mixed
     */
    public function list(array $query = [])
    {
        $orgId = $query['orgId'] ?? null;
        unset($query['orgId']);
        return $this->pedir('GET', $orgId, '/oportunidades', ['query' => $query]);
    }

    /** @return mixed */
    public function get(string $id, ?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, "/oportunidades/{$id}");
    }

    /**
     * @param array<string,mixed> $input titulo, embudoId?, etapaId?, contactId?, empresaId?, duenoUserId?, monto?,
     *   moneda? ('COP'|'USD'|'MXN'), cierreEsperadoAt?, conversationId?, orgId?
     * @return mixed
     */
    public function create(array $input)
    {
        return $this->conCuerpo('POST', '/oportunidades', $input);
    }

    /** @param array<string,mixed> $input titulo?, contactId?, empresaId?, duenoUserId?, monto?, moneda?, cierreEsperadoAt?, orgId? @return mixed */
    public function update(string $id, array $input)
    {
        return $this->conCuerpo('PATCH', "/oportunidades/{$id}", $input);
    }

    /** @param array<string,mixed> $input etapaId, motivoDePerdida?, orgId? @return mixed */
    public function move(string $id, array $input)
    {
        return $this->conCuerpo('POST', "/oportunidades/{$id}/mover", $input);
    }

    /** @param array<string,mixed> $input monto?, orgId? @return mixed */
    public function win(string $id, array $input = [])
    {
        return $this->conCuerpo('POST', "/oportunidades/{$id}/ganar", $input);
    }

    /** @return mixed */
    public function lose(string $id, string $motivo, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/oportunidades/{$id}/perder", ['body' => ['motivo' => $motivo]]);
    }

    /** @return mixed */
    public function reopen(string $id, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/oportunidades/{$id}/reabrir");
    }

    /** @return mixed */
    public function delete(string $id, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/oportunidades/{$id}");
    }

    // ── Cadencias ──

    /** @return mixed */
    public function listSequences(?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, '/cadencias');
    }

    /**
     * @param array<string,mixed> $input nombre, phoneNumberId, pasos [{dias, plantilla, idioma?, variables?}],
     *   detenerAlResponder?, activa?, orgId?
     * @return mixed
     */
    public function createSequence(array $input)
    {
        return $this->conCuerpo('POST', '/cadencias', $input);
    }

    /** @param array<string,mixed> $input mismos campos que createSequence, todos opcionales @return mixed */
    public function updateSequence(string $id, array $input)
    {
        return $this->conCuerpo('PATCH', "/cadencias/{$id}", $input);
    }

    /** @return mixed */
    public function deleteSequence(string $id, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/cadencias/{$id}");
    }

    /** @return mixed */
    public function sequenceEnrollments(string $id, ?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, "/cadencias/{$id}/inscritos");
    }

    /** @param array<string,mixed> $input contactIds, oportunidadId?, orgId? @return mixed */
    public function enroll(string $id, array $input)
    {
        return $this->conCuerpo('POST', "/cadencias/{$id}/inscribir", $input);
    }

    /** @return mixed */
    public function stopEnrollment(string $enrollmentId, ?string $orgId = null)
    {
        return $this->pedir('POST', $orgId, "/inscripciones/{$enrollmentId}/detener");
    }

    // ── Automatizaciones ──

    /** @return mixed */
    public function listAutomations(?string $orgId = null)
    {
        return $this->pedir('GET', $orgId, '/automatizaciones');
    }

    /**
     * @param array<string,mixed> $input nombre, disparador (contacto.creado|contacto.etapa|contacto.etiquetado|
     *   oportunidad.creada|oportunidad.etapa|oportunidad.ganada|oportunidad.perdida), condiciones?, acciones, activa?, orgId?
     * @return mixed
     */
    public function createAutomation(array $input)
    {
        return $this->conCuerpo('POST', '/automatizaciones', $input);
    }

    /** @param array<string,mixed> $input mismos campos que createAutomation, todos opcionales @return mixed */
    public function updateAutomation(string $id, array $input)
    {
        return $this->conCuerpo('PATCH', "/automatizaciones/{$id}", $input);
    }

    /** @return mixed */
    public function deleteAutomation(string $id, ?string $orgId = null)
    {
        return $this->pedir('DELETE', $orgId, "/automatizaciones/{$id}");
    }

    // ── Reportes ──

    /** Conversión por etapa, tasa de cierre y resultados por asesor, origen y anuncio. @param array<string,mixed> $query embudoId?, desde?, hasta?, orgId? @return mixed */
    public function report(array $query = [])
    {
        $orgId = $query['orgId'] ?? null;
        unset($query['orgId']);
        return $this->pedir('GET', $orgId, '/reportes/embudo', ['query' => $query]);
    }

    /**
     * @param array<string,mixed> $input cuerpo + orgId?
     * @return mixed
     */
    private function conCuerpo(string $method, string $ruta, array $input)
    {
        $orgId = $input['orgId'] ?? null;
        unset($input['orgId']);
        // Sin campos no se manda cuerpo: `[]` llegaría como arreglo JSON, no como objeto.
        return $this->pedir($method, $orgId, $ruta, $input === [] ? [] : ['body' => $input]);
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
