<?php

namespace Fluedis\RedmineBundle\Service;

use JMS\Serializer\Serializer;
use Redmine\Client;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RedmineApiClient
{
    /** @var TokenStorageInterface  */
    private $tokenStorage;
    /** @var RequestStack */
    private $requestStack;
    /** @var Serializer */
    private $serializer;

    /** @var bool */
    private $enabled;
    /** @var mixed */
    private $api_uri;
    /** @var mixed */
    private $api_key;
    /** @var mixed */
    private $project_id;
    /** @var mixed  */
    private $priority_id;
    /** @var mixed  */
    private $status_id;
    /** @var mixed  */
    private $assigned_to;
    /** @var array */
    private $watchers;
    /** @var mixed  */
    private $tracker_id;

    /**
     * RedmineApiClient constructor.
     * @param RequestStack $requestStack
     * @param Serializer $serializer
     * @param array $redmineParams
     */
    public function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack, Serializer $serializer, array $redmineParams)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->serializer = $serializer;

        $this->enabled = $redmineParams['enabled'];
        $this->api_uri = $redmineParams['uri'];
        $this->api_key = $redmineParams['api_key'];
        $this->tracker_id = $redmineParams['tracker_id'];
        $this->project_id = $redmineParams['project_id'];
        $this->priority_id = $redmineParams['priority_id'];
        $this->status_id = $redmineParams['status_id'];
        $this->assigned_to = $redmineParams['assigned_to'];
        $this->watchers = $redmineParams['watchers'];
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param \Exception $exception
     * @param array $params
     */
    public function createIssue(\Exception $exception, array $params = [])
    {
        $client = new Client($this->api_uri, $this->api_key);
        $projectId = $client->project->getIdByName($this->project_id);

        $user_id = null;
        if (!is_null($this->assigned_to)) {
            $user_id = $client->user->getIdByUsername($this->assigned_to, ['limit' => 100]);
        }

        $watchers = null;
        if (is_array($this->watchers)) {
            foreach ($this->watchers as $watcher) {
                $watchers[] = $client->user->getIdByUsername($watcher);
            }
        }

        $defaultParams = [
            'project_id' => $projectId,
            'subject' => $exception->getMessage(),
            'description' => $this->descriptionFormatter($exception->getTraceAsString()),
            'priority_id' => $this->priority_id,
            'status_id' => $this->status_id,
            'assigned_to_id' => $user_id,
            'watcher_user_ids' => $watchers
        ];

        $issueParams = array_merge($defaultParams, $params);

        $result = $client->issue->create($issueParams);

    }

    /**
     * @param $exceptionTrace
     * @return string
     */
    private function descriptionFormatter($exceptionTrace)
    {
        $email = 'anonymous';
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $email = $token->getUsername();
        }
        return <<<EOT
**URL :**
{$this->requestStack->getCurrentRequest()->getUri()}

**REFERER :**
{$this->requestStack->getCurrentRequest()->server->get('HTTP_REFERER')}

**CONNECTED USER**
$email

**REQUEST PARAMETERS :** 
{$this->serializer->serialize($this->requestStack->getCurrentRequest()->attributes, 'json')}

**TRACE :**
{$exceptionTrace}
EOT;
    }
}
