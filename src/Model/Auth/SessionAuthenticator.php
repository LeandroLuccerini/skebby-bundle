<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 17/05/19
 * Time: 11.34
 */

namespace Szopen\SkebbyBundle\Model\Auth;


use GuzzleHttp\Client;
use Szopen\SkebbyBundle\Exception\AuthenticationException;
use Szopen\SkebbyBundle\Exception\UnknownErrorException;
use Szopen\SkebbyBundle\Model\Endpoint;

class SessionAuthenticator extends AbstractAuthenticator
{

    /**
     * This is the key of the array used to authorise all the next API calls
     *
     * @const
     */
    const AUTH_ARRAY_SESSION_KEY = 'Session_key';

    /**
     * User key returned by Skebby service after login.
     * It's common between all the login methods.
     *
     * @var string
     */
    protected $userKey;

    /**
     * Session_key returned by Skebby service after login.
     *
     * @var string
     */
    protected $sessionKey;

    /**
     * Logs into Skebby account and sets the userKey-Session_key|Access_token couple
     *
     * @param string $username
     * @param string $password
     * @throws AuthenticationException
     * @throws UnknownErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(string $username, string $password)
    {
        $httpClient = new Client(['base_uri' => Endpoint::BASE_URL]);

        $response = $httpClient->request('GET', 'login',
            ['query' => ['username' => $username, 'password' => $password]]);

        switch($response->getStatusCode()){
            case 200:
                list($this->userKey, $this->sessionKey) = explode(";", $response->getBody());
                break;
            case 404:
                throw new AuthenticationException("Credentials are incorrect.");
                break;
            default:
                throw new UnknownErrorException("Something wrong occurred: ".$response->getBody());
                break;
        }

    }

    /**
     * Returns the couple "key" => "param" used for authentication in next API calls
     * (E.g.: "user_key" => $userKey, Session_key => $param)
     *
     * @return array
     */
    public function getAuthArray(): array
    {
        return [self::AUTH_ARRAY_USER_KEY => $this->userKey,
            self::AUTH_ARRAY_SESSION_KEY => $this->sessionKey];
    }
}