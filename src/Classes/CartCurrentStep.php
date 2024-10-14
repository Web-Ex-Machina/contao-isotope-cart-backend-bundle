<?php

declare(strict_types=1);

/**
 * Isotope Cart Backend Bundle for Contao Open Source CMS
 * Copyright (c) 2015-2024 Web ex Machina
 *
 * @category ContaoBundle
 * @package  Web-Ex-Machina/contao-isotope-cart-backend-bundle
 * @author   Web ex Machina <contact@webexmachina.fr>
 * @link     https://github.com/Web-Ex-Machina/contao-isotope-cart-backend-bundle/
 */

namespace IsotopeCartBackendBundle\Classes;

use Contao\System;
use Isotope\Model\ProductCollection\Cart;
use Isotope\Model\ProductCollection\Order;

class CartCurrentStep
{
    public const WEBSITE_SURFING = 'websiteSurfing';
    public const PAYING = 'paying';
    public const CHECKOUT_INFO = 'checkoutInfo';
    public const PAYMENT_METHOD_SELECTION = 'paymentMethodSelection';
    public const SHIPMENT_METHOD_SELECTION = 'shipmentMethodSelection';
    public const ADDRESS_FILLING = 'addressFilling';

    /**
     * Returns the current step of cart.
     *
     * @param Cart  $objCart       The cart
     * @param Order $objDraftOrder The linked draft order if any
     *
     * @return string|null The calculated cart step
     */
    public static function calculateCartCurrentStep(
        Cart $objCart,
        ?Order $objDraftOrder
    ): ?string {
        // default steps :
        // 1/ chose address
        // 2/ chose shipping method
        // 3/ chose payment method
        // 4/ review order
        // 5/ pay
        $step = null;
        if (!$objDraftOrder) { // no draft order = user never clicked on "order"
            $step = self::WEBSITE_SURFING;
        } elseif (null !== $objDraftOrder->locked) { // order locked = user is paying (if payment was done, the cart wouldn't exists anymore)
            $step = self::PAYING;
        } elseif (0 !== (int) $objDraftOrder->payment_id) { // user chose payment method
            $step = self::CHECKOUT_INFO;
        } elseif (0 !== (int) $objDraftOrder->shipping_id) { // user chose shipping method
            $step = self::PAYMENT_METHOD_SELECTION;
        } elseif (0 !== (int) $objDraftOrder->billing_address_id) { // user chose its address
            $step = self::SHIPMENT_METHOD_SELECTION;
        } else { // draft order exists but is "empty" = user is just starting the checkout process
            $step = self::ADDRESS_FILLING;
        }

        if (isset($GLOBALS['ISO_CART_BE_HOOKS']['calculateCartCurrentStep']) && \is_array($GLOBALS['ISO_CART_BE_HOOKS']['calculateCartCurrentStep'])) {
            foreach ($GLOBALS['ISO_CART_BE_HOOKS']['calculateCartCurrentStep'] as $callback) {
                $step = System::importStatic($callback[0])->{$callback[1]}($objCart, $objDraftOrder, $step);
            }
        }

        return $step;
    }
}
