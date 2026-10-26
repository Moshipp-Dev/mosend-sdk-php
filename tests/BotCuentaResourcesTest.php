<?php

declare(strict_types=1);

namespace Mosend\Tests;

use Mosend\Resources\AccountDeletionResource;
use Mosend\Resources\AdCampaignsResource;
use Mosend\Resources\AiCreditsResource;
use Mosend\Resources\AttendanceResource;
use Mosend\Resources\AuthResource;
use Mosend\Resources\BillingResource;
use Mosend\Resources\BotAgentsResource;
use Mosend\Resources\BotImagesResource;
use Mosend\Resources\BotServicesResource;
use Mosend\Resources\BotSummaryResource;
use Mosend\Resources\ConnectionTestResource;
use Mosend\Resources\ConnectorsResource;
use Mosend\Resources\DayOffResource;
use Mosend\Resources\HealthResource;
use Mosend\Resources\InvitationsResource;
use Mosend\Resources\KnowledgeResource;
use Mosend\Resources\MembershipsResource;
use Mosend\Resources\MessagingLimitResource;
use Mosend\Resources\OAuthResource;
use Mosend\Resources\OrganizationsResource;
use Mosend\Resources\PushResource;
use Mosend\Resources\ReportsResource;
use Mosend\Resources\ScheduleResource;
use Mosend\Resources\ShiftRemindersResource;
use Mosend\Resources\SurveysResource;
use Mosend\Resources\UsersResource;
use Mosend\Resources\WabaUsageResource;
use Mosend\Resources\WebhooksOutboundResource;
use Mosend\Tests\Support\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

/**
 * Agentes del bot, conectores, cuenta y equipo: método, ruta y cuerpo de cada
 * endpoint contra el backend.
 */
final class BotCuentaResourcesTest extends TestCase
{
    private const ORG = 'org-1';
    private const O = '/organizations/org-1';

