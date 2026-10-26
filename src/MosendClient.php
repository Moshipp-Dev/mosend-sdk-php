<?php

declare(strict_types=1);

namespace Mosend;

use Mosend\Http\HttpClient;
use Mosend\Resources\AddonsResource;
use Mosend\Resources\AiCreditsResource;
use Mosend\Resources\ApiKeysResource;
use Mosend\Resources\AttendanceResource;
use Mosend\Resources\AuditResource;
use Mosend\Resources\AuthResource;
use Mosend\Resources\AutoRepliesResource;
use Mosend\Resources\BillingResource;
use Mosend\Resources\BotConfigResource;
use Mosend\Resources\BotEventsResource;
use Mosend\Resources\BroadcastsResource;
use Mosend\Resources\ContactListsResource;
use Mosend\Resources\ContactsResource;
use Mosend\Resources\ConversationsResource;
use Mosend\Resources\CreditNotesResource;
use Mosend\Resources\DocumentsResource;
use Mosend\Resources\FlowsResource;
use Mosend\Resources\HealthResource;
use Mosend\Resources\IntegrationsResource;
use Mosend\Resources\InvitationsResource;
use Mosend\Resources\InvoicesResource;
use Mosend\Resources\KnowledgeResource;
use Mosend\Resources\LeadsResource;
use Mosend\Resources\LinkPagesResource;
use Mosend\Resources\MediaResource;
use Mosend\Resources\MembershipsResource;
use Mosend\Resources\MercadoPagoResource;
use Mosend\Resources\MessagesResource;
use Mosend\Resources\NotificationsResource;
use Mosend\Resources\OptInsResource;
use Mosend\Resources\OrgAiProvidersResource;
use Mosend\Resources\OrganizationsResource;
use Mosend\Resources\PasskeysResource;
use Mosend\Resources\PaymentMethodsResource;
use Mosend\Resources\PermissionsResource;
use Mosend\Resources\PhoneNumbersResource;
use Mosend\Resources\PlanLimitsResource;
use Mosend\Resources\PlansResource;
use Mosend\Resources\PricingResource;
use Mosend\Resources\ProfilesResource;
use Mosend\Resources\PushResource;
use Mosend\Resources\QuickRepliesResource;
use Mosend\Resources\ReactionsResource;
use Mosend\Resources\ReportsResource;
use Mosend\Resources\RolesResource;
use Mosend\Resources\ScheduleResource;
use Mosend\Resources\ShiftRemindersResource;
use Mosend\Resources\SolutionsResource;
use Mosend\Resources\StickersResource;
use Mosend\Resources\StoreConnectionsResource;
use Mosend\Resources\StoreTemplatesResource;
use Mosend\Resources\SystemNoticesResource;
use Mosend\Resources\TagsResource;
use Mosend\Resources\TasksResource;
use Mosend\Resources\TemplatesResource;
use Mosend\Resources\TwoFactorResource;
use Mosend\Resources\UsageResource;
use Mosend\Resources\UsersResource;
use Mosend\Resources\WabaResource;
use Mosend\Resources\WalletResource;
use Mosend\Resources\WalletAlertsResource;
use Mosend\Resources\WebChatResource;
use Mosend\Resources\WebChatPublicResource;
use Mosend\Resources\WebhooksOutboundResource;
use Mosend\Resources\WhatsappLinksResource;
use Mosend\Resources\LicenseDeliveryResource;
use Mosend\Resources\AgendaResource;
use Mosend\Resources\SalesResource;
use Mosend\Resources\QuotesResource;
use Mosend\Resources\OrdersResource;
use Mosend\Resources\CatalogsResource;
use Mosend\Resources\NequiPaymentsResource;
use Mosend\Resources\PaymentReportsResource;
use Mosend\Resources\WompiResource;
use Mosend\Resources\AccountDeletionResource;
use Mosend\Resources\AdCampaignsResource;
use Mosend\Resources\BotAgentsResource;
use Mosend\Resources\BotImagesResource;
use Mosend\Resources\BotServicesResource;
use Mosend\Resources\BotSummaryResource;
use Mosend\Resources\ConnectorsResource;
use Mosend\Resources\DayOffResource;
use Mosend\Resources\MessagingLimitResource;
use Mosend\Resources\ConnectionTestResource;
use Mosend\Resources\SurveysResource;
use Mosend\Resources\WabaUsageResource;
use Mosend\Resources\OAuthResource;
use Mosend\Resources\WhatsappFlowsResource;
use Mosend\Resources\WhatsappGroupsResource;
use Mosend\Resources\CrmResource;
use Mosend\Resources\DealsResource;
use Mosend\Resources\SignupLinksResource;

