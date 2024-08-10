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

namespace Dss\Reindex\Model\System\Message;

use Dss\Reindex\Helper\Data;

class CoreModuleRequired implements \Magento\Framework\Notification\MessageInterface
{
    public const MESSAGE_IDENTITY = 'dss_core_module_required';

    /**
     * CoreModuleRequired constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        public Data $helper
    ) {
    }

    /**
     * Retrieve unique system message identity
     *
     * @return string
     */
    public function getIdentity(): string
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether the system message should be shown
     *
     * @return bool
     */
    public function isDisplayed(): bool
    {
        // The message will be shown
        return !$this->helper->isCoreModuleEnabled();
    }

    /**
     * Retrieve system message text
     *
     * @return string
     */
    public function getText(): string
    {
        $moduleName = $this->helper->getModuleName();
        $text = __(
            '<b>Your module "%1" can not work without Dss\'s
                Core Module included in the package</b>',
            $moduleName
        );
        $script =
            '<script>
                setTimeout(function() {
                    jQuery("button.message-system-action-dropdown").trigger("click");
                }, 100);
            </script>';
        return $text . $script;
    }

    /**
     * Retrieve system message severity
     * Possible default system message types:
     * - MessageInterface::SEVERITY_CRITICAL
     * - MessageInterface::SEVERITY_MAJOR
     * - MessageInterface::SEVERITY_MINOR
     * - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity(): int
    {
        return self::SEVERITY_MAJOR;
    }
}
