<?php
/**
Returns:

{"user_info":"{\n  \"login\": \"_username\",\n  \"id\": 18223840,\n  \"node_id\": \"MDQ6VXNlcjE4MjIzODQw\",\n  \"avatar_url\": \"https:\/\/avatars3.githubusercontent.com\/u\/18223840?v=4\",\n  \"gravatar_id\": \"\",\n  \"url\": \"https:\/\/api.github.com\/users\/bugdetector\",\n  \"html_url\": \"https:\/\/github.com\/bugdetector\",\n  \"followers_url\": \"https:\/\/api.github.com\/users\/bugdetector\/followers\",\n  \"following_url\": \"https:\/\/api.github.com\/users\/bugdetector\/following{\/other_user}\",\n  \"gists_url\": \"https:\/\/api.github.com\/users\/bugdetector\/gists{\/gist_id}\",\n  \"starred_url\": \"https:\/\/api.github.com\/users\/bugdetector\/starred{\/owner}{\/repo}\",\n  \"subscriptions_url\": \"https:\/\/api.github.com\/users\/bugdetector\/subscriptions\",\n  \"organizations_url\": \"https:\/\/api.github.com\/users\/bugdetector\/orgs\",\n  \"repos_url\": \"https:\/\/api.github.com\/users\/bugdetector\/repos\",\n  \"events_url\": \"https:\/\/api.github.com\/users\/bugdetector\/events{\/privacy}\",\n  \"received_events_url\": \"https:\/\/api.github.com\/users\/bugdetector\/received_events\",\n  \"type\": \"User\",\n  \"site_admin\": _,\n  \"name\": \"Murat Baki Y\u00fccel\",\n  \"company\": null,\n  \"blog\": \"\",\n  \"location\": \"\u0130stanbul\",\n  \"email\": \"bakiyucel38@gmail.com\",\n  \"hireable\": null,\n  \"bio\": null,\n  \"public_repos\": 16,\n  \"public_gists\": 0,\n  \"followers\": 5,\n  \"following\": 5,\n  \"created_at\": \"2016-04-02T02:16:08Z\",\n  \"updated_at\": \"2018-11-28T13:47:46Z\",\n  \"private_gists\": 1,\n  \"total_private_repos\": 0,\n  \"owned_private_repos\": 0,\n  \"disk_usage\": 38734,\n  \"collaborators\": 0,\n  \"two_factor_authentication\": _,\n  \"plan\": {\n    \"name\": \"free\",\n    \"space\": 976562499,\n    \"collaborators\": 0,\n    \"private_repos\": 0\n  }\n}\n","access_token":"_access_token"}

*/

function get_github_user_info($redirect_uri, $github_client_id, $github_client_secret, $code = NULL){
    $code = $code ? $code : (isset($_GET["code"]) ? $_GET["code"] : "");
    if(!$code){
        return;
    }
    // Getting access token
    $token_url = "https://github.com/login/oauth/access_token";
    $token_response = file_get_contents($token_url, false, stream_context_create( array(
	            "http" => array(
	            	"header" => "Content-type: application/x-www-form-urlencoded",
	                "method" => "POST",
	                "content" => http_build_query(array(
	                        "code" => $code,
	                        "client_id" => $github_client_id,
	                        "client_secret" => $github_client_secret,
	                        "redirect_uri" => $redirect_uri
	                    )
	                )
	            )
	        )
		)
	);
    $token_data = array();
    parse_str($token_response, $token_data);
    if(isset($token_data["error"])){
    	return $token_data["error_description"];
    }
    $access_token = $token_data["access_token"];
    
    // Getting user info
    $user_info_url = "https://api.github.com/user?access_token={$access_token}";
    $user_info_response = file_get_contents($user_info_url, false, stream_context_create(
	array( 
		"http" => array(
			'method' => "GET",
			'user_agent'=> $_SERVER ['HTTP_USER_AGENT']
		) )
) ) ;
    return json_encode( 
        array(
            "user_info" => $user_info_response, 
            "access_token" => $access_token
        ) 
    );
    
}
