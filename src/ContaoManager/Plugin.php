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

namespace IsotopeCartBackendBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use IsotopeCartBackendBundle\IsotopeCartBackendBundle;

/**
 * Plugin for the Contao Manager.
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(IsotopeCartBackendBundle::class)
                ->setLoadAfter([
                    ContaoCoreBundle::class,
                    'isotope',
                    'isotope_rules', // otherwise our BE menu link gets wrongly placed
                ])
                ->setReplace(['isotopecartbackend']),
        ];
    }
}
