<?php

namespace ETNA\Silex\Provider\Check;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class CheckServiceProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match("/check", function (Request $req) use ($app) {
            $ip = $req->getClientIp();
            if (0 === preg_match_all("#(^127\.)|(^192\.168\.)|(^10\.)|(^172\.1[6-9]\.)|(^172\.2[0-9]\.)|(^172\.3[0-1]\.)|(^::1$)#", $ip)) {
                $app->abort(403);
            }

            // host
            $stat["hostname"] = gethostname();
            $stat["ip"]       = $_SERVER['SERVER_ADDR'];
            $stat["load"]     = sys_getloadavg();
            //memory stat
            $stat['mem_percent'] = round(shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'"), 2);
            $mem_result = shell_exec("cat /proc/meminfo | grep MemTotal");

            $stat['mem_total'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
            $mem_result = shell_exec("cat /proc/meminfo | grep MemFree");

            $stat['mem_free'] = round(preg_replace("#[^0-9]+(?:\.[0-9]*)?#", "", $mem_result) / 1024 / 1024, 3);
            $stat['mem_used'] = $stat['mem_total'] - $stat['mem_free'];
            //hdd stat
            $stat['hdd_free'] = round(disk_free_space("/") / 1024 / 1024 / 1024, 2);
            $stat['hdd_total'] = round(disk_total_space("/") / 1024 / 1024/ 1024, 2);
            $stat['hdd_used'] = $stat['hdd_total'] - $stat['hdd_free'];
            $stat['hdd_percent'] = round(sprintf('%.2f',($stat['hdd_used'] / $stat['hdd_total']) * 100), 2);
            //network stat
            $stat['network_rx'] = round(trim(file_get_contents("/sys/class/net/eth0/statistics/rx_bytes")) / 1024/ 1024/ 1024, 2);
            $stat['network_tx'] = round(trim(file_get_contents("/sys/class/net/eth0/statistics/tx_bytes")) / 1024/ 1024/ 1024, 2);

            return $app->json($stat, 200);
        });

        return $controllers;
    }
}
