<?php

class MSAT_AzureAuthorization
{
    const MSAT_AZURE_AUTHORIZATION_URL = "https://api.cognitive.microsoft.com/sts/v1.0/issueToken";
    
    public static function msat_get_authorization( $api_key ) {
    
        $authorization_obj = wp_remote_post(
            MSAT_AzureAuthorization::MSAT_AZURE_AUTHORIZATION_URL,
            array(
                "headers" => array(
                    "Ocp-Apim-Subscription-Key" => $api_key,
                    "Content-length" => 0
                )
            )
        );
    
        if ( is_wp_error( $authorization_obj ) ) {
            $authorization_obj = MSAT_AzureAuthorization::msat_get_authorization( $api_key );
        }
    
        return $authorization_obj;
    }
}