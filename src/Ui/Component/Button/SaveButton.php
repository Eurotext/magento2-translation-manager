<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\Ui\Component\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton implements ButtonProviderInterface
{
    /** @var string */
    private $targetName;

    /** @var int */
    private $sortOrder;

    public function __construct()
    {
        $this->targetName = 'eurotext_project_form.eurotext_project_form';
        $this->sortOrder  = 20;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'          => __('Save'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->targetName,
                                'actionName' => 'save',
                                'params'     => [
                                    true,
                                    [
                                        'back' => 'continue',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'class_name'     => \Magento\Ui\Component\Control\Container::SPLIT_BUTTON,
            'options'        => $this->getOptions(),
        ];
    }

    private function getOptions(): array
    {
        $options = [
            [
                'label'          => __('Save &amp; Close'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => $this->targetName,
                                    'actionName' => 'save',
                                    'params'     => [
                                        // first param is redirect flag
                                        true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'sort_order'     => $this->sortOrder,
            ],
        ];

        return $options;
    }
}
