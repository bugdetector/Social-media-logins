<?php

/** get_facebook_user_info($redirect_uri, $facebook_app_id, $facebook_app_secret, $code or NULL, array("name", "picture", "email") or NULL ));
 * 
 *
 *  Returns:
 *	Array
 *       (
 *           [user_info] => {"name":_name,"picture":{"data":{"height":50,"is_silhouette":false,"url":_url,"width":50}},"email":_email,"id":_id}
 *           [user_id] => user_id
 *           [bearer_token] => _bearer_token
 *           [access_token] => _access_token
 *       )
*/
function get_facebook_user_info($redirect_uri, $facebook_app_id, $facebook_app_secret, $code = NULL, $fields_array = array() ){ 
    $code = $code ? $code : (isset($_GET["code"]) ? $_GET["code"] : "");
    if(!$code){
        return;
    }
    if(!count($fields_array)){
        $fields = "name,picture,email";
    }else{
        $fields = implode(",", $fields_array);
    }
    // Getting bearer token
    $bearer_token_url = "https://graph.facebook.com/v3.1/oauth/access_token?client_id={$facebook_app_id}&client_secret={$facebook_app_secret}&redirect_uri={$redirect_uri}&code={$code}";
    $bearer_token_response =  file_get_contents($bearer_token_url);
    $bearer_token_data = json_decode($bearer_token_response);

    $bearer_token = $bearer_token_data->access_token;

    // Getting access token
    $access_token_url = "https://graph.facebook.com/oauth/access_token?client_id={$facebook_app_id}&client_secret={$facebook_app_secret}&redirect_uri={$redirect_uri}&grant_type=client_credentials";
    $access_token_response = file_get_contents($access_token_url);
    $access_token_data = json_decode($access_token_response);
    $access_token = $access_token_data->access_token;

    // Getting user id
    $user_id_url = "https://graph.facebook.com/debug_token?input_token={$bearer_token}&access_token=$access_token";
    $user_id_response = file_get_contents($user_id_url);    
    $user_id_data = json_decode($user_id_response);
    $user_id = $user_id_data->data->user_id;
    
    if(!$user_id){
        return ["error" => "An error occured"];
    }

    // Getting user info
    $user_info_url = "https://graph.facebook.com/v3.1/{$user_id}?fields=$fields";
    $user_info_response = file_get_contents($user_info_url, false, stream_context_create(array(
            "http" => array(
                "header" => "Authorization: Bearer ".$bearer_token
            )
        )));
     return array(
            "user_info" => $user_info_response, 
            "user_id" => $user_id, 
            "bearer_token" => $bearer_token, 
            "access_token" => $access_token
        );
}