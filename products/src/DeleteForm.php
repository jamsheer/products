<?php

namespace Drupal\products;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class DeleteForm extends ConfirmFormBase {
  protected $id;

  function getFormId() {
    return 'products_delete';
  }

  function getQuestion() {
    return t('Are you sure you want to delete product %id?', array('%id' => $this->id));
  }

  function getConfirmText() {
    return t('Delete');
  }

  function getCancelUrl() {
    return new Url('products_list');
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $this->id = \Drupal::request()->get('id');
    return parent::buildForm($form, $form_state);
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    ProductsStorage::delete($this->id);
    //watchdog('products', 'Deleted Products Submission with id %id.', array('%id' => $this->id));
    \Drupal::logger('products')->notice('@type: deleted %title.',
        array(
            '@type' => $this->id,
            '%title' => $this->id,
        ));
    drupal_set_message(t('Products %id has been deleted.', array('%id' => $this->id)));
    $form_state->setRedirect('products_list');
  }
}
