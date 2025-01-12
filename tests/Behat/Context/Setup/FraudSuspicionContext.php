<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusBlacklistPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;

use BitBag\SyliusBlacklistPlugin\Entity\FraudPrevention\FraudSuspicionInterface;
use BitBag\SyliusBlacklistPlugin\Factory\FraudSuspicionFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Tests\BitBag\SyliusBlacklistPlugin\Behat\Service\RandomStringGeneratorInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final class FraudSuspicionContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var RandomStringGeneratorInterface */
    private $randomStringGenerator;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var FactoryInterface */
    private $orderFactory;

    /** @var FactoryInterface */
    private $addressFactory;

    /** @var FraudSuspicionFactoryInterface */
    private $fraudSuspicionFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        RandomStringGeneratorInterface $randomStringGenerator,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        FactoryInterface $orderFactory,
        FactoryInterface $addressFactory,
        FraudSuspicionFactoryInterface $fraudSuspicionFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->randomStringGenerator = $randomStringGenerator;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->customerFactory = $customerFactory;
        $this->orderFactory = $orderFactory;
        $this->addressFactory = $addressFactory;
        $this->fraudSuspicionFactory = $fraudSuspicionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given the order :orderNumber is marked as suspicious by fake :addressType address
     */
    public function theOrderIsMarkedAsSuspiciousByFakeAddress(string $orderNumber, string $addressType): void
    {
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);

        if (empty($order)) {
            throw new \Exception('The order has not been found!');
        }

        $fraudSuspicion = $this->fraudSuspicionFactory->createForOrder($order);
        $fraudSuspicion->setAddressType($addressType);

        $this->entityManager->persist($fraudSuspicion);
        $this->entityManager->flush();
    }

    /**
     * @Given the store has customer :email with placed order with number :orderNumber
     */
    public function thereIsACustomerWithPlacedOrderInTheStore(string $email, string $orderNumber): void
    {
        $customer = $this->createCustomer($email, 'John', 'Doe');
        $order = $this->createOrder($customer, $orderNumber);
        $customer->addAddress($order->getBillingAddress());

        $this->entityManager->persist($customer);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    private function createCustomer(
        $email,
        $firstName = null,
        $lastName = null,
        \DateTimeInterface $createdAt = null,
        $phoneNumber = null
    ): CustomerInterface {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        $customer->setFirstName($firstName);
        $customer->setLastName($lastName);
        $customer->setEmail($email);
        $customer->setPhoneNumber($phoneNumber);
        if (null !== $createdAt) {
            $customer->setCreatedAt($createdAt);
        }

        $this->sharedStorage->set('customer', $customer);

        return $customer;
    }

    private function createOrder(
        CustomerInterface $customer,
        $number = null,
        ChannelInterface $channel = null,
        $localeCode = null
    ): OrderInterface {
        $order = $this->createCart($customer, $channel, $localeCode);

        if (!empty($order)) {
            $order->setNumber($number);
        }
        $address = $this->createAddress($order->getCustomer());

        $order->setBillingAddress($address);
        $order->setShippingAddress($address);
        $order->setState('new');

        $order->completeCheckout();

        return $order;
    }

    private function createCart(
        \Sylius\Component\Customer\Model\CustomerInterface $customer,
        ChannelInterface $channel = null,
        $localeCode = null
    ): OrderInterface {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setChannel($channel ?? $this->sharedStorage->get('channel'));
        $order->setLocaleCode($localeCode ?? $this->sharedStorage->get('locale')->getCode());
        $order->setCurrencyCode($order->getChannel()->getBaseCurrency()->getCode());

        return $order;
    }

    private function createAddress(CustomerInterface $customer): AddressInterface
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        
        $address->setCustomer($customer);
        $address->setFirstName($customer->getFirstName());
        $address->setLastName($customer->getLastName());
        $address->setStreet("Groove Street 21");
        $address->setCity("San Andreas");
        $address->setPostcode('00-000');
        $address->setCountryCode('US');

        return $address;
    }

    /**
     * @Given the store has fraud suspicion related to order with number :orderNumber
     */
    public function theStoreHasFraudSuspicionRelatedToOrderWithNumber(string $orderNumber): void
    {
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);

        $fraudSuspicion = $this->fraudSuspicionFactory->createForOrder($order);
        $fraudSuspicion->setAddressType(FraudSuspicionInterface::BILLING_ADDRESS_TYPE);

        $this->entityManager->persist($fraudSuspicion);
        $this->entityManager->flush();
    }
}
