<?php  

/**  
 * @file  
 * Contains Drupal\PropertySearch\Form\PropertySearchFormKeyword.  
 */  

namespace Drupal\property_search\Form;  

use Drupal\property_search\Controller\PropertyController;

use Drupal\node\Entity\Node;
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  
use Drupal\field_collection\Entity\FieldCollectionItem;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;
use Drupal\views\Views;
Use \Drupal\Core\Routing;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;


class PropertySearchFormKeyword extends ConfigFormBase {
    public $PropertyController = '';
    
    protected function getEditableConfigNames() {
        return [
          'property_search.settings', 
        ]; 
    } 
    
    /**
     * construction
     */
    public function __construct($id = NULL) {
        global $base_url;
        $this->PropertyController = new PropertyController;
    }
    
    /**  
    * {@inheritdoc}  
    */  
    public function getFormId($id = NULL) {  
        return 'property_search_form_keyword';  
    }
    
    /**
     * print data
     */
    public function print_data ($data = null, $title = 'Data') {
        echo "<pre>{$title}:";print_r($data);echo "</pre>";
    }
    
    
    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
        global $base_url;
        
        $isFront = \Drupal::service('path.matcher')->isFrontPage();
        $current_path = \Drupal::service('path.current')->getPath();
        $current_uri = \Drupal::request()->getRequestUri();

        $parameters = \Drupal::routeMatch()->getParameters();
        #$state = \Drupal::routeMatch()->getParameter('arg_0');
        #$city = \Drupal::routeMatch()->getParameter('arg_1');
        $title = \Drupal::routeMatch()->getParameter('arg_2');
        
        $keyword_prefix = '<div class="input-group col-12">';
        $keyword_suffix = '';

        $form['title'] = array(
            '#type' => 'textfield',
            //'#title' => $this->t('Keyword or Property Name'),
            '#placeholder' => $this->t('FIND A PROPERTY'),
            //'#default_value' => $title,
            '#prefix' => $keyword_prefix,
            '#suffix' => $keyword_suffix,
            '#attributes' => array('class'=>['form-control border-0'],'size'=>20, 'id' => 'edit-title-keyword'),
            #'#autocomplete_route_name' => 'property_search.autocomplete.keywords',
        );

        $submit_text = '';
        $submit_prefix = '<div class="input-group-prepend">';
        $submit_suffix  = '</div></div>';
        $submit_class = 'btn btn-primary px-4 search-button-icon';   

        //$form['actions']['submit'] = array(
        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => t($submit_text),
            '#prefix' => $submit_prefix,
            '#suffix' => $submit_suffix,
            '#attributes' => array('class'=>[$submit_class], 'id' => 'edit-submit-keyword'),
            //'#submit' => 'property_search_custom_form_submit', <button type="button" class="btn btn-primary px-4"><img src="{{ theme_images_path }}search-icon.svg"></button>
        );

        $form['#theme'] = 'property-search-form';
        
        return $form;
    }
   
   /**
   * {@inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      /*if (strlen($form_state->getValue('name')) < 10) {
        $form_state->setErrorByName('name', $this->t('Name is too short.'));
      }*/
    }
    
    /**  
    * {@inheritdoc}  
    * submit
    */  
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();
        $state = 'all states';
        $city = 'all cities';
        $title = $values['title'];

        $path = "/properties/{$state}/{$city}/{$title}";
        //$url = Url::fromUserInput($path, ['query' => $path_param]);
        $url = Url::fromUserInput($path);
        #kint($url);exit;
        $form_state->setRedirectUrl($url);
    }
} 
