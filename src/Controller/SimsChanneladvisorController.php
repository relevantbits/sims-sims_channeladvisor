<?php

namespace Drupal\sims_channeladvisor\Controller;

use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\sims_channeladvisor\Services\IframeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Sims ChannelAdvisor routes.
 */
class SimsChanneladvisorController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The sims_channeladvisor.iframe_manager service.
   *
   * @var \Drupal\sims_channeladvisor\Services\IframeManager $manager
   */

  /**
   * SimsChanneladvisorController constructor.
   */
  public function __construct(IframeManager $manager) {
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('sims_channeladvisor.iframe_manager')
    );
  }


  /**
   * Builds the response.
   *
   * If product is set the result appears in modal, otherwise as a normal page.
   */
  public function build(Product $product = null) {
    $build = [
      '#theme' => 'sims_channeladvisor_embed',
      'widget' => $this->manager->getWidget($product),
    ];

    if (is_null($product)) {
      return $build;
    }

    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand($this->t('Where To Buy'), $build, ['width' => '90%']));

    return $response;
  }

}
