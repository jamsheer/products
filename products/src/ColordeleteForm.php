<?php

namespace Drupal\products;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class ColordeleteForm extends ConfirmFormBase {
  protected $id;
  protected $pid;

  function getFormId() {
    return 'color_delete';
  }

  function getQuestion() {
    return t('Are you sure you want to delete color %id?', array('%id' => $this->id));
  }

  function getConfirmText() {
    return t('Delete');
  }

  function getCancelUrl() {
    return new Url('products_list');
  }

  function buildForm(array $form, FormStateInterface $form_state) {
    $idcombo=\Drupal::request()->get('id');
    $ids=explode('~', $idcombo);
    $this->id = $ids[0];
    $this->pid =$ids[1];
    return parent::buildForm($form, $form_state);
  }

  function submitForm(array &$form, FormStateInterface $form_state) {
    PColorStorage::delete($this->id);
    $form_state->setRedirect('products_list');
  }
}
