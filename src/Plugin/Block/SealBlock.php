<?php

namespace Drupal\siwecos\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a seal block.
 *
 * @Block(
 *   id = "siwecos_seal",
 *   admin_label = @Translation("Siwecos seal"),
 *   category = @Translation("Siwecos")
 * )
 */
class SealBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new SealBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'date_format' => $this->t('Date format'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['date_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Date format'),
      '#default_value' => $this->configuration['date_format'],
      '#options' => [
        'd.m.y' => $this->t('British english format (d.m.y)'),
        'y-m-d' => $this->t('ISO-8601 format (y-m-d)'),
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['date_format'] = $form_state->getValue('date_format');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#type' => 'inline_template',
      '#template' => '<a href="https://siwecos.de/scanned-by-siwecos/?data-siwecos={{ domain }}"><svg width="{{ width }}" height="{{ height }}" id="siwecos-seal" data-format="{{ date_format }}"/></a>',
      '#context' => [
        'domain' => $this->configFactory->get('siwecos.settings')->get('domain'),
        'date_format' => $this->configFactory->get('siwecos.settings')->get('domain'),
        'height' => 150,
        'width' => 58,
      ],
      '#attached' => [
        'library' => [
          'siwecos/seal',
        ],
      ],
    ];
    return $build;
  }

}
