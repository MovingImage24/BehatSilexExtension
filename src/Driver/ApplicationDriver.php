<?php

namespace Mi24\Behat\SilexExtension\Driver;

use Behat\Mink\Driver\BrowserKitDriver;
use Silex\Application;
use Symfony\Component\HttpKernel\Client;

/**
 * @author Alexander Miehe <alexander.miehe@movingimage24.com>
 */
class ApplicationDriver extends BrowserKitDriver
{
    /**
     * @param Application $application
     * @param string|null $baseUrl
     */
    public function __construct(Application $application, $baseUrl = null)
    {
        parent::__construct(new Client($application), $baseUrl);
    }
}