/**
 * Cliente principal del SDK de Mosend. Instancia con tu API key (y opcionalmente
 * tu orgId default) y accedé a los resources tipados:
 *
 *   $mosend = new MosendClient(['apiKey' => 'mk_live_...', 'orgId' => '...']);
 *   $mosend->messages->send([...]);
 */
final class MosendClient
{
    /** @var AddonsResource */
    public $addons;
    /** @var AiCreditsResource */
    public $aiCredits;
    /** @var ApiKeysResource */
    public $apiKeys;
    /** @var AttendanceResource */
    public $attendance;
    /** @var AuditResource */
    public $audit;
    /** @var AuthResource */
    public $auth;
    /** @var AutoRepliesResource */
    public $autoReplies;
    /** @var BillingResource */
    public $billing;
    /** @var BotConfigResource */
    public $botConfig;
    /** @var BotEventsResource */
    public $botEvents;
    /** @var BroadcastsResource */
    public $broadcasts;
    /** @var ContactListsResource */
    public $contactLists;
    /** @var ContactsResource */
    public $contacts;
    /** @var ConversationsResource */
    public $conversations;
    /** @var CreditNotesResource */
    public $creditNotes;
    /** @var DocumentsResource */
    public $documents;
    /** @var FlowsResource */
    public $flows;
    /** @var HealthResource */
    public $health;
    /** @var IntegrationsResource */
    public $integrations;
    /** @var InvitationsResource */
    public $invitations;
    /** @var InvoicesResource */
    public $invoices;
    /** @var KnowledgeResource */
    public $knowledge;
    /** @var LeadsResource */
    public $leads;
    /** @var LinkPagesResource */
    public $linkPages;
    /** @var MediaResource */
    public $media;
    /** @var MembershipsResource */
    public $memberships;
    /** @var MercadoPagoResource */
    public $mercadoPago;
    /** @var MessagesResource */
    public $messages;
    /** @var NotificationsResource */
    public $notifications;
    /** @var OptInsResource */
    public $optIns;
    /** @var OrgAiProvidersResource */
    public $orgAiProviders;
    /** @var OrganizationsResource */
    public $organizations;
    /** @var PasskeysResource */
    public $passkeys;
    /** @var PaymentMethodsResource */
    public $paymentMethods;
    /** @var PermissionsResource */
    public $permissions;
    /** @var PhoneNumbersResource */
    public $phoneNumbers;
    /** @var PlanLimitsResource */
    public $planLimits;
    /** @var PlansResource */
    public $plans;
    /** @var PricingResource */
    public $pricing;
    /** @var ProfilesResource */
    public $profiles;
    /** @var PushResource */
    public $push;
    /** @var QuickRepliesResource */
    public $quickReplies;
    /** @var ReactionsResource */
    public $reactions;
    /** @var ReportsResource */
    public $reports;
    /** @var RolesResource */
    public $roles;
    /** @var ScheduleResource */
    public $schedule;
    /** @var ShiftRemindersResource */
    public $shiftReminders;
    /** @var SolutionsResource */
    public $solutions;
    /** @var StickersResource */
    public $stickers;
    /** @var StoreConnectionsResource */
    public $storeConnections;
    /** @var StoreTemplatesResource */
    public $storeTemplates;
    /** @var SystemNoticesResource */
    public $systemNotices;
    /** @var TagsResource */
    public $tags;
    /** @var TasksResource */
    public $tasks;
    /** @var TemplatesResource */
    public $templates;
    /** @var TwoFactorResource */
    public $twoFactor;
    /** @var UsageResource */
    public $usage;
    /** @var UsersResource */
    public $users;
    /** @var WabaResource */
    public $waba;
    /** @var WalletResource */
    public $wallet;
    /** @var WalletAlertsResource */
    public $walletAlerts;
    /** @var WebChatResource */
    public $webChat;
    /** @var WebChatPublicResource */
    public $webChatPublic;
    /** @var WebhooksOutboundResource */
    public $webhooksOutbound;
    /** @var WhatsappLinksResource */
    public $whatsappLinks;

    /** @var LicenseDeliveryResource */
    public $licenseDelivery;

    /** @var AgendaResource */
    public $agenda;

    /** @var SalesResource */
    public $sales;

    /** @var QuotesResource */
    public $quotes;

    /** @var OrdersResource */
    public $orders;

    /** @var CatalogsResource */
    public $catalogs;

