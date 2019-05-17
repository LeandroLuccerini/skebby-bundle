<?php
/**
 * Project: bundle-development
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 17/05/19
 * Time: 14.14
 */

namespace Szopen\SkebbyBundle\Model\Auth;

/**
 * Class AuthenticatorInterface
 *
 * This is the interface of a Strategy Pattern associated to Auth
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Auth
 */
abstract class AuthenticatorInterface
{

    /**
     * This is one of the key of the array used to authorize all the next API calls
     *
     * @const
     */
    const AUTH_ARRAY_USER_KEY = 'user_key';

    /**
     * Logs into Skebby account and sets the userKey-Session_key|Access_token couple
     * Returns the couple "key" => "param" used for authentication in next API calls
     * E.g.: "user_key" => $userKey, Session_key => $param
     *
     * @param string $username
     * @param string $password
     * @return array
     */
    abstract public function login(string $username, string $password): array;
}