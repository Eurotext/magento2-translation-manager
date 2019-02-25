<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Ui\Component\Listing\Columns;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ProjectActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }
        foreach ($dataSource['data']['items'] as &$item) {
            $name   = $this->getData('name');
            $status = $item['status'];
            $id     = $item['id'];

            // Edit
            $editUrl = $this->urlBuilder->getUrl('eurotext_translationmanager/project/edit', ['id' => $id]);

            $item[$name]['edit'] = [
                'href'   => $editUrl,
                'label'  => __('Edit'),
                'hidden' => false,
            ];

            // Set Status Transfer
            if ($status === ProjectInterface::STATUS_NEW) {
                $transferUrl = $this->urlBuilder->getUrl(
                    'eurotext_translationmanager/project/setStatus',
                    ['id' => $id, 'status' => ProjectInterface::STATUS_TRANSFER]
                );

                $item[$name]['transfer'] = [
                    'href'     => $transferUrl,
                    'label'    => __('Approve Transfer'),
                    'hidden'   => false,
                    'disable'  => true,
                    'disabled' => true,
                ];
            }

            // Set Status Accepted
            if ($status === ProjectInterface::STATUS_TRANSLATED) {
                $transferUrl = $this->urlBuilder->getUrl(
                    'eurotext_translationmanager/project/setStatus',
                    ['id' => $id, 'status' => ProjectInterface::STATUS_ACCEPTED]
                );

                $item[$name]['transfer'] = [
                    'href'   => $transferUrl,
                    'label'  => __('Approve Transfer'),
                    'hidden' => false,
                ];
            }

        }

        return $dataSource;
    }
}
