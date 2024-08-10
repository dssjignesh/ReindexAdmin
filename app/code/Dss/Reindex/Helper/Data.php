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

namespace Dss\Reindex\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\Serializer\Json;

class Data extends AbstractHelper
{
    private const DSS_CORE_MODULE_NAME = 'Dss_Core';
    private const DSS_GRAPHQL_ENDPOINT = 'https://digitsoftsol.com/graphql';

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Manager $moduleManager
     * @param Reader $moduleReader
     * @param File $filesystem
     * @param Json $json
     */
    public function __construct(
        protected Context $context,
        private Manager $moduleManager,
        private Reader $moduleReader,
        private File $filesystem,
        private Json $json
    ) {
        parent::__construct($context);
    }

    /**
     * Core module enable
     *
     * @return bool
     */
    public function isCoreModuleEnabled(): bool
    {
        return $this->moduleManager->isEnabled(self::DSS_CORE_MODULE_NAME);
    }

    /**
     * Module name
     *
     * @return array|mixed|string
     */
    public function getModuleName(): string|array
    {
        $localModule = $this->getLocalModuleInfo();

        if (empty($localModule)) {
            return '';
        }

        $suite = null;
        if (isset($localModule['extra']['suite'])) {
            $suite = $localModule['extra']['suite'];
        }

        if ($this->moduleManager->isEnabled('Dss_Breadcrumbs') && $suite == 'seo-suite') {
            return '';
        }

        $packageName = $localModule['description'];
        $apiName = $localModule['name'];

        $remoteModuleInfo = $this->getDssModuleInfo($apiName);

        if (!empty($remoteModuleInfo) && isset($remoteModuleInfo['data']['module']['product_name'])) {
            $moduleName = $remoteModuleInfo['data']['module']['product_name'];
        }

        if (empty($moduleName)) {
            $moduleName = $packageName;
        }

        return $moduleName;
    }

    /**
     * Get installed module info by composer.json.
     *
     * @return array|bool|float|int|mixed|string|null
     */
    public function getLocalModuleInfo(): array|bool|float|int|string|null
    {
        try {
            $dir = $this->moduleReader->getModuleDir('', $this->_getModuleName());
            $file = $dir . '/composer.json';

            $string = $this->filesystem->fileGetContents($file);
            $result = $this->json->unserialize($string);

            if (!is_array($result)
                || !array_key_exists('version', $result)
                || !array_key_exists('description', $result)
            ) {
                return '';
            }

            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Dss module info
     *
     * @param string $apiName
     * @return array
     */
    protected function getDssModuleInfo(string $apiName): array
    {
        $headers = ['Content-Type: application/json'];
        $query = "
        query {
            module (api_name: \"$apiName\") {
                product_name
                product_url
            }
	    }";
        try {
            if (false === $data = $this->file->fileGetContents(
                self::DSS_GRAPHQL_ENDPOINT,
                false,
                $this->file->streamContextCreate(
                    [
                        'http' => [
                            'method' => 'POST',
                            'header' => $headers,
                            'content' => $this->json->serialize(['query' => $query]),
                        ]
                    ]
                )
            )
                ) {
                $error = error_get_last();
                // throw new \ErrorException($error['message'], $error['type']);
            }

            return $this->json->unserialize($data);
        } catch (\ErrorException $exception) {
            $this->_logger->critical($exception->getMessage());
            return [];
        } catch (\Exception $exception) {
            $this->_logger->critical($exception->getMessage());
            return [];
        }
    }
}
