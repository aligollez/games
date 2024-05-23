<?php
    function auth($request)
    {
        $auth_token = $request->data->user_auth_token;
                
        $user = getUser($request->data->user_id, $request->data->user_auth_token, $request->data->currency);
                
		$game_token = md5(time() . mt_rand(1,1000000));
		       
		initSession($request->data->user_id, $game_token, $request->data->currency);
        
        $response = new stdClass();
        $response->answer = new stdClass();
        $response->answer->balance = "{$user->amount}";
        $response->answer->bonus_balance = "0";        
        $response->answer->user_id = $user->id;
        $response->answer->operator_id = OPERATOR_ID;
        $response->answer->currency = $request->data->currency;
        $response->answer->user_nickname = $user->nick;
        $response->answer->auth_token = $request->data->user_auth_token;
        $response->answer->game_token = $game_token; 
        $response->answer->error_code = 0;
        $response->answer->error_description = "ok";
        $response->answer->timestamp = '"'.time().'"';   		
        $response->api = $request->api;
        $response->success = true;
		        
        return $response;        
    }
    
    function getUser($user_id, $auth_token, $currency)
    {
        $qres = mysql_query("select u.*, a.currency, a.amount from users u join amount a on id = user where id = $user_id and a.currency = '$currency'");
        
        if (!$user = mysql_fetch_object($qres))
        {     
            throw new Exception("User not found");                    
        }      
        
        if ($user->auth_token !== $auth_token)
        {     
            throw new Exception("auth_token not valid");                    
        }        
        
        $auth_token_date = date_create($user->auth_token_date);
        $current_date = new DateTime("now");
        
        $interval = $auth_token_date->diff($current_date);
                
        if ($interval->m > AUTH_TOKEN_TTL)
        {     
            throw new Exception("auth_token expired");                    
        }    
        
        return $user;
    }
    
    function initSession($user_id, $game_token, $currency)
    {
        mysql_query("update users set game_token = '$game_token', game_token_date = now(), current_currency = '$currency' where id=$user_id");

        if (mysql_error())
        {
            throw new Exception("Init session error");                
        }        
    }
?>
