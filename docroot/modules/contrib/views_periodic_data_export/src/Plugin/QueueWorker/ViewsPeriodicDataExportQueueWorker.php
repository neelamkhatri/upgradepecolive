<?php
/**
 * @file
 * Contains Drupal\views_periodic_data_export\Plugin\QueueWorker\RefundUpdateSendTimeCron.php
 */
namespace Drupal\views_periodic_data_export\Plugin\QueueWorker;

use Drupal\Core\Config\StorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\views\Views;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Drupal\views_periodic_data_export\Services\ViewsPeriodicDataExportService;
use Drupal\views\ViewExecutable;


/**
 * RefundUpdateSendTimeCron.
 *
 * @QueueWorker(
 *   id = "ViewsPeriodicDataExportQueueWorker",
 *   title = @Translation("Views periodic data export queue worker"),
 *   cron = {"time" = 100}
 * )
 */
class ViewsPeriodicDataExportQueueWorker extends QueueWorkerBase
{

  /**
   * {@inheritdoc}
   */
  public function processItem($item)
  {
    $view = Views::getView($item->viewId);
    $view->setDisplay($item->displayId);

    if(!($view instanceof ViewExecutable)){
      \Drupal::logger('views_periodic_data_export')->error(t('View and/or display not found.'));
      return TRUE;
    }
    $view->preExecute();
    $view->build();

    $view->query->setLimit($item->limit);
    $view->query->setOffset($item->offset);

    $view->execute($item->displayId);
    // Check to see if the build failed.
    if (!empty($view->build_info['fail'])) {
      \Drupal::logger('views_periodic_data_export')->error(t('Failed'));
      return;
    }
    if (!empty($view->build_info['denied'])) {
      \Drupal::logger('views_periodic_data_export')->error(t('Denied'));
      return;
    }
    $rendered_rows = $view->render();
    $content = (string) $rendered_rows['#markup'];

    if($item->offset != 0){
      $content = preg_replace('/^[^\n]+/', '', $content);
    }
    $fileSystem = \Drupal::service('file_system');
    if (file_put_contents($item->destination, $content, FILE_APPEND) === FALSE) {
      // Write to output file failed - log in logger and in ResponseText on
      // batch execution page user will end up on if write to file fails.
      $message = t('Could not write to temporary output file for result export (@file). Check permissions.', ['@file' => $item->destination]);
      \Drupal::logger('views_periodic_data_export')->error($message);
    }
    $newOffset = $item->offset+$item->limit;
    $periodicExportService = new ViewsPeriodicDataExportService($view);

    if($item->count > $newOffset){
      $item->offset = $newOffset;

      $clonedItem = clone $item;
      $periodicExportService->reQueueViewsPeriodicDataExport($clonedItem);
    }
    $periodicExportService->setFileProgress($item);
    return TRUE;
  }
}
