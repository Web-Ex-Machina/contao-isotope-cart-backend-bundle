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

use Contao\ArrayUtil;

ArrayUtil::arrayInsert(
    $GLOBALS['BE_MOD']['isotope'],
    array_search('iso_orders', array_keys($GLOBALS['BE_MOD']['isotope']), true) + 1,
    [
        'iso_carts' => [
            'tables' => [Isotope\Model\ProductCollection::getTable(), Isotope\Model\ProductCollectionItem::getTable(), Isotope\Model\ProductCollectionSurcharge::getTable(), Isotope\Model\ProductCollectionDownload::getTable(), Isotope\Model\Address::getTable()],
        ],
    ]
);

$GLOBALS['ISO_CART_BE_HOOKS'] = $GLOBALS['ISO_CART_BE_HOOKS'] ?? [];
$GLOBALS['ISO_CART_BE_HOOKS']['getCartLabel'] = $GLOBALS['ISO_CART_BE_HOOKS']['getCartLabel'] ?? [];
$GLOBALS['ISO_CART_BE_HOOKS']['calculateCartCurrentStep'] = $GLOBALS['ISO_CART_BE_HOOKS']['calculateCartCurrentStep'] ?? [];
