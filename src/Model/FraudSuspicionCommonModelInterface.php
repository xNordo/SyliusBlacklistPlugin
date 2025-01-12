<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusBlacklistPlugin\Model;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

interface FraudSuspicionCommonModelInterface
{
    public function getOrder(): ?OrderInterface;

    public function setOrder(?OrderInterface $order): void;

    public function getCustomer(): ?CustomerInterface;

    public function setCustomer(CustomerInterface $customer): void;

    public function getCompany(): ?string;

    public function setCompany(?string $company);

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName);

    public function getLastName(): ?string;

    public function setLastName(?string $lastName);

    public function getEmail(): ?string;

    public function setEmail(?string $email);

    public function getPhoneNumber(): ?string;

    public function setPhoneNumber(?string $phoneNumber): void;

    public function getStreet(): ?string;

    public function setStreet(?string $street);

    public function getCity(): ?string;

    public function setCity(?string $city);

    public function getProvince(): ?string;

    public function setProvince(?string $province);

    public function getCountry(): ?string;

    public function setCountry(?string $country);

    public function getPostcode(): ?string;

    public function setPostcode(?string $postcode): void;

    public function getCustomerIp(): ?string;

    public function setCustomerIp(?string $customerIp): void;
}