<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 24/05/19
 * Time: 15.50
 */

namespace Szopen\SkebbyBundle\Model\Data;


class Group implements RecipientInterface
{

    /**
     * @var string
     */
    protected $recipient;

    public function __construct(string $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * Returns the array form of the recipient
     *
     * @return array
     */
    public function toArray(): array
    {
        return ['recipient' => $this->recipient];
    }

    /**
     * Groups can't have variables
     *
     * @param string $name
     * @return bool
     */
    public function hasVariable(string $name): bool
    {
        return false;
    }
}