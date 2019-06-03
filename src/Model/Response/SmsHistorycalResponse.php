<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 03/06/19
 * Time: 10.26
 */

namespace Szopen\SkebbyBundle\Model\Response;


use Szopen\SkebbyBundle\Exception\InvalidMessageTypeException;
use Szopen\SkebbyBundle\Model\Data\Sms;

/**
 * Class SmsHistorycalResponse
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Response
 */
class SmsHistorycalResponse
{

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $scheduled = null;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $sender;

    /**
     * @var int
     */
    private $numberOfRecipients;

    /**
     * SmsHistorycalResponse constructor.
     *
     * @param string $orderId
     * @param string $createdAt
     * @param string $type
     * @param int $numberOfRecipients
     * @param string $sender
     * @param string $scheduled
     *
     * @throws InvalidMessageTypeException
     */
    public function __construct(string $orderId,
                                string $createdAt,
                                string $type,
                                int $numberOfRecipients,
                                string $sender = '',
                                string $scheduled = '')
    {

        if(!in_array($type, [Sms::SMS_CLASSIC_KEY, Sms::SMS_BASIC_KEY, Sms::SMS_CLASSIC_PLUS_KEY])){
            throw new InvalidMessageTypeException();
        }

        $this->orderId = $orderId;
        $this->createdAt = \DateTime::createFromFormat("YmdHis", $createdAt);
        $this->type = $type;
        $this->numberOfRecipients = $numberOfRecipients;
        $this->sender = $sender;

        if(!empty($scheduled)){
            $this->scheduled = \DateTime::createFromFormat('YmdHis', $scheduled);
        }
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     *
     * @return SmsHistorycalResponse
     */
    public function setOrderId(string $orderId): SmsHistorycalResponse
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return SmsHistorycalResponse
     */
    public function setCreatedAt(\DateTime $createdAt): SmsHistorycalResponse
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getScheduled(): \DateTime
    {
        return $this->scheduled;
    }

    /**
     * @param \DateTime $scheduled
     *
     * @return SmsHistorycalResponse
     */
    public function setScheduled(\DateTime $scheduled): SmsHistorycalResponse
    {
        $this->scheduled = $scheduled;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return SmsHistorycalResponse
     *
     * @throws InvalidMessageTypeException
     */
    public function setType(string $type): SmsHistorycalResponse
    {
        if(!in_array($type, [Sms::SMS_CLASSIC_KEY, Sms::SMS_BASIC_KEY, Sms::SMS_CLASSIC_PLUS_KEY])){
            throw new InvalidMessageTypeException();
        }

        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     *
     * @return SmsHistorycalResponse
     */
    public function setSender(string $sender): SmsHistorycalResponse
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumberOfRecipients(): int
    {
        return $this->numberOfRecipients;
    }

    /**
     * @param int $numberOfRecipients
     *
     * @return SmsHistorycalResponse
     */
    public function setNumberOfRecipients(int $numberOfRecipients): SmsHistorycalResponse
    {
        $this->numberOfRecipients = $numberOfRecipients;
        return $this;
    }
}