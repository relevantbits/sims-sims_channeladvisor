<?php

namespace Drupal\sims_channeladvisor\Services;

use Drupal\commerce_product\Entity\Product;
use Drupal\sims_channeladvisor\WidgetUrlHelper;

/**
 * IframeManager service.
 */
class IframeManager {

  /**
   * Gets the renderable array of widget's iframe .
   */
  public function getWidget(Product $product = NULL) {
    $widgetUrl = new WidgetUrlHelper($product);
    return [
      '#type' => 'html_tag',
      '#tag' => 'iframe',
      '#attributes' => [
        'src' => $widgetUrl->getIframeUrl()->toString(),
        'frameborder' => 0,
        'scrolling' => 'no',
      ],
    ];
  }

}
