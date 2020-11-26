<?php

declare(strict_types=1);

namespace BitBag\SyliusBlacklistPlugin\Checker\BlacklistingRule\Address;

use BitBag\SyliusBlacklistPlugin\Checker\BlacklistingRule\BlacklistingRuleCheckerInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Order\Model\OrderInterface;

class StreetBlacklistingRuleChecker implements BlacklistingRuleCheckerInterface
{
    /** @var string */
    public const STREET_ATTRIBUTE_NAME = 'street';

    public function checkIfCustomerIsBlacklisted(QueryBuilder $builder, OrderInterface $order, AddressInterface $address): void
    {
        $builder
            ->andWhere('o.street = :street')
            ->setParameter('street', $address->getStreet())
        ;
    }

    public function getAttributeName(): string
    {
        return self::STREET_ATTRIBUTE_NAME;
    }
}