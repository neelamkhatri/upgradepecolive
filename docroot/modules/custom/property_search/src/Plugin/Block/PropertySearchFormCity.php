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
 * Provides a 'PropertySearchFormCity' block.
 *
 * @Block(
 *   id = "property_search_form_city",
 *   admin_label = @Translation("Property search form - City"),
 *   category = @Translation("Add Form block for Property Search - City")
 * )
 */
class PropertySearchFormCity extends BlockBase {
    
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
        $form = \Drupal::formBuilder()->getForm('Drupal\property_search\Form\PropertySearchForm', 'city');
        #$renderArray['form'] = $form;
        return $form;    
    }
}