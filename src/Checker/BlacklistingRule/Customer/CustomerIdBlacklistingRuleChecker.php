<?php

declare(strict_types=1);

namespace BitBag\SyliusBlacklistPlugin\Checker\BlacklistingRule\Customer;

use BitBag\SyliusBlacklistPlugin\Checker\BlacklistingRule\BlacklistingRuleCheckerInterface;
use BitBag\SyliusBlacklistPlugin\Entity\FraudPrevention\FraudSuspicion;
use BitBag\SyliusBlacklistPlugin\Model\FraudSuspicionCommonModel;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Order\Model\OrderInterface;

class CustomerIdBlacklistingRuleChecker implements BlacklistingRuleCheckerInterface
{
    /** @var string */
    public const CUSTOMER_ID_ATTRIBUTE_NAME = 'customer_id';

    public function checkIfCustomerIsBlacklisted(QueryBuilder $builder, FraudSuspicionCommonModel $fraudSuspicionCommonModel): void
    {
        $builder
            ->andWhere('customer.id = :customerId')
            ->setParameter('customerId', $fraudSuspicionCommonModel->getCustomer()->getId())
        ;
    }

    public function getAttributeName(): string
    {
        return self::CUSTOMER_ID_ATTRIBUTE_NAME;
    }
}