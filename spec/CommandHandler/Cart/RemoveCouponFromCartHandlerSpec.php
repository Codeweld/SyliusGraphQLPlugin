<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusGraphqlPlugin\CommandHandler\Cart;

use BitBag\SyliusGraphqlPlugin\Command\Cart\RemoveCouponFromCart;
use BitBag\SyliusGraphqlPlugin\CommandHandler\Cart\RemoveCouponFromCartHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RemoveCouponFromCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderProcessorInterface $orderProcessor,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $this->beConstructedWith($orderRepository, $promotionCouponRepository, $orderProcessor, $eventDispatcher);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveCouponFromCartHandler::class);
    }

    function it_is_invokable(
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon,
        PromotionCouponInterface $cartCoupon,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        PromotionInterface $promotion,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $tokenValue = 'token';
        $couponCode = 'PROMO';
        $command = new RemoveCouponFromCart($tokenValue, $couponCode);

        $orderRepository->findCartByTokenValue($tokenValue)->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => $couponCode])->willReturn($promotionCoupon);
        $promotionCoupon->getPromotion()->willReturn($promotion);

        $cart->removePromotion($promotion)->shouldBeCalledOnce();

        $cart->getPromotionCoupon()->willReturn($cartCoupon);
        $cartCoupon->getCode()->willReturn($couponCode);
        $cart->setPromotionCoupon(null)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalledOnce();

        $eventDispatcher->dispatch(Argument::any(), RemoveCouponFromCartHandler::EVENT_NAME)->willReturn(Argument::any());

        $this->__invoke($command);
    }

    function it_throws_an_exception_when_cart_not_found(
        OrderRepositoryInterface $orderRepository
    ): void {
        $tokenValue = 'token';
        $couponCode = 'PROMO';
        $command = new RemoveCouponFromCart($tokenValue, $couponCode);

        $orderRepository->findCartByTokenValue($tokenValue)->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command]);
    }

    function it_throws_an_exception_when_promotion_not_found(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        PromotionCouponInterface $promotionCoupon,
        PromotionCouponRepositoryInterface $promotionCouponRepository
    ): void {
        $tokenValue = 'token';
        $couponCode = 'PROMO';
        $command = new RemoveCouponFromCart($tokenValue, $couponCode);

        $orderRepository->findCartByTokenValue($tokenValue)->willReturn($cart);

        $promotionCouponRepository->findOneBy(['code' => $couponCode])->willReturn($promotionCoupon);
        $promotionCoupon->getPromotion()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command]);
    }
}
