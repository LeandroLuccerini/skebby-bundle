<?php
/**
 * Project: skebby-bundle
 * User: Leandro Luccerini <leandro.luccerini@gmail.com>
 * Date: 24/05/19
 * Time: 15.49
 */

namespace Szopen\SkebbyBundle\Model\Data;


/**
 * Interface RecipientInterface
 *
 * @author Leandro Luccerini <leandro.luccerini@gmail.com>
 * @package Szopen\SkebbyBundle\Model\Data
 */
interface RecipientInterface
{
    /**
     * @return string
     */
    public function getRecipient(): string;

    /**
     * @param string $name
     * @return bool
     */
    public function hasVariable(string $name): bool;

    /**
     * Returns the array form of the recipient
     *
     * @return array
     */
    public function toArray(): array;
}