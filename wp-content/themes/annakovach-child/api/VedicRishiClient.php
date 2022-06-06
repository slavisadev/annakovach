<?php
/**
 * Vedic Rishi Client for consuming Vedic Rishi Astro Web APIs
 * http://www.vedicrishiastro.com/astro-api/
 * Author: Chandan Tiwari
 * Date: 06/12/14
 * Time: 5:42 PM
 */

class VedicRishiClient
{
    private $userId = null;
    private $apiKey = null;
    private $language = null;
    private $apiEndPoint = "https://json.astrologyapi.com/v1/";
    //private $apiEndPoint = "http://localhost:3000/v1/";
    /**
     * @param $uid userId for Vedic Rishi Astro API
     * @param $key api key for Vedic Rishi Astro API access
     */
    public function __construct($uid, $key, $lan)
    {
        $this->userId = $uid;
        $this->apiKey = $key;
        $this->language = $lan;
    }

    private function getCurlReponse($resource, array $data)
    {
        $serviceUrl = $this->apiEndPoint.$resource;
        $authData = $this->userId.":".$this->apiKey;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $serviceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $headers = array(
            'Authorization: Basic '. base64_encode($authData),
            'Accept-Language:'.$this->language
        );
        //$header[] = 'Authorization: Basic '. base64_encode($authData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function packageHoroData($name,$date, $month, $year, $hour, $minute, $latitude, $longitude, $timezone)
    {
        return array(
            'day' => $date,
            'month' => $month,
            'year' => $year,
            'hour' => $hour,
            'min' => $minute,
            'lat' => $latitude,
            'lon' => $longitude,
            'tzone' => $timezone,
            'name'=>$name
        );
    }

    private function packageMatchMakingData($maleBirthData, $femaleBirthData)
    {
        $mData = array(
            'm_day' => $maleBirthData['date'],
            'm_month' => $maleBirthData['month'],
            'm_year' => $maleBirthData['year'],
            'm_hour' => $maleBirthData['hour'],
            'm_min' => $maleBirthData['minute'],
            'm_lat' => $maleBirthData['latitude'],
            'm_lon' => $maleBirthData['longitude'],
            'm_tzone' => $maleBirthData['timezone']
        );
        $fData = array(
            'f_day' => $femaleBirthData['date'],
            'f_month' => $femaleBirthData['month'],
            'f_year' => $femaleBirthData['year'],
            'f_hour' => $femaleBirthData['hour'],
            'f_min' => $femaleBirthData['minute'],
            'f_lat' => $femaleBirthData['latitude'],
            'f_lon' => $femaleBirthData['longitude'],
            'f_tzone' => $femaleBirthData['timezone']
        );

        return array_merge($mData, $fData);
    }

    private function packageCompatibilityData($maleBirthData, $femaleBirthData)
    {
        $mData = array(
            'p_day' => $maleBirthData['date'],
            'p_month' => $maleBirthData['month'],
            'p_year' => $maleBirthData['year'],
            'p_hour' => $maleBirthData['hour'],
            'p_min' => $maleBirthData['minute'],
            'p_lat' => $maleBirthData['latitude'],
            'p_lon' => $maleBirthData['longitude'],
            'p_tzone' => $maleBirthData['timezone']
        );
        $fData = array(
            's_day' => $femaleBirthData['date'],
            's_month' => $femaleBirthData['month'],
            's_year' => $femaleBirthData['year'],
            's_hour' => $femaleBirthData['hour'],
            's_min' => $femaleBirthData['minute'],
            's_lat' => $femaleBirthData['latitude'],
            's_lon' => $femaleBirthData['longitude'],
            's_tzone' => $femaleBirthData['timezone']
        );

        return array_merge($mData, $fData);
    }
    
    private function packageNumeroData($date, $month, $year, $name)
    {
        return array(
            'day' => $date,
            'month' => $month,
            'year' => $year,
            'name' => $name
        );
    }

    private function dataSanityCheck($data)
    {

    }

    /**
     * @param $resourceName string apiName name of an api without any begining and end slashes (ex 'birth_details')
     * @param $date date
     * @param $month month
     * @param $year year
     * @param $hour hour
     * @param $minute minute
     * @param $latitude latitude
     * @param $longitude longitude
     * @param $timezone timezone
     * @return array response data decoded in PHP associative array format
     */
    public function call($resourceName,$name, $date, $month, $year, $hour, $minute, $latitude, $longitude, $timezone)
    {

        $data = $this->packageHoroData($name,$date, $month, $year, $hour, $minute, $latitude, $longitude, $timezone);
        $data['aspects'] = 'none';
        $resData = $this->getCurlReponse($resourceName, $data);
        return $resData;
        //return json_decode($resData);
    }

    public function sunSignCall($resourceName,array $data)
    {

        $resData = $this->getCurlReponse($resourceName, $data);
        return $resData;
        //return json_decode($resData);
    }

    /**
     * @param $resourceName apiName name of an api along with begining and end slashes (ex /birth_details)
     * @param array $maleBirthData  maleBirthdata associative array format
     * @param array $femaleBirthData femaleBirthdata associative array format
     * @return array response data decoded in PHP associative array format
     */
    public function matchMakingCall($resourceName, array $maleBirthData, array $femaleBirthData)
    {
        //TODO:  needs to validate male and female birth data against expected keys
        //$this->dataSanityCheck($maleBirthData);
        //$this->dataSanityCheck($femaleBirthData);

        $data = $this->packageMatchMakingData($maleBirthData, $femaleBirthData);
        $response = $this->getCurlReponse($resourceName, $data);
        return $response;
    }

    public function compatibilityApiCall($resourceName, array $maleBirthData, array $femaleBirthData)
    {
        //TODO:  needs to validate male and female birth data against expected keys
        //$this->dataSanityCheck($maleBirthData);
        //$this->dataSanityCheck($femaleBirthData);

        $data = $this->packageCompatibilityData($maleBirthData, $femaleBirthData);
        $response = $this->getCurlReponse($resourceName, $data);
        return $response;
    }


    /**
     * @param $resourceName string apiName name of numerological api (numero_table and numero_report)
     * @param $date int date of birth
     * @param $month int month of birth
     * @param $year int year of birth
     * @param $name string name
     * @return array response data decoded in PHP associative array format
     */
    public function numeroCall($resourceName, $date, $month, $year, $name)
    {
        $data = $this->packageNumeroData($date, $month, $year, $name);
        $resData = $this->getCurlReponse($resourceName, $data);
        return $resData;
    }

    /*For Geo details*/
    public function getGeo($resourceName, $data)
    {
        $resData = $this->getCurlReponse($resourceName, $data);
        return $resData;
    }

    public function timezone($data,$resourceName)
    {
        $resData = $this->getCurlReponse($resourceName, $data);
        return $resData;
    }
}