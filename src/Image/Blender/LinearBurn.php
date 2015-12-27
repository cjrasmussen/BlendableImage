<?php
namespace Manticorp\Image\Blender;

class LinearBurn extends \Manticorp\Image\Blender
{
    public function _blend($opacity = 1, $fill = 1)
    {
        $opacity = min(max($opacity,0),1);

        if($opacity === 0){
            return $this->base->getImage();
        }

        $destX = ($this->base->getWidth()  - $this->top->getWidth()) / 2;
        $destY = ($this->base->getHeight() - $this->top->getHeight()) / 2;

        $w = $this->top->getWidth();
        $h = $this->top->getHeight();

        $baseImg    = $this->base->getImage();
        $overlayImg = $this->top->getImage();

        $itcBase = $this->base->getIsTrueColor();
        $itcTop = $this->top->getIsTrueColor();

        for ($x = 0; $x < $w; ++$x) {
            for ($y = 0; $y < $h; ++$y) {

                // First get the colors for the base and top pixels.
                $baseColor = $this->normalisePixel(
                    $this->getColorAtPixel($baseImg, $x + $destX, $y + $destY, $itcBase)
                );
                $topColor  = $this->normalisePixel(
                    $this->getColorAtPixel($overlayImg, $x, $y, $itcTop)
                );

                // A+B−1
                $destColor = $baseColor;
                foreach($destColor as $key => &$color){
                    $color = max(($topColor[$key] + $color) - 1, 0);
                }
                if($opacity !== 1) {
                    $destColor = $this->opacityPixel($baseColor, $destColor, $opacity);
                }

                $destColor = $this->integerPixel($this->deNormalisePixel($destColor));

                // Now that we have a valid color index, set the pixel to that color.
                imagesetpixel(
                    $baseImg,
                    $x + $destX, $y + $destY,
                    $this->getColorIndex($baseImg, $destColor)
                );
            }
        }
        return $baseImg;
    }

    /**
     * @todo implement
     */
    public function _imagickBlend($opacity = 1, $fill = 1)
    {
        $baseImg    = $this->base->getImage();
        $overlayImg = $this->top->getImage();

        // $overlayImg->setImageOpacity($opacity);

        // $baseImg->compositeImage($overlayImg, \Imagick::COMPOSITE_T, 0, 0);

        return $baseImg;
    }
}