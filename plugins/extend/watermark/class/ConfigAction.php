<?php

namespace SunlightExtend\Watermark;

use Sunlight\Plugin\Action\ConfigAction as BaseConfigAction;
use Sunlight\Util\Form;
use Sunlight\Util\Request;

class ConfigAction extends BaseConfigAction
{
    protected function getFields(): array
    {
        $config = $this->plugin->getConfig();
        $position = $config['watermark_position'] ?? Watermark::POS_CENTER;
        $positions = [];
        foreach (Watermark::getPositions() as $pos) {
            $positions[$pos] = _lang('watermark.config.pos.' . $pos);
        }

        return [
            'watermark_file' => [
                'label' => _lang('watermark.config.watermark_file'),
                'input' => '<input type="text" name="config[watermark_file]" ' . Request::post('config[watermark_file]', $config['watermark_file']) . ' class="inputmedium">',
                'type' => 'text',
            ],
            'watermark_position' => [
                'label' => _lang('watermark.config.watermark_position'),
                'input' => Form::select('config[watermark_position]', $positions, $position, ['class' => 'inputmedium']),
                'type' => 'text',
            ],
            'resize_large_watermark' => [
                'label' => _lang('watermark.config.resize_large_watermark'),
                'input' => '<input type="checkbox" name="config[resize_large_watermark]" ' . Request::post('config[resize_large_watermark]', $config['resize_large_watermark']) . '>',
                'type' => 'checkbox',
            ]
        ];
    }
}