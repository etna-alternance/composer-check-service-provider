<?php

use Behat\Behat\Context\BehatContext;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

putenv("APPLICATION_ENV=" . (false !== getenv("APPLICATION_ENV") ?: "testing"));

use ETNA\FeatureContext as EtnaFeatureContext;

/**
 * Features context
 */
class FeatureContext extends BehatContext
{
    use EtnaFeatureContext\Check;
    use EtnaFeatureContext\SilexApplication;
    use EtnaFeatureContext\setUpScenarioDirectories;
    use EtnaFeatureContext\Coverage;

    static private $_parameters;

    /**
     * Initialize context
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        self::$_parameters = $parameters;

        $this->base_url = "http://localhost:8080";
        $this->request  = [
            "headers" => [],
            "cookies" => [],
            "files"   => [],
        ];
        $this->data = [];
        $this->response = [];
    }

    /**
     * @When /^je fais un (GET|POST|PUT|DELETE) sur ((?:[a-zA-Z0-9,:!\/\.\?\&\=\+_%-]*)|"(?:[^"]+)")$/
     */
    public function jeFaisUneRequetteHTTP($method, $url, $body = null)
    {
        if ($body !== null) {
            $body = @file_get_contents($this->requests_path . $body);
            if (false === $body) {
                throw new Exception("File not found : {$this->requests_path}${body}");
            }
        }
        $this->jeFaisUneRequetteHTTPAvecDuJSON($method, $url, $body);
    }

    /**
     * @When /^je fais un (GET|POST|PUT|DELETE) sur ((?:[a-zA-Z0-9,:!\/\.\?\&\=\+_%-]*)|"(?:[^"]+)") avec le JSON suivant :$/
     */
    public function jeFaisUneRequetteHTTPAvecDuJSON($method, $url, $body)
    {
        if (preg_match('/^".*"$/', $url) === 1) {
            $url = substr($url, 1, -1);
        }

        if ($body !== null) {
            if (true === is_object($body)) {
                $body = $body->getRaw();
            }
            $this->request["headers"]["Content-Type"] = 'application/json';
            //TODO add content-length ...
        }

        $request = Request::create($this->base_url . $url, $method, [], [], [], [], $body);
        $request->headers->add($this->request["headers"]);
        $request->cookies->add($this->request["cookies"]);
        $request->files->add($this->request["files"]);

        $response = self::$silex_app->handle($request, HttpKernelInterface::MASTER_REQUEST, true);

        $result = [
            "http_code"    => $response->getStatusCode(),
            "http_message" => Response::$statusTexts[$response->getStatusCode()],
            "body"         => $response->getContent(),
            "headers"      => array_map(
                function ($item) {
                    return $item[0];
                },
                $response->headers->all()
            ),
        ];

        $this->response = $result;
    }

    /**
     * @Then /^le status HTTP devrait être (\d+)$/
     */
    public function leStatusHTTPDevraitEtre($code)
    {
        $retCode = $this->response["http_code"];
        if ("$retCode" !== "$code") {
            echo $this->response["body"];
            throw new Exception("Bad http response code {$retCode} != {$code}");
        }
    }

    /**
     * @Then /^je devrais avoir un résultat d\'API en JSON$/
     */
    public function jeDevraisAvoirUnResultatDApiEnJSON()
    {
        if ("application/json" !== $this->response["headers"]["content-type"]) {
            throw new Exception("Invalid response type");
        }
        if ($this->response['body'] === "") {
            throw new Exception("No response");
        }
        $json = json_decode($this->response['body']);

        if ($json === null && json_last_error() === true) {
            throw new Exception("Invalid response");
        }
        $this->data = $json;
    }

    /**
     * @Then /^le résultat devrait être identique au fichier "(.*)"$/
     */
    public function leResultatDevraitRessemblerAuFichier($file)
    {
        $file = realpath($this->results_path . "/" . $file);
        $this->leResultatDevraitRessemblerAuJsonSuivant(file_get_contents($file));
    }

    /**
     * @Then /^le résultat devrait être identique à "(.*)"$/
     * @Then /^le résultat devrait être identique au JSON suivant :$/
     * @Then /^le résultat devrait ressembler au JSON suivant :$/
     * @param string $string
     */
    public function leResultatDevraitRessemblerAuJsonSuivant($string)
    {
        $result = json_decode($string);
        if ($result === null) {
            throw new Exception("json_decode error");
        }

        $this->check($result, $this->data, "result", $errors);
        if (($nb_errors = count($errors)) > 0) {
            echo json_encode($this->data, JSON_PRETTY_PRINT);
            throw new Exception("{$nb_errors} errors :\n" . implode("\n", $errors));
        }
    }
}
