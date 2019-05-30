<?php
/**
 * Project: bundle-development
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 29/05/19
 * Time: 17.31
 */

namespace Szopen\SkebbyBundle\Model\Transformers;


use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;
use Szopen\SkebbyBundle\Model\Response\SmsResponse;

class SmsResponseTransformer implements Transformer
{

    /**
     * register field, array, alias and callback bindings.
     *
     * @param ClassBindings $classBindings
     * @throws \Karriere\JsonDecoder\Exceptions\InvalidBindingException
     */
    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new AliasBinding('orderId', 'order_id', true));
        $classBindings->register(new AliasBinding('internalOrderId', 'internal_order_id'));
        $classBindings->register(new AliasBinding('totalSent', 'total_sent', true));
        $classBindings->register(new AliasBinding('remainingCredits', 'remaining_credits'));

    }

    /**
     * @return string the full qualified class name that the transformer transforms
     */
    public function transforms()
    {
        return SmsResponse::class;
    }
}