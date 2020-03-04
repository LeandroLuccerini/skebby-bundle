<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 23/05/19
 * Time: 9.44
 */

namespace Szopen\SkebbyBundle\Model\Data;


use Instasent\SMSCounter\SMSCounter;
use Szopen\SkebbyBundle\Exception\InvalidDeliveryDate;
use Szopen\SkebbyBundle\Exception\InvalidOrderIdException;
use Szopen\SkebbyBundle\Exception\MessageLengthException;
use Szopen\SkebbyBundle\Exception\TooMuchRecipientsException;
use Szopen\SkebbyBundle\Exception\InvalidMessageTypeException;

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
    const PARAMETER_REGEX = '/\$\{[a-z0-9]{1,}\}/i';

    /**
     * @const
     */
    const DEFAULT_LOCALE = 'IT';

    /**
     * @const
     */
    const SMS_CLASSIC_PLUS_KEY = 'GP';

    /**
     * @const
     */
    const SMS_CLASSIC_KEY = 'TI';

    /**
     * @const
     */
    const SMS_BASIC_KEY = 'SI';

    /**
     * @var string
     */
    private $sender = '';

    /**
     * @var RecipientInterface[]
     */
    private $recipients;

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
    private $landingPageId;

    /**
     * The campaign name
     *
     * @var string
     */
    private $campaignName = '';

    /**
     * The url where the short link redirects. Also add the %SHORT_LINK% placeholder in the message body
     *
     * @var string
     */
    private $shortLinkUrl = '';

    /**
     * Array list of paramaters
     *
     * @var array
     */
    private $parameters = [];

    /**
     * Sms constructor.
     *
     * @param string $messageType
     *
     * @throws InvalidMessageTypeException
     */
    public function __construct(string $messageType)
    {
        $arrayOfTypes = [Sms::SMS_BASIC_KEY, Sms::SMS_CLASSIC_KEY, Sms::SMS_CLASSIC_PLUS_KEY];

        if (!in_array($messageType, $arrayOfTypes)) {
            $message = "Invalid message type, given %s, allowed %s";
            throw new InvalidMessageTypeException(sprintf($message, $messageType, implode(", ", $arrayOfTypes)));
        }

        $this->messageType = $messageType;

        $this->recipients = [];
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
     * @return bool
     */
    public function hasSender(): bool
    {
        return !empty($this->sender);
    }

    /**
     * @return RecipientInterface[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param RecipientInterface[] $recipients
     *
     * @return Sms
     *
     * @throws TooMuchRecipientsException
     * @throws \libphonenumber\NumberParseException
     */
    public function setRecipients(array $recipients): Sms
    {
        if (count($recipients) > self::MAX_RECIPIENTS) {
            throw new TooMuchRecipientsException(sprintf("Trying to send sms to %d recipients, %d allowed",
                count($recipients), self::MAX_RECIPIENTS));
        }

        $this->recipients = [];

        foreach ($recipients as $r) {
            if (is_a($r, RecipientInterface::class)) {
                $this->recipients[] = $r;
            }
        }

        return $this;
    }

    /**
     * Add a recipient.
     *
     * @param RecipientInterface $recipient
     *
     * @return Sms
     *
     * @throws TooMuchRecipientsException
     */
    public function addRecipient(RecipientInterface $recipient): Sms
    {

        if ((count($this->recipients) + 1) > self::MAX_RECIPIENTS) {
            throw new TooMuchRecipientsException(sprintf("Trying to send sms to %d recipients, %d allowed",
                (count($this->recipients) + 1), self::MAX_RECIPIENTS));
        }

        $this->recipients[] = $recipient;

        return $this;
    }

    /**
     * Removes a recipient based on phone number|group name
     *
     * @param string $recipient
     *
     * @return Sms
     */
    public function removeRecipient(string $recipient): Sms
    {

        for ($i = 0; $i < count($this->recipients); $i++) {
            if ($this->recipients[$i]->getRecipient() == $recipient) {
                unset($this->recipients[$i]);
                exit;
            }
        }

        return $this;
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
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Checks if a parameter exists
     *
     * @param string $name
     * @return bool
     */
    public function hasParameter(string $name): bool
    {
        return in_array($name, $this->parameters);
    }

    /**
     * Checks if the message contains parameters
     *
     * @return bool
     */
    public function hasParameters(): bool
    {
        return !empty($this->parameters);
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
            if ($report->length > self::ENCONDING_GMS_MAX_LENGTH) {
                throw new MessageLengthException(sprintf($exMessage, $report->length, self::ENCONDING_GMS, self::ENCONDING_GMS_MAX_LENGTH));
            }
        } else {
            $this->encoding = self::ENCODING_UCS2;
            if ($report->length > self::ENCONDING_UCS2_MAX_LENGTH) {
                throw new MessageLengthException(sprintf($exMessage, $report->length, self::ENCODING_UCS2, self::ENCONDING_UCS2_MAX_LENGTH));
            }
        }

        $this->message = $message;
        // Reset parameters
        $this->parameters = [];
        // Finds all the parameters placeholders
        preg_match_all(self::PARAMETER_REGEX, $message, $matches);
        // Sets all the parameters name
        foreach ($matches[0] as $paramPlaceHolder) {
            $this->parameters[] = str_replace(["$", "{", "}"], "", $paramPlaceHolder);
        }

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeliveryTime(): ?\DateTime
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
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return int
     */
    public function getLandingPageId(): int
    {
        return $this->landingPageId;
    }

    /**
     * @param int $landingPageId
     *
     * @return Sms
     */
    public function setLandingPageId(int $landingPageId): Sms
    {
        $this->landingPaegId = $landingPageId;
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
     * @return bool
     */
    public function hasCampaignName(): bool
    {
        return !empty($this->campaignName);
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

    /**
     * @return bool
     */
    public function hasShortLinkUrl(): bool
    {
        return !empty($this->shortLinkUrl);
    }
}