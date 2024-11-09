<?php

namespace Magentoyan\SpinToWin\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class StatusOptions implements OptionSourceInterface {

    public function toOptionArray() {
        $generatorArray = [];

        $options = [
            'percent' => 'Percent',
            'fix' => 'Fix',
            
        ];

        foreach ($options as $k => $item)
            $generatorArray[] = [
                'value' => $k,
                'label' => $item
            ];

        return $generatorArray;
    }
}
