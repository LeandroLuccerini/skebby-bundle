<?php
/**
 * Project: bundle-development
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 23/05/19
 * Time: 17.27
 */

namespace Szopen\SkebbyBundle\Model\Client;


use Szopen\SkebbyBundle\Exception\CustomSenderNotAllowedException;
use Szopen\SkebbyBundle\Exception\MissingParameterException;
use Szopen\SkebbyBundle\Exception\RecipientsNotFoundException;
use Szopen\SkebbyBundle\Model\Data\Sms;

class SmsClient extends AbstractClient
{
    /**
     * @const
     */
    const ACTION_SEND_SMS = 'sms';

    /**
     * @param Sms $sms
     * @param bool $allowInvalidRecipents Sending to an invalid recipient does not block the operation
     * @param bool $returnRemaining
     * @param bool $returnCredits
     *
     * @return array
     *
     * @throws CustomSenderNotAllowedException
     * @throws MissingParameterException
     * @throws RecipientsNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Szopen\SkebbyBundle\Exception\AuthenticationException
     * @throws \Szopen\SkebbyBundle\Exception\NotFoundException
     * @throws \Szopen\SkebbyBundle\Exception\UnknownErrorException
     */
    public function sendSms(Sms $sms, bool $allowInvalidRecipents = false, bool $returnRemaining = false, bool $returnCredits = false)
    {
        $data = $this->prepareRequest($sms, $allowInvalidRecipents, $returnRemaining, $returnCredits);

        if($sms->hasParameters()) {
            $response = $this->executeAction(self::ACTION_SEND_SMS, self::ACTION_METHOD_POST, json_encode($data, JSON_FORCE_OBJECT));
        } else {
            $response = $this->executeAction(self::ACTION_SEND_SMS, self::ACTION_METHOD_POST, json_encode($data));
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Prepares the request to be sent
     *
     * @param Sms $sms
     * @param bool $allowInvalidRecipents
     * @param bool $returnRemaining
     * @param bool $returnCredits
     *
     * @return array
     *
     * @throws CustomSenderNotAllowedException
     * @throws RecipientsNotFoundException
     * @throws MissingParameterException
     */
    protected function prepareRequest(Sms $sms, bool $allowInvalidRecipents = false,  bool $returnRemaining = false, bool $returnCredits = false): array
    {

        if (!$sms->hasRecipients()) {
            throw new RecipientsNotFoundException("No recipients were found in sms");
        }

        $request = ['message_type' => $sms->getMessageType(),
            'message' => $sms->getMessage(),
            'allowInvalidRecipients' => $allowInvalidRecipents,
            'returnCredits' => $returnCredits,
            'returnRemaining' => $returnRemaining,
            'encoding' => $sms->getEncoding(),
        ];

        if($sms->hasParameters()){
            $request['recipients'] = $this->prepareRecipients($sms, true);
        } else {
            $request['recipient'] = $this->prepareRecipients($sms);
        }

        if ($sms->hasSender()) {

            if ($sms->getMessageType() == Sms::SMS_BASIC_KEY) {
                throw new CustomSenderNotAllowedException(sprintf("With %s type of sms you can't specify the sender", Sms::SMS_BASIC_KEY));
            }

            $request['sender'] = $sms->getSender();
        }

        if (null !== $sms->getDeliveryTime()) {
            $request['scheduled_delivery_time'] = $sms->getDeliveryTime()->format("YmdHis");
        }

        if (!$sms->isEmptyOrderId()) {
            $request['order_id'] = $sms->getOrderId();
        }

        if ($sms->hasCampaignName()) {
            $request['campaign_name'] = $sms->getCampaignName();
        }

        if ($sms->hasShortLinkUrl()) {
            $request['short_link_url'] = $sms->getShortLinkUrl();
        }

        return $request;
    }

    /**
     * Prepares the recipients to be added to the request.
     * If Sms message contains parameters in the form ${paramName}, all the Recipient(s) must contains
     * those parameters, otherwise MissingParameterException will be raised
     *
     * @param Sms $sms
     * @param bool $parameters
     *
     * @return array
     *
     * @throws MissingParameterException
     */
    private function prepareRecipients(Sms $sms, bool $parameters = false): array
    {

        $recipients = $sms->getRecipients();

        $returnRecipients = [];

        if($parameters){

            $smsParameters = $sms->getParameters();

            foreach ($recipients as $r){
                foreach ($smsParameters as $p){
                    if(!$r->hasVariable($p)){
                        $message = "Recipient %s must contain parameter '%s'";
                        throw new MissingParameterException(sprintf($message, $r->getRecipient(), $p));
                    }
                }
                $returnRecipients[] = $r->toArray();
            }

        } else {
           foreach ($recipients as $r){
               $returnRecipients[] = $r->getRecipient();
           }
        }

        return $returnRecipients;
    }
}