    /**
     * @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null,4:array<string,string>|null}>
     */
    public static function casos(): array
    {
        $o = self::O;
        $org = self::ORG;
        return [
            // account-deletion
            'accountDeletion.status' => [function ($r) use ($org) { return (new AccountDeletionResource($r, $org))->status(); }, 'GET', "{$o}/deletion-request", null, null],
            'accountDeletion.request' => [function ($r) use ($org) { return (new AccountDeletionResource($r, $org))->request(['confirmOrgName' => 'Acme']); }, 'POST', "{$o}/deletion-request", ['confirmOrgName' => 'Acme'], null],
            'accountDeletion.cancel' => [function ($r) use ($org) { return (new AccountDeletionResource($r, $org))->cancel(); }, 'DELETE', "{$o}/deletion-request", null, null],
            // ad-campaigns
            'adCampaigns.list' => [function ($r) use ($org) { return (new AdCampaignsResource($r, $org))->list(); }, 'GET', "{$o}/ad-campaigns", null, null],
            'adCampaigns.update' => [function ($r) use ($org) { return (new AdCampaignsResource($r, $org))->update('a1', ['activo' => true]); }, 'PATCH', "{$o}/ad-campaigns/a1", ['activo' => true], null],
            'adCampaigns.porSeguir' => [function ($r) use ($org) { return (new AdCampaignsResource($r, $org))->porSeguir('a1'); }, 'GET', "{$o}/ad-campaigns/a1/por-seguir", null, null],
            // ai-credits
            'aiCredits.publicPolicy' => [function ($r) { return (new AiCreditsResource($r))->publicPolicy(); }, 'GET', '/ai-credits/public-policy', null, null],
            'aiCredits.getPolicy' => [function ($r) { return (new AiCreditsResource($r))->getPolicy(); }, 'GET', '/admin/ai-credit-policy', null, null],
            'aiCredits.updatePolicy' => [function ($r) { return (new AiCreditsResource($r))->updatePolicy(['signupCreditUsd' => 1]); }, 'PATCH', '/admin/ai-credit-policy', ['signupCreditUsd' => 1], null],
            'aiCredits.adjust' => [function ($r) { return (new AiCreditsResource($r))->adjust('o2', ['amountUsd' => 5, 'note' => 'bono']); }, 'POST', '/admin/organizations/o2/ai-credits/adjust', ['amountUsd' => 5, 'note' => 'bono'], null],
            // attendance
            'attendance.periodPresets' => [function ($r) use ($org) { return (new AttendanceResource($r, $org))->periodPresets(); }, 'GET', "{$o}/attendance/period-presets", null, null],
            'attendance.listWorkModes' => [function ($r) use ($org) { return (new AttendanceResource($r, $org))->listWorkModes(); }, 'GET', "{$o}/attendance/work-modes", null, null],
            'attendance.setWorkMode' => [function ($r) use ($org) { return (new AttendanceResource($r, $org))->setWorkMode('u1', ['workMode' => 'HOURLY']); }, 'PATCH', "{$o}/attendance/work-modes/u1", ['workMode' => 'HOURLY'], null],
            // auth
            'auth.listSessions' => [function ($r) { return (new AuthResource($r))->listSessions(); }, 'GET', '/auth/me/sessions', null, null],
            'auth.closeOtherSessions' => [function ($r) { return (new AuthResource($r))->closeOtherSessions(); }, 'DELETE', '/auth/me/sessions', null, null],
            'auth.closeSession' => [function ($r) { return (new AuthResource($r))->closeSession('s1'); }, 'DELETE', '/auth/me/sessions/s1', null, null],
            'auth.signupConfig' => [function ($r) { return (new AuthResource($r))->signupConfig(); }, 'GET', '/auth/signup/config', null, null],
            'auth.signupRequestCode' => [function ($r) { return (new AuthResource($r))->signupRequestCode(['email' => 'a@b.co', 'password' => 'p', 'name' => 'A', 'phone' => '57300']); }, 'POST', '/auth/signup/request-code', ['email' => 'a@b.co', 'password' => 'p', 'name' => 'A', 'phone' => '57300'], null],
            'auth.signupResendCode' => [function ($r) { return (new AuthResource($r))->signupResendCode(['email' => 'a@b.co', 'phone' => '57300']); }, 'POST', '/auth/signup/resend-code', ['email' => 'a@b.co', 'phone' => '57300'], null],
            'auth.signupVerifyCode' => [function ($r) { return (new AuthResource($r))->signupVerifyCode(['email' => 'a@b.co', 'phone' => '57300', 'code' => '1']); }, 'POST', '/auth/signup/verify-code', ['email' => 'a@b.co', 'phone' => '57300', 'code' => '1'], null],
            // billing
            'billing.setInterval' => [function ($r) use ($org) { return (new BillingResource($r, $org))->setInterval(['interval' => 'YEARLY']); }, 'PATCH', "{$o}/billing/interval", ['interval' => 'YEARLY'], null],
            // bot-agents + configurador + probador
            'botAgents.list' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->list(['phoneNumberId' => 'p1']); }, 'GET', "{$o}/bot/agents", null, ['phoneNumberId' => 'p1']],
            'botAgents.create' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->create(['name' => 'Ventas']); }, 'POST', "{$o}/bot/agents", ['name' => 'Ventas'], null],
            'botAgents.capabilities' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->capabilities(); }, 'GET', "{$o}/bot/agents/capabilities", null, null],
            'botAgents.modelos' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->modelos(); }, 'GET', "{$o}/bot/agents/modelos", null, null],
            'botAgents.stats' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->stats(['dias' => 7]); }, 'GET', "{$o}/bot/agents/stats", null, ['dias' => '7']],
            'botAgents.retrieve' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->retrieve('g1'); }, 'GET', "{$o}/bot/agents/g1", null, null],
            'botAgents.update' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->update('g1', ['enabled' => false]); }, 'PATCH', "{$o}/bot/agents/g1", ['enabled' => false], null],
            'botAgents.delete' => [function ($r) use ($org) { (new BotAgentsResource($r, $org))->delete('g1'); return null; }, 'DELETE', "{$o}/bot/agents/g1", null, null],
            'botAgents.configurador' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->configurador('g1', ['mensajes' => [['rol' => 'user', 'texto' => 'hola']]]); }, 'POST', "{$o}/bot/agents/g1/configurador", ['mensajes' => [['rol' => 'user', 'texto' => 'hola']]], null],
            'botAgents.probador' => [function ($r) use ($org) { return (new BotAgentsResource($r, $org))->probador('g1', ['mensajes' => [['rol' => 'user', 'texto' => 'precio?']]]); }, 'POST', "{$o}/bot/agents/g1/probador", ['mensajes' => [['rol' => 'user', 'texto' => 'precio?']]], null],
            // bot-images
            'botImages.list' => [function ($r) use ($org) { return (new BotImagesResource($r, $org))->list(); }, 'GET', "{$o}/bot/images", null, null],
            'botImages.update' => [function ($r) use ($org) { return (new BotImagesResource($r, $org))->update('i1', ['caption' => 'Promo']); }, 'PATCH', "{$o}/bot/images/i1", ['caption' => 'Promo'], null],
            'botImages.delete' => [function ($r) use ($org) { return (new BotImagesResource($r, $org))->delete('i1', true); }, 'DELETE', "{$o}/bot/images/i1", null, ['forzar' => 'true']],
            // servicios
            'botServices.list' => [function ($r) use ($org) { return (new BotServicesResource($r, $org))->list(); }, 'GET', "{$o}/bot/servicios", null, null],
            'botServices.create' => [function ($r) use ($org) { return (new BotServicesResource($r, $org))->create(['nombre' => 'Hosting', 'precios' => [['periodo' => 'anual', 'valorCents' => 100]]]); }, 'POST', "{$o}/bot/servicios", ['nombre' => 'Hosting', 'precios' => [['periodo' => 'anual', 'valorCents' => 100]]], null],
            'botServices.update' => [function ($r) use ($org) { return (new BotServicesResource($r, $org))->update('s1', ['activo' => false]); }, 'PATCH', "{$o}/bot/servicios/s1", ['activo' => false], null],
            'botServices.delete' => [function ($r) use ($org) { (new BotServicesResource($r, $org))->delete('s1'); return null; }, 'DELETE', "{$o}/bot/servicios/s1", null, null],
            // resumen + salud
            'botSummary.summary' => [function ($r) use ($org) { return (new BotSummaryResource($r, $org))->summary(['dias' => 7]); }, 'GET', "{$o}/bot/resumen", null, ['dias' => '7']],
            'botSummary.daySpend' => [function ($r) use ($org) { return (new BotSummaryResource($r, $org))->daySpend(['fecha' => '2026-10-01']); }, 'GET', "{$o}/bot/resumen/dia", null, ['fecha' => '2026-10-01']],
            'botSummary.health' => [function ($r) use ($org) { return (new BotSummaryResource($r, $org))->health(['dias' => 3]); }, 'GET', "{$o}/bot/salud", null, ['dias' => '3']],
            // conectores
            'connectors.list' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->list(); }, 'GET', "{$o}/conectores", null, null],
            'connectors.create' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->create(['type' => 'WHMCS', 'name' => 'W', 'baseUrl' => 'https://w.co']); }, 'POST', "{$o}/conectores", ['type' => 'WHMCS', 'name' => 'W', 'baseUrl' => 'https://w.co'], null],
            'connectors.catalog' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->catalog(); }, 'GET', "{$o}/conectores/catalogo", null, null],
            'connectors.update' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->update('k1', ['enabled' => false]); }, 'PATCH', "{$o}/conectores/k1", ['enabled' => false], null],
            'connectors.delete' => [function ($r) use ($org) { (new ConnectorsResource($r, $org))->delete('k1'); return null; }, 'DELETE', "{$o}/conectores/k1", null, null],
            'connectors.test' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->test('k1'); }, 'POST', "{$o}/conectores/k1/probar", null, null],
            'connectors.execute' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->execute('k1', ['actionKey' => 'whmcs.cliente']); }, 'POST', "{$o}/conectores/k1/ejecutar", ['actionKey' => 'whmcs.cliente'], null],
            'connectors.listApprovals' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->listApprovals(); }, 'GET', "{$o}/conectores/aprobaciones", null, null],
            'connectors.approve' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->approve('x1'); }, 'POST', "{$o}/conectores/aprobaciones/x1/aprobar", null, null],
            'connectors.reject' => [function ($r) use ($org) { return (new ConnectorsResource($r, $org))->reject('x1'); }, 'POST', "{$o}/conectores/aprobaciones/x1/rechazar", null, null],
            // day-off
            'dayOff.listHolidays' => [function ($r) use ($org) { return (new DayOffResource($r, $org))->listHolidays(['year' => 2026]); }, 'GET', "{$o}/attendance/holidays", null, ['year' => '2026']],
            'dayOff.upsertHolidays' => [function ($r) use ($org) { return (new DayOffResource($r, $org))->upsertHolidays(['items' => [['date' => '2026-12-25', 'name' => 'Navidad']]]); }, 'POST', "{$o}/attendance/holidays", ['items' => [['date' => '2026-12-25', 'name' => 'Navidad']]], null],
            'dayOff.seedHolidays' => [function ($r) use ($org) { return (new DayOffResource($r, $org))->seedHolidays(['year' => 2027, 'country' => 'CO']); }, 'POST', "{$o}/attendance/holidays/seed", null, ['year' => '2027', 'country' => 'CO']],
            'dayOff.deleteHoliday' => [function ($r) use ($org) { (new DayOffResource($r, $org))->deleteHoliday('h1'); return null; }, 'DELETE', "{$o}/attendance/holidays/h1", null, null],
            'dayOff.listLeaves' => [function ($r) use ($org) { return (new DayOffResource($r, $org))->listLeaves(['from' => '2026-10-01', 'to' => '2026-10-31']); }, 'GET', "{$o}/attendance/leaves", null, ['from' => '2026-10-01', 'to' => '2026-10-31']],
            'dayOff.createLeave' => [function ($r) use ($org) { return (new DayOffResource($r, $org))->createLeave(['agentUserId' => 'u1', 'type' => 'VACATION', 'startDate' => '2026-11-01', 'endDate' => '2026-11-05']); }, 'POST', "{$o}/attendance/leaves", ['agentUserId' => 'u1', 'type' => 'VACATION', 'startDate' => '2026-11-01', 'endDate' => '2026-11-05'], null],
            'dayOff.deleteLeave' => [function ($r) use ($org) { (new DayOffResource($r, $org))->deleteLeave('l1'); return null; }, 'DELETE', "{$o}/attendance/leaves/l1", null, null],
            // health
            'health.status' => [function ($r) { return (new HealthResource($r))->status(); }, 'GET', '/health', null, null],
            // invitations
            'invitations.lookup' => [function ($r) { return (new InvitationsResource($r))->lookup('tok'); }, 'GET', '/invitations/lookup', null, ['token' => 'tok']],
            'invitations.activateLookup' => [function ($r) { return (new InvitationsResource($r))->activateLookup('tok'); }, 'GET', '/invitations/activate-lookup', null, ['token' => 'tok']],
            'invitations.acceptSignup' => [function ($r) { return (new InvitationsResource($r))->acceptSignup(['token' => 't', 'name' => 'A', 'password' => 'p']); }, 'POST', '/invitations/accept-signup', ['token' => 't', 'name' => 'A', 'password' => 'p'], null],
            'invitations.activate' => [function ($r) { return (new InvitationsResource($r))->activate(['token' => 't', 'password' => 'p']); }, 'POST', '/invitations/activate', ['token' => 't', 'password' => 'p'], null],
            'invitations.bulk' => [function ($r) use ($org) { return (new InvitationsResource($r, $org))->bulk(['emails' => ['a@b.co'], 'roleId' => 'r1']); }, 'POST', "{$o}/invitations/bulk", ['emails' => ['a@b.co'], 'roleId' => 'r1'], null],
            'invitations.preregister' => [function ($r) use ($org) { return (new InvitationsResource($r, $org))->preregister(['members' => [['email' => 'a@b.co']], 'roleId' => 'r1']); }, 'POST', "{$o}/invitations/preregister", ['members' => [['email' => 'a@b.co']], 'roleId' => 'r1'], null],
            'invitations.reactivate' => [function ($r) use ($org) { return (new InvitationsResource($r, $org))->reactivate('m1', ['notify' => true]); }, 'POST', "{$o}/invitations/activation/m1", ['notify' => true], null],
            'invitations.link' => [function ($r) use ($org) { return (new InvitationsResource($r, $org))->link('i1'); }, 'POST', "{$o}/invitations/i1/link", null, null],
            // knowledge
            'knowledge.insights' => [function ($r) use ($org) { return (new KnowledgeResource($r, $org))->insights(['dias' => 30]); }, 'GET', "{$o}/bot/knowledge/insights", null, ['dias' => '30']],
            'knowledge.search' => [function ($r) use ($org) { return (new KnowledgeResource($r, $org))->search(['query' => 'precio', 'topK' => 3]); }, 'POST', "{$o}/bot/knowledge/search", ['query' => 'precio', 'topK' => 3], null],
            'knowledge.createNote' => [function ($r) use ($org) { return (new KnowledgeResource($r, $org))->createNote(['title' => 'T', 'content' => 'C']); }, 'POST', "{$o}/bot/knowledge/notes", ['title' => 'T', 'content' => 'C'], null],
            'knowledge.updateNote' => [function ($r) use ($org) { return (new KnowledgeResource($r, $org))->updateNote('d1', ['content' => 'C2']); }, 'PATCH', "{$o}/bot/knowledge/notes/d1", ['content' => 'C2'], null],
            'knowledge.createFromUrl' => [function ($r) use ($org) { return (new KnowledgeResource($r, $org))->createFromUrl(['url' => 'https://x.co']); }, 'POST', "{$o}/bot/knowledge/url", ['url' => 'https://x.co'], null],
            'knowledge.updateAgents' => [function ($r) use ($org) { return (new KnowledgeResource($r, $org))->updateAgents('d1', ['agentIds' => ['g1'], 'compartido' => false]); }, 'PATCH', "{$o}/bot/knowledge/d1/agentes", ['agentIds' => ['g1'], 'compartido' => false], null],
            // limite-de-mensajes
            'messagingLimit.retrieve' => [function ($r) use ($org) { return (new MessagingLimitResource($r, $org))->retrieve(['phoneNumberId' => 'p1']); }, 'GET', "{$o}/messaging-limit", null, ['phoneNumberId' => 'p1']],
            // memberships
            'memberships.updateMyPreferences' => [function ($r) use ($org) { return (new MembershipsResource($r, $org))->updateMyPreferences(['contactPanelDefaultTab' => 'notes']); }, 'PATCH', "{$o}/memberships/me/preferences", ['contactPanelDefaultTab' => 'notes'], null],
            'memberships.setSuspension' => [function ($r) use ($org) { return (new MembershipsResource($r, $org))->setSuspension('m1', ['suspended' => true]); }, 'PATCH', "{$o}/memberships/m1/suspension", ['suspended' => true], null],
            // oauth
            'oauth.getRequest' => [function ($r) { return (new OAuthResource($r))->getRequest('q1'); }, 'GET', '/oauth/requests/q1', null, null],
            'oauth.decide' => [function ($r) { return (new OAuthResource($r))->decide('q1', ['approve' => true, 'organizationId' => 'org-1']); }, 'POST', '/oauth/requests/q1/decision', ['approve' => true, 'organizationId' => 'org-1'], null],
            'oauth.userinfo' => [function ($r) { return (new OAuthResource($r))->userinfo(); }, 'GET', '/oauth/userinfo', null, null],
            'oauth.listGrants' => [function ($r) use ($org) { return (new OAuthResource($r, $org))->listGrants(); }, 'GET', "{$o}/oauth/grants", null, null],
            'oauth.revokeGrant' => [function ($r) use ($org) { (new OAuthResource($r, $org))->revokeGrant('gr1'); return null; }, 'DELETE', "{$o}/oauth/grants/gr1", null, null],
            'oauth.authorize' => [function ($r) { return (new OAuthResource($r))->authorize(['response_type' => 'code', 'client_id' => 'c', 'state' => 's']); }, 'GET', '/oauth/authorize', null, ['response_type' => 'code', 'client_id' => 'c', 'state' => 's']],
            'oauth.token' => [function ($r) { return (new OAuthResource($r))->token(['grant_type' => 'refresh_token', 'refresh_token' => 'mor_x']); }, 'POST', '/oauth/token', ['grant_type' => 'refresh_token', 'refresh_token' => 'mor_x'], null],
            'oauth.revoke' => [function ($r) { return (new OAuthResource($r))->revoke(['token' => 'mot_x']); }, 'POST', '/oauth/revoke', ['token' => 'mot_x'], null],
            'oauth.introspect' => [function ($r) { return (new OAuthResource($r))->introspect(['token' => 'mot_x']); }, 'POST', '/oauth/introspect', ['token' => 'mot_x'], null],
            'oauth.metadata' => [function ($r) { return (new OAuthResource($r))->metadata(); }, 'GET', '/.well-known/oauth-authorization-server', null, null],
            'oauth.metadataAlias' => [function ($r) { return (new OAuthResource($r))->metadataAlias(); }, 'GET', '/oauth/metadata', null, null],
            // organizations
            'organizations.quota' => [function ($r) { return (new OrganizationsResource($r))->quota(); }, 'GET', '/organizations/quota', null, null],
            'organizations.suspension' => [function ($r) { return (new OrganizationsResource($r))->suspension('o9'); }, 'GET', '/organizations/o9/suspension', null, null],
            'organizations.billingGroup' => [function ($r) use ($org) { return (new OrganizationsResource($r, $org))->billingGroup(); }, 'GET', "{$o}/billing-group", null, null],
            'organizations.linkBillingGroup' => [function ($r) use ($org) { return (new OrganizationsResource($r, $org))->linkBillingGroup(['parentOrgId' => 'p0']); }, 'POST', "{$o}/billing-group/link", ['parentOrgId' => 'p0'], null],
            'organizations.unlinkBillingGroup' => [function ($r) use ($org) { return (new OrganizationsResource($r, $org))->unlinkBillingGroup(); }, 'POST', "{$o}/billing-group/unlink", null, null],
            'organizations.setAiLimits' => [function ($r) use ($org) { return (new OrganizationsResource($r, $org))->setAiLimits('o2', ['dailyUsd' => 1]); }, 'PATCH', "{$o}/billing-group/o2/ai-limits", ['dailyUsd' => 1], null],
            // prueba-de-conexion
            'connectionTest.start' => [function ($r) use ($org) { return (new ConnectionTestResource($r, $org))->start('p1'); }, 'POST', "{$o}/phone-numbers/p1/prueba-de-conexion", null, null],
            'connectionTest.status' => [function ($r) use ($org) { return (new ConnectionTestResource($r, $org))->status('p1', 'ABC123'); }, 'GET', "{$o}/phone-numbers/p1/prueba-de-conexion/ABC123", null, null],
            // push
            'push.registerDevice' => [function ($r) { return (new PushResource($r))->registerDevice(['token' => 'fcm', 'platform' => 'ANDROID']); }, 'POST', '/push/device', ['token' => 'fcm', 'platform' => 'ANDROID'], null],
            'push.unregisterDevice' => [function ($r) { return (new PushResource($r))->unregisterDevice(['token' => 'fcm']); }, 'DELETE', '/push/device', ['token' => 'fcm'], null],
            // reports
            'reports.channelsSummary' => [function ($r) use ($org) { return (new ReportsResource($r, $org))->channelsSummary(); }, 'GET', "{$o}/reports/channels-summary", null, null],
            'reports.portada' => [function ($r) use ($org) { return (new ReportsResource($r, $org))->portada(['dias' => 7]); }, 'GET', "{$o}/reports/portada", null, ['dias' => '7']],
            'reports.teamSeries' => [function ($r) use ($org) { return (new ReportsResource($r, $org))->teamSeries(['granularity' => 'day', 'channel' => 'WEB']); }, 'GET', "{$o}/reports/team/series", null, ['granularity' => 'day', 'channel' => 'WEB']],
            'reports.ventas' => [function ($r) use ($org) { return (new ReportsResource($r, $org))->ventas(['agrupar' => 'semana']); }, 'GET', "{$o}/reports/ventas", null, ['agrupar' => 'semana']],
            // schedule
            'schedule.myUpcoming' => [function ($r) use ($org) { return (new ScheduleResource($r, $org))->myUpcoming(['semana' => 1]); }, 'GET', "{$o}/attendance/my-schedule/proximos", null, ['semana' => '1']],
            // shift-reminders
            'shiftReminders.test' => [function ($r) use ($org) { return (new ShiftRemindersResource($r, $org))->test(); }, 'POST', "{$o}/shift-reminders/test", null, null],
            // surveys
            'surveys.getConfig' => [function ($r) use ($org) { return (new SurveysResource($r, $org))->getConfig(); }, 'GET', "{$o}/surveys/config", null, null],
            'surveys.updateConfig' => [function ($r) use ($org) { return (new SurveysResource($r, $org))->updateConfig(['active' => true, 'audience' => 'HUMAN']); }, 'PATCH', "{$o}/surveys/config", ['active' => true, 'audience' => 'HUMAN'], null],
            'surveys.results' => [function ($r) use ($org) { return (new SurveysResource($r, $org))->results(['dias' => 30]); }, 'GET', "{$o}/surveys/results", null, ['dias' => '30']],
            // users
            'users.requestPhoneCode' => [function ($r) { return (new UsersResource($r))->requestPhoneCode(); }, 'POST', '/users/me/phone/request-code', null, null],
            'users.verifyPhoneCode' => [function ($r) { return (new UsersResource($r))->verifyPhoneCode(['code' => '123456']); }, 'POST', '/users/me/phone/verify-code', ['code' => '123456'], null],
            // uso-por-waba
            'wabaUsage.list' => [function ($r) { return (new WabaUsageResource($r))->list(['days' => 30]); }, 'GET', '/admin/wabas/uso', null, ['days' => '30']],
            'wabaUsage.detail' => [function ($r) { return (new WabaUsageResource($r))->detail('w1', ['desde' => '2026-10-01']); }, 'GET', '/admin/wabas/w1/detalle', null, ['desde' => '2026-10-01']],
            // webhooks-outbound
            'webhooksOutbound.sendTest' => [function ($r) use ($org) { return (new WebhooksOutboundResource($r, $org))->sendTest('h1'); }, 'POST', "{$o}/webhooks-outbound/h1/test", null, null],
        ];
    }

    /**
     * @dataProvider casos
     * @param array<string,mixed>|null $body
     * @param array<string,string>|null $query
     */
    public function testMetodoRutaYCuerpo(callable $call, string $method, string $path, ?array $body, ?array $query): void
    {
        $r = new RecordingHttpClient();
        $call($r);
        $last = $r->lastCall();
        self::assertSame($method, $last['method']);
        self::assertSame($path, parse_url($last['url'], PHP_URL_PATH));
        if ($body !== null) {
            self::assertSame($body, $r->lastBody());
        }
        if ($query !== null) {
            parse_str((string) parse_url($last['url'], PHP_URL_QUERY), $q);
            self::assertSame($query, $q);
        }
    }

    public function testBotImagesUploadEsMultipart(): void
    {
        $r = new RecordingHttpClient();
        (new BotImagesResource($r, self::ORG))->upload(['file' => __FILE__, 'label' => 'Promo', 'caption' => 'Hoy']);
        $last = $r->lastCall();
        self::assertSame('POST', $last['method']);
        self::assertSame(self::O . '/bot/images', parse_url($last['url'], PHP_URL_PATH));
        self::assertTrue($last['multipart']);
        self::assertSame('Promo', $last['opts']['multipart']['label']);
        self::assertSame('Hoy', $last['opts']['multipart']['caption']);
    }

    public function testEndpointsPublicosNoMandanCredenciales(): void
    {
        $r = new RecordingHttpClient();
        (new OAuthResource($r))->token(['grant_type' => 'authorization_code', 'code' => 'c']);
        self::assertTrue($r->lastCall()['opts']['skipAuth']);
        (new AuthResource($r))->signupVerifyCode(['email' => 'a@b.co', 'phone' => '1', 'code' => '1']);
        self::assertTrue($r->lastCall()['opts']['skipAuth']);
        (new InvitationsResource($r))->lookup('tok');
        self::assertTrue($r->lastCall()['opts']['skipAuth']);
    }

    public function testOrgIdPorLlamadaNoViajaEnElCuerpo(): void
    {
        $r = new RecordingHttpClient();
        (new DayOffResource($r, self::ORG))->createLeave(['orgId' => 'otra', 'agentUserId' => 'u', 'type' => 'SICK', 'startDate' => '2026-01-01', 'endDate' => '2026-01-02']);
        self::assertSame('/organizations/otra/attendance/leaves', parse_url($r->lastCall()['url'], PHP_URL_PATH));
        self::assertArrayNotHasKey('orgId', $r->lastBody() ?? []);
        (new BotAgentsResource($r, self::ORG))->list(['orgId' => 'otra']);
        self::assertSame('/organizations/otra/bot/agents', parse_url($r->lastCall()['url'], PHP_URL_PATH));
    }
}
