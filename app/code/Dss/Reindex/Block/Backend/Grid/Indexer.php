<?php

declare(strict_types=1);

/**
 * Digit Software Solutions..
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category   Dss
 * @package    Dss_Reindex
 * @author     Extension Team
 * @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
 */

namespace Dss\Reindex\Block\Backend\Grid;

use Dss\Reindex\Helper\Data;
use Magento\Framework\View\Element\Context;

class Indexer extends \Magento\Framework\View\Element\Text
{
    /**
     * Indexer constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        protected Context $context,
        private Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Indexer html
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $script = "
        <script>
            var isCoreModuleEnabled = '{$this->helper->isCoreModuleEnabled()}';
            require(['jquery', 'domReady!'], function($) {
                'use strict';
                if (Boolean(isCoreModuleEnabled) !== true) {
                    $('#gridIndexer_massaction-select option[value=\"change_mode_reindex\"]').remove();
                }

                $('.bss-reindex-info').closest('.message-success.success').addClass('bss-hidden');
                $('.bss-reindex-show').click(function () {
                    if ($('.bss-reindex-info').length > 0) {
                        $('.bss-reindex-info').each(function () {
                            if ($(this).closest('.message-success.success').hasClass('bss-hidden')) {
                                $(this).closest('.message-success.success').removeClass('bss-hidden');
                            } else {
                                $(this).closest('.message-success.success').addClass('bss-hidden');
                            }
                        });
                    }
                });
            });
        </script>
        <style>
            .bss-hidden{
                display: none;
            }
        </style>";
        return $script;
    }
}
