<?php

namespace SunlightExtend\Watermark;

use Sunlight\Plugin\Action\ConfigAction as BaseConfigAction;
use Sunlight\Util\Form;

class ConfigAction extends BaseConfigAction
{
    protected function getFields(): array
    {
        $config = $this->plugin->getConfig();
        $positions = Watermark::getPositions();

        return [
            'watermark_file' => [
                'label' => _lang('watermark.config.watermark_file'),
                'input' => '<input type="text" name="config[watermark_file]" ' . Form::restorePostValue('config[watermark_file]', $config['watermark_file']) . ' class="inputmedium">',
                'type' => 'text',
            ],
            'watermark_position' => [
                'label' => _lang('watermark.config.watermark_position'),
                'input' => _buffer(function () use ($config, $positions) { ?>
                    <select name="config[watermark_position]" class="inputmedium">
                        <?php foreach ($positions as $position) : ?>
                            <option value="<?= $position ?>"<?= $config['watermark_position'] == $position ? ' selected' : '' ?>><?= _lang('watermark.config.pos.' . $position) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php }),
                'type' => 'text',
            ],
            'resize_large_watermark' => [
                'label' => _lang('watermark.config.resize_large_watermark'),
                'input' => '<input type="checkbox" name="config[resize_large_watermark]" ' . Form::restorePostValue('config[resize_large_watermark]', $config['resize_large_watermark']) . '>',
                'type' => 'checkbox',
            ]
        ];
    }
}