<?php

namespace Drupal\views_periodic_data_export\Plugin\views\style;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RedirectDestinationTrait;
use Drupal\Core\Url;
use Drupal\rest\Plugin\views\style\Serializer;
use Drupal\views_data_export\Plugin\views\style\DataExport AS ViewsStyleExport;

/**
 * A style plugin for data export views.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "periodic_data_export",
 *   title = @Translation("Periodic data export"),
 *   help = @Translation("Configurable row output for data exports."),
 *   display_types = {"data"}
 * )
 */
class DataExport extends ViewsStyleExport {

  use RedirectDestinationTrait;

  /**
   * Field labels should be enabled by default for this Style.
   *
   * @var bool
   */
  protected $defaultFieldLabels = TRUE;

  /**
   * {@inheritdoc}
   */
  public function defineOptions() {
    $options = parent::defineOptions();

    unset($options['xls_settings']);
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['formats']['#options'] = ['csv' => t('csv')];
    $form['formats']['#default_value'] = 'csv';
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {
    parent::submitOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @todo This should implement AttachableStyleInterface once
   * https://www.drupal.org/node/2779205 lands.
   */
  public function attachTo(array &$build, $display_id, Url $url, $title) {
    $url = Url::fromRoute('views_periodic_data_export.Periodic_export', ['viewId' => $this->view->id(), 'displayId' => $this->view->current_display])->toString();
    $this->view->feedIcons[] = [
      '#theme' => 'periodic_export_list',
      '#url' => $url,
      '#theme_wrappers' => [
        'container' => [
          '#attributes' => [
            'class' => [
              'views-periodic-data-export-feed',
            ],
          ],
        ],
      ]
    ];

    // Attach a link to the CSV feed, which is an alternate representation.
    $build['#attached']['html_head_link'][][] = [
      'rel' => 'alternate',
      'type' => $this->displayHandler->getMimeType(),
      'title' => $title,
      'href' => $url,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    // This is pretty close to the parent implementation.
    // Difference (noted below) stems from not being able to get anything other
    // than json rendered even when the display was set to export csv or xml.
    $rows = [];
    foreach ($this->view->result as $row_index => $row) {
      $this->view->row_index = $row_index;
      $rows[] = $this->view->rowPlugin->render($row);
    }

    unset($this->view->row_index);

    // Get the format configured in the display or fallback to json.
    // We intentionally implement this different from the parent method because
    // $this->displayHandler->getContentType() will always return json due to
    // the request's header (i.e. "accept:application/json") and
    // we want to be able to render csv data as well in accordance with
    // the data export format configured in the display.
    $format = !empty($this->options['formats']) ? reset($this->options['formats']) : 'csv';

    // If data is being exported as a CSV we give the option to not use the
    // Symfony normalize method which increases performance on large data sets.
    // This option can be configured in the CSV Settings section of the data
    // export.
    if ($format === 'csv' && $this->options['csv_settings']['use_serializer_encode_only'] == 1) {
      return $this->serializer->encode($rows, $format, ['views_style_plugin' => $this]);
    }
    else {
      return $this->serializer->serialize($rows, $format, ['views_style_plugin' => $this]);
    }
  }
}
