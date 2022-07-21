<?php
/**
 * @file
 * Contains \Drupal\property_search\Plugin\Block\MyCustomBlock.
 */
namespace Drupal\property_search\Plugin\Block;
use Drupal\Core\Block\BlockBase;

use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityManager;
use \Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;

/**
 * Provides a 'PropertySearchFormPropertyType' block. - Use Type
 *
 * @Block(
 *   id = "property_search_form_property_type",
 *   admin_label = @Translation("Property search form - Use Type"),
 *   category = @Translation("Add Form block for Property Search - Use Type")
 * )
 */
class PropertySearchFormPropertyType extends BlockBase {
    
    /**
    * {@inheritdoc}
    */
    public function defaultConfiguration() {
      return ['label_display' => FALSE];
    }

    /**
     * {@inheritdoc}
     */
    public function build() {
        #$form = \Drupal::formBuilder()->getForm('Drupal\property_search\Form\PropertySearchForm', 'property_type');
        $form = \Drupal::formBuilder()->getForm('Drupal\property_search\Form\PropertySearchForm', 'use_type');
        #$renderArray['form'] = $form;
        return $form;    
    }
}