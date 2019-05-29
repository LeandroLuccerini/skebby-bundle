<?php
/**
 * Project: bundle-development
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 29/05/19
 * Time: 16.22
 */

namespace Szopen\SkebbyBundle\Model\Response;


class SmsSend
{
    /**
     * @var string
     */
    public $result = '';

    /**
     * @var null|string
     */
    public $orderId = null;

    /**
     * @var null|string
     */
    public $internalOrderId = null;

    /**
     * @var int
     */
    public $totalSent = 0;

    /**
     * @var int
     */
    public $remainingCredits = 0;

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     *
     * @return SmsSend
     */
    public function setResult(string $result): SmsSend
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    /**
     * @param string|null $orderId
     *
     * @return SmsSend
     */
    public function setOrderId(?string $orderId): SmsSend
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInternalOrderId(): ?string
    {
        return $this->internalOrderId;
    }

    /**
     * @param string|null $internalOrderId
     *
     * @return SmsSend
     */
    public function setInternalOrderId(?string $internalOrderId): SmsSend
    {
        $this->internalOrderId = $internalOrderId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalSent(): int
    {
        return $this->totalSent;
    }

    /**
     * @param int $totalSent
     *
     * @return SmsSend
     */
    public function setTotalSent(int $totalSent): SmsSend
    {
        $this->totalSent = $totalSent;
        return $this;
    }

    /**
     * @return int
     */
    public function getRemainingCredits(): int
    {
        return $this->remainingCredits;
    }

    /**
     * @param int $remainingCredits
     *
     * @return SmsSend
     */
    public function setRemainingCredits(int $remainingCredits): SmsSend
    {
        $this->remainingCredits = $remainingCredits;
        return $this;
    }

}