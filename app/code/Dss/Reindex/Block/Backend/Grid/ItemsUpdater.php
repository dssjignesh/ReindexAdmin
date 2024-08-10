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

class ItemsUpdater extends \Magento\Indexer\Block\Backend\Grid\ItemsUpdater
{
    /**
     * Update Itemupdater
     *
     * @param string $argument
     * @return array
     */
    public function update($argument): array
    {
        if (false === $this->authorization->isAllowed('Magento_Indexer::changeMode')) {
            unset($argument['change_mode_onthefly']);
            unset($argument['change_mode_changelog']);
        }
        if (false === $this->authorization->isAllowed('Dss_Reindex::reindexdata')) {
            unset($argument['change_mode_reindex']);
        }
        return $argument;
    }
}
