<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\Command\Checkout;

use BitBag\SyliusGraphqlPlugin\Command\Checkout\ChoosePaymentMethod;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Command\PaymentMethodCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

class ChoosePaymentMethodSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('orderTokenValue', 'paymentMethodCode', 'paymentId');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ChoosePaymentMethod::class);
    }

    function it_gets_subresource_id_attribute_key(): void
    {
        $this->getSubresourceIdAttributeKey()->shouldReturn('paymentId');
    }

    function it_implements_order_token_value_interface(): void
    {
        $this->shouldImplement(OrderTokenValueAwareInterface::class);
    }

    function it_implements_payment_method_code_aware_interface(): void
    {
        $this->shouldImplement(PaymentMethodCodeAwareInterface::class);
    }

    function it_implements_subresource_id_aware_interface(): void
    {
        $this->shouldImplement(SubresourceIdAwareInterface::class);
    }
}
