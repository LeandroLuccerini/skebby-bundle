<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 20/05/19
 * Time: 12.22
 */

namespace Szopen\SkebbyBundle\Model\Response;

use Szopen\SkebbyBundle\Model\Data\StatusEmail;

/**
 * Class Status
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Response
 */
class Status
{

    /**
     * @const
     */
    const SMS_KEY = 'sms';

    /**
     * @const
     */
    const SMS_CLASSIC_PLUS_KEY = 'N';

    /**
     * @const
     */
    const SMS_CLASSIC_KEY = 'L';

    /**
     * @const
     */
    const SMS_FOREIGN_KEY = 'EE';

    /**
     * @const
     */
    const SMS_BASIC_KEY = 'LL';

    /**
     * @const
     */
    const SMS_ADVERTISE_KEY = 'AD';

    /**
     * @var null|float
     */
    private $money = null;

    /**
     * Amount of remaining Classic+ Sms
     *
     * @var int
     */
    private $classicPlusSms = 0;

    /**
     * Amount of remaining Cassic Sms
     *
     * @var int
     */
    private $classicSms = 0;

    /**
     * Amount of remaining Foreign Sms
     *
     * @var int
     */
    private $foreignSms = 0;

    /**
     * Amount of remaining Basic Sms
     * @var int
     */
    private $basicSms = 0;

    /**
     * Amount of remaining Advertising Sms
     *
     * @var int
     */
    private $advertisingSms = 0;

    /**
     * Don't know what's about
     *
     * @var null
     */
    private $landingPage = null;

    /**
     * @var StatusEmail
     */
    private $email;

    /**
     * @return float|null
     */
    public function getMoney(): ?float
    {
        return $this->money;
    }

    /**
     * @param float|null $money
     * @return Status
     */
    public function setMoney(?float $money): Status
    {
        $this->money = $money;
        return $this;
    }

    /**
     * @return int
     */
    public function getClassicPlusSms(): int
    {
        return $this->classicPlusSms;
    }

    /**
     * @param int $classicPlusSms
     * @return Status
     */
    public function setClassicPlusSms(int $classicPlusSms): Status
    {
        $this->classicPlusSms = $classicPlusSms;
        return $this;
    }

    /**
     * @return int
     */
    public function getClassicSms(): int
    {
        return $this->classicSms;
    }

    /**
     * @param int $classicSms
     * @return Status
     */
    public function setClassicSms(int $classicSms): Status
    {
        $this->classicSms = $classicSms;
        return $this;
    }

    /**
     * @return int
     */
    public function getForeignSms(): int
    {
        return $this->foreignSms;
    }

    /**
     * @param int $foreignSms
     * @return Status
     */
    public function setForeignSms(int $foreignSms): Status
    {
        $this->foreignSms = $foreignSms;
        return $this;
    }

    /**
     * @return int
     */
    public function getBasicSms(): int
    {
        return $this->basicSms;
    }

    /**
     * @param int $basicSms
     * @return Status
     */
    public function setBasicSms(int $basicSms): Status
    {
        $this->basicSms = $basicSms;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdvertisingSms(): int
    {
        return $this->advertisingSms;
    }

    /**
     * @param int $advertisingSms
     * @return Status
     */
    public function setAdvertisingSms(int $advertisingSms): Status
    {
        $this->advertisingSms = $advertisingSms;
        return $this;
    }

    /**
     * @return null
     */
    public function getLandingPage()
    {
        return $this->landingPage;
    }

    /**
     * @param null $landingPage
     * @return Status
     */
    public function setLandingPage($landingPage)
    {
        $this->landingPage = $landingPage;
        return $this;
    }

    /**
     * @return StatusEmail
     */
    public function getEmail(): StatusEmail
    {
        return $this->email;
    }

    /**
     * @param StatusEmail $email
     * @return Status
     */
    public function setEmail(StatusEmail $email): Status
    {
        $this->email = $email;
        return $this;
    }

}