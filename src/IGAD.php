<?php
namespace Andach\IGAD;
use GuzzleHttp\Client;

class IGAD
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;
    /**
     * @var string
     */
    protected $xboxapikey;
    /**
     * @var array
     */
    const VALID_RESOURCES = [
        'games' => 'games',
        'characters' => 'characters',
        'companies' => 'companies',
        'game_engines' => 'game_engines',
        'game_modes' => 'game_modes',
        'keywords' => 'keywords',
        'people' => 'people',
        'platforms' => 'platforms',
        'pulses' => 'pulses',
        'themes' => 'themes',
        'collections' => 'collections',
        'player_perspectives' => 'player_perspectives',
        'reviews' => 'reviews',
        'franchises' => 'franchises',
        'genres' => 'genres',
        'release_dates' => 'release_dates'
    ];
    /**
     * IGAD constructor.
     *
     * @param $xboxapikey
     *
     * @throws \Exception
     */
    public function __construct($xboxapikey, $xuid = '')
    {
        if (!is_string($xboxapikey) || empty($xboxapikey)) 
        {
            throw new \Exception('IGAD Xbox API key is required');
        }
        if (!is_string($xuid))
        {
            throw new \Exception('IGAD Xuid must be cast to a string before being submitted to the class.');
        }

        $this->xboxapikey = $xboxapikey;
        $this->xuid = $xuid;
        $this->httpClient = new Client();
    }
    
    public function getAccountXuid()
    {
        $apiUrl = $this->getEndpoint('accountXuid');
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }

    public function getAchievements($titleid, $xuid = '')
    {
        if (!$xuid)
        {
            if (!$this->xuid)
            {
                throw new \Exception('No Xuid passed to getAchievements or stored in class.');
            }
            $xuid = $this->xuid;
        }
        if (!is_string($xuid))
        {
            throw new \Exception('IGAD Xuid must be cast to a string before being submitted to getAchievements.');
        }

        $xuid = (string) $xuid;
        $titleid = (string) $titleid;

        if (strlen($titleid) <= 9)
        {
            $type = 'xboxone';
        } else {
            $type = 'xbox360';
        }

        $apiUrl = $this->getEndpoint($xuid.'/achievements/'.$titleid);
        $apiData = $this->apiGet($apiUrl);
        
        $data = $this->decode($apiData);
        
        if ($type == 'xboxone')
        {
            foreach ($data as $ach)
            {
                $array = array();

                if ($ach['rarity']['currentCategory'] == 'Rare')
                {
                    $rare = 1;
                } else {
                    $rare = 0;
                }

                $array['name'] = $ach['name'];
                $array['gamerscore'] = $ach['rewards'][0]['value'];
                $array['achievement_description'] = $ach['description'];
                $array['achievement_locked_description'] = $ach['lockedDescription'];
                $array['is_secret'] = $ach['isSecret'];
                $array['is_rare'] = $rare;
                $array['percentage_unlocked'] = $ach['rarity']['currentPercentage'];
                $array['image'] = $ach['mediaAssets'][0]['url'];
                $array['type'] = $type;

                $return[] = $array;
            }

            return $return;
        } else {
            foreach ($data as $ach)
            {
                $array = array();

                $array['name'] = $ach['name'];
                $array['gamerscore'] = $ach['gamerscore'];
                $array['achievement_description'] = $ach['description'];
                $array['achievement_locked_description'] = $ach['lockedDescription'];
                $array['is_secret'] = $ach['isSecret'];
                $array['is_rare'] = false;
                $array['percentage_unlocked'] = 0;
                $array['image'] = $ach['imageUnlocked'];
                $array['type'] = $type;

                $return[] = $array;
            }

            return $return;
        }
        
    }

    public function getGameDetails($titleid)
    {
        $apiUrl = $this->getEndpoint('/game-details-hex/'.dechex($titleid));
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }

    public function getGamertag($xuid)
    {
        $apiUrl = $this->getEndpoint('gamertag/'.$xuid);
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData)['gamertag'];
    }

    public function getMyConversations()
    {
        $apiUrl = $this->getEndpoint('conversations');
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }

    public function getMyMessages()
    {
        $apiUrl = $this->getEndpoint('messages');
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }
    
    public function getMyProfile()
    {
        $apiUrl = $this->getEndpoint('profile');
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }
    
    public function getProfile($xuid)
    {
        $apiUrl = $this->getEndpoint($xuid.'/profile');
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }

    public function getUserInfo($xuid, $info)
    {
        switch ($info)
        {
            case 'profile':
            case 'gamercard':
            case 'presence':
            case 'activity':
            case 'xbox360games':
            case 'xboxonegames':
            case 'friends':
                $url = $info;
            break;

            case 'recentactivity':
                $url = '/activity/recent';
            break;

            default:
                throw new \Exception('The info type of '.$info.' is unrecognised');
            break;
        }

        $apiUrl = $this->getEndpoint($url);
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }

    public function getXuid($gamertag)
    {
        $apiUrl = $this->getEndpoint('xuid/'.$gamertag);
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData)['xuid'];
    }

    /*
        Private Functions
    */

    private function getEndpoint($name)
    {
        return 'https://xboxapi.com/v2/'.$name;
    }
    
    private function decode(&$apiData)
    {
        $resObj = json_decode($apiData, true, 512, JSON_BIGINT_AS_STRING);
        return $resObj;
    }
    
    private function apiGet($url, $params = array())
    {
        $url = $url . (strpos($url, '?') === false ? '?' : '') . http_build_query($params);
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'X-AUTH' => $this->xboxapikey,
                    'Accept' => 'application/json'
                ]
            ]);
        } catch (RequestException $exception) {
            if ($response = $exception->getResponse()) {
                throw new \Exception($exception);
            }
            throw new \Exception($exception);
        } catch (Exception $exception) {
            throw new \Exception($exception);
        }

        return $response->getBody();
    }
}