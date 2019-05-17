<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 17/05/19
 * Time: 12.22
 */

namespace Szopen\SkebbyBundle\Model;

/**
 * Class Endpoint
 *
 * Contains all the endpoints used in methods
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model
 */
class Endpoint
{
    /**
     * Base rest endpoint url
     *
     * @const
     */
    const BASE_URL = 'https://api.skebby.it/API/v1.0/REST/';

    /**
     * Session login endpoint
     *
     * @const
     */
    const SESSION_LOGIN = self::BASE_URL."login";

    /**
     * Token login endpoint
     *
     * @const
     */
    const TOKEN_LOGIN = self::BASE_URL."token";
}