    /** @var NequiPaymentsResource */
    public $nequiPayments;

    /** @var PaymentReportsResource */
    public $paymentReports;

    /** @var WompiResource */
    public $wompi;

    /** @var AccountDeletionResource */
    public $accountDeletion;

    /** @var AdCampaignsResource */
    public $adCampaigns;

    /** @var BotAgentsResource */
    public $botAgents;

    /** @var BotImagesResource */
    public $botImages;

    /** @var BotServicesResource */
    public $botServices;

    /** @var BotSummaryResource */
    public $botSummary;

    /** @var ConnectorsResource */
    public $connectors;

    /** @var DayOffResource */
    public $dayOff;

    /** @var MessagingLimitResource */
    public $messagingLimit;

    /** @var ConnectionTestResource */
    public $connectionTest;

    /** @var SurveysResource */
    public $surveys;

    /** @var WabaUsageResource */
    public $wabaUsage;

    /** @var OAuthResource */
    public $oauth;

    /** @var WhatsappFlowsResource */
    public $whatsappFlows;

    /** @var WhatsappGroupsResource */
    public $whatsappGroups;

    /** @var CrmResource */
    public $crm;

    /** @var DealsResource */
    public $deals;

    /** @var SignupLinksResource */
    public $signupLinks;

    /** @var HttpClient */
    private $http;

