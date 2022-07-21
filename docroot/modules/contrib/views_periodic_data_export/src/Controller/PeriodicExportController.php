<?php

namespace Drupal\views_periodic_data_export\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\views\Views;
use Drupal\views\ViewExecutable;
use Drupal\views_periodic_data_export\Services\ViewsPeriodicDataExportService;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Url;
use Drupal\Core\Access\AccessResult;

/**
 * Class PeriodicExportController.
 */
class PeriodicExportController extends ControllerBase {

  /**
   * Periodic export.
   *
   * @return string
   *   Return Hello string.
   */
  public function AllPeriodicExports() {
    $periodic_export_views = Views::getApplicableViews('periodic_export');
    $rows = [];
    foreach($periodic_export_views as $item){
      $viewId = $item[0];
      $displayId = $item[1];

      $view = Views::getView($viewId);
      $view->setDisplay($displayId);

      if($view->display_handler->isEnabled()){
        $rows []= [
          $view->display_handler->getOption('title'),
          $view->display_handler->getOption('queue_interval'),
          t('<a href="@link">Preview</a>',[
              '@link' => Url::fromRoute('views_periodic_data_export.Periodic_export',
                ['viewId' => $viewId, 'displayId' => $displayId])->toString()
          ]
          )
        ];
      }
    }

    return [
      '#type' => 'table',
      '#header' => [t('title'), t('Interval'), t('Exports')],
      '#rows' => $rows,
      '#empty' => t('There are no periodic exports definded.'),
    ];
  }

  /**
   * Periodic export for given view and diplay id.
   *
   * @return string
   */
  public function viewDisplayPeriodicExports($viewId, $displayId) {
    $view = Views::getView($viewId);

    $view->setDisplay($displayId);
    $rows = [];

    $empty_message = t('There are no periodic exports definded.');
    if($view->display_handler->isEnabled()){
      $rows = $this->getExports($view);
    }else{
      $empty_message = t('Not enabled');
    }
    return [
      '#type' => 'table',
      '#header' => [t('title'),t('Progress'), t('Size'), t('Modified'), t('Downlaod')],
      '#rows' => $rows,
      '#empty' => $empty_message ,
    ];
  }

  private function getExports(ViewExecutable &$view){
    $viewsPeriodicDataExportService = new ViewsPeriodicDataExportService($view);

    $exports = $viewsPeriodicDataExportService->getExports();
    $results = [];
    if(!empty($exports)){

      foreach($exports as $export){
        $download_url = t('<a href="@link">Download</a>', ['@link' => \Drupal::service('stream_wrapper_manager')->getViaUri($export->uri)->getExternalUrl()]);
        $progres = intval($viewsPeriodicDataExportService->getFileProgress($export->uri)).'%';

        $size = number_format(filesize($export->uri)/1024, 2).t('MB');
        $modified = date('d-m-Y H:i', filemtime($export->uri));

        $results []= [$export->name, $progres, $size, $modified, $download_url];
      }
    }
    $keys = array_column($results, 4);
    array_multisort($keys, SORT_DESC, $results);
    return $results;
  }

  public static function accessViewDisplayPeriodicExports(){
    $viewId = \Drupal::routeMatch()->getParameters()->get('viewId');
    $displayId = \Drupal::routeMatch()->getParameters()->get('displayId');

    $view = Views::getView($viewId);
    $view->setDisplay($displayId);

    if($view->access($displayId)){
      return AccessResult::allowed();
    }else{
      return AccessResult::forbidden();
    }
  }
}
