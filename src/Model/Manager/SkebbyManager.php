<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 30/05/19
 * Time: 14.37
 */

namespace Szopen\SkebbyBundle\Model\Manager;


use GuzzleHttp\Exception\GuzzleException;
use libphonenumber\NumberParseException;
use Szopen\SkebbyBundle\Exception\AuthenticationException;
use Szopen\SkebbyBundle\Exception\CustomSenderNotAllowedException;
use Szopen\SkebbyBundle\Exception\InvalidInputException;
use Szopen\SkebbyBundle\Exception\InvalidMessageTypeException;
use Szopen\SkebbyBundle\Exception\InvalidOrderIdException;
use Szopen\SkebbyBundle\Exception\InvalidRecipientTypeException;
use Szopen\SkebbyBundle\Exception\MessageLengthException;
use Szopen\SkebbyBundle\Exception\MissingParameterException;
use Szopen\SkebbyBundle\Exception\NotFoundException;
use Szopen\SkebbyBundle\Exception\RecipientsNotFoundException;
use Szopen\SkebbyBundle\Exception\TooMuchRecipientsException;
use Szopen\SkebbyBundle\Exception\UnknownAuthenticatorException;
use Szopen\SkebbyBundle\Exception\UnknownErrorException;
use Szopen\SkebbyBundle\Model\Auth\AuthenticatorFactory;
use Szopen\SkebbyBundle\Model\Auth\AuthenticatorInterface;
use Szopen\SkebbyBundle\Model\Client\SmsClient;
use Szopen\SkebbyBundle\Model\Client\UserClient;
use Szopen\SkebbyBundle\Model\Data\RecipientInterface;
use Szopen\SkebbyBundle\Model\Data\Sms;
use Szopen\SkebbyBundle\Model\Data\SmsRecipientDeliveryState;
use Szopen\SkebbyBundle\Model\Response\SmsHistorycalResponse;
use Szopen\SkebbyBundle\Model\Response\SmsResponse;
use Szopen\SkebbyBundle\Model\Response\Status;

/**
 * Class SkebbyManager
 * This is a wrapper class for all the clients
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Manager
 */
class SkebbyManager
{

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $authType;

    /**
     * @var string
     */
    private $messageType;

    /**
     * @var string
     */
    private $sender;

    /**
     * @var null|AuthenticatorInterface
     */
    private $authenticator = null;

    /**
     * @var null
     */
    private $userClient = null;

    private $smsClient = null;


    /**
     * SkebbyManager constructor.
     *
     * @param string $username
     * @param string $password
     * @param string $authType
     * @param string $messageType
     * @param string $sender
     * @param string $locale
     *
     * @throws UnknownAuthenticatorException
     */
    public function __construct(string $username,
                                string $password,
                                string $authType,
                                string $messageType,
                                string $sender)
    {

        $this->username = $username;
        $this->password = $password;
        $this->messageType = $messageType;
        $this->sender = $sender;
        $this->authType = $authType;

        $this->authenticator = AuthenticatorFactory::create($this->authType);

    }

    ##############################################################################
    # USER CLIENT API WRAPPER
    ##############################################################################

    /**
     * API used to retrieve the dashboard URL of the authenticated user
     *
     * @return string
     *
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws UnknownErrorException
     */
    public function getDashboard(): string
    {
        return $this->getUserClient()->getDashboard();
    }

    /**
     * Checks whether the user session is still active and valid (without renewal).
     *
     * @return bool
     *
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws UnknownErrorException
     */
    public function checkSession(): bool
    {
        return $this->getUserClient()->checkSession();
    }

    /**
     * Changes the authenticated user's password
     *
     * @param string $password
     *
     * @return bool
     *
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws UnknownErrorException
     */
    public function resetPassword(string $password): bool
    {
        return $this->getUserClient()->resetPassword($password);
    }

    /**
     * Used to retrieve the credits and other information of the user identified by the id.
     *
     * @param bool $getMoney
     *
     * @return Status
     *
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws UnknownErrorException
     */
    public function getStatus(bool $getMoney = true): Status
    {
        return $this->getUserClient()->getStatus($getMoney);
    }

    ##############################################################################
    # SMS CLIENT API WRAPPER
    ##############################################################################

