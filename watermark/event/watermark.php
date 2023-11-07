<?php

use Sunlight\Image\ImageLoader;
use SunlightExtend\Watermark\Watermark;

return function (array $args) {
    if ($args['options']['resize']['w'] >= 500 && $args['options']['resize']['h'] >= 100) {

        $watermarkFile = SL_ROOT . $this->getConfig()['watermark_file'];
        if (!file_exists($watermarkFile)) {
            return;
        }

        $watermark = new Watermark($args['image'], ImageLoader::load($watermarkFile));
        if ($this->getConfig()['resize_large_watermark']) {
            $watermark->setResizeLargeWatermark(true);
        }
        $watermark->apply($this->getConfig()['watermark_position']);
    }
};
