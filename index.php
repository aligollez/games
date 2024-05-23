<?php        
    error_reporting(E_ERROR);
    date_default_timezone_set('europe/moscow');  
    
    try
    {
        header("Connection: close");
    
        $data = file_get_contents('php://input');      
        $request = json_decode($data);
    
        require_once('../lib/lib.php');
        require_once('../lib/db.php');
        
        switch ($request->api)
        {
            case 'do-auth-user-ingame' :
                require_once('auth.php');
                $data = auth($request);
                out($data);
            break;  
        
            case 'do-debit-user-ingame' :
                require_once('debit.php');
                $data = debit($request);
                out($data);
            break;

            case 'do-credit-user-ingame' :
                require_once('credit.php');
                $data = credit($request);
                out($data);
            break;
        
            case 'do-rollback-user-ingame' :
                require_once('rollback.php');
                $data = rollback($request);
                out($data);
            break;        

            case 'do-get-features-user-ingame' :
                require_once('features.php');
                $data = getFeatures($request);
                out($data);
            break;

            case 'do-activate-features-user-ingame' :
                require_once('featuses.php');
                $data = activateFeatures($request);
                out($data);
            break;	

			case 'do-end-features-user-ingame' :
                require_once('featuses.php');
                $data = endFeatures($request);
                out($data);
            break;

            default :
                throw new Exception("Unknown api");
        }               
    }
    catch (Exception $e)
    {
        $response = new stdClass();
        $response->answer = new stdClass();
        $response->answer->error_code = 1;
        $response->answer->error_description = $e->getMessage();
        $response->answer->timestamp = '"'.time().'"';   
        $response->api = $request->api;
        $response->success = true;
        
        out($response);       
    }
?>