<?php
declare(strict_types=1);
/**
 * @copyright see PROJECT_LICENSE.txt
 *
 * @see PROJECT_LICENSE.txt
 */

namespace Eurotext\TranslationManager\System\Source;

use Eurotext\TranslationManager\Api\Data\ProjectInterface;
use Magento\Framework\Data\OptionSourceInterface;

class ProjectStatusSource implements OptionSourceInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => ProjectInterface::STATUS_NEW,
                'label' => ProjectInterface::STATUS_NEW,
            ],
            [
                'value' => ProjectInterface::STATUS_TRANSFER,
                'label' => ProjectInterface::STATUS_TRANSFER,
            ],
            [
                'value' => ProjectInterface::STATUS_EXPORTED,
                'label' => ProjectInterface::STATUS_EXPORTED,
            ],
            [
                'value' => ProjectInterface::STATUS_TRANSLATED,
                'label' => ProjectInterface::STATUS_TRANSLATED,
            ],
            [
                'value' => ProjectInterface::STATUS_ACCEPTED,
                'label' => ProjectInterface::STATUS_ACCEPTED,
            ],
            [
                'value' => ProjectInterface::STATUS_IMPORTED,
                'label' => ProjectInterface::STATUS_IMPORTED,
            ],
            [
                'value' => ProjectInterface::STATUS_ERROR,
                'label' => ProjectInterface::STATUS_ERROR,
            ],
        ];
    }
}