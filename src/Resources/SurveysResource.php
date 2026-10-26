<?php

declare(strict_types=1);

namespace Mosend\Resources;

/** `/organizations/{orgId}/surveys` — encuesta de satisfacción tras el cierre. */
final class SurveysResource extends Resource
{
    /**
     * Configuración actual; si nunca se guardó, los valores por defecto (apagada).
     *
     * @return mixed
     */
    public function getConfig(?string $orgId = null)
    {
        $orgId = $this->requireOrgId($orgId);
        return $this->http->request('GET', "/organizations/{$orgId}/surveys/config");
    }

    /**
     * Cambia la configuración de la encuesta.
     *
     * @param array<string,mixed> $input active?, delayMinutes?, audience? (ALL|HUMAN|BOT), cooldownDays?, questions?, askComment?, commentQuestion?, thanksMessage?, answerTimeoutHours?, phoneNumberIds?, orgId?
     * @return mixed
     */
    public function updateConfig(array $input)
    {
        $orgId = $this->requireOrgId($input['orgId'] ?? null);
        unset($input['orgId']);
        return $this->http->request('PATCH', "/organizations/{$orgId}/surveys/config", ['body' => $input]);
    }

    /**
     * Nota media, tasa de respuesta, desglose por opción y por asesor, comentarios.
     *
     * @param array<string,mixed> $query dias?, desde?, hasta?, orgId?
     * @return mixed
     */
    public function results(array $query = [])
    {
        $orgId = $this->requireOrgId($query['orgId'] ?? null);
        unset($query['orgId']);
        return $this->http->request('GET', "/organizations/{$orgId}/surveys/results", ['query' => $query]);
    }
}