    /**
     * @param array<string,mixed> $config apiKey, accessToken, orgId, baseUrl, timeout(ms), retries, userAgent, defaultHeaders
     */
    public function __construct(array $config = [])
    {
        $httpConfig = [
            'baseUrl' => $config['baseUrl'] ?? 'https://api.mosend.dev',
            'timeoutMs' => $config['timeout'] ?? 30000,
            'userAgent' => $config['userAgent'] ?? 'moshipp-mosend-sdk-php/1.1.0',
            'defaultHeaders' => $config['defaultHeaders'] ?? [],
            'retries' => $config['retries'] ?? null,
        ];
        if (isset($config['apiKey'])) {
            $httpConfig['apiKey'] = $config['apiKey'];
        }
        if (isset($config['accessToken'])) {
            $httpConfig['accessToken'] = $config['accessToken'];
        }

        $this->http = new HttpClient($httpConfig);
        $orgId = isset($config['orgId']) ? (string) $config['orgId'] : null;

        $this->addons = new AddonsResource($this->http, $orgId);
        $this->aiCredits = new AiCreditsResource($this->http, $orgId);
        $this->apiKeys = new ApiKeysResource($this->http, $orgId);
        $this->attendance = new AttendanceResource($this->http, $orgId);
        $this->audit = new AuditResource($this->http, $orgId);
        $this->auth = new AuthResource($this->http, $orgId);
        $this->autoReplies = new AutoRepliesResource($this->http, $orgId);
        $this->billing = new BillingResource($this->http, $orgId);
        $this->botConfig = new BotConfigResource($this->http, $orgId);
        $this->botEvents = new BotEventsResource($this->http, $orgId);
        $this->broadcasts = new BroadcastsResource($this->http, $orgId);
        $this->contactLists = new ContactListsResource($this->http, $orgId);
        $this->contacts = new ContactsResource($this->http, $orgId);
        $this->conversations = new ConversationsResource($this->http, $orgId);
        $this->creditNotes = new CreditNotesResource($this->http, $orgId);
        $this->documents = new DocumentsResource($this->http, $orgId);
        $this->flows = new FlowsResource($this->http, $orgId);
        $this->health = new HealthResource($this->http, $orgId);
        $this->integrations = new IntegrationsResource($this->http, $orgId);
        $this->invitations = new InvitationsResource($this->http, $orgId);
        $this->invoices = new InvoicesResource($this->http, $orgId);
        $this->knowledge = new KnowledgeResource($this->http, $orgId);
        $this->leads = new LeadsResource($this->http, $orgId);
        $this->linkPages = new LinkPagesResource($this->http, $orgId);
        $this->media = new MediaResource($this->http, $orgId);
        $this->memberships = new MembershipsResource($this->http, $orgId);
        $this->mercadoPago = new MercadoPagoResource($this->http, $orgId);
        $this->messages = new MessagesResource($this->http, $orgId);
        $this->notifications = new NotificationsResource($this->http, $orgId);
        $this->optIns = new OptInsResource($this->http, $orgId);
        $this->orgAiProviders = new OrgAiProvidersResource($this->http, $orgId);
        $this->organizations = new OrganizationsResource($this->http, $orgId);
        $this->passkeys = new PasskeysResource($this->http, $orgId);
        $this->paymentMethods = new PaymentMethodsResource($this->http, $orgId);
        $this->permissions = new PermissionsResource($this->http, $orgId);
        $this->phoneNumbers = new PhoneNumbersResource($this->http, $orgId);
        $this->planLimits = new PlanLimitsResource($this->http, $orgId);
        $this->plans = new PlansResource($this->http, $orgId);
        $this->pricing = new PricingResource($this->http, $orgId);
        $this->profiles = new ProfilesResource($this->http, $orgId);
        $this->push = new PushResource($this->http, $orgId);
        $this->quickReplies = new QuickRepliesResource($this->http, $orgId);
        $this->reactions = new ReactionsResource($this->http, $orgId);
        $this->reports = new ReportsResource($this->http, $orgId);
        $this->roles = new RolesResource($this->http, $orgId);
        $this->schedule = new ScheduleResource($this->http, $orgId);
        $this->shiftReminders = new ShiftRemindersResource($this->http, $orgId);
        $this->solutions = new SolutionsResource($this->http, $orgId);
        $this->stickers = new StickersResource($this->http, $orgId);
        $this->storeConnections = new StoreConnectionsResource($this->http, $orgId);
        $this->storeTemplates = new StoreTemplatesResource($this->http, $orgId);
        $this->systemNotices = new SystemNoticesResource($this->http, $orgId);
        $this->tags = new TagsResource($this->http, $orgId);
        $this->tasks = new TasksResource($this->http, $orgId);
        $this->templates = new TemplatesResource($this->http, $orgId);
        $this->twoFactor = new TwoFactorResource($this->http, $orgId);
        $this->usage = new UsageResource($this->http, $orgId);
        $this->users = new UsersResource($this->http, $orgId);
        $this->waba = new WabaResource($this->http, $orgId);
        $this->wallet = new WalletResource($this->http, $orgId);
        $this->walletAlerts = new WalletAlertsResource($this->http, $orgId);
        $this->webChat = new WebChatResource($this->http, $orgId);
        $this->webChatPublic = new WebChatPublicResource($this->http, $orgId);
        $this->webhooksOutbound = new WebhooksOutboundResource($this->http, $orgId);
        $this->whatsappLinks = new WhatsappLinksResource($this->http, $orgId);
        $this->licenseDelivery = new LicenseDeliveryResource($this->http, $orgId);
        $this->agenda = new AgendaResource($this->http, $orgId);
        $this->sales = new SalesResource($this->http, $orgId);
        $this->quotes = new QuotesResource($this->http, $orgId);
        $this->orders = new OrdersResource($this->http, $orgId);
        $this->catalogs = new CatalogsResource($this->http, $orgId);
        $this->nequiPayments = new NequiPaymentsResource($this->http, $orgId);
        $this->paymentReports = new PaymentReportsResource($this->http, $orgId);
        $this->wompi = new WompiResource($this->http, $orgId);
        $this->accountDeletion = new AccountDeletionResource($this->http, $orgId);
        $this->adCampaigns = new AdCampaignsResource($this->http, $orgId);
        $this->botAgents = new BotAgentsResource($this->http, $orgId);
        $this->botImages = new BotImagesResource($this->http, $orgId);
        $this->botServices = new BotServicesResource($this->http, $orgId);
        $this->botSummary = new BotSummaryResource($this->http, $orgId);
        $this->connectors = new ConnectorsResource($this->http, $orgId);
        $this->dayOff = new DayOffResource($this->http, $orgId);
        $this->messagingLimit = new MessagingLimitResource($this->http, $orgId);
        $this->connectionTest = new ConnectionTestResource($this->http, $orgId);
        $this->surveys = new SurveysResource($this->http, $orgId);
        $this->wabaUsage = new WabaUsageResource($this->http, $orgId);
        $this->oauth = new OAuthResource($this->http, $orgId);
        $this->whatsappFlows = new WhatsappFlowsResource($this->http, $orgId);
        $this->whatsappGroups = new WhatsappGroupsResource($this->http, $orgId);
        $this->crm = new CrmResource($this->http, $orgId);
        $this->deals = new DealsResource($this->http, $orgId);
        $this->signupLinks = new SignupLinksResource($this->http, $orgId);
    }

    public function setApiKey(?string $key): void
    {
        $this->http->setApiKey($key);
    }

    public function setAccessToken(?string $token): void
    {
        $this->http->setAccessToken($token);
    }

    public function getHttpClient(): HttpClient
    {
        return $this->http;
    }
}
