<?php

namespace App\Http\Services;

class GoogleAnalyticsService
{
    public function getAccessToken()                                            
    {
        $tokenURL = env("GOOGLE_ANALYTICS_TOKEN_URL");                         
                                                                               
        $postData = array( 
            'client_secret' => env("GOOGLE_ANALYTICS_CLIENT_SECRET"),           
            'grant_type' => env("GOOGLE_ANALYTICS_GRANT_TYPE"),                 
            'refresh_token' => env("GOOGLE_ANALYTICS_REFRESH_TOKEN"),           
            'client_id' => env("GOOGLE_ANALYTICS_CLIENT_ID")                    
        );                                                                      

        $ch = curl_init();                                                      
        curl_setopt($ch, CURLOPT_URL, $tokenURL);                              
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                       
        curl_setopt($ch, CURLOPT_POST, 1);                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $tokenReturn = curl_exec($ch);                                         
        $token = json_decode($tokenReturn);                                     
                                                                               

        return $token->access_token;                                           
    }

    public function getDataFromApi($url, $accessToken, $startDate, $endDate)   
    {                                                                          
                                                                              
        $urlWToken = $url . "?access_token=" . $accessToken;                  
                                                                              

        $postData = array(
            "dateRanges" => [ "startDate" => $startDate, "endDate" => $endDate ], 
            "dimensions" => [ "name" => "country" ],                           
            "metrics" => [ "name" => "sessions" ]                              
        );                                                                     

        $strPostData = json_encode($postData);                                 
                                                                               

        $ch = curl_init();                                                     
        curl_setopt($ch, CURLOPT_URL, $urlWToken);                             
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strPostData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($strPostData))
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);                                                
        return json_decode($data, true);      
    }                                
}