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
    public function __construct($xboxapikey)
    {
        if (!is_string($xboxapikey) || empty($xboxapikey)) {
            throw new \Exception('IGAD Xbox API key is required');
        }
        $this->xboxapikey = $xboxapikey;
        $this->httpClient = new Client();
    }
    
    public function getAccountXuid()
    {
        $apiUrl = $this->getEndpoint('accountXuid');
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }

    public function getAchievements($xuid, $titleid)
    {
        $apiUrl = $this->getEndpoint($xuid.'/achievements/'.$titleid);
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }

    public function getConversations()
    {
        $apiUrl = $this->getEndpoint('conversations');
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
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
        return $this->apiGet($apiUrl);
    }

    public function getMessages()
    {
        $apiUrl = $this->getEndpoint('messages');
        $apiData = $this->apiGet($apiUrl);
        return $this->decode($apiData);
    }
    
    public function getMyProfile($xuid = '')
    {
        if ($xuid)
        {
            $apiUrl = $this->getEndpoint($xuid.'/profile');
        } else {
            $apiUrl = $this->getEndpoint('profile');
        }
        
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
        return $this->apiGet($apiUrl);
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