<?php
namespace Andach\IGAD;
use GuzzleHttp\Client;

class IGDB
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
        return $this->decodeSingle($apiData);
    }
    
    public function getProfile()
    {
        $apiUrl = $this->getEndpoint('profile');
        $apiData = $this->apiGet($apiUrl);
        return $this->decodeSingle($apiData);
    }

    /*
     *  Internally used Methods, set visibility to public to enable more flexibility
     */
    /**
     * @param $name
     * @return mixed
     */
    private function getEndpoint($name)
    {
        return 'https://xboxapi.com/v2/'.$name;
    }
    /**
     * Decode the response from IGDB, extract the single resource object.
     * (Don't use this to decode the response containing list of objects)
     *
     * @param  string $apiData the api response from IGDB
     * @throws \Exception
     * @return \StdClass  an IGDB resource object
     */
    private function decodeSingle(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->status)) {
            $msg = "Error " . $resObj->status . " " . $resObj->message;
            throw new \Exception($msg);
        }
        if (!is_array($resObj) || count($resObj) == 0) {
            return false;
        }
        return $resObj[0];
    }
    /**
     * Decode the response from IGDB, extract the multiple resource object.
     *
     * @param  string $apiData the api response from IGDB
     * @throws \Exception
     * @return \StdClass  an IGDB resource object
     */
    private function decodeMultiple(&$apiData)
    {
        $resObj = json_decode($apiData);
        if (isset($resObj->status)) {
            $msg = "Error " . $resObj->status . " " . $resObj->message;
            throw new \Exception($msg);
        } else {
            //$itemsArray = $resObj->items;
            if (!is_array($resObj)) {
                return false;
            } else {
                return $resObj;
            }
        }
    }
    /**
     * Using CURL to issue a GET request
     *
     * @param $url
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    private function apiGet($url, $params)
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