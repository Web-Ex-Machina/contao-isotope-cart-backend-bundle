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

$GLOBALS['TL_DCA']['tl_iso_product_collection']['config']['onload_callback'][] = [IsotopeCartBackendBundle\DataContainer\IsoProductCollectionContainer::class, 'onloadCallback'];
$GLOBALS['TL_DCA']['tl_iso_product_collection']['fields']['cart_last_action'] = []; // fake field to allow translations to be displayed
$GLOBALS['TL_DCA']['tl_iso_product_collection']['fields']['cart_current_step'] = []; // fake field to allow translations to be displayed
$GLOBALS['TL_DCA']['tl_iso_product_collection']['fields']['cart_actions'] = []; // fake field to allow translations to be displayed
