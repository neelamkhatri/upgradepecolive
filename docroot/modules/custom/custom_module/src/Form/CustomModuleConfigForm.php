<?php
/**
 * @file
 * Contains \Drupal\simple\Form\SimpleConfigForm.
 */
 
namespace Drupal\custom_module\Form;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
 
class CustomModuleConfigForm extends ConfigFormBase {
 
    public function getFormId() {
        return 'custom_module_config_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
        global $base_url;
        $form = parent::buildForm($form, $form_state);

        $config = $this->config('custom_module.cron_settings');

        $cron_url = "{$base_url}/admin/import_data/";
        $desc_html = "<p><a href='{$cron_url}agents'><strong>Import - Agents</strong></a></p>";
        $desc_html.= "<p><a href='{$cron_url}property'><strong>Import - Property</strong></a></p>";
        $desc_html.= "<p><a href='{$cron_url}property_demos'><strong>Import - Property Demos</strong></a></p>";
        $desc_html.= "<p><a href='{$cron_url}property_documents'><strong>Import - Property Documents</strong></a></p>";
        $desc_html.= "<p><a href='{$cron_url}property_images'><strong>Import - Property Images</strong></a></p>";
        $desc_html.= "<p><a href='{$cron_url}property_space_available'><strong>Import - Property Space Available</strong></a></p>";
                
        /*$form['cron_jobs'] = array(
          '#type' => 'details',
          '#title' => t('Cron jobs for Import data from CSV'),
          '#description' => t($desc_html),
          '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
        );*/
        $form['import_data'] = array(
            '#type' => 'details',
            '#title' => t('Import data'),
            '#description' => t($desc_html),
            #'#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
        );
        
        $form['cron'] = array(
            '#type' => 'details',
            '#title' => t('Cron Config'),
            #'#description' => t('Lorem ipsum.'),
            #'#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
        );
        $form['cron']['identifier'] = array( 
            '#type' => 'textfield', 
            '#title' => $this->t('Property/Agents Identifier'), 
            '#description' => t('Add comma (,) seperated values for multiple identifier (If added any identifier then only that record(s) will be processed)'),
            '#default_value' => $config->get('identifier'),
            //'#required' => TRUE, 
        );
        $form['cron']['import_limit'] = array( 
            '#type' => 'textfield', 
            '#title' => $this->t('Import Limit'), 
            '#description' => t("Number of data to import from CSV (Blank for All)"),
            '#size' => 3,
            #'#maxlength' => 5,
            '#default_value' => $config->get('import_limit'),
            //'#required' => TRUE, 
        );
        
        $form['email'] = array(
            '#type' => 'details',
            '#title' => t('Email Notification'),
            #'#description' => t('Lorem ipsum.'),
            #'#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
        );
        $form['email']['cron_logs_webform_id'] = array( 
            '#type' => 'textfield', 
            '#title' => $this->t('Webform ID for Cron Logs'), 
            '#description' => t("Add webform ID for saving cron logs for sending logs email."),
            //'#size' => 2,
            //'#maxlength' => 3,
            '#default_value' => $config->get('cron_logs_webform_id'),
            //'#required' => TRUE, 
        );
        /*$form['email']['from_name'] = array( 
            '#type' => 'textfield', 
            '#title' => $this->t('Email From Name'), 
            '#default_value' => $config->get('from_name'),
            //'#required' => TRUE, 
        );        
        $form['email']['from_email'] = array( 
            '#type' => 'textfield', 
            '#title' => $this->t('From Email'), 
            '#default_value' => $config->get('from_email'),
            //'#required' => TRUE, 
        );        
        $form['email']['to_email'] = array( 
            '#type' => 'textfield', 
            '#title' => $this->t('To Email'), 
            '#default_value' => $config->get('to_email'),
            //'#required' => TRUE, 
        );
        
        
        $form['email']['email_subject'] = array(
          '#type' => 'textfield',
          '#title' => t('Subject'),
          '#default_value' => $config->get('email_subject'),
          //'#required' => TRUE, // Added
        );
        $form['email']['email_message'] = array( 
          '#type' => 'text_format', 
          '#title' => $this->t('Message'), 
          '#format' => 'full_html',
          '#rows'  =>  8,
          '#description' => t("Email template body message"),
          '#default_value' => $config->get('email_message'), 
          //'#required' => TRUE, 
        );*/

        return $form; 
    } 

    public function submitForm(array &$form, FormStateInterface $form_state) {
        #echo "<pre>Form:";print_r($form_state->getValue('email_subject'));kint($form_state);exit;
        $config = $this->config('custom_module.cron_settings');
        $config->set('identifier', $form_state->getValue('identifier'));
        $config->set('import_limit', $form_state->getValue('import_limit'));
        
        #$config->set('from_name', $form_state->getValue('from_name'));
        #$config->set('from_email', $form_state->getValue('from_email'));
        #$config->set('to_email', $form_state->getValue('to_email'));
        $config->set('cron_logs_webform_id', $form_state->getValue('cron_logs_webform_id'));
        #$config->set('email_subject', $form_state->getValue('email_subject'));
        #$config->set('email_message', $form_state->getValue('email_message')['value']);
        
        $config->save();

        return parent::submitForm($form, $form_state); 
    }
 
    protected function getEditableConfigNames() {
        return [
          'custom_module.cron_settings', 
        ]; 
    } 
}