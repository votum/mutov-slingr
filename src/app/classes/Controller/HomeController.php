<?php

namespace MutovSlingr\Controller;
use Ospinto\dBug;

use Slim\Http\Request;
use Slim\Http\Response;


class HomeController
{

    const _API_END_POINT = 'http://api.mutov-slingr.votum-local.de/api/v1/data';
    const _TEMPLATES_FOLDER = '/var/www/mutov-slingr/app/var'; // NEED to be changed

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return string
     */
    public function indexAction(Request $request, Response $response, $args)
    {
        return 'There no place like /home';
//        return '> You suck '.$args['name'].'! #NOT';
    }


    /**
     *
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function testAction($request, $response, $args)
    {

        $testData = '{
            "numRows": 10,
            "rows": [
                        {
                        "type": "AlphaNumeric",
            "title": "Random Password",
            "settings": {
                "placeholder": "LLLxxLLLxLL"
            }
        },
        {
            "type": "AlphaNumeric",
            "title": "US Zipcode",
            "settings": {
                "placeholder": "xxxxx"
            }
        }
    ],
    "export": {
        "type": "XML",
        "settings": {
            "stripWhitespace": false,
            "recordNodeName":"test",
            "rootNodeName":"MyRoot",
            "dataStructureFormat": "simple"
        }
    }
    }';


        return $this->apiCall($testData);

    }


    /**
     * Process the json template and generate the demo data
     * @param $request
     * @param $response
     * @param $args
     */
    public function processAction($request, $response, $args)
    {
        $this->processTemplate('test.json');
    }



    protected function processTemplate($templateFileName)
    {

        $fullPath = self::_TEMPLATES_FOLDER.'/'.$templateFileName;
        $contents = file_get_contents($fullPath);

        $contents = json_decode($contents, true);
        new dbug($contents);

        $entitiesList = array();
        foreach($contents['templates'] as $template){

            $label = $template['label'];
            $definition = json_encode($template['definition']);
            $entitiesList[$label] = json_decode($this->apiCall($definition));
        }

        new dBug($entitiesList);

    }


    /**
     * @param $json
     * @return mixed
     */
    protected function apiCall($json)
    {
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, self::_API_END_POINT);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);

        return $result;
    }


    
}
