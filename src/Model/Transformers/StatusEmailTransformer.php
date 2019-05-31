<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 22/05/19
 * Time: 17.39
 */

namespace Szopen\SkebbyBundle\Model\Transformers;


use Karriere\JsonDecoder\Bindings\CallbackBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;
use Szopen\SkebbyBundle\Model\Data\StatusEmail;

class StatusEmailTransformer implements Transformer
{

    /**
     * register field, array, alias and callback bindings.
     *
     * @param ClassBindings $classBindings
     * @throws \Karriere\JsonDecoder\Exceptions\InvalidBindingException
     */
    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new CallbackBinding('expiry', function($data){
            return new \DateTime($data['expiry']);
        }));

        $classBindings->register(new CallbackBinding('purchased', function($data){
            return new \DateTime($data['purchased']);
        }));
    }

    /**
     * @return string the full qualified class name that the transformer transforms
     */
    public function transforms()
    {
        return StatusEmail::class;
    }
}