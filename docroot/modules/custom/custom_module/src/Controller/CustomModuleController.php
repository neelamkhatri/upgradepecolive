<?php

namespace Drupal\custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller routines for contentimport routes.
 */
class CustomModuleController extends ControllerBase {
    protected $redirect_url;
    
    public function __construct() {
        global $base_url;
        
        $this->redirect_url = $base_url . "/admin/config/system/cron_config";
    }
    
    public function redirectToUrl($url = '') {
        if ($url == '') {
            $url = $this->redirect_url;
        }
        
        header('Location:' . $url);
        exit;
    }
    
    public function setPageTitle($type = '') {
        $type = str_replace('_', ' ', $type);
        $type = ucfirst($type);
        return  "Import - {$type}";
    }
    
    /**
     * Call Cron Job
     *
     * return and redirect
     */
    public function importData($type = '') {
        $cron_type = str_replace('_', ' ', $type);
        $cron_type = ucfirst($cron_type);
        
        $response = "Import process for '{$cron_type}' completed successfully.";
        
        switch ($type) {
            case 'agents':
                custom_module_import_agents_callback();
                break;
            case 'property':
                custom_module_import_property_callback();
                break;
            case 'property_demos':
                custom_module_import_property_demos_data_callback();
                break;
            case 'property_documents':
                custom_module_import_property_documents_data_callback();
                break;
            case 'property_images':
                custom_module_import_property_images_callback();
                break;
            case 'property_space_available':
                custom_module_import_property_space_data_callback();
                break;
            default:
                $response = "Import type '{$type}' is invalid.";
                break;
        }        
        
        \Drupal::messenger()->addStatus(t($response));        
        $this->redirectToUrl();
        
        /*return [
          '#markup' => 'Cron jobs',
        ];*/
    }
    
    public function customClearAllCache() {
        custom_module_clear_all_cache ('web');
        //$this->redirectToUrl('/admin');
        //return 'Custom - Clear All Cache';
        $response = new Response();
        return $response;
    }

}
