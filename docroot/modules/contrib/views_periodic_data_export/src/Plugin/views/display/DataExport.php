<?php

namespace Drupal\views_periodic_data_export\Plugin\views\display;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views_data_export\Plugin\views\display\DataExport AS viewsDataExport;
use Drupal\views\Views;
use Drupal\views\ViewExecutable;
use Drupal\Core\Datetime\DateHelper;
use Drupal\Core\File\FileSystemInterface;
use Drupal\views_periodic_data_export\Services\ViewsPeriodicDataExportService;

/**
 * Provides a data export display plugin.
 *
 * This overrides the REST Export display to make labeling clearer on the admin
 * UI, and to allow attaching of these to other displays.
 *
 * @ingroup views_display_plugins
 *
 * @ViewsDisplay(
 *   id = "periodic_data_export",
 *   title = @Translation("Periodic data export"),
 *   help = @Translation("Export the view results to a file via queue. Can handle very large result sets."),
 *   periodic_export = TRUE,
 *   admin = @Translation("Periodic data export")
 * )
 */
class DataExport extends  viewsDataExport
{
  const queue_limit_min = 1000;

  /**
   * {@inheritdoc}
   */
  public static function buildResponse($view_id, $display_id, array $args = [], &$view = [])
  {
    $view = Views::getView($view_id);
    $view->setDisplay($display_id);

    $url = \Drupal\Core\Url::fromRoute('views_periodic_data_export.Periodic_export', ['viewId' => $view_id, 'displayId' => $display_id])->toString();
    $response = new RedirectResponse($url);
    $response->send();
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions()
  {
    $options = parent::defineOptions();
    $options['queue_interval']['default'] = 'daily';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function optionsSummary(&$categories, &$options)
  {
    parent::optionsSummary($categories, $options);
    $options['path']['value'] = $this->getPath();
    // Doesn't make sense to have a pager for data export so remove it.
    unset($categories["pager"]);

    switch ($this->getOption('export_method')) {
      case 'queue':
        $options['export_method']['value'] = $this->t('Periodic queue @value', ['@value' => $this->getOption('queue_interval')]);
        break;
    }
  }

  public function getPath(){
    return Url::fromRoute('views_periodic_data_export.Periodic_export', ['viewId' => $this->view->id(), 'displayId' => $this->view->current_display])->toString();
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state)
  {
    parent::buildOptionsForm($form, $form_state);
    $form['automatic_download']['#access'] = $form['include_query_params']['#access']
      = $form['custom_redirect_path']['#access'] = $form['redirect_path']['#access']
      = $form['redirect_to_display']['#access'] = $form['redirect_fieldset']['#access']
      = $form['automatic_download']['#access'] = FALSE;

    $form['path']['#type'] = 'item';
    $form['path']['#default_value'] = $form['path']['#markup'] = $this->getPath();

    // Remove the 'serializer' option to avoid confusion.
    switch ($form_state->get('section')) {

      case 'export_method':
        $form['export_method']['queue']['#description'] = $this->t('Periodic queue export.');
        $form['export_method']['#options'] += ['queue' => $this->t('Queue')];

        $form['queue_limit'] = [
          '#type' => 'number',
          '#attributes' => ['min' => SELF::queue_limit_min, 'max' => 5000, 'step' => 100],
          '#description' => $this->t("Views query limit, Min 1000, Step 100."),
          '#default_value' => isset($this->options['queue_limit']) ? $this->options['queue_limit'] : SELF::queue_limit_min,
          '#required' => TRUE
        ];

        $form['queue_interval'] = [
          '#type' => 'radios',
          '#title' => $this->t('Interval'),
          '#description' => $this->t("Interval."),
          '#default_value' => $this->options['queue_interval'],
          '#required' => TRUE,
          '#options' => [
            'daily' => $this->t('daily'),
            'weekly' => $this->t('Weekly'),
            'monthly' => $this->t('Monthly'),
          ],
          '#states' => [
            'visible' => [':input[name=export_method]' => ['value' => 'queue']],
          ],
        ];
        $form['week_day'] = [
          '#type' => 'select',
          '#title' => $this->t('Week day'),
          '#description' => $this->t("Week day to start export."),
          '#default_value' => isset($this->options['week_day']) ? $this->options['week_day'] : \Drupal::config('system.date')->get('first_day'),
          '#required' => TRUE,
          '#options' => DateHelper::weekDaysOrdered(DateHelper::weekDays(TRUE)),
          '#states' => [
            'visible' => [
              ':input[name=export_method]' => ['value' => 'queue'],
              ':input[name=queue_interval]' => ['value' => 'weekly']
            ],
          ],
        ];
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state)
  {
    parent::submitOptionsForm($form, $form_state);
    $section = $form_state->get('section');
    switch ($section) {

      case 'export_method':
        $this->setOption('queue_interval', $form_state->getValue('queue_interval'));
        $queue_interval = $form_state->getValue('queue_interval');
        $this->setOption('queue_interval', empty($queue_interval) ? 'daily' : $queue_interval);

        $this->setOption('week_day', $form_state->getValue('week_day'));
        $week_day = $form_state->getValue('week_day');
        $this->setOption('week_day', is_null($week_day) ? \Drupal::config('system.date')->get('first_day') : $week_day);

        $this->setOption('queue_limit', $form_state->getValue('queue_limit'));
        $queue_limit = $form_state->getValue('queue_limit');
        $this->setOption('queue_limit', empty($queue_limit) ?  SELF::queue_limit_min : $queue_limit);
        break;
    }
  }
}
