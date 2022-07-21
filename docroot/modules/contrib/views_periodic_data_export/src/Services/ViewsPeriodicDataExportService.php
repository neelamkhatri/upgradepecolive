<?php
namespace Drupal\views_periodic_data_export\Services;

use Drupal\Core\Config\StorageException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\views\Views;
use Drupal\views\ViewExecutable;
use Drupal\Core\Datetime\DateHelper;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

class ViewsPeriodicDataExportService
{
  private $viewObject;
  private $limit = 500;

  public function __construct(ViewExecutable &$viewObject){
    $this->viewObject = $viewObject;
    $this->viewDisplayKey =  $this->viewObject->id().'-'.$this->viewObject->current_display;

    $this->limit = $this->viewObject->display_handler->getOption('queue_limit');
  }

  public function canQueueNow(){
    $requestTime = \Drupal::time()->getRequestTime();
    $interval = $this->viewObject->display_handler->getOption('queue_interval');

    $date = new DrupalDateTime();
    $week_day = $this->viewObject->display_handler->getOption('week_day');

    if(\file_exists($this->getDestination())){
      return FALSE;
    }

    if($interval == 'daily' && $date->format('H') == '00'){
      return TRUE;
    }
    if($interval == 'weekly' && DateHelper::dayOfWeek() == $week_day){
      return TRUE;
    }
    if($interval == 'monthly' && $date->format('j') == 1){
      return TRUE;
    }
    return FALSE;
  }

  public function initQueueViewsPeriodicDataExport(){
    $item = new \stdClass();

    $this->viewObject->preExecute([]);
    $this->viewObject->build();

    $item->viewId = $this->viewObject->id();
    $item->displayId = $this->viewObject->current_display;

    $item->count = $this->viewObject->query->query()->countQuery()->execute()->fetchField();
    $this->setDestination($item);

    if(!$item->destination){
      \Drupal::logger('views_periodic_data_export')->error(t('Periodic export not queued due to file issue.'));
      return;
    }
    $item->limit = $this->limit;
    $item->offset = 0;

    if($this->queueViewsPeriodicDataExport($item)) {

      $this->setFileProgress($item);
      \Drupal::logger('views_periodic_data_export')->info(t('Periodic export @name queued successfully.', ['@name' => $item->destination]));
    }
  }

  public function queueViewsPeriodicDataExport(&$item){
    $queue = \Drupal::service('queue')->get('ViewsPeriodicDataExportQueueWorker');
    $queue->createItem($item);
    return TRUE;
  }

  public function reQueueViewsPeriodicDataExport(&$item){
    if($this->queueViewsPeriodicDataExport($item)){
      \Drupal::logger('views_periodic_data_export')->info(t('Periodic export @name re-queued successfully.', ['@name' => $item->destination]));
    }
  }

  public function setDestination(&$item){
    $fileSystem = \Drupal::service('file_system');

    $destination = $this->getDestination();
    $directory = $this->getDirectory();

    $fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $uri = $fileSystem->saveData('', $destination, FileSystemInterface::EXISTS_REPLACE);

    if (!$uri) {
      \Drupal::logger('views_periodic_data_export')->error(t('Periodic export file not careated'));
      return FALSE;
    }
    $item->destination = $destination;

    @chmod($item->destination, 0775);
  }

  private function getDirectory(){
    if (\Drupal::service('stream_wrapper_manager')->isValidScheme('private')) {
      $directory = 'private://views_periodic_data_export/';
    }else{
      $directory = 'public://views_periodic_data_export/';
    }
    return $directory;
  }

  private function getDestination(){
    $date_string = date('dMy', \Drupal::time()->getRequestTime());

    $directory = $this->getDirectory();
    $directory .= $this->viewDisplayKey.'-'.$date_string.'-';

    $filename = $this->viewObject->getDisplay()->getOption('filename').'.csv';
    return $directory.$filename;
  }

  public function setFileProgress(&$item){
    $progress = $item->offset > 0 ? ($item->offset/$item->count)*100 : 0;

    if(($item->offset+$item->limit) >= $item->count){
      $progress = 100;
    }
    \Drupal::state()->set($item->destination, $progress);
  }

  public function getFileProgress($file_uri){
    return \Drupal::state()->get($file_uri);
  }

  public function getExports(){
    if(!\file_exists($this->getDirectory())){

      \drupal::messenger()->addMessage(t('No exports exist yet.'));
      $response =  new RedirectResponse(Url::fromRoute('views_periodic_data_export.All_Periodic_export')->toString());

      $response->send();
      return;
    }
    $matches = \Drupal::service('file_system')->scanDirectory($this->getDirectory(), '/'.$this->viewDisplayKey.'*/');
    return $matches;
  }
}
