<?php
    function getFeatures($request)
    {
        $user = getUser($request->data->user_id, $request->data->user_game_token, $request->data->currency);
                                
        $response = new stdClass();
        $response->answer = new stdClass();
/*
		$response->answer->free_rounds = new stdClass();
		$response->answer->free_rounds->id = 10;
		$response->answer->free_rounds->count = 3;
		$response->answer->free_rounds->bet = 5;
		$response->answer->free_rounds->lines = 21;	
		$response->answer->free_rounds->mpl = 2;	
		$response->answer->free_rounds->cp = 0.5;	
*/
        $response->answer->transaction_id = $request->data->transaction_id; 
        $response->answer->balance = $user->amount;
        $response->answer->bonus_balance = 0;        
        $response->answer->user_id = $user->id;
        $response->answer->operator_id = OPERATOR_ID;
        $response->answer->currency = $user->current_currency;
        $response->answer->game_token = $user->game_token;
        $response->answer->user_nickname = $user->nick;
        $response->answer->error_code = 0;
        $response->answer->error_description = "ok";
        $response->answer->timestamp = '"'.time().'"';    
        $response->api = $request->api;
        $response->success = true;       
		        
        return $response; 
    }
	
    function activateFeatures($request)
    {
		$user = getUser($request->data->user_id, $request->data->user_game_token, $request->data->currency);
		
        $response = new stdClass();                           
        $response->answer = new stdClass();			
				
        $response->answer->balance = $user->amount;
        $response->answer->bonus_balance = 0;     
		$response->answer->game_id = 0;
        $response->answer->user_id = $user->id;
        $response->answer->operator_id = OPERATOR_ID;
        $response->answer->currency = $user->current_currency;
        $response->answer->game_token = $user->game_token;
        $response->answer->user_nickname = $user->nick;
        $response->answer->error_code = 0;
        $response->answer->error_description = "ok";				
        $response->answer->timestamp = '"'.time().'"';  		
        $response->api = $request->api;
        $response->success = true;		    
		        
        return $response; 
    }	
	
    function endFeatures($request)
    {
		$user = getUser($request->data->user_id, $request->data->user_game_token, $request->data->currency);
		
        $response = new stdClass();                           
        $response->answer = new stdClass();			
				
        $response->answer->balance =  $user->amount;
        $response->answer->bonus_balance = 0;
		$response->answer->game_id = 0;
        $response->answer->user_id = $user->id;
        $response->answer->operator_id = OPERATOR_ID;
        $response->answer->currency = $user->current_currency;
        $response->answer->game_token = $user->game_token;
        $response->answer->user_nickname = $user->nick;
        $response->answer->error_code = 0;
        $response->answer->error_description = "ok";				
        $response->answer->timestamp = '"'.time().'"';  		
        $response->api = $request->api;
        $response->success = true;		    
		        
        return $response; 
    }
	
    function getUser($user_id, $game_token, $currency)
    {
        $qres = mysql_query("select u.*, a.currency, a.amount from users u join amount a on id = user where id = $user_id and a.currency = '$currency'");
        
        if (!$user = mysql_fetch_object($qres))
        {     
            throw new Exception("User not found");                    
        }      
        
        if ($user->game_token !== $game_token)
        {     
            throw new Exception("game_token not valid");                    
        }        
        
        $game_token_date = date_create($user->game_token_date);
        $current_date = new DateTime("now");
        
        $interval = $game_token_date->diff($current_date);
                
        if ($interval->m > AUTH_TOKEN_TTL)
        {     
            throw new Exception("game_token expired");                    
        }    
        
        return $user;
    }	
?>