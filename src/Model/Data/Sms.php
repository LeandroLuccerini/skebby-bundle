<?php
/**
 * Project: bundle-development
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 23/05/19
 * Time: 9.44
 */

namespace Szopen\SkebbyBundle\Model\Data;


use Instasent\SMSCounter\SMSCounter;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Szopen\SkebbyBundle\Exception\InvalidDeliveryDate;
use Szopen\SkebbyBundle\Exception\InvalidOrderIdException;
use Szopen\SkebbyBundle\Exception\MessageLengthException;
use Szopen\SkebbyBundle\Exception\TooMuchRecipientsException;

/**
 * Class Sms
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Data
 */
class Sms
{

    /**
     * @const
     */
    const ENCONDING_GMS = 'gsm';

    /**
     * @const
     */
    const ENCONDING_GMS_MAX_LENGTH = 1000;

    /**
     * @const
     */
    const ENCODING_UCS2 = 'ucs2';

    /**
     * @const
     */
    const ENCONDING_UCS2_MAX_LENGTH = 450;

    /**
     * @const
     */
    const MAX_RECIPIENTS = 10000;

    /**
     * @const
     */
    const ORDER_ID_REGEX = "/^[A-Z0-9\._-]{1,32}$/i";

    /**
     * @const
     */
    const DEFAULT_LOCALE = 'IT';

    /**
     * @var string
     */
    private $sender = '';

    /**
     * @var string[]
     */
    private $recipients;

    /**
     * @var string[][]
     */
    private $recipientVariables;

    /**
     * @var string
     */
    private $message;

    /**
     * The messages will be sent at the given scheduled time.
     *
     * @var \DateTime
     */
    private $deliveryTime = null;

    /**
     * The type of SMS.
     * "GP" for Classic+, "TI" for Classic, "SI" for Basic
     *
     * @var string
     */
    private $messageType;

    /**
     * Specifies a custom order ID
     * Max 32 chars, accepts only any letters, numbers, underscore, dot and dash.
     *
     * @var string
     */
    private $orderId = '';

    /**
     * Sending to an invalid recipient does not block the operation
     *
     * @var bool
     */
    private $allowInvalidRecipients;

    /**
     * The SMS encoding. Use UCS2 for non standard character sets
     *
     * @var string
     */
    private $encoding;

    /**
     * The id of the published page. Also add the %PAGESLINK____________% placeholder in the message body
     *
     * @var int
     */
    private $landingPagId;

    /**
     * The campaign name
     *
     * @var string
     */
    private $campaignName;

    /**
     * The url where the short link redirects. Also add the %SHORT_LINK% placeholder in the message body
     *
     * @var string
     */
    private $shortLinkUrl;

