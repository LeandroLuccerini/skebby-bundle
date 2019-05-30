<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 17/05/19
 * Time: 15.53
 */

namespace Szopen\SkebbyBundle\Model\Auth;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Szopen\SkebbyBundle\Exception\AuthenticationException;
use Szopen\SkebbyBundle\Exception\NotFoundException;
use Szopen\SkebbyBundle\Exception\UnknownErrorException;
use Szopen\SkebbyBundle\Model\Endpoint;

/**
 * Class TokenAuthenticator
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Auth
 */
class TokenAuthenticator extends AuthenticatorInterface
{

    /**
     * This is onf of the key of the array used to authorise all the next API calls
     *
     * @const
     */
    const AUTH_ARRAY_ACCESS_TOKEN = 'Access_token';

    /**
     * User key returned by Skebby service after login.
     * It's common between all the login methods.
     *
     * @var string
     */
    protected $userKey;

    /**
     * Access_token returned by Skebby service after login.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Logs into Skebby account and sets the userKey-Session_key|Access_token couple
     * Returns the couple "key" => "param" used for authentication in next API calls
     * E.g.: "user_key" => $userKey, Access_token => $param
     *
     * @param string $username
     * @param string $password
     * @return array
     * @throws AuthenticationException
     * @throws UnknownErrorException
     * @throws GuzzleException
     * @throws NotFoundException
     */
    public function login(string $username, string $password): array
    {
        $httpClient = new Client(['base_uri' => Endpoint::BASE_URL]);

        try {
            $response = $httpClient->request('GET', 'token',
                ['query' => ['username' => $username, 'password' => $password]]);

            list($this->userKey, $this->accessToken) = explode(";", $response->getBody());

            return [self::AUTH_ARRAY_USER_KEY => $this->userKey, self::AUTH_ARRAY_ACCESS_TOKEN => $this->accessToken];
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 200:
                    // Do nothing
                    break;
                case 401:
                    throw new AuthenticationException("User_key, Token or Session_key are invalid or not provided", 1);
                    break;
                case 404:
                    throw new NotFoundException('User key does not exist');
                    break;
                default:
                    throw new UnknownErrorException("Something wrong occurred: " . $e->getMessage());
                    break;
            }

        }
    }
}