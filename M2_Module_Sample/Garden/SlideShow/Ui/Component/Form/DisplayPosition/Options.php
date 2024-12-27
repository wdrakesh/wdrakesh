<?php
/**
 * Copyright © The Garden Health & Beauty Store, Inc All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Garden\SlideShow\Ui\Component\Form\DisplayPosition;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    public const SLIDER_KEY = 'slider';
    public const BRAND_KEY = 'favourite_brand';
    public const BANNER_KEY = 'cms_banner';
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SLIDER_KEY, 'label' => __('Home Slider')],
            ['value' => self::BRAND_KEY, 'label' => __('Favourite Brand')],
            ['value' => self::BANNER_KEY, 'label' => __('Home Banner')],
        ];
    }
}
