<?php
/**
 * Project: bundle-development
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 22/05/19
 * Time: 9.04
 */

namespace Szopen\SkebbyBundle\Model\Transformers;


use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;
use Szopen\SkebbyBundle\Exception\NotFoundException;
use Szopen\SkebbyBundle\Model\Data\StatusEmail;
use Szopen\SkebbyBundle\Model\Response\Status;

/**
 * Class StatusTransformer
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Transformers
 */
class StatusTransformer implements Transformer
{

    /**
     * Register field, array, alias and callback bindings.
     *
     * @param ClassBindings $classBindings
     * @throws \Karriere\JsonDecoder\Exceptions\InvalidBindingException
     */
    public function register(ClassBindings $classBindings)
    {

        // Binds Email
        $classBindings->register(new FieldBinding('email', 'email', StatusEmail::class));

        // Binds Classic Sms
        $classBindings->register(new CallbackBinding('classicSms', function ($data) {

            foreach ($data[Status::SMS_KEY] as $smsArr) {
                if($smsArr['type'] == Status::SMS_CLASSIC_KEY){
                    return $smsArr['quantity'];
                }
            }

            throw new NotFoundException(sprintf("Can't find %s type in Status response", Status::SMS_KEY));

        }));

        // Binds Classic Plus Sms
        $classBindings->register(new CallbackBinding('classicPlusSms', function ($data) {

            foreach ($data[Status::SMS_KEY] as $smsArr) {
                if($smsArr['type'] == Status::SMS_CLASSIC_PLUS_KEY){
                    return $smsArr['quantity'];
                }
            }

            throw new NotFoundException(sprintf("Can't find %s type in Status response", Status::SMS_CLASSIC_PLUS_KEY));

        }));

        // Binds Foreign Sms
        $classBindings->register(new CallbackBinding('foreignSms', function ($data) {

            foreach ($data[Status::SMS_KEY] as $smsArr) {
                if($smsArr['type'] == Status::SMS_FOREIGN_KEY){
                    return $smsArr['quantity'];
                }
            }

            throw new NotFoundException(sprintf("Can't find %s type in Status response", Status::SMS_FOREIGN_KEY));

        }));

        // Binds Basic Sms
        $classBindings->register(new CallbackBinding('basicSms', function ($data) {

            foreach ($data[Status::SMS_KEY] as $smsArr) {
                if($smsArr['type'] == Status::SMS_BASIC_KEY){
                    return $smsArr['quantity'];
                }
            }

            throw new NotFoundException(sprintf("Can't find %s type in Status response", Status::SMS_BASIC_KEY));

        }));

        // Binds Advertising Sms
        $classBindings->register(new CallbackBinding('advertisingSms', function ($data) {

            foreach ($data[Status::SMS_KEY] as $smsArr) {
                if($smsArr['type'] == Status::SMS_ADVERTISE_KEY){
                    return $smsArr['quantity'];
                }
            }

            throw new NotFoundException(sprintf("Can't find %s type in Status response", Status::SMS_ADVERTISE_KEY));

        }));
    }

    /**
     * @return string the full qualified class name that the transformer transforms
     */
    public function transforms()
    {
        return Status::class;
    }
}