    /**
     * Sms constructor.
     *
     * @param bool $allowInvalidRecipients
     */
    public function __construct(bool $allowInvalidRecipients)
    {
        $this->allowInvalidRecipients = $allowInvalidRecipients;
        $this->recipients = [];
        $this->recipientVariables = [];
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
     * @return Sms
     */
    public function setSender(string $sender): Sms
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param string[] $recipients
     * @param string $locale
     *
     * @return Sms
     *
     * @throws TooMuchRecipientsException
     * @throws \libphonenumber\NumberParseException
     */
    public function setRecipients(array $recipients, string $locale = self::DEFAULT_LOCALE): Sms
    {
        if (count($recipients) > self::MAX_RECIPIENTS) {
            throw new TooMuchRecipientsException(sprintf("Triyng to send sms to %d recipients, %d allowed",
                count($recipients), self::MAX_RECIPIENTS));
        }

        // Checks for validity of every number
        if (!$this->allowInvalidRecipients) {
            foreach ($recipients as $r) {
                $this->addRecipient($r, $locale);
            }
        } else {
            $this->recipients = $recipients;
        }

        return $this;
    }

    /**
     * Add a recipient phone number.
     * It will be processed and normalizeb by giggsey/libphonenumber-for-php
     *
     * @param string $recipient
     * @param string $locale
     *
     * @return Sms
     *
     * @throws TooMuchRecipientsException
     * @throws \libphonenumber\NumberParseException
     */
    public function addRecipient(string $recipient, string $locale = self::DEFAULT_LOCALE): Sms
    {

        if ((count($this->recipients) + 1) > self::MAX_RECIPIENTS) {
            throw new TooMuchRecipientsException(sprintf("Triyng to send sms to %d recipients, %d allowed",
                (count($this->recipients) + 1), self::MAX_RECIPIENTS));
        }

        if (false !== array_search($recipient, $this->recipients)) {

            if (!$this->allowInvalidRecipients) {
                $pnu = PhoneNumberUtil::getInstance();
                $numberProto = $pnu->parse($recipient, $locale);

                $this->recipients[] = $pnu->format($numberProto, PhoneNumberFormat::E164);
            } else {
                $this->recipients[] = $recipient;
            }
        }

        return $this;
    }

    /**
     * Add a group to recipients list.
     * Group must exists in Skebby control panel.
     *
     * @param string $group
     *
     * @return Sms
     */
    public function addGroup(string $group): Sms
    {
        if (false !== array_search($group, $this->recipients)) {
            $this->recipients[] = $group;
        }

        return $this;
    }

    /**
     * Removes a recipient and its variables
     *
     * @param string $recipient
     *
     * @return Sms
     */
    public function removeRecipient(string $recipient): Sms
    {
        if (false !== $index = array_search($recipient, $this->recipients)) {
            unset($this->recipients[$index]);
        }

        unset($this->recipientVariables[$recipient]);
    }

    /**
     * It's an alias of Sms::removeRecipient
     *
     * @param string $group
     *
     * @return Sms
     */
    public function removeGroup(string $group): Sms
    {
        return $this->removeRecipient($group);
    }

    /**
     * Whether the current sms has or not recipients.
     *
     * @return bool
     */
    public function hasRecipients(): bool
    {
        return !empty($this->recipients);
    }

    /**
     * Gets the recipient variables.
     *
     * @return string[][]
     */
    public function getRecipientVariables(): array
    {
        return $this->recipientVariables;
    }

    /**
     * Sets the recipient variables for the specified recipient.
     *
     * @param string $recipient
     * @param string[] $recipientVariables
     *
     * @return Sms
     */
    public function setRecipientVariables(string $recipient, array $recipientVariables): Sms
    {
        $this->recipientVariables[$recipient] = $recipientVariables;
        return $this;
    }

    /**
     * Adds a single recipient variable for the specified recipient.
     *
     * @param string $recipient
     * @param string $variable
     * @param string $value
     *
     * @return Sms
     */
    public function addRecipientVariable(string $recipient, string $variable, string $value): Sms
    {

        if (!isset($this->recipientVariables[$recipient])) {
            $this->recipientVariables[$recipient] = [];
        }

        $this->recipientVariables[$recipient][$variable] = $value;
        return $this;
    }

    /**
     * Removes the recipient variable for the recipient specified.
     *
     * @param string $recipient
     * @param string $recipientVariable
     *
     * @return Sms
     */
    public function removeRecipientVariable(string $recipient, string $recipientVariable): Sms
    {
        unset($this->recipientVariables[$recipient][$recipientVariable]);
        return $this;
    }

    /**
     * Whether the current sms has or not recipient variables.
     *
     * @return bool
     */
    public function hasRecipientVariables(): bool
    {
        return !empty($this->recipientVariables);
    }

    /**
     * Clears the recipient variables.
     *
     * @return $this
     */
    public function clearRecipientVariables(): self
    {
        $this->recipientVariables = [];
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Sms
     *
     * @throws MessageLengthException
     */
    public function setMessage(string $message): Sms
    {

        $smsCounter = new SMSCounter();
        $report = $smsCounter->count($message);
        $exMessage = "Message is long %d that's too much for %s encoding, max is %d";

        // Sets the right encoding according to charaters used
        if ($report->encoding == SMSCounter::GSM_7BIT || $report->encoding == SMSCounter::GSM_7BIT_EX) {
            $this->encoding = self::ENCONDING_GMS;
            if ($report->lentgh > self::ENCONDING_GMS_MAX_LENGTH) {
                throw new MessageLengthException(sprintf($exMessage, $report->length, self::ENCONDING_GMS, self::ENCONDING_GMS_MAX_LENGTH));
            }
        } else {
            $this->encoding = self::ENCODING_UCS2;
            if ($report->lentgh > self::ENCONDING_UCS2_MAX_LENGTH) {
                throw new MessageLengthException(sprintf($exMessage, $report->length, self::ENCODING_UCS2, self::ENCONDING_UCS2_MAX_LENGTH));
            }
        }

        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryTime(): \DateTime
    {
        return $this->deliveryTime;
    }

    /**
     * @param \DateTime $deliveryTime
     *
     * @return Sms
     *
     * @throws InvalidDeliveryDate
     */
    public function setDeliveryTime(\DateTime $deliveryTime = null): Sms
    {
        if (null !== $deliveryTime && $deliveryTime < new \DateTime('now')) {
            throw new InvalidDeliveryDate("Delivery time can't be earlier then now");
        }

        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessageType(): string
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType
     *
     * @return Sms
     */
    public function setMessageType(string $messageType): Sms
    {
        $this->messageType = $messageType;
        return $this;
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
     * @return Sms
     *
     * @throws InvalidOrderIdException
     */
    public function setOrderId(string $orderId = ''): Sms
    {
        if (!preg_match(self::ORDER_ID_REGEX, $orderId) && !empty($orderId)) {
            $message = "Order ID is max 32 chars, accepts only letters, numbers, underscore, dot and dash. Given %s";
            throw new InvalidOrderIdException(sprintf($message, $orderId));
        }

        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmptyOrderId(): bool
    {
        return empty($this->orderId);
    }

    /**
     * @return bool
     */
    public function isAllowInvalidRecipients(): bool
    {
        return $this->allowInvalidRecipients;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return int
     */
    public function getLandingPagId(): int
    {
        return $this->landingPagId;
    }

    /**
     * @param int $landingPagId
     *
     * @return Sms
     */
    public function setLandingPagId(int $landingPagId): Sms
    {
        $this->landingPagId = $landingPagId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCampaignName(): string
    {
        return $this->campaignName;
    }

    /**
     * @param string $campaignName
     *
     * @return Sms
     */
    public function setCampaignName(string $campaignName): Sms
    {
        $this->campaignName = $campaignName;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortLinkUrl(): string
    {
        return $this->shortLinkUrl;
    }

    /**
     * @param string $shortLinkUrl
     *
     * @return Sms
     */
    public function setShortLinkUrl(string $shortLinkUrl): Sms
    {
        $this->shortLinkUrl = $shortLinkUrl;
        return $this;
    }

}