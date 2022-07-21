<?php

namespace Drupal\custom_module\Form;

use Drupal\custom_module\Controller\CMContentImportController;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * HelloForm controller.
 */
class CustomModuleForm extends ConfigFormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'custom_module_form';
  }

  protected function getEditableConfigNames() {
    return [
      'custom_module.settings',
    ];
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $contentTypes = CMContentImportController::getAllContentTypes();

    $form['custom_module_contenttype'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Content Type'),
      '#options' => $contentTypes,
      '#default_value' => $this->t('Select'),
      '#required' => TRUE,
      '#ajax' => [
        'event' => 'change',
        'callback' => '::contentImportcallback',
        'wrapper' => 'content_import_fields_change_wrapper',
        'progress' => [
          'type' => 'throbber',
          'message' => NULL,
        ],
      ],
    ];

    $form['file_upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Import CSV File'),
      '#size' => 40,
      '#description' => $this->t('Select the CSV file to be imported.'),
      '#required' => FALSE,
      '#autoupload' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => ['csv']
      ],
    ];

    $form['loglink'] = [
      '#type' => 'link',
      '#title' => $this->t('Check Log..'),
      '#url' => Url::fromUri('base:sites/default/files/custom_module_log.txt'),
    ];

    $form['import_ct_markup'] = [
      '#suffix' => '<div id="content_import_fields_change_wrapper"></div>',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
      '#button_type' => 'primary',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate the title and the checkbox of the form.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->file = file_save_upload('file_upload', $form['file_upload']['#upload_validators'], FALSE, 0);
#echo "Validate form";kint($this->file);exit;
    if (!$this->file) {
      $form_state->setErrorByName('file_upload', $this->t('Provided file is not a CSV file or is corrupted.'));
    }
  }


  /**
   * Content Import Sample CSV Creation.
   */
  public function contentImportcallback(array &$form, FormStateInterface $form_state) {
    global $base_url;
    
    $ajax_response = new AjaxResponse();
    
    #echo '<pre>';print_r($form_state);exit;
    
    $contentType = $form_state->getValue('custom_module_contenttype');
    $fields = get_fields($contentType);
    $fieldArray = $fields['name'];
    $contentTypeFields = 'title,';
    $contentTypeFields .= 'langcode,';
    
    foreach ($fieldArray as $val) {
      $contentTypeFields .= $val . ',';
    }
    $contentTypeFields = substr($contentTypeFields, 0, -1);
    
    $sampleFile = $contentType . '.csv';
    
    $handle = fopen("sites/default/files/" . $sampleFile, "w+") or die("There is no permission to create log file. Please give permission for sites/default/file!");
    
    fwrite($handle, $contentTypeFields);
    
    $result = '<a class="button button--primary" href="' . $base_url . '/sites/default/files/' . $sampleFile . '">Click here to download Sample CSV</a>';
    
    $ajax_response->addCommand(new HtmlCommand('#content_import_fields_change_wrapper', $result));
    
    return $ajax_response;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $contentType = $form_state->getValue('custom_module_contenttype');
    $import = custom_module_import_content_callback($contentType, $this->file, 'custom');
    /*if ($import) {
        \Drupal::messenger()->addStatus(t($import));
        #\Drupal::messenger()->addWarning(t('This is a warning message.'));
        #\Drupal::messenger()->addError(t('This is an error message.'));
    } else {
        $import = "Import process for '{$contentType}' completed.";
        \Drupal::messenger()->addStatus(t($import));
    }*/
    #echo "Submit form"; kint($form_state);exit;
    #create_node($this->file, $contentType);
  }

}