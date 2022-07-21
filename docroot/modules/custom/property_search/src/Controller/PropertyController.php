<?php

/**
 * @file
 * Contains \Drupal\property_search\Controller\PropertyController.
 */
namespace Drupal\property_search\Controller;

use Drupal\Core\Controller\ControllerBase;

use Drupal\node\Entity\Node;
use Drupal\Core\Form\ConfigFormBase;  
use Drupal\Core\Form\FormStateInterface;  
use Drupal\file\Entity\File;
use Drupal\field_collection\Entity\FieldCollectionItem;
use Symfony\Component\HttpFoundation\Request;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\paragraphs\Entity\Paragraph;

class PropertyController extends ControllerBase {
    /**
    * The node storage.
    *
    * @var \Drupal\node\NodeStorage
    */
    protected $nodeStorage;

    /**
    * {@inheritdoc}
    */
//    public function __construct(EntityTypeManagerInterface $entity_type_manager) {
//        //$this->nodeStroage = $entity_type_manager->getStorage('node');
//    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
      // Instantiates this form class.
      return new static(
            $container->get('entity_type.manager')
      );
    }
    
    public function content() {
        $form = \Drupal::formBuilder()->getForm('Drupal\property_search\Form\PropertySearchForm');

        return array(
            '#theme' => 'property-search-form',
            '#title' => 'Property search',
            '#form' => $form,
        );
    }
    
    /**
     * Get tenant from Paragraph - property_space_available
     * field_tenant
     * @return type list
     */
    public function getTenantOptions($name = '') {
        $results = array();
        
        if ($name != '') {
            $query = \Drupal::entityQuery('paragraph');
        
            $query->condition('type', 'property_space_available');
                        
            $query->condition('field_tenant', $name, 'CONTAINS');
            $query->groupBy('field_tenant');
            $query->sort('field_tenant','ASC');
            $query->range(0, 50);
            $ids = $query->execute();
        #kint($ids);
            $paragraph_storage = \Drupal::entityTypeManager()->getStorage('paragraph');
            $paragraphs_objects = $paragraph_storage->loadMultiple($ids);
            /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
            foreach ($paragraphs_objects as $key=>$paragraph) {
                #echo "$key<br>";
                
                // Get field from the paragraph.
                $name = $paragraph->get('field_tenant')->value;
                $name = str_replace('"', '', $name);
                $name = trim($name);
                
                $label = [
                  $name,
                  "<small><strong>(Tenant)</strong></small>",
                ];

                $results[$name] = [
                  //'value' => EntityAutocomplete::getEntityLabels([$node]),
                  'value' => $name,
                  'label' => implode(' ', $label),
                ];
            }
            $results = array_values($results);
            #kint($results);
            //$terms = taxonomy_term_load_multiple(array_keys($tids));
            /*$data = \Drupal::entityTypeManager()->getStorage('paragraph')->loadMultiple($ids);
            
            if (!empty($data)) {
                
                foreach ($data as $item) {
                    $name = $item->get('field_tenant')->value;
                    $id   = $item->id();
              
                    $label = [
                      $name,
                      "<small><strong>(Tenant - {$id})</strong></small>",
                    ];

                    $results[] = [
                      //'value' => EntityAutocomplete::getEntityLabels([$node]),
                      'value' => $name,
                      'label' => implode(' ', $label),
                    ];
                }
            }*/
        }
        
        return $results;
    }
    
    /**
     * Taxonomy get States and city lists
     * @return type list
     */
    public function getStateCityOptions($name = '', $type = '') {
        $results = array();
        
        if ($name != '') {
            $query = \Drupal::entityQuery('taxonomy_term');
        
            $query->condition('status', 1);
            $query->condition('vid', 'countries_state_and_city');
            
            if ($type == 'city') {
                $query->condition('parent', 0, '!=');
            } else {
                $query->condition('parent', 0);
            }
            
            $query->condition('name', $name, 'CONTAINS');
            
            $tids = $query->execute();
        #kint($tids);
            //$terms = taxonomy_term_load_multiple(array_keys($tids));
            $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);

            if (!empty($terms)) {
                
                foreach ($terms as $term) {
                    $term_name = $term->label();
                    $tid = $term->id();
              
                    $label = [
                      $term_name,
                      "<small><strong>({$type})</strong></small>",
                    ];

                    $results[] = [
                      //'value' => EntityAutocomplete::getEntityLabels([$node]),
                      'value' => $term_name,
                      'label' => implode(' ', $label),
                    ];
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Taxonomy get States and city lists
     * @return type list
     */
    public function getUseTypeOptions($name = '', $type = '') {
        $results = array();
        
        if ($name != '') {
            $query = \Drupal::entityQuery('taxonomy_term');
        
            $query->condition('status', 1);
            $query->condition('vid', 'use_type');
                        
            $query->condition('name', $name, 'CONTAINS');
            
            $tids = $query->execute();
        #kint($tids);
            //$terms = taxonomy_term_load_multiple(array_keys($tids));
            $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);

            if (!empty($terms)) {
                
                foreach ($terms as $term) {
                    $term_name = $term->label();
                    $tid = $term->id();
              
                    $label = [
                      $term_name,
                      "<small><strong>(Use Type)</strong></small>",
                    ];

                    $results[] = [
                      //'value' => EntityAutocomplete::getEntityLabels([$node]),
                      'value' => $term_name,
                      'label' => implode(' ', $label),
                    ];
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Taxonomy get States and city lists
     * @return type list
     */
    public function getAuosuggestList($input = '', $field_name = 'title', $type = 'properties') {
        $results = array();
        
        $list_type = 'Property';
        if ($type == 'agents') {
            $list_type = 'Contact';
        }
        
        $query = \Drupal::entityQuery('node');
        
        $query->condition('status', 1);
        $query->condition('type', $type);
        $query->condition($field_name, $input, 'CONTAINS');
        
        if ($type == 'agents') {
            $query->condition('field_agenttype', 'Leasing');
        }
        
        $query->sort($field_name, 'ASC')
              ->range(0, 50);
        $nids = $query->execute();
        
        //$nodes = $ids ? $this->nodeStroage->loadMultiple($ids) : [];
        $nodes = entity_load_multiple('node', $nids);
        
        if (!empty($nodes)) {
            foreach ($nodes as $node) {
              $label = [
                $node->getTitle(),
                "<small><strong>({$list_type})</strong></small>",
              ];

              $results[] = [
                //'value' => EntityAutocomplete::getEntityLabels([$node]),
                'value' => $node->getTitle(),
                'label' => implode(' ', $label),
              ];
            }
        }
                
        return $results;
    }
    
    /**
    * Returns response for the autocompletion.
    *
    * @param \Symfony\Component\HttpFoundation\Request $request
    *   The current request object containing the search string.
    *
    * @return \Symfony\Component\HttpFoundation\JsonResponse
    *   A JSON response containing the autocomplete suggestions.
    */
    public function handleAutocomplete(request $request) {
        $matches = array();
        $results = [];
        $input = $request->query->get('q');
        
        // Get the typed string from the URL, if it exists.
        if (!$input) {
          return new JsonResponse($matches);
        }
        
        $input = Xss::filter($input);
        
        $properties = $this->getAuosuggestList($input, 'title');
        $agents     = $this->getAuosuggestList($input, 'title', 'agents');
        $states     = $this->getStateCityOptions($input, 'state');
        $cities     = $this->getStateCityOptions($input, 'city');
        $tenants    = $this->getTenantOptions($input);
        $use_types  = $this->getUseTypeOptions($input);
        
        $results = array_merge($properties, $agents, $states, $cities, $tenants, $use_types);
        
        usort($results, function($a, $b) {
            return $a['value'] <=> $b['value'];
        });
        
        return new JsonResponse($results);
        
        /*
        if ($input) {
            $query = \Drupal::entityQuery('node')
            ->condition('status', 1)
            ->condition('type', 'properties')
            ->condition('title', '%'.db_like($input).'%', 'LIKE');
            //->condition('field_tags.entity.name', 'node_access');
            $nids = $query->execute();
            
            $result = entity_load_multiple('node', $nids);
            
            foreach ($result as $row) {
                //$matches[$row->nid->value] = $row->title->value;
                $matches[] = ['value' => $row->nid->value, 'label' => $row->title->value];
            }
        }
        return new JsonResponse($matches);*/
    }
}