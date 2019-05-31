<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 24/05/19
 * Time: 15.19
 */

namespace Szopen\SkebbyBundle\Model\Data;


use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Szopen\SkebbyBundle\Exception\InvalidVariableTypeException;

/**
 * Class Recipient
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Dataù
 */
class Recipient implements RecipientInterface
{

    /**
     * @const
     */
    const DEFAULT_LOCALE = 'IT';

    /**
     * @var string
     */
    protected $recipient;

    /**
     * @var array
     */
    protected $variables;

    /**
     * Recipient constructor.
     *
     * @param string $recipient
     * @param string $locale
     *
     * @throws \libphonenumber\NumberParseException
     */
    public function __construct(string $recipient, string $locale = self::DEFAULT_LOCALE)
    {
        $pnu = PhoneNumberUtil::getInstance();
        $numberProto = $pnu->parse($recipient, $locale);

        $this->recipient = $pnu->format($numberProto, PhoneNumberFormat::E164);

        $this->variables = [];
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Recipient
     */
    public function addVariable(string $name, string $value): Recipient
    {
        $this->variables[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     */
    public function removeVariable(string $name)
    {
        unset($this->variables[$name]);
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Recipient
     *
     * @throws InvalidVariableTypeException
     */
    public function setVariables(array $variables): Recipient
    {
        foreach ($variables as $name => $value) {
            if (!is_scalar($value)) {
                throw new InvalidVariableTypeException("Variable $name is not a scalar");
            }

            $this->addVariable($name, $value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getVariable(string $name)
    {
        return isset($this->variables[$name]) ? $this->variables[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasVariable(string $name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $response = [];

        $response['recipient'] = $this->recipient;

        foreach ($this->variables as $name => $value) {
            $response[$name] = $value;
        }

        return $response;
    }

}