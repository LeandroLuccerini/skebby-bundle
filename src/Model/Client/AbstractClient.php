<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 17/05/19
 * Time: 16.44
 */

namespace Szopen\SkebbyBundle\Model\Client;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Szopen\SkebbyBundle\Exception\AuthenticationException;
use Szopen\SkebbyBundle\Exception\InvalidInputException;
use Szopen\SkebbyBundle\Exception\NotFoundException;
use Szopen\SkebbyBundle\Exception\UnknownErrorException;
use Szopen\SkebbyBundle\Model\Auth\AuthenticatorInterface;
use Szopen\SkebbyBundle\Model\Endpoint;

/**
 * Class Client
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Client
 */
abstract class AbstractClient
{

    /**
     * @const
     */
    const ACTION_METHOD_GET = 'GET';

    /**
     * @const
     */
    const ACTION_METHOD_POST = 'POST';

    /**
     * @const
     */
    const ACTION_METHOD_DELETE = 'DELETE';

    /**
     * @var AuthenticatorInterface
     */
    protected $authenticator;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Keeps the array resulted from the authentication.
     *
     * @var null|array
     */
    private $authArray = null;

    /**
     * AbstractClient constructor.
     *
     * @param string $username
     * @param string $password
     * @param AuthenticatorInterface $authenticator
     */
    public function __construct(string $username, string $password, AuthenticatorInterface $authenticator)
    {
        $this->username = $username;
        $this->password = $password;
        $this->authenticator = $authenticator;
        $this->httpClient = new HttpClient(['base_uri' => Endpoint::BASE_URL,
            'headers' => array_merge(['content-type' => 'application/json'], $this->getAuthArray())]);
    }


    /**
     * Executes the http request passing JSON data.
     * Raises exceptions based on Http Status Code.
     *
     * @param string $action
     * @param string $method
     * @param string $data
     * @return ResponseInterface
     * @throws AuthenticationException
     * @throws InvalidInputException
     * @throws NotFoundException
     * @throws UnknownErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function executeAction(string $action,
                                     string $method = self::ACTION_METHOD_GET,
                                     string $data = ''): ResponseInterface
    {
        try {
            $response = $this->httpClient->request($method, $action, [RequestOptions::BODY => $data]);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 200: // No errors
                case 201: // Message scheduled
                    // Do nothing
                    break;
                case 400:
                    throw new InvalidInputException($e->getMessage());
                case 401:
                    throw new AuthenticationException("User_key, Token or Session_key are invalid or not provided", 1);
                    break;
                case 404:
                    throw new NotFoundException();
                    break;
                default:
                    throw new UnknownErrorException("Something wrong occurred: " . $e->getMessage());
                    break;
            }
        }

        return $response;
    }

    /**
     * All the query string parameters in URL must be UTF-8 URL Encoded
     *
     * @param string $string
     * @return string
     */
    protected function urlEncode(string $string): string
    {
        return urlencode(utf8_encode($string));
    }

    /**
     * All the query string parameters in URL must be UTF-8 URL Encoded
     *
     * @param $string
     * @return string
     */
    protected function urlDecode($string): string
    {
        return utf8_decode(urldecode($string));
    }

    /**
     * Gets the Auth array used to authenticate the API call
     *
     * @return array
     */
    private function getAuthArray(): array
    {
        if (null === $this->authArray) {
            $this->authArray = $this->authenticator->login($this->username, $this->password);
        }

        return $this->authArray;
    }
}