<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 22/05/19
 * Time: 17.32
 */

namespace Szopen\SkebbyBundle\Model\Data;


class StatusEmail
{

    /**
     * @var int
     */
    private $bandwidth = 0;

    /**
     * @var null|\DateTime
     */
    private $purchased = null;

    /**
     * @var string
     */
    private $billing = '';

    /**
     * @var null|\DateTime
     */
    private $expiry = null;

    /**
     * @return int
     */
    public function getBandwidth(): int
    {
        return $this->bandwidth;
    }

    /**
     * @param int $bandwidth
     * @return StatusEmail
     */
    public function setBandwidth(int $bandwidth): StatusEmail
    {
        $this->bandwidth = $bandwidth;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPurchased(): ?\DateTime
    {
        return $this->purchased;
    }

    /**
     * @param \DateTime|null $purchased
     * @return StatusEmail
     */
    public function setPurchased(?\DateTime $purchased): StatusEmail
    {
        $this->purchased = $purchased;
        return $this;
    }

    /**
     * @return string
     */
    public function getBilling(): string
    {
        return $this->billing;
    }

    /**
     * @param string $billing
     * @return StatusEmail
     */
    public function setBilling(string $billing): StatusEmail
    {
        $this->billing = $billing;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiry(): ?\DateTime
    {
        return $this->expiry;
    }

    /**
     * @param \DateTime|null $expiry
     * @return StatusEmail
     */
    public function setExpiry(?\DateTime $expiry): StatusEmail
    {
        $this->expiry = $expiry;
        return $this;
    }

}