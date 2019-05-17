<?php
/**
 * Project: bundle-development
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 17/05/19
 * Time: 17.52
 */

namespace Szopen\SkebbyBundle\Model\Client;


/**
 * Class UserClient
 *
 * API dedicated to User
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Client
 */
class UserClient extends Client
{
    /**
     * @const
     */
    const ACTION_DASHBOARD = 'dashboard';

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Szopen\SkebbyBundle\Exception\AuthenticationException
     * @throws \Szopen\SkebbyBundle\Exception\UnknownErrorException
     */
    public function getDashboardLink(): string{
        $response = $this->executeAction(self::ACTION_DASHBOARD);

        return $response->getBody();
    }
}