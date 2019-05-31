<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 30/05/19
 * Time: 9.56
 */

namespace Szopen\SkebbyBundle\Model\Data;

/**
 * Class SmsRecipientDeliveryState
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Data
 */
class SmsRecipientDeliveryState
{

    /**
     * @const
     */
    const WAITING = "WAITING";

    /**
     * @const
     */
    const SENT_TO_SMSC = "SENT";

    /**
     * @const
     */
    const WAITING_DELIVERY = "WAIT4DLVR";

    /**
     * @const
     */
    const SENT = "SENT";

    /**
     * @const
     */
    const DELIVERY_RECEIVED = "DLVRD";

    /**
     * @const
     */
    const TOO_MANY_SMS_FROM_USER = "TOOM4USER";

    /**
     * @const
     */
    const TOO_MANY_SMS_FOR_NUMBER = "TOOM4NUM";

    /**
     * @const
     */
    const ERROR = "ERROR";

    /**
     * @const
     */
    const TIMEOUT = "TIMEOUT";

    /**
     * @const
     */
    const UNPARSABLE_RCPT = "UNKNRCPT";

    /**
     * @const
     */
    const UNKNOWN_PREFIX = "UNKNPFX";

    /**
     * @const
     */
    const SENT_IN_DEMO_MODE = "DEMO";

    /**
     * @const
     */
    const WAITING_DELAYED = "SCHEDULED";

    /**
     * @const
     */
    const INVALID_DESTINATION = "INVALIDDST";

    /**
     * @const
     */
    const NUMBER_BLACKLISTED = "BLACKLISTED";

    /**
     * @const
     */
    const NUMBER_USER_BLACKLISTED = "BLACKLISTED";

    /**
     * @const
     */
    const SMS_REJECTED = "KO";

    /**
     * @const
     */
    const INVALID_CONTENTS = "INVALIDCONTENTS";

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $recipient;

    /**
     * @var \DateTime
     */
    public $deliveryDate = null;

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return SmsRecipientDeliveryState
     */
    public function setStatus(string $status): SmsRecipientDeliveryState
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     * @return SmsRecipientDeliveryState
     */
    public function setRecipient(string $recipient): SmsRecipientDeliveryState
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeliveryDate(): ?\DateTime
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     * @return SmsRecipientDeliveryState
     */
    public function setDeliveryDate(\DateTime $deliveryDate = null): SmsRecipientDeliveryState
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

}