<?php
/**
 * @file
 * Contains Drupal\sims_channeladvisor.
 */

namespace Drupal\sims_channeladvisor;


use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Language\Language;
use Drupal\Core\Url;
use http\Exception\InvalidArgumentException;

class WidgetUrlHelper {

  const CA_BASE_URL = 'https://where-to-buy.co/widgets/core/%type%/index.html';

  /**
   * The Commerce product entity or null if we don't have one.
   *
   * @var \Drupal\commerce_product\Entity\Product|null
   */
  private $product;

  /**
   * The
   *
   * @var \Drupal\config_pages\Entity\ConfigPages
   */
  private $config;

  /**
   * The base url of widget.
   *
   * @var string
   */
  private $baseUri;


  /**
   * An array of query parameters.
   *
   * @var array
   */
  private $query;

  /**
   * UrlHelper constructor.
   *
   * There are 2 different kind of widget urls:
   * Product URL:
   * https://where-to-buy.co/widgets/core/Offers/index.html?pid=12037171&model=084691828945
   * Store Locator URL:
   * https://where-to-buy.co/widgets/core/BuyOnlineBuyLocal/index.html?pid=12037171&model=GEStoreLocator&type=local
   *
   * The common 'pid' parameter is set on admin/settings config page. The model
   * of product url is the field_model_name of the product entity. The model of
   * of store locator url is also a config page setting.
   *
   * The 'Offers' and 'BuyOnlineBuyLocal' after /core/ part is hardcoded here,
   * it doesn't need to be explicitly set.
   */
  public function __construct(Product $product = NULL) {
    $this->product = $product;
    /** @var Language $language */
    $language = !is_null($product) ? $this->product->language() : \Drupal::languageManager()->getCurrentLanguage();
    $this->config = config_pages_config('settings', $language);
    $this->setBaseUri();
    $this->setQuery();
  }

  /**
   * Generates the widget's url.
   *
   * @return \Drupal\Core\Url
   */
  public function getIframeUrl() {
    return Url::fromUri($this->baseUri, [
      'query' => $this->query,
    ]);
  }

  /**
   * Setter of the iframe url's query.
   *
   * @throw http\Exception\InvalidArgumentException
   */
  private function setQuery() {
    if (empty($this->product->field_whatever) && empty($this->config->field_ca_default_model)) {
      throw new InvalidArgumentException('ChannelAdvisor iframe url cannot be generated because neither product nor default model value found.');
    }
    $query = [
      'pid' => $this->config->field_ca_pid->value,
      'model' => !is_null($this->product) ? $this->product->field_whatever->value : $this->config->field_ca_default_model->value,
    ];

    if (!is_null($this->product)) {
      $query['type'] = 'local';
    }

    $this->query = $query;
  }

  /**
   * Creates the base url of ChannelAdvisor iframe.
   */
  private function setBaseUri() {
    $replaceTo = is_null($this->product) ? 'BuyOnlineBuyLocal' : 'Offers';
    $this->baseUri = str_replace('%type%', $replaceTo, self::CA_BASE_URL);
  }

}
