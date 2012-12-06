<?php

/* Performs an update query to the triple store.
 * Used for inserting and deleting.
 */
function sparqlUpdate($query)
{
    global $storeConfig, $log;
    try 
    {
        $parameterString = "update=" . urlencode( $query );   

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $storeConfig['store_username'] . ':' . $storeConfig['store_password']);
        curl_setopt($ch, CURLOPT_URL, $storeConfig['store_endpoint']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameterString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        // doesn t work (with Fuseki !?) => no accents... curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8'); 

        
        $response = curl_exec($ch);

        echo('<br>');
        echo('$query: ' . htmlspecialchars($query));
        echo('<br>');
        echo($response);
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo('<br>');
        
        
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)
        {
            //$log->write("Update: success" . curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
            return true;
        }
        else
        {
            $log->write("Update  FAILED: " . curl_getinfo($ch, CURLINFO_CONTENT_TYPE));

            /*
            print_r('<pre>');
            echo(htmlspecialchars(urldecode($parameterString)));
            print_r($response);
            print_r('</pre>');
            */
            
            return false;
        }
        curl_close($ch);
    }
    catch (Exception $e)
    {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        return false;
    }
}

/* Sends a SELECT query to the triple store 
 * and gets the result back.
 */
function getQueryResults($query)
{
    global $storeConfig, $log;
    try 
    {
        //$db = sparql_connect( "http://dbpedia.org/sparql");
        $db = sparql_connect( "http://hxl.humanitarianresponse.info/sparql" );
        
        if( !$db )
        {
            $log->write($db->errno() . ": " . $db->error(). "\n");
            exit;
        }
        $result = $db->query($query);
        if( !$result )
        {
            $log->write($db->errno() . ": " . $db->error(). "\n");
            exit;
        }
    }
    catch (Exception $e)
    {
        $log->write('Caught exception: ',  $e->getMessage(), "\n");
    }
    return $result;
}

?>