    /**
     * Send an Sms to Recipients identified by a number, one by one.
     * If the message of the Sms contains at least one parameter placeholder ${paramname}, the system decides which
     * endpoint to use.
     *
     * All recipients must be of type Szopen\SkebbyBundle\Model\Data\Recipient otherwise raises
     * an InvalidRecipientTypeException exception
     *
     * @param Sms $sms
     *
     * @param bool $allowInvalidRecipents
     * @param bool $returnRemaining
     * @param bool $returnCredits
     *
     * @return SmsResponse
     *
     * @throws CustomSenderNotAllowedException
     * @throws InvalidRecipientTypeException
     * @throws MissingParameterException
     * @throws RecipientsNotFoundException
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws InvalidInputException
     * @throws InvalidOrderIdException
     * @throws NotFoundException
     * @throws UnknownErrorException
     */
    public function sendSms(Sms $sms,
                            bool $allowInvalidRecipents = false,
                            bool $returnRemaining = false,
                            bool $returnCredits = false): SmsResponse
    {
        return $this->getSmsClient()->sendSms($sms, $allowInvalidRecipents, $returnRemaining, $returnCredits);
    }

    /**
     * Send an Sms to Recipients identified by a groups
     * All recipients must by of type Szopen\SkebbyBundle\Model\Data\Group otherwise raises
     * an InvalidRecipientTypeException exception
     *
     * @param Sms $sms
     * @param bool $allowInvalidRecipents Sending to an invalid recipient does not block the operation
     * @param bool $returnRemaining
     * @param bool $returnCredits
     *
     * @return mixed
     *
     * @throws CustomSenderNotAllowedException
     * @throws InvalidRecipientTypeException
     * @throws MissingParameterException
     * @throws RecipientsNotFoundException
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws InvalidInputException
     * @throws InvalidOrderIdException
     * @throws NotFoundException
     * @throws UnknownErrorException
     */
    public function sendGroupSms(Sms $sms,
                                 bool $allowInvalidRecipents = false,
                                 bool $returnRemaining = false,
                                 bool $returnCredits = false): SmsResponse
    {
        return $this->getSmsClient()->sendGroupSms($sms, $allowInvalidRecipents, $returnRemaining, $returnCredits);
    }

    /**
     * Get informations on the SMS delivery status of the given $orderId.
     *
     * @param string $orderId
     *
     * @return SmsRecipientDeliveryState[]
     *
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws InvalidInputException
     * @throws UnknownErrorException
     */
    public function getSmsState(string $orderId): array
    {
        return $this->getSmsClient()->getSmsState($orderId);
    }

    /**
     * Deletes the SMS delivery process of the given $orderId.
     *
     * @param string $orderId
     *
     * @return bool
     *
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws InvalidInputException
     * @throws UnknownErrorException
     */
    public function deleteScheduledDelivery(string $orderId): bool
    {
        return $this->getSmsClient()->deleteScheduledDelivery($orderId);
    }

    /**
     * This API is used to retrieve the SMS messages sending history.
     *
     * @param \DateTime $from
     * @param \DateTime|null $to
     * @param int|null $page
     * @param int|null $pageSize
     *
     * @return SmsHistorycalResponse[]
     *
     * @throws NotFoundException
     * @throws GuzzleException
     * @throws AuthenticationException
     * @throws InvalidInputException
     * @throws InvalidMessageTypeException
     * @throws UnknownErrorException
     */
    public function getMessagesHistory(\DateTime $from,
                                       \DateTime $to = null,
                                       int $page = null,
                                       int $pageSize = null): array
    {
        return $this->getSmsClient()->getMessagesHistory($from, $to, $page, $pageSize);
    }

    /**
     * Creates a default Sms with parameters configured in config/packages/skebby_bundle.yaml file.
     *
     * @param string $message Message to be sent
     * @param RecipientInterface[] $recipients Array of recipients. Recipients can be added later.
     *
     * @return Sms
     *
     * @throws InvalidMessageTypeException
     * @throws MessageLengthException
     * @throws TooMuchRecipientsException
     * @throws NumberParseException
     */
    public function createDefaultSms(string $message, array $recipients = []): Sms
    {
        $sms = new Sms($this->messageType);
        $sms->setSender($this->sender);
        $sms->setMessage($message);

        if (!empty($recipients)) {
            $sms->setRecipients($recipients);
        }

        return $sms;
    }

    /**
     * @return UserClient
     */
    private function getUserClient(): UserClient
    {
        if (null === $this->userClient) {
            $this->userClient = new UserClient($this->username,
                $this->password, $this->authenticator);
        }

        return $this->userClient;
    }

    /**
     * @return SmsClient
     */
    private function getSmsClient(): SmsClient
    {
        if (null === $this->smsClient) {
            $this->smsClient = new SmsClient($this->username,
                $this->password, $this->authenticator);
        }

        return $this->smsClient;
    }

}