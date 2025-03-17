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

namespace IsotopeCartBackendBundle\DataContainer;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\DataContainer;
use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use Isotope\Isotope;
use Isotope\Model\Config as IsotopeConfig;
use Isotope\Model\ProductCollection\Cart;
use Isotope\Model\ProductCollection\Order;
use IsotopeCartBackendBundle\Classes\CartCurrentStep;

class IsoProductCollectionContainer
{
    public function onloadCallback(): void
    {
        if ('iso_carts' === $_GET['do']) {
            $GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['sorting']['filter'] = [['type=?', 'cart']];
            $GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['sorting']['fields'] = ['id DESC'];
            $GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['label']['fields'] = ['id', 'member', 'total', 'store_id', 'cart_current_step', 'cart_last_action', 'cart_actions'];
            $GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['label']['label_callback'] = [self::class, 'getCartLabel'];
            $GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['global_operations'] = [];
            $GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['operations'] = array_filter($GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['operations'], function ($k) {return 'delete' === $k; }, \ARRAY_FILTER_USE_KEY);

            foreach ($GLOBALS['TL_DCA']['tl_iso_product_collection']['fields'] as $field => &$config) {
                if (true === ($config['filter'] ?? false)) {
                    $config['filter'] = false;
                }
                if (true === ($config['sorting'] ?? false)) {
                    $config['sorting'] = false;
                }
            }
        }
    }

    /**
     * Generate the order label and return it as string.
     *
     * @param array  $row
     * @param string $label
     *
     * @return array
     */
    public function getCartLabel($row, $label, DataContainer $dc, array $args)
    {
        $argsOriginal = $args;
        /** @var Cart $objCart */
        $objCart = Cart::findByPk($row['id']);

        if (null === $objCart) {
            return $args;
        }

        $objDraftOrder = Order::findOneBy(['source_collection_id = ?'], [$row['id']]);
        // Override system to correctly format currencies etc
        Isotope::setConfig($objCart->getRelated('config_id'));

        foreach ($GLOBALS['TL_DCA'][$dc->table]['list']['label']['fields'] as $i => $field) {
            switch ($field) {
                case 'billing_address_id':
                    if (null !== ($objAddress = $objCart->getBillingAddress())) {
                        $arrTokens = $objAddress->getTokens(Isotope::getConfig()->getBillingFieldsConfig());
                        $args[$i] = $arrTokens['hcard_fn'];
                    }
                    break;

                case 'total':
                    $args[$i] = Isotope::formatPriceWithCurrency($row['total']);
                    break;

                case 'member':
                    if (0 === $row['member']) {
                        $args[$i] = '-';
                        break;
                    }
                    $memberModel = Model::getClassFromTable('tl_member');

                    $objMember = $memberModel::findByPk($row['member']);

                    System::loadLanguageFile('tl_member');

                    $title = \sprintf(\is_array($GLOBALS['TL_LANG']['tl_member']['show'] ?? null) ? $GLOBALS['TL_LANG']['tl_member']['show'][1] : ($GLOBALS['TL_LANG']['tl_member']['show'] ?? ''), $row['member']);
                    $token = System::getContainer()->get('contao.csrf.token_manager')->getToken(System::getContainer()->getParameter('contao.csrf_token_name'))->getValue();

                    $args[$i] = '<a href="'.System::getContainer()->getParameter('contao.backend.route_prefix').'?do=member&act=show&popup=1&id='.$objMember->id.'&rt='.$token.'" title="'.$title.'" onclick="Backend.openModalIframe({\'title\':\''.StringUtil::specialchars(str_replace("'", "\\'", $title)).'\',\'url\':this.href});return false">'.$objMember->firstname.' '.$objMember->lastname.'</a>';
                    break;

                case 'cart_last_action':
                    $args[$i] = (new \DateTime())->setTimestamp((int) $row['tstamp'])->format(Config::get('datimFormat'));
                    break;
                case 'cart_current_step':
                    $step = CartCurrentStep::calculateCartCurrentStep($objCart, $objDraftOrder);
                    $args[$i] = \array_key_exists($step, $GLOBALS['TL_LANG']['ICBE']['LBL']['cartStep'])
                    ? $GLOBALS['TL_LANG']['ICBE']['LBL']['cartStep'][$step]
                    : $step;
                    break;
                case 'store_id':
                    $isoConfig = IsotopeConfig::findByPk($row['store_id']);
                    $args[$i] = $isoConfig ? $isoConfig->name : $row['store_id'];
                    break;
                case 'cart_actions':
                    $objTemplate = new BackendTemplate('be_iso_cart_actions');
                    $objTemplate->cart_id = $row['id'];

                    $objTemplate->checkout_info = ($objDraftOrder && !empty($objDraftOrder->checkout_info)) ? StringUtil::deserialize($objDraftOrder->checkout_info, true) : null;

                    $productCollectionItems = $objCart->getItems();
                    $cartItems = [];
                    foreach ($productCollectionItems as $objProductCollectionItem) {
                        $cartItems[] = ['sku' => $objProductCollectionItem->sku, 'name' => $objProductCollectionItem->getName(), 'qty' => $objProductCollectionItem->quantity, 'tax_free_price' => $objProductCollectionItem->tax_free_price, 'price' => $objProductCollectionItem->price];
                    }
                    $objTemplate->cart_items = $cartItems;

                    $args[$i] = $objTemplate->parse();
                    break;
            }
        }

        // HOOK: add custom logic
        if (isset($GLOBALS['ISO_CART_BE_HOOKS']['getCartLabel']) && \is_array($GLOBALS['ISO_CART_BE_HOOKS']['getCartLabel'])) {
            foreach ($GLOBALS['ISO_CART_BE_HOOKS']['getCartLabel'] as $callback) {
                $args = System::importStatic($callback[0])->{$callback[1]}($row, $label, $dc, $argsOriginal, $objCart, $objDraftOrder, $args);
            }
        }

        return $args;
    }
}
