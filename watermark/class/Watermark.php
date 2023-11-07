<?php

namespace SunlightExtend\Watermark;

use Sunlight\Image\Image;

class Watermark
{
    public const POS_TOP_CENTER = 'top-center';
    public const POS_TOP_LEFT = 'top-left';
    public const POS_TOP_RIGHT = 'top-right';
    public const POS_CENTER = 'center';
    public const POS_CENTER_LEFT = 'center-left';
    public const POS_CENTER_RIGHT = 'center-right';
    public const POS_BOTTOM_CENTER = 'bottom-center';
    public const POS_BOTTOM_LEFT = 'bottom-left';
    public const POS_BOTTOM_RIGHT = 'bottom-right';

    /** @var Image */
    private $source;
    /** @var Image */
    private $watermark;
    /** @var bool */
    private $resizeLargeWatermark = false;

    public function __construct(Image $source, Image $watermark)
    {
        $this->source = $source;
        $this->watermark = $watermark;
    }

    public static function getPositions(): array
    {
        return [
            self::POS_TOP_CENTER,
            self::POS_TOP_LEFT,
            self::POS_TOP_RIGHT,
            self::POS_CENTER,
            self::POS_CENTER_LEFT,
            self::POS_CENTER_RIGHT,
            self::POS_BOTTOM_CENTER,
            self::POS_BOTTOM_LEFT,
            self::POS_BOTTOM_RIGHT,
        ];
    }

    private function getPositionCoordinate(Image $source, Image $watermark, string $position): array
    {
        // default
        $positionX = 0;
        $positionY = 0;

        switch ($position) {
            case self::POS_TOP_CENTER:
                $positionX = ($source->width / 2) - ($watermark->width / 2);
                // $positionY = 0;
                break;
            case self::POS_TOP_RIGHT:
                $positionX = $source->width - $watermark->width;
                // $positionY = 0;
                break;
            case self::POS_CENTER:
                $positionX = ($source->width / 2) - ($watermark->width / 2);
                $positionY = ($source->height / 2) - ($watermark->height / 2);
                break;
            case self::POS_CENTER_LEFT:
                // $positionX = 0;
                $positionY = ($source->height / 2) - ($watermark->height / 2);
                break;
            case self::POS_CENTER_RIGHT:
                $positionX = $source->width - $watermark->width;
                $positionY = ($source->height / 2) - ($watermark->height / 2);
                break;
            case self::POS_BOTTOM_CENTER:
                $positionX = ($source->width / 2) - ($watermark->width / 2);
                $positionY = $source->height - $watermark->height;
                break;
            case self::POS_BOTTOM_LEFT:
                // $positionX = 0;
                $positionY = $source->height - $watermark->height;
                break;
            case self::POS_BOTTOM_RIGHT:
                $positionX = ($source->width - $watermark->width) - 5;
                $positionY = ($source->height - $watermark->height) - 5;
                break;
            case self::POS_TOP_LEFT:
            default:
                // $positionX = 0;
                // $positionY = 0;
                break;
        }

        return ['x' => (int)$positionX, 'y' => (int)$positionY];
    }

    public function setResizeLargeWatermark(bool $resizeLargeWatermark): void
    {
        $this->resizeLargeWatermark = $resizeLargeWatermark;
    }

    /**
     * Supported parameters:
     *
     * - top-center     - position of the watermark at the top centre
     * - top-left       - position of the watermark at the top left
     * - top-right      - position of the watermark at the top right
     * - center         - position of the watermark centered
     * - center-left    - position of the watermark at the center left
     * - center-right   - position of the watermark at the center right
     * - bottom-center  - position of the watermark at the bottom center
     * - bottom-left    - position of the watermark at the bottom left
     * - bottom-right   - position of the watermark at the bottom right
     */
    public function apply(string $position = self::POS_CENTER): void
    {
        imagealphablending($this->source->resource, true);
        imagesavealpha($this->source->resource, true);

        $watermark = $this->resizeLargeWatermark();

        $watermarkPositions = $this->getPositionCoordinate(
            $this->source,
            $watermark,
            $position
        );

        // image merge
        imagecopy(
            $this->source->resource,
            $watermark->resource,
            $watermarkPositions['x'],
            $watermarkPositions['y'],
            0,
            0,
            $watermark->width,
            $watermark->height
        );
    }

    /**
     * Reduce the watermark to one third of the original image
     * @return Image
     */
    private function resizeLargeWatermark(): Image
    {
        $sourceWidth = $this->source->width;
        $sourceHeight = $this->source->height;
        $watermarkWidth = $this->watermark->width;
        $watermarkHeight = $this->watermark->height;

        // compare the dimensions and shrink the watermark if it is larger
        if (
            $this->resizeLargeWatermark
            && ($watermarkWidth > $sourceWidth || $watermarkHeight > $sourceHeight)
        ) {
            // determine whether the watermark is portrait or landscape
            if ($watermarkWidth > $watermarkHeight) {
                $newWidth = (int)($sourceWidth / 3);
                $newHeight = (int)($watermarkHeight * ($newWidth / $watermarkWidth));
            } else {
                $newHeight = (int)($sourceHeight / 3);
                $newWidth = (int)($watermarkWidth * ($newHeight / $watermarkHeight));
            }

            // create a new watermark with a modified size
            $newWatermark = imagecreatetruecolor($newWidth, $newHeight);

            // transparent color
            imagesavealpha($newWatermark, true);
            $transparentColor = imagecolorallocatealpha($newWatermark, 0, 0, 0, 127);
            imagefill($newWatermark, 0, 0, $transparentColor);

            // resize
            imagecopyresampled($newWatermark, $this->watermark->resource, 0, 0, 0, 0, $newWidth, $newHeight, $watermarkWidth, $watermarkHeight);

            return new Image($newWatermark, $newWidth, $newHeight);
        }
        // return original watermark
        return $this->watermark;
    }
}