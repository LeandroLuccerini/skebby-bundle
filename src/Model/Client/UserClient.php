<?php
/**
 * Project: bundle-development
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 17/05/19
 * Time: 17.52
 */

namespace Szopen\SkebbyBundle\Model\Client;


use Karriere\JsonDecoder\JsonDecoder;
use Szopen\SkebbyBundle\Model\Response\Status;
use Szopen\SkebbyBundle\Model\Transformers\StatusEmailTransformer;
use Szopen\SkebbyBundle\Model\Transformers\StatusTransformer;

/**
 * Class UserClient
 *
 * API dedicated to User
 * https://developers.skebby.it/?php#user-api
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Client
 */
class UserClient extends AbstractClient
{
    /**
     * @const
     */
    const ACTION_DASHBOARD = 'dashboard';

    /**
     * @const
     */
    const ACTION_CHECK_SESSION = 'checksession';

    /**
     * @const
     */
    const ACTION_RESET_PASSWORD = 'pwdreset';

    /**
     * @const
     */
    const ACTION_STATUS = 'status';

    /**
     * API used to retrieve the dashboard URL of the authenticated user
     *
     * @return string
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Szopen\SkebbyBundle\Exception\AuthenticationException
     * @throws \Szopen\SkebbyBundle\Exception\NotFoundException
     * @throws \Szopen\SkebbyBundle\Exception\UnknownErrorException
     */
    public function getDashboard(): string
    {
        $response = $this->executeAction(self::ACTION_DASHBOARD);

        return $response->getBody();
    }

    /**
     * Checks whether the user session is still active and valid (without renewal).
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Szopen\SkebbyBundle\Exception\AuthenticationException
     * @throws \Szopen\SkebbyBundle\Exception\NotFoundException
     * @throws \Szopen\SkebbyBundle\Exception\UnknownErrorException
     */
    public function checkSession(): bool
    {
        $response = $this->executeAction(self::ACTION_CHECK_SESSION);

        return $response->getBody()->getContents();
    }

    /**
     * Changes the authenticated user’s password
     *
     * @param string $password
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Szopen\SkebbyBundle\Exception\AuthenticationException
     * @throws \Szopen\SkebbyBundle\Exception\NotFoundException
     * @throws \Szopen\SkebbyBundle\Exception\UnknownErrorException
     */
    public function resetPassword(string $password): bool
    {
        $password = $this->urlEncode($password);
        $url = sprintf(self::ACTION_RESET_PASSWORD . "?password=%s", $password);
        $response = $this->executeAction($url);

        return $response->getBody()->getContents();
    }

    /**
     * Used to retrieve the credits and other information of the user identified by the id.
     *
     * @param bool $getMoney Add user current money to response.
     *
     * @return Status
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Szopen\SkebbyBundle\Exception\AuthenticationException
     * @throws \Szopen\SkebbyBundle\Exception\UnknownErrorException
     * @throws \Szopen\SkebbyBundle\Exception\NotFoundException
     */
    public function getStatus(bool $getMoney = true): Status
    {
        // typeAliases -> Returns the actual names for the message types instead of the internal ID.
        // This is recommended to be set to true, there's the possibility to choose for retro compatibility.
        // This bundle uses new names.
        $response = $this->executeAction(self::ACTION_STATUS . "?getMoney=$getMoney&typeAliases=true");
        $r = $response->getBody()->getContents();

        $jsonDecoder = new JsonDecoder(true, true);
        $jsonDecoder->register(new StatusTransformer());
        $jsonDecoder->register(new StatusEmailTransformer());

        $status = $jsonDecoder->decode($r, Status::class);

        return $status;
    }
}