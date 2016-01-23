<?php namespace Modules\Activity\Services\Activity\Github\Events;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Support\Facades\Cache;

class PushEvent extends BaseEventClass implements GithubEventInterface
{
    public function handle($eventData)
    {
        try {
            $link = $this->getCommitLink($this->getUsername($eventData['repo']['name']),
                $this->getRepositoryName($eventData['repo']['name']), $eventData['payload']['head']);
        } catch (\Exception $e) {
            $link = '';
        }

        return [
            'time' => $this->getDate($eventData['created_at']),
            'actor' => $eventData['actor']['login'],
            'actor_avatar' => $eventData['actor']['avatar_url'],
            'verb' => 'pushed to ',
            'action_object' => $eventData['repo']['name'],
            'target' => $link
        ];
    }

    private function getCommitLink($username, $repository, $ref)
    {
        if (!Cache::has("$username-commitLink")) {
            $commitLink = Github::api('repo')->commits()->show($username, $repository, $ref)['html_url'];
            Cache::put("$username-commitLink", $commitLink, 120);
        }

        return Cache::get("$username-commitLink", []);
    }

    private function getRepositoryName($fullRepoName)
    {
        return explode('/', $fullRepoName)[1];
    }

    private function getUsername($fullRepoName)
    {
        return explode('/', $fullRepoName)[0];
    }
}
