<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Ui\Component\Button;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var string
     */
    private $confirmationMessage;

    /**
     * @var string
     */
    private $idFieldName;

    /**
     * @var string
     */
    private $deleteRoutePath;

    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param Escaper $escaper
     * @param string $confirmationMessage
     * @param string $idFieldName
     * @param string $deleteRoutePath
     * @param int $sortOrder
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        Escaper $escaper
    ) {
        $this->request             = $request;
        $this->urlBuilder          = $urlBuilder;
        $this->escaper             = $escaper;
        $this->confirmationMessage = __('Are you sure you want to delete this project?');
        $this->idFieldName         = 'id';
        $this->deleteRoutePath     = '*/*/delete';
        $this->sortOrder           = 20;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];

        $fieldId = $this->escape($this->request->getParam($this->idFieldName));

        if (null !== $fieldId) {
            $url = $this->urlBuilder->getUrl($this->deleteRoutePath);

            $message = $this->escape($this->confirmationMessage);

            $data = [
                'label'      => __('Delete'),
                'class'      => 'delete',
                'on_click'   => "deleteConfirm('{$message}', '{$url}', {data:{{$this->idFieldName}:{$fieldId}}})",
                'sort_order' => $this->sortOrder,
            ];
        }

        return $data;
    }

    private function escape($param): string
    {
        return $this->escaper->escapeJs($this->escaper->escapeHtml($param));
    }
}
