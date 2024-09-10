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

namespace Dss\Reindex\Controller\Adminhtml\Indexer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Dss\Reindex\Helper\Data;

class MassReindexData extends \Magento\Backend\App\Action
{
    /**
     * MassReindexData constructor.
     *
     * @param Context $context
     * @param IndexerRegistry $registry
     * @param Data $helper
     */
    public function __construct(
        protected Context $context,
        protected IndexerRegistry $registry,
        protected Data $helper
    ) {
        parent::__construct($context);
    }

    /**
     * Determine if action is allowed for module
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        if ($this->_request->getActionName() == 'massReindexData') {
            return $this->_authorization->isAllowed('Dss_Reindex::reindexdata')
                && $this->helper->isCoreModuleEnabled();
        }
        return false;
    }

    /**
     * Mass action reindex
     *
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(__('Please select indexers.'));
        } else {
            $startTime = microtime(true);
            foreach ($indexerIds as $indexerId) {
                try {
                    $indexer = $this->registry->get($indexerId);
                    $indexer->reindexAll();
                    $resultTime = (int)(microtime(true) - $startTime);
                    $this->messageManager->addSuccess(
                        '<div class="dss-reindex-info">'
                            . $indexer->getTitle() . ' index has been rebuilt successfully in '
                            . gmdate('H:i:s', (int)$resultTime) . '</div>'
                    );
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError(
                        $indexer->getTitle() . ' indexer process unknown error:',
                        $e
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addException(
                        $e,
                        __("We couldn't reindex data because of an error.")
                    );
                }
            }
            $this->messageManager->addSuccess(
                __('%1 indexer(s) have been rebuilt successfully
                    <a href="javascript:void(0)" class="dss-reindex-show">Show More</a>', count($indexerIds))
            );
        }
        $this->_redirect('indexer/indexer/list');
    }
}
