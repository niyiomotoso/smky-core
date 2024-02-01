<?php
/**
 * =======================================================================================
 *                           GemFramework (c) GemPixel
 * ---------------------------------------------------------------------------------------
 *  This software is packaged with an exclusive framework as such distribution
 *  or modification of this framework is not allowed before prior consent from
 *  GemPixel. If you find that this framework is packaged in a software not distributed
 *  by GemPixel or authorized parties, you must not use this software and contact GemPixel
 *  at https://gempixel.com/contact to inform them of this misuse.
 * =======================================================================================
 *
 * @package GemPixel\Premium-URL-Shortener
 * @author GemPixel (https://gempixel.com)
 * @license https://gempixel.com/licenses
 * @link https://gempixel.com
 */

declare(strict_types = 1);

namespace Helpers\Qr;

/**
 * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
 * another product without prior authorization, we will take actions to take it down. We will not 
 * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
 */
final class FrameGenerator{
    /**
     * Frame Constructor
     * 
     * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
     * another product without prior authorization, we will take actions to take it down. We will not 
     * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     */
    static public function build(string $qr, int $size, array $frame){

        $type = $frame['type'] ?? null; 
        $color = $frame['color'] ?? '#000000'; 
        $text = $frame['text'] ?? null; 
        $textcolor = $frame['textcolor'] ?? '#ffffff'; 
        $bg = $frame['bg'] ?? '#ffffff';
        $font = $frame['font'] ?? 'arial';

        $list = [
			'window' => [self::class, 'window'],
			'popup' => [self::class, 'popup'],
			'camera' => [self::class, 'camera'],
			'phone' => [self::class, 'phone'],
			'arrow' => [self::class, 'arrow'],
			'labeled' => [self::class, 'labeled'],
		];

		if($extended = \Core\Plugin::dispatch('qrframes.extend')){
			foreach($extended as $fn){
				$list = array_merge($list, $fn);
			}
		}

		if(isset($list[$type])) return call_user_func_array($list[$type], [$qr, $size, $color, $text, $textcolor, $bg, $font]);

		return $qr;
    }
    /**
     * Prepare QR code
     *
     * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
     * another product without prior authorization, we will take actions to take it down. We will not 
     * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
     * 
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     * @param string $qr
     * @return $qr
     */
    private static function prepare(string $qr){
        $qr = str_replace(['<?xml version="1.0" encoding="utf-8"?>', "\n"], '', $qr);
        $qr = strip_tags($qr, '<g><path><image><linearGradient><stop><rect><radialGradient><defs>');
        return $qr;
    }
    /**
     * Return QR
     *
     * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
     * another product without prior authorization, we will take actions to take it down. We will not 
     * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     * @param string $qr
     * @param integer $size
     * @param string $frameColor
     * @param string|null $text
     * @param string|null $textColor
     * @param string $bgColor
     * @param string $font
     * @return $qr
     */
    private static function window($qr, $size, $frameColor = "#000000", $text = null, $textColor = null, $bgColor = "#ffffff", $font = "Arial"){
        
        $qr = self::prepare($qr);
        
        $width = $size;
        $height = round(0.96*$size);
        $scale = round(0.034*$size);
        $frameWidth = round($size*0.59284);
        $halfPoint = (($size/2) - ($frameWidth/2))/$scale;
        $frameHeight = round($size*0.59284);
        $qrScale = 0.65;
        $qrXY = [round(0.262*$size, 4), round(0.345*$size, 4)];
        $fontsize = 0.1*$size;
        $fontY = 0.105*$size;

        $length = strlen($text);
        
        if($length > 9){        
            if($length < 15) {
                $fontY = 0.99*$fontY;
                $fontsize =  0.8 * $fontsize;
            } else {
                $fontY = 0.99*$fontY;
                $fontsize =  0.7 * $fontsize;
            }
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'.("\n").'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.("\n").'<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'"><g transform="scale('.$scale.') translate(2.5 0.1)" fill="'.$frameColor.'"><path d="M 1.3 28 L 22.6 28 C 23.3 28 23.9 27.4 23.9 26.7 L 24 1.4 C 24 0.7 23.33 -0.04 22.63 -0.04 L 1.4 0 C 0.7 0 0.1 0.6 0 1.3 L 0 26.6 C -0.1 27.4 0.5 28 1.3 28 Z M 1 6 C 1 5.4 1.5 5 2 5 L 22 5 C 22.6 5 23 5.5 23 6 L 23 26 C 23 26.6 22.5 27 22 27 L 2 27 C 1.4 27 1 26.5 1 26 L 1 6 Z"/></g>'.($text && !empty($text) ? '<text x="50%" y="'.$fontY.'" dominant-baseline="middle" text-anchor="middle" style="font-size:'.$fontsize.'px;fill:'.$textColor.';font-family:'.$font.',sans-serif;font-weight:bold;">'.$text.'</text>': '').'<rect x="'.(0.118*$size).'" y="'.(0.174*$size).'" width="'.(0.75*$size).'" height="'.(0.748*$size).'" fill="'.$bgColor.'" rx="20" /><g transform="scale('.$qrScale.') translate('.implode(', ', $qrXY).')">'.$qr.'</g></svg>';
    }
    /**
     * Popup Frame
     * 
     * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
     * another product without prior authorization, we will take actions to take it down. We will not 
     * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     * @param string $qr
     * @param integer $size
     * @param string $frameColor
     * @param string|null $text
     * @param string|null $textColor
     * @param string $bgColor
     * @param string $font
     * @return $qr
     */
    private static function popup(string $qr, int $size, $frameColor = "#000000", string $text = null, string $textColor = null, $bgColor = "#ffffff", $font = "Arial"){    
        
        $qr = self::prepare($qr);

        $width = $size;
        $height = round(0.96*$size);
        $scale = round(0.0315*$size);
        $frameWidth = round($size*0.59284);
        $halfPoint = (($size/2) - ($frameWidth/2))/$scale;
        $frameHeight = round($size*0.59284);
        $qrScale = 0.68;
        $qrXY = [round(0.228*$size, 4), round(0.35*$size, 4)];
        $fontsize = 0.08*$size;
        $fontY = 0.09*$size;

        $length = strlen($text);
        
        if($length > 9){
            
            if($length < 15) {
                $fontY = 0.99*$fontY;
                $fontsize =  0.8 * $fontsize;
            } else {
                $fontY = 0.99*$fontY;
                $fontsize =  0.7 * $fontsize;
            }
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'.("\n").'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.("\n").'<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'"><g transform="scale('.$scale.') translate(3.5 0)" fill="'.$frameColor.'"><path d="M22.7,6L1.3,6C0.6,6,0,6.6,0,7.3l0,21.3C0,29.4,0.6,30,1.3,30l21.3,0c0.7,0,1.3-0.6,1.3-1.3l0-21.3 C24,6.6,23.4,6,22.7,6z M23,28c0,0.6-0.5,1-1,1L2,29c-0.6,0-1-0.5-1-1V8c0-0.6,0.5-1,1-1l20,0c0.6,0,1,0.5,1,1V28z"/><path d="M23,0H1C0.4,0,0,0.4,0,1v3c0,0.5,0.4,1,1,1h10l1,1l1-1h10c0.5,0,1-0.4,1-1V1C24,0.4,23.6,0,23,0z"/></g>'.($text ? '<text x="50%" y="'.$fontY.'" dominant-baseline="middle" text-anchor="middle" style="font-size:'.$fontsize.'px;fill:'.$textColor.';font-family:'.$font.', sans-serif;font-weight:bold;">'.$text.'</text>': '').'<rect x="'.(0.14*$size).'" y="'.(0.224*$size).'" width="'.(0.71*$size).'" height="'.(0.71*$size).'" fill="'.$bgColor.'" rx="20" /><g transform="scale('.$qrScale.') translate('.implode(', ', $qrXY).')">'.$qr.'</g></svg>';
    }
    /**
     * Camera
     *
     * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
     * another product without prior authorization, we will take actions to take it down. We will not 
     * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
     * 
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     * @param string $qr
     * @param integer $size
     * @param string $frameColor
     * @param string|null $text
     * @param string|null $textColor
     * @param string $bgColor
     * @param string $font
     * @return void
     */
    private static function camera(string $qr, int $size, $frameColor = "#000000", string $text = null, string $textColor = null, $bgColor = "#ffffff", $font = "Arial"){    
        
        $qr = self::prepare($qr);

        $width = $size;
        $height = round(0.96*$size);
        $scale = round(0.0032*$size, 3);
        $frameWidth = round($size*0.59284);
        $halfPoint = (($size/2) - ($frameWidth/2))/$scale;
        $frameHeight = round($size*0.59284);
        $qrScale = 0.55;
        $qrXY = [round(0.39*$size, 4), round(0.6155*$size, 4)];
        $fontsize = 0.08*$size;
        $fontY = 0.11*$size;
        $length = strlen($text);
        
        if($length > 9){
            
            if($length < 15) {
                $fontY = 0.99*$fontY;
                $fontsize =  0.8 * $fontsize;
            } else {
                $fontY = 0.99*$fontY;
                $fontsize =  0.7 * $fontsize;
            }
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'.("\n").'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.("\n").'<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'"><g transform="scale('.$scale.') translate(3.5 0)" fill="'.$frameColor.'"><path d="M224.88,93.12h19.39a5,5,0,0,1,5,5v18.73H254V98.12a9.68,9.68,0,0,0-9.68-9.68H224.88Z"></path><path d="M50.73,116.85V98.12a5,5,0,0,1,5-5H73.8V88.44H55.73a9.68,9.68,0,0,0-9.68,9.68v18.73Z"></path><path d="M73.8,291.67H55.73a5,5,0,0,1-5-5V267.94H46.05v18.73a9.68,9.68,0,0,0,9.68,9.68H73.8Z"></path><path d="M249.27,267.94v18.73a5,5,0,0,1-5,5H224.88v4.68h19.39a9.68,9.68,0,0,0,9.68-9.68V267.94Z"></path><path d="M244.75,3.65H55.45A9.25,9.25,0,0,0,46.2,12.9V54.46a9.25,9.25,0,0,0,9.25,9.26H126a2.32,2.32,0,0,1,1.64.67l20.74,20.74a2.33,2.33,0,0,0,3.28,0l20.75-20.74a2.28,2.28,0,0,1,1.64-.67h70.58a9.25,9.25,0,0,0,9.25-9.26V12.9A9.18,9.18,0,0,0,244.75,3.65Z"></path></g>'.($text ? '<text x="50%" y="'.$fontY.'" dominant-baseline="middle" text-anchor="middle" style="font-size:'.$fontsize.'px;fill:'.$textColor.';font-family:'.$font.', sans-serif;font-weight:bold;">'.$text.'</text>': '').'<rect x="'.(0.205*$size).'" y="'.(0.33*$size).'" width="'.(0.57*$size).'" height="'.(0.57*$size).'" fill="'.$bgColor.'" rx="10" /><g transform="scale('.$qrScale.') translate('.implode(', ', $qrXY).')">'.$qr.'</g></svg>';
    }
    /**
     * Phone Frame
     * 
     * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
     * another product without prior authorization, we will take actions to take it down. We will not 
     * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
     *
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     * @param string $qr
     * @param integer $size
     * @param string $frameColor
     * @param string|null $text
     * @param string|null $textColor
     * @param string $bgColor
     * @param string $font
     * @return void
     */
    private static function phone(string $qr, int $size, $frameColor = "#000000", string $text = null, string $textColor = null, $bgColor = "#ffffff", $font = "Arial"){    
        $qr = self::prepare($qr);

        $width = $size;
        $height = round(0.96*$size);
        $scale = round(0.0032*$size, 3);
        $frameWidth = round($size*0.59284);
        $halfPoint = (($size/2) - ($frameWidth/2))/$scale;
        $frameHeight = round($size*0.59284);
        $qrScale = 0.55;
        $qrXY = [round(0.39*$size, 4), round(0.3155*$size, 4)];
        $fontsize = 0.08*$size;
        $fontY = 0.87*$size;
        $length = strlen($text);
        
        if($length > 9){
            
            if($length < 15) {
                $fontY = 0.99*$fontY;
                $fontsize =  0.8 * $fontsize;
            } else {
                $fontY = 0.99*$fontY;
                $fontsize =  0.7 * $fontsize;
            }
        }
        return '<?xml version="1.0" encoding="UTF-8"?>'.("\n").'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.("\n").'<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'"><g transform="scale('.$scale.') translate(3.5 0)" fill="'.$frameColor.'"><path d="M57.6,251.64H56.27V37.3H57.6Zm185.47,0h1.34V37.3h-1.34Z"></path><path d="M220.31,1.06H80.36a24.08,24.08,0,0,0-24.09,24.1V39.41H244.41V25.16A24.09,24.09,0,0,0,220.31,1.06Zm-51.94,21.1H132.3a2,2,0,0,1,0-4h36.07a2,2,0,0,1,0,4Z"></path><path d="M164.93,241.1l-14.32-12.52L135.9,241.1H56.27v33.3a24.07,24.07,0,0,0,24.09,24.09h140a24.08,24.08,0,0,0,24.1-24.09V241.1Z"></path></g>'.($text ? '<text x="50%" y="'.$fontY.'" dominant-baseline="middle" text-anchor="middle" style="font-size:'.$fontsize.'px;fill:'.$textColor.';font-family:'.$font.', sans-serif;font-weight:bold;">'.$text.'</text>': '').'<rect x="'.(0.195*$size).'" y="'.(0.126*$size).'" width="'.(0.595*$size).'" height="'.(0.650*$size).'" fill="'.$bgColor.'" /><g transform="scale('.$qrScale.') translate('.implode(', ', $qrXY).')">'.$qr.'</g></svg>';
    }
    /**
     * Arrow
     *
     * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
     * another product without prior authorization, we will take actions to take it down. We will not 
     * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
     * 
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     * @param string $qr
     * @param integer $size
     * @param string $frameColor
     * @param string|null $text
     * @param string|null $textColor
     * @param string $bgColor
     * @param string $font
     * @return void
     */
    private static function arrow(string $qr, int $size, $frameColor = "#000000", string $text = null, string $textColor = null, $bgColor = "#ffffff", $font = "Arial"){  

        $qr = self::prepare($qr);
        
        $width = $size;
        $height = round(0.96*$size);
        $scale = round(0.0032*$size, 3);
        $frameWidth = round($size*0.59284);
        $halfPoint = (($size/2) - ($frameWidth/2))/$scale;
        $frameHeight = round($size*0.59284);
        $qrScale = 0.7;
        $qrXY = [round(0.29*$size, 4), round(0.1*$size, 4)];
        $fontsize = 0.08*$size;
        $fontY = 0.87*$size;

        return '<?xml version="1.0" encoding="UTF-8"?>'.("\n").'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.("\n").'<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'"><g transform="scale('.$scale.') translate(-30 0)" fill="'.$frameColor.'"><path d="M 74.713 178.459 C 74.83 176.468 75.016 174.408 75.193 172.417 C 75.369 170.425 75.663 168.424 76.026 166.49 C 76.388 164.557 76.8 162.623 77.27 160.738 C 77.741 158.982 78.378 157.273 79.171 155.633 C 79.23 155.459 79.298 155.218 79.357 154.986 C 79.741 153.118 78.509 151.3 76.613 150.935 L 76.437 150.877 C 76.265 150.832 76.086 150.812 75.908 150.819 C 75.722 150.819 75.604 150.761 75.428 150.761 C 75.252 150.761 74.83 150.703 74.536 150.703 C 73.939 150.703 73.351 150.761 72.753 150.761 C 71.567 150.877 70.431 151.003 69.304 151.177 C 67.058 151.521 64.834 151.989 62.641 152.578 C 60.499 153.201 58.395 153.947 56.341 154.812 C 55.361 155.218 54.313 155.778 53.304 156.165 C 52.294 156.552 51.344 157.219 50.364 157.683 C 49.869 157.968 49.872 158.675 50.37 158.956 C 50.529 159.045 50.715 159.074 50.893 159.037 L 50.962 159.037 C 52.03 158.805 53.039 158.505 54.048 158.273 C 55.057 158.041 56.135 157.809 57.144 157.683 C 59.202 157.302 61.285 157.063 63.376 156.968 C 64.689 156.91 66.051 156.91 67.364 156.91 C 64.662 160.025 62.289 163.404 60.28 166.993 C 57.655 171.725 55.506 176.698 53.862 181.843 C 50.608 192.017 48.956 202.623 48.963 213.292 C 48.92 223.887 50.573 234.423 53.862 244.509 C 57.21 254.531 62.369 263.873 69.088 272.082 C 69.39 272.457 69.934 272.537 70.333 272.265 C 70.741 271.979 70.821 271.414 70.509 271.028 C 64.329 262.805 59.717 253.541 56.9 243.688 C 51.512 223.976 52.405 203.105 59.457 183.912 C 61.201 179.206 63.393 174.675 66.002 170.377 C 67.626 167.709 69.499 165.198 71.597 162.875 C 71.597 164.044 71.597 165.156 71.656 166.336 C 71.773 168.443 71.891 170.503 72.126 172.552 C 72.361 174.602 72.606 176.661 72.959 178.71 C 73.026 179.156 73.396 179.497 73.85 179.532 C 74.317 179.36 74.649 178.947 74.713 178.459 Z"></path></g>'.($text ? '<text x="20%" y="'.$fontY.'" style="font-size:'.$fontsize.'px;fill:'.$textColor.';font-family:'.$font.', sans-serif;font-weight:bold;">'.$text.'</text>': '').'<g transform="scale('.$qrScale.') translate('.implode(', ', $qrXY).')">'.$qr.'</g></svg>';
    }
    /**
     * Labeled
     *
     * This is a proprietary code owned by GemPixel coded for Premium URL Shortener, exclusively. If this code is detected in 
     * another product without prior authorization, we will take actions to take it down. We will not 
     * tolerate plagiarism. If you want to use the code, you will need to ask us permission.
     * 
     * @author GemPixel <https://gempixel.com> 
     * @version 7.1
     * @param string $qr
     * @param integer $size
     * @param string $frameColor
     * @param string|null $text
     * @param string|null $textColor
     * @param string $bgColor
     * @param string $font
     * @return void
     */
    private static function labeled(string $qr, int $size, $frameColor = "#000000", string $text = null, string $textColor = null, $bgColor = "#ffffff", $font = "Arial"){  

        $qr = self::prepare($qr);
        
        $width = $size;
        $height = round(0.96*$size);
        $scale = round(0.0032*$size, 3);
        $frameWidth = round($size*0.59284);
        $halfPoint = (($size/2) - ($frameWidth/2))/$scale;
        $frameHeight = round($size*0.59284);
        $qrScale = 0.60;
        $qrXY = [round(0.32*$size, 4), round(0.12*$size, 4)];
        $fontsize = 0.08*$size;
        $fontY = 0.89*$size;
        $length = strlen($text);
        
        if($length > 9){

            if($length < 15) {
                $fontY = 0.99*$fontY;
                $fontsize =  0.8 * $fontsize;
            } else {
                $fontY = 0.99*$fontY;
                $fontsize =  0.7 * $fontsize;
            }
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'.("\n").'<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">'.("\n").'<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" xmlns:xlink="http://www.w3.org/1999/xlink" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'"><g transform="scale('.$scale.') translate(3.5 0)" fill="'.$frameColor.'"><path d="M253.83.69H46.09A11.28,11.28,0,0,0,34.77,12V219.83a11.33,11.33,0,0,0,11.32,11.31H253.91a11.33,11.33,0,0,0,11.32-11.31V12A11.41,11.41,0,0,0,253.83.69Zm2.64,215.59a6.1,6.1,0,0,1-6.11,6.11H49.55a6.1,6.1,0,0,1-6.11-6.11V15.47a6.1,6.1,0,0,1,6.11-6.11H250.36a6.09,6.09,0,0,1,6.11,6.11Z"></path><path id="IconCircleOutline" d="M64.42,246.09A23.53,23.53,0,1,0,88,269.62a23.47,23.47,0,0,0-23.53-23.53Z" fill-opacity="0"></path><path id="PhoneIcon" d="M74.57,254.59v29.73a3.39,3.39,0,0,1-3.38,3.38H56.57a3.39,3.39,0,0,1-3.38-3.38V254.59a3.39,3.39,0,0,1,3.38-3.38H71.19A3.46,3.46,0,0,1,74.57,254.59Zm-15.11.17A1.57,1.57,0,0,0,61,256.33h5.62a1.57,1.57,0,0,0,1.56-1.57,1.62,1.62,0,0,0-1.56-1.57H61.11A1.59,1.59,0,0,0,59.46,254.76ZM72,258.64l-16.43-.17v22H72Zm-10.4,25.43a2.23,2.23,0,1,0,2.23-2.23A2.22,2.22,0,0,0,61.61,284.07Z"></path><path id="PhoneIconBlack" d="M74.57,254.59v29.73a3.39,3.39,0,0,1-3.38,3.38H56.57a3.39,3.39,0,0,1-3.38-3.38V254.59a3.39,3.39,0,0,1,3.38-3.38H71.19A3.46,3.46,0,0,1,74.57,254.59Zm-15.11.17A1.57,1.57,0,0,0,61,256.33h5.62a1.57,1.57,0,0,0,1.56-1.57,1.62,1.62,0,0,0-1.56-1.57H61.11A1.59,1.59,0,0,0,59.46,254.76ZM72,258.64l-16.43-.17v22H72Zm-10.4,25.43a2.23,2.23,0,1,0,2.23-2.23A2.22,2.22,0,0,0,61.61,284.07Z" fill-opacity="0"></path><path d="M235.5,240H64.42a29.68,29.68,0,0,0,0,59.36h171A29.68,29.68,0,0,0,235.5,240ZM64.42,293.15A23.53,23.53,0,1,1,88,269.62,23.47,23.47,0,0,1,64.42,293.15Z"></path></g>'.($text ? '<text x="32%" y="'.$fontY.'" style="font-size:'.$fontsize.'px;fill:'.$textColor.';font-family:'.$font.', sans-serif;font-weight:bold;">'.$text.'</text>': '').'<rect x="'.(0.15*$size).'" y="'.(0.03*$size).'" width="'.(0.682*$size).'" height="'.(0.682*$size).'" fill="'.$bgColor.'" rx="20" /><g transform="scale('.$qrScale.') translate('.implode(', ', $qrXY).')">'.$qr.'</g></svg>';
    }
}


