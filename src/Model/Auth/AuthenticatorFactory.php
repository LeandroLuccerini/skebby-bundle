<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 17/05/19
 * Time: 15.58
 */

namespace Szopen\SkebbyBundle\Model\Auth;


use Szopen\SkebbyBundle\Exception\UnknownAuthenticatorException;

/**
 * Class AuthenticatorFactory
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Auth
 */
class AuthenticatorFactory
{

    /**
     * Creates an Authenticator based on configuration
     *
     * @param string $type
     *
     * @return AuthenticatorInterface
     *
     * @throws UnknownAuthenticatorException
     */
    public static function create(string $type) : AuthenticatorInterface
    {
        switch ($type){
            case 'session':
                return new SessionAuthenticator();
                break;
            case 'token':
                return new TokenAuthenticator();
                break;
        }

        throw new UnknownAuthenticatorException("$type is not a valid Authenticator");
    }
}