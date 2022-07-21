<?php  

/**  
 * @file  
 * Contains Drupal\PropertySearch\Form\PropertySearchForm.  
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


class PropertySearchForm extends ConfigFormBase {
    
    public $PropertyController = '';
    public $property_search_url = '';
    public $all_states_label = '';
    public $all_cities_label = '';
    
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
        $this->property_search_url = "{$base_url}/property-search"; // changed to below url
        //$this->property_search_url = "{$base_url}/properties";
        $this->all_states_label = 'all states';
        $this->all_cities_label = 'all cities';
    }
    
    /**  
    * {@inheritdoc}  
    */  
    public function getFormId() {
        return 'property_search_form';  
    }
    
    /**
     * print data
     */
    public function print_data ($data = null, $title = 'Data') {
        echo "<pre>{$title}:";print_r($data);echo "</pre>";
    }
    
    /**
     * get list
     * 
     */
    function getTermStateCityPropertyTypeInfo($name = '') {
        $info = [];
        $voc = 'state_city_type_info';
        
        $filter = ['vid' => $voc];
        if ($name != '') {
            $filter = ['name' => $name, 'vid' => $voc];
        }
        
        
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
            ->loadByProperties($filter);

        if (!empty($terms)) {
            foreach ($terms as $key => $term) {
                $term_name = $term->label();
                $tid = $term->id();
                $term_description = '';
                if ($term->getDescription() != '') {
                    $term_description = $term->getDescription();
                }
                
                $info[$term_name] = $term_description;
            }
        }
        
        return $info;
    }
           
    /**
     * get list
     * 
     */
    function getTermByName($name = '', $voc = '', $mode = 'id') {
        $tid = '';
        $term = [];
        if ($name != '') {
            if ($voc == '') {
                $voc = 'countries_state_and_city';
            }
            $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
                ->loadByProperties(['name' => $name, 'vid' => $voc]);
             
            if (!empty($term)) {
                $term = reset($term);
                $tid = $term->id();
            }
        }
        
        if ($mode == 'id') {
            return trim($tid);
        }
        
        return $term;
    }
    
    /**
     * get term list from taxonomy reference view
     * 
     * 
     */
    function getTermsFromReferenceView($type = '', $tid = '') {
        global $base_url;
        $result = [];
        
        if ($type != 'use_type') {
            $key = 'all';
            if ($type == 'state') {
                $key = $this->all_states_label;
            } else if ($type == 'city') {
                $key = $this->all_cities_label;
            }
            
            $result[$key] = "Select {$type}";
        }
        
        if ($type != '') {
            $display = "entity_reference_{$type}";
            // Firstly, get the view in question.
            $view = Views::getView('reference');
            
            if (is_object($view)) {
                // Set which view display we want.
                $view->setDisplay($display);

                // Pass any arguments that the view display requires.
                if ($tid != '') {
                    $view->setArguments([$tid]);
                }

                // Execute the view.
                $view->execute();
                $view_result = $view->result;


                #print '<pre>View';kint($view_result);print '</pre>';

                // Access field data from the view results.
                foreach ($view_result as $data) {
                    $entity = $data->_entity;
                    #kint($entity->getName(),$entity);
                    $name = $entity->getName();
                    #$name = ucfirst(strtolower($name));

                    if ($type == 'use_type') {
                        $url_name = (strtolower($name));
                        $use_type_url = "{$this->property_search_url}/{$this->all_states_label}/{$this->all_cities_label}/{$url_name}";
                        #$use_type_url = Url::fromUserInput($use_type_url); // error
                        $result[] = '<li class="d-inline-block m-1">'
                                . '<a href="'.$use_type_url.'" class="btn btn-primary btn-sm px-4">'.$name.'</a>'
                                . '</li>';
                    } else {
                        $result[strtolower($name)] = $name;
                    }
                }
            }
        }
        #print '<pre>View';kint($result);print '</pre>';
        #dpm($result);
        return $result;
    }
    
    /**
    * {@inheritdoc}
    */
    public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
        global $base_url;
        
        /*if ($id == 'keyword') {
            $form_id = $this->getFormId()."_{$id}";
            $form['#id'] = $form_id;
        }*/
        
        $isFront = \Drupal::service('path.matcher')->isFrontPage();
        $current_path = \Drupal::service('path.current')->getPath();
        $current_uri = \Drupal::request()->getRequestUri();

        $theme_path = \Drupal::theme()->getActiveTheme()->getPath();
        $theme_images_path = $base_url.'/'.$theme_path.'/images/';

        
        $parameters = \Drupal::routeMatch()->getParameters();
        $state = \Drupal::routeMatch()->getParameter('arg_0');
        $city = \Drupal::routeMatch()->getParameter('arg_1');
        $title = \Drupal::routeMatch()->getParameter('arg_2');
        
        $stateName = ucwords($state);
        $cityName  = ucwords($city);
        
        if ($stateName == ucwords($this->all_states_label)) {
            $stateName = '';
        }
        if ($cityName == ucwords($this->all_cities_label)) {
            $cityName = '';
        }
        
        #$this->print_data($current_uri);
        
        $state = strtolower($state);
        $city  = strtolower($city);
        #dpm($parameters,$state, $city, $title);
        #kint($parameters, $state, $city, $title);
        
        #$states = $this->PropertyController->getStateCityOptions();
        #$states = $this->getStateCity('state');
        
        $states_options = $this->getTermsFromReferenceView('state');
        
        $stateCityTypeinfo = $this->getTermStateCityPropertyTypeInfo();
        #print '<pre>12';kint($stateCityTypeinfo);print '</pre>';#exit;
        
        $properties_uri = strpos($current_uri,'/properties/');
        #var_dump($properties_uri);
        if ($id == 'use_type') {
            $term_description = isset($stateCityTypeinfo['use_type'])?$stateCityTypeinfo['use_type']:'';
            $tid = '';
            $term_name = ($title != 'all')?$title:'';
            
            #if ($term_description == '') {
                $term = $this->getTermByName($title,'use_type','term');                
                #$term_description = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';
                if ($term) {
                    $term_name = $term->label();
                    $tid = $term->id();
                    if ($term->getDescription() != '' && $term_description == '') {
                        $term_description = $term->getDescription();
                    }
                }
            #}
            
            $term_description = str_replace('[state-name]', $stateName, $term_description);
            
            $form['state_info'] = [
                '#type' => 'markup',
                '#markup' => '<div class="">'
                . '<h2>'.$term_name.'</h2>'
                . $term_description
                . '<div class="text-center mb-4 mt-5 property-search-button"><a class="btn btn-primary btn-xl px-4" href="'.$this->property_search_url.'">Property Search</a></div>'
                . '</div>',
                //'#weight' => '3',
            ];
            
        } else if ($id == 'city') {
            $term_description = isset($stateCityTypeinfo['city'])?$stateCityTypeinfo['city']:'';
            
            $search_title = 'Properties by city';
            if ($city != '') {
                $term_description = isset($stateCityTypeinfo['state_default'])?$stateCityTypeinfo['state_default']:'';
                $search_title = 'Properties by state';
            }
            $term_description = str_replace('[state-name]', $stateName, $term_description);
            
            // for state page
            $stateId = '';
            $stateDescription = $term_description;
            #if ($term_description == '') {
                $stateInfo = $this->getTermByName($state,'','term');
                
                if ($stateInfo) {
                    $stateId = $stateInfo->id();
                    if ($stateDescription == '') {
                        $stateDescription = $stateInfo->getDescription();
                    }
                }
            #}
            
            $form['state_info'] = [
                '#type' => 'markup',
                '#markup' => '<div class="col-md-4 common-text form-inline">'
                . '<h2>'.$search_title.'</h2>'
                . $term_description
                . '',
                //'#weight' => '3',
            ];
            
            
            $state_prefix = '<div class="input-group select-state">';
            #$state_suffix = '</div>';
            #$state_prefix = '';
            $state_suffix = '';
            
            $city_prefix = '<div class="input-group" id="select-city">';
            $city_suffix = '';
            
            $submit_prefix = '<div class="input-group-prepend"><button type="button" class="btn btn-primary px-4">';
            $submit_suffix = '</button></div></div></div>';
            $submit_class = 'btn btn-primary px-4 search-button-icon';
            
            if ($city != '') {
                $form['state'] = array(
                    '#type' => 'select',
                    '#options' => $states_options,
                    '#default_value' => $state,
                    '#prefix' => $state_prefix,
                    '#suffix' => $state_suffix,
                    '#attributes' => array('class'=>['form-control border-0']),
                );
            } else {
                $city_options = $this->getTermsFromReferenceView('city',$stateId);

                $form['city'] = array(
                    '#type' => 'select',
                    '#options' => $city_options,
                    '#default_value' => $city,
                    '#prefix' => $city_prefix,
                    '#suffix' => $city_suffix,
                    '#attributes' => array('class'=>['form-control border-0']),
                );
            }
            $form['help'] = [
                    '#type' => 'markup',
                    '#markup' => '<div class="input-group-prepend input-group-prepend-pipe">
                                    <span class="input-group-text p-0 search-pipe"></span>
                                  </div>',
                    //'#weight' => '3',
                ];
            
            $submit_text = 'Search';
            if (!$isFront) {
                $submit_text = '';
            }
            //$form['actions']['submit'] = array(
            $form['submit'] = array(
                '#type' => 'submit',
                '#value' => t($submit_text),
                '#prefix' => $submit_prefix,
                '#suffix' => $submit_suffix,
                '#attributes' => array('class'=>[$submit_class]),
                //'#submit' => 'property_search_custom_form_submit', <button type="button" class="btn btn-primary px-4"><img src="{{ theme_images_path }}search-icon.svg"></button>
            );
            
            $term_description = isset($stateCityTypeinfo['use_type_default'])?$stateCityTypeinfo['use_type_default']:'';
            if ($stateName != '') {
                $term_description = isset($stateCityTypeinfo['use_type'])?$stateCityTypeinfo['use_type']:'';
            } 
            $term_description = str_replace('[state-name]', $stateName, $term_description);
            
            if ($term_description == '') {
                $term_description = '<p></p>';
                #$term_description = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';
            }
            
            $type_list = $this->getTermsFromReferenceView('use_type');
            $type_list = '<ul class="d-inline-block list-unstyled mt-2 mb-4 pl-0">'.implode('',$type_list).'</ul>';
            $form['use_type'] = [
                '#type' => 'markup',
                '#markup' => '<div class="col-md-1"></div><div class="col-md-4 common-text">'
                . '<h2>Properties by type</h2>'
                . $term_description
                . $type_list
                . '</div>',
                //'#weight' => '3',
            ];
            
        } else if ($current_uri == '/portfolio') { //$current_uri == '/properties'
            $term_description = isset($stateCityTypeinfo['state_default'])?$stateCityTypeinfo['state_default']:'';
            $term_description = str_replace('[state-name]', $stateName, $term_description);
            
            // for properties page
            $form['state_info'] = [
                '#type' => 'markup',
                '#markup' => '<div class="col-md-4 common-text form-inline">'
                . '<h2>Properties by state</h2>'
                . $term_description
                . '',
                //'#weight' => '3',
            ];
            
            $state_prefix = '<div class="input-group select-state">';
            #$state_suffix = '</div>';
            #$state_prefix = '';
            $state_suffix = '<div class="input-group-prepend input-group-prepend-pipe">
                                <span class="input-group-text p-0 search-pipe"></span>
                              </div>';
            
            $submit_prefix = '<div class="input-group-prepend"><button type="button" class="btn btn-primary px-4">';
            $submit_suffix = '</button></div></div></div>';
            $submit_class = 'btn btn-primary px-4 search-button-icon';
            
            $form['state'] = array(
                '#type' => 'select',
                '#options' => $states_options,
                '#default_value' => $state,
                '#prefix' => $state_prefix,
                '#suffix' => $state_suffix,
                '#attributes' => array('class'=>['form-control border-0']),
            );
            
            
            $submit_text = 'Search';
            if (!$isFront) {
                $submit_text = '';
            }
            //$form['actions']['submit'] = array(
            $form['submit'] = array(
                '#type' => 'submit',
                '#value' => t($submit_text),
                '#prefix' => $submit_prefix,
                '#suffix' => $submit_suffix,
                '#attributes' => array('class'=>[$submit_class]),
                //'#submit' => 'property_search_custom_form_submit', <button type="button" class="btn btn-primary px-4"><img src="{{ theme_images_path }}search-icon.svg"></button>
            );
            
            $term_description = isset($stateCityTypeinfo['use_type_default'])?$stateCityTypeinfo['use_type_default']:'';
            if ($stateName != '') {
                $term_description = isset($stateCityTypeinfo['use_type'])?$stateCityTypeinfo['use_type']:'';
            }             
            $term_description = str_replace('[state-name]', $stateName, $term_description);
            
            if ($term_description == '') {
                $term_description = '<p></p>';
                #$term_description = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';
            }
            
            $type_list = $this->getTermsFromReferenceView('use_type');
            $type_list = '<ul class="d-inline-block list-unstyled mt-2 mb-4 pl-0">'.implode('',$type_list).'</ul>';
            $form['use_type'] = [
                '#type' => 'markup',
                '#markup' => '<div class="col-md-1"></div><div class="col-md-4 common-text">'
                . '<h2>Properties by type</h2>'
                . $term_description
                . $type_list
                . '</div>',
                //'#weight' => '3',
            ];
        } else if ($id == 'keyword') {
            #$form['#id'] = 'property_search_form_keyword';
                    
            $keyword_prefix = '<div class="input-group col-12">';
            $keyword_suffix = '';
                
            $form['title'] = array(
                '#type' => 'textfield',
                //'#title' => $this->t('Keyword or Property Name'),
                '#placeholder' => $this->t('KEYWORD OR PROPERTY NAME'),
                '#default_value' => $title,
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
        }  else {
            $term_description = isset($stateCityTypeinfo['state_default'])?$stateCityTypeinfo['state_default']:'';
            $term_description = str_replace('[state-name]', $stateName, $term_description);
            
            $prop_search = strpos($current_uri,'/property-search');
            #echo "uri:$current_uri";kint($current_uri);
            #$isFront = 1;
            // for front and property search pages
            #$state = 'Arizona';
            #$stateId = $this->getTermByName($state);
            $stateId = '';
            $stateDescription = $term_description;
            #if ($term_description == '') {
                $stateInfo = $this->getTermByName($state,'','term');
                
                if ($stateInfo) {
                    $stateId = $stateInfo->id();
                    $stateName = ($stateName != '')?ucwords($stateName):ucwords(strtolower($stateInfo->label()));
                    if ($stateDescription == '') {
                        $stateDescription = $stateInfo->getDescription();
                    }
                }
                #kint($stateInfo->id(),$stateInfo->getDescription());
            #}
            
            $state_prefix = $state_suffix = '';

            $state_prefix = '<div class="input-group mb-4"><div class="input-group-prepend">
                                <span class="input-group-text pl-4"><img src="'.$theme_images_path.'map.svg"></span>
                            </div>';

            $city_prefix = '<div id="select-city">';
            $city_suffix = '</div>';

            #$submit_prefix = '<div class="input-group-prepend">';
            #$submit_suffix = '<button type="button" class="btn btn-primary px-4"><img src="'.$theme_images_path.'search-icon.svg"></button></div>';
            $submit_prefix = '<div class="input-group-prepend"><button type="button" class="btn btn-primary px-4">';
            $submit_suffix = '</button></div></div>';
            $submit_class = 'btn btn-primary px-4 search-button-icon';            
            
            if ($state != '') {
                $state_lable = $label = ($stateName != '' && $stateName != $this->all_states_label)?$stateName:'&nbsp;';
                #$state_info = '<p>Phillips Edison & Company owns and manages a national portfolio of grocery-anchored shopping centers in well-populated suburban markets. Our centers provide an ideal home for retailers of all varieties and sizes. Our portfolio of properties includes turn-key spaces for restaurants, salons, or offices ready for you to move right in, many with furniture, fixtures and equipment in place.</p>';
                $state_info = $info = $stateDescription;
                                
                #$label = $state_lable;
                #$info  = $state_info;
                
                $cityId = '';
                $cityName = '';
                $cityDescription = $state_info;
                if ($city != '' && $city != ucwords($this->all_cities_label)) {
                    $cityInfo = $this->getTermByName($city,'','term');
                    
                    if ($cityInfo) {
                        $cityId = $cityInfo->id();
                        $cityName = $label = $cityInfo->label();
                        $cityDescription = $cityInfo->getDescription();
                        
                        if ($cityDescription != '') {
                            $info = $cityDescription;
                        }
                    }
                }
                
				//done
                $titlearray = ['retail','restaurant','salon','medical'];
				if($title !='' && $title != 'all' && $cityName  != '' && $stateName != '' ){
                   $newtitle = strtolower($title); 
                    
					if(!in_array($newtitle, $titlearray)){
                        $keywords = ucwords($newtitle);
                    }
                   
                    else{
					    $keywords = ucwords($title).' Space for Lease in '.$cityName. ', ' . $stateName;
                    }
				}


 
				elseif($cityName != '' && $stateName != '' && $title ==''){
					$keywords = 'Space for Lease in '. ' ' .$cityName.', '. $stateName;
				}
				else if($cityName != '' && $stateName == ''  && $title ==''){
					$keywords = 'Space for Lease in '. $cityName;
				}else if(!empty($stateName) && $cityName == ''  && $title ==''){
					
					$keywords = 'Space for Lease in '. $stateName;
				}else if($cityName == '' && $stateName != '' && $title !='' && $title !='all'){
                    $newtitle = strtolower($title); 
                    
					if(!in_array($newtitle, $titlearray)){
                        $keywords = ucwords($newtitle);
                    }
                   
                    else{
					    $keywords = ucfirst($title).' Space for Lease in '. $stateName;
                    }
					
				}else if($cityName != '' && $stateName !== ''  && $title !='' && $title !='all'){
					$keywords = ucfirst($title).' Space for Lease in '.$cityName.'';
				}

                
				//done
                 elseif ($title == 'medical' || $title == 'Medical'){
                    $keywords = ucfirst($title).' Space for Lease ';

                }

                elseif ($title == 'restaurant' || $title == 'Restaurant'){
                    $keywords = ucfirst($title).' Space for Lease ';

                }
                elseif ($title == 'salon' || $title == 'Salon'){
                    $keywords = ucfirst($title).' Space for Lease ';

                }
                elseif ($title == 'retail' || $title == 'Retail'){
                    $keywords = ucfirst($title).' Space for Lease ';

                }
                
                elseif ($title != '' && $title != 'all'){
                    $keywords = ucfirst($title).' ';
                }
                else{
					$keywords  = '';
				}
				

                // elseif ($title != '' && $title != 'all'){
                //     $keywords = ucfirst($title).' ';
                // } elseif ($title == 'all' && $cityName == '' && $stateName == ''){
                //     $keywords = ucfirst($title).' Space for Lease ';
                // }elseif ($title == 'all' && $cityName != '' && $stateName != ''){
                //     $keywords = ucfirst($title).' Space for Lease in '.$cityName.', '. $stateName;
                // }elseif ($title == 'all' && $cityName != '' && $stateName == ''){
                //     $keywords = ucfirst($title).' Space for Lease in '.$cityName;
                // }elseif ($title == 'all' && $cityName == '' && $stateName != ''){
                //     $keywords = ucfirst($title).' Space for Lease in '. $stateName;
                // }
                // else{
				// 	$keywords  = '';
				// }
                #$states_label = $states_options[$state];
                $form['state_info'] = [
                    '#type' => 'markup',
                    '#markup' => '<div class="state-info">'
                   // . '<h1>'.$label.'</h1>'
				  //  . '<h1> Properties '.$names .''.$keywords.'</h1>'
				   . '<h1 class="text-center"> '.$keywords.'</h1>'
                    . $info
                    . '</div>',
                    //'#weight' => '3',
                ];

            }

            /*if (!$isFront && $prop_search !== false) {
                #$state_prefix = '<div class="col-md-6"><div class="input-group">';
                $state_prefix = '<div class="col-md-6"><div class="input-group"><div class="input-group-prepend">
                                <span class="input-group-text pl-4"><img src="'.$theme_images_path.'map.svg"></span>
                            </div>';
                $state_suffix = '</div></div>';

                $city_prefix = '<div class="col-md-6" id="select-city"><div class="input-group">';
                $city_suffix = '</div></div>';

                #$submit_prefix = '<div class="col-md-12">';
                #$submit_suffix = '</div>';
                #$submit_class = 'btn btn-primary';
            }*/

            #echo "Term::";dpm($stateId);
            $form['state'] = array(
                '#type' => 'select',
                '#options' => $states_options,
                '#default_value' => $state,
                '#prefix' => $state_prefix,
                '#suffix' => $state_suffix,
                '#attributes' => array('class'=>['form-control border-0 state-select']),
                '#ajax' => [
                    'callback' => '::cityOptionAjaxCallback', // don't forget :: when calling a class method.
                    //'callback' => [$this, 'myAjaxCallback'], //alternative notation
                    'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
                    'event' => 'change',
                    'wrapper' => 'select-city', // This element is updated with this AJAX callback.
                    'progress' => [
                      'type' => 'throbber',
                      //'message' => $this->t('loading...'),
                    ],
                ]
            );

            #if ($isFront || $prop_search === false) {
                $form['help'] = [
                    '#type' => 'markup',
                    '#markup' => '<div class="input-group-prepend input-group-prepend-pipe">
                                    <span class="input-group-text p-0 search-pipe"></span>
                                  </div>',
                    //'#weight' => '3',
                ];
            #}


            $city_options = $this->getTermsFromReferenceView('city',$stateId);

            $form['city'] = array(
                '#type' => 'select',
                '#options' => $city_options,
                '#default_value' => $city,
                '#prefix' => $city_prefix,
                '#suffix' => $city_suffix,
                '#attributes' => array('class'=>['form-control border-0']),
            );


            #if ($isFront || $prop_search !== false) { // for keyword search on front page
            #if ($prop_search !== false) { 
                $keyword_prefix = '<div class="col-md-12"><div class="input-group">';
                #$keyword_suffix = '</div></div>';
                $keyword_suffix = '';
                $submit_suffix = '</div></div></div>';
                
                if (!$isFront && $prop_search === false) {
                    $submit_suffix = '</div></div></div></div>';
                }
                
                #if ($isFront) {
                    $keyword_prefix = '</div><div class="input-group">';
                    #$keyword_suffix = '</div>';
                    $keyword_suffix = '';
                    #$submit_suffix = '</div>';
                    $submit_suffix = '</div></div>';
                #}
                #$keyword_prefix = '';
                #$keyword_suffix = '';
                
                $form['title'] = array(
                    '#type' => 'textfield',
                    //'#title' => $this->t('Keyword or Property Name'),
                    '#placeholder' => $this->t('Keyword or Property Name'),
                    '#default_value' => $title,
                    '#prefix' => $keyword_prefix,
                    '#suffix' => $keyword_suffix,
                    '#attributes' => array('class'=>['form-control border-0 keyword-search-input']),
                    #'#autocomplete_route_name' => 'property_search.autocomplete.keywords',
                );
            #}

            #$submit_text = 'Search';
            #if ($isFront || $prop_search === false) {
                $submit_text = '';
            #}

            //$form['actions']['submit'] = array(
            $form['submit'] = array(
                '#type' => 'submit',
                '#value' => t($submit_text),
                '#prefix' => $submit_prefix,
                '#suffix' => $submit_suffix,
                '#attributes' => array('class'=>[$submit_class], 'id' => 'edit-submit'),
                //'#submit' => 'property_search_custom_form_submit', <button type="button" class="btn btn-primary px-4"><img src="{{ theme_images_path }}search-icon.svg"></button>
            );
            
            $form['all_properties'] = [
                    '#type' => 'markup',
                    '#markup' => '<div class="input-group w-100 common-text mt-2 justify-content-end shadow-none">'
                    . '<a href="'.$this->property_search_url.'">VIEW ALL PROPERTIES</a>'
                    . '</div>',
                    //'#weight' => '3',
                ];

            if ($isFront) {
                $type_list = $this->getTermsFromReferenceView('use_type');
                $type_list = '<ul class="d-inline-block list-unstyled mt-2 mb-4">'.implode('',$type_list).'</ul>';
                $form['use_type'] = [
                    '#type' => 'markup',
                    '#markup' => '<div class="d-inline-block w-100 text-center">'
                    . $type_list
                    . '</div>',
                    //'#weight' => '3',
                ];
            }
        }
        
        if (isset($_REQUEST['debug'])) {
            echo "<br>=>This is test<br>";exit;
        }
        // below code will create issue in ajax callback
        /*$form['submit']['#type'] = 'image_button';
        $form['submit']['#id'] = 'edit-submit';
        #$form['submit']['#value'] = 'Search';
        #$form['submit']['#name'] = '';
        $form['submit']['#src'] = "{$theme_images_path}search-icon.svg";*/
        
        $form['#theme'] = 'property-search-form';
        #$form['#attributes'] = array('class'=>['w-100 form-box']);
        #$form['#attributes']['class'][] = 'w-100 form-box';
        
        return $form;
    }
   
    // Get the value from example select field and fill
    // the textbox with the selected text.
    public function cityOptionAjaxCallback($form, FormStateInterface $form_state) {
        // Prepare our textfield. check if the example select field has a selected option.
        if ($selectedValue = $form_state->getValue('state')) {
            // Get the text of the selected option.
            $selectedVal = $form['state']['#options'][$selectedValue];
            $stateId = $this->getTermByName($selectedVal);
            // Place the text of the selected option in our textfield.
            #kint($selectedVal);
            $city_options = $this->getTermsFromReferenceView('city',$stateId);
            $form['city']['#options'] = $city_options;
        }
        
        
        #kint($city_options);
        $form['city']['#attributes']['id'] = 'edit-city';
                
        // Return the prepared field.
        return $form['city']; 
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
        $state = $values['state'];
        $city = $values['city'];
        $title = $values['title'];

        $path = "/properties/{$state}/{$city}";
        //$url = Url::fromUserInput($path, ['query' => $path_param]);
        $url = Url::fromUserInput($path);
        #kint($url);exit;
        $form_state->setRedirectUrl($url);

        // Redirect to home
        #$form_state->setRedirect('/properties/california/atwater/');
//        echo "Hello:";
//         foreach ($form_state->getValues() as $key => $value) {
//        print_r( $value); // i cant catch my custom element value here T_T why?
//    }
//        print_r($values);exit;
        #$url = \Drupal::url("properties/california/atwater/", [], ['absolute' => TRUE]);
        $url = "properties/california/atwater/";
        #echo "<pre>URL: {$url}"; print_r($values);exit;
        #return new RedirectResponse($url);
    }
} 
