<?php

/**
 * @file
 * Contains \Drupal\products\AddForm.
 */

namespace Drupal\products;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\SafeMarkup;

class AddForm extends FormBase {

    protected $id;

    function getFormId() {
        return 'products_add';
    }

    function buildForm(array $form, FormStateInterface $form_state) {
        $this->id = \Drupal::request()->get('id');
        $products = ProductsStorage::get($this->id);

        $form['title'] = array(
            '#type' => 'textfield',
            '#title' => t('Title'),
            '#default_value' => ($products) ? $products->title : '',
            '#maxlength' => 255,
            '#required' => TRUE,
        );
        $form['description'] = array(
            '#type' => 'textarea',
            '#title' => t('Description'),
            '#default_value' => ($products) ? $products->description : '',
            '#cols' => '15',
            '#maxlength' => 500,
            '#required' => TRUE,
        );
        $form['sku'] = array(
            '#type' => 'textfield',
            '#title' => t('Product SKU'),
            '#description' => t('Stock Keeping Unit.'),
            '#default_value' => ($products) ? $products->sku : '',
            '#maxlength' => 40,
            '#required' => TRUE,
        );
        $form['price'] = array(
            '#type' => 'number',
            '#title' => t('Product Price'),
            '#description' => t('Product Price'),
            '#default_value' => ($products) ? $products->price : '',
            '#required' => TRUE,
        );
        $form['status'] = array(
            '#type' => 'radios',
            '#title' => t('Status'),
            '#description' => t('Products Status.'),
            '#options' => array(
                'ACTIVE' => t('Active'),
                'INACTIVE' => t('Inactive'),
            ),
            '#default_value' => $products->status,
            '#required' => TRUE,
        );
        if ($this->id > 0) {


            $form['information'] = [
                '#type' => 'vertical_tabs',
                '#default_tab' => 'edit-pimages',
            ];

            // Product Images
            $form['pimages'] = [
                '#type' => 'details',
                '#title' => 'Images',
                '#group' => 'information',
            ];

            $form['pimages']['addimages_wrapper'] = [
                '#type' => 'container',
                '#attributes' => ['id' => 'addimages-wrapper']
            ];

            $imagesheader = [
                'id' => t('id'),
                'cid' => t('Color Variant ID'),
                'images' => t('Product Images'),
                'operations2' => t('Delete'),
            ];
            $imagesrows = array();
            foreach (PImageStorage::getPImages($this->id) as $id => $content) {
                // Row with attributes on the row and some of its cells.
                $deleteimageUrl = Url::fromRoute('image_delete', array('id' => $id . '~' . $this->id));
                $imagesrows[] = array(
                    'data' => array(
                        $id, $content->cid,
                        'public://' . $content->url,
                        \Drupal::l('Delete', $deleteimageUrl),
                    ),
                );
            }

            $imagestable = array(
                '#type' => 'table',
                '#header' => $imagesheader,
                '#rows' => $imagesrows,
                '#attributes' => array(
                    'id' => 'images-table',
                ),
            );

            $form['pimages']['addimages_wrapper']['table'] = $imagestable;

            $form['pimages']['images'] = array(
                '#type' => 'managed_file',
                '#title' => t('Images'),
                '#multiple' => 1,
                '#preview' => 1,
                '#upload_location' => 'public://',
                '#upload_validators' => array(
                    'file_validate_extensions' => array('gif png jpg jpeg'),
                    // Pass the maximum file size in bytes
                    'file_validate_size' => array(1000000),
                ),
            );

            // Color Variants
            $form['pcolor'] = [
                '#type' => 'details',
                '#title' => 'Color',
                '#group' => 'information',
            ];

            $form['pcolor']['addcolor_wrapper'] = [
                '#type' => 'container',
                '#attributes' => ['id' => 'addcolor-wrapper']
            ];


            $colorheader = [
                'id' => t('id'),
                'cid' => t('Color Variant ID'),
                'price' => t('Price'),
                'operations2' => t('Delete'),
            ];
            $colorrows = array();
            foreach (PColorStorage::getPColors($this->id) as $id => $content) {
                // Row with attributes on the row and some of its cells.
                $deletecolorUrl = Url::fromRoute('color_delete', array('id' => $id . '~' . $this->id));
                $colorrows[] = array(
                    'data' => array(
                        $id,
                        $content->cid, $content->price, \Drupal::l('Delete', $deletecolorUrl),
                    ),
                );
            }
            $colortable = array(
                '#type' => 'table',
                '#header' => $colorheader,
                '#rows' => $colorrows,
                '#attributes' => array(
                    'id' => 'color-table',
                ),
            );

            $form['pcolor']['addcolor_wrapper']['table'] = $colortable;

            $form['pcolor']['cid'] = [
                '#type' => 'textfield',
                '#title' => t('Color Variant ID'),
            ];
            $form['pcolor']['cimages'] = [
                '#type' => 'managed_file',
                '#title' => t('Color Images'),
                '#multiple' => 1,
                '#preview' => 1,
                '#upload_location' => 'public://',
                '#upload_validators' => array(
                    'file_validate_extensions' => array('gif png jpg jpeg'),
                    // Pass the maximum file size in bytes
                    'file_validate_size' => array(1000000),
                ),
            ];
            $form['pcolor']['cprice'] = [
                '#type' => 'number',
                '#title' => t('Color Price'),
                '#description' => t('Color Price'),
                '#default_value' => ($products) ? $products->price : '',
                '#required' => TRUE,
            ];

            $form['pcolor']['addcolor'] = [
                '#title' => t('AddColor'),
                '#value' => 'Add Color',
                '#type' => 'button',
                '#ajax' => [
                    'callback' => '::addcolorCallback',
                    'wrapper' => 'addcolor-wrapper',
                ],
                '#limit_validation_errors' => array(),
            ];


            // Size Variants
            $form['psize'] = [
                '#type' => 'details',
                '#title' => t('Size'),
                '#group' => 'information',
            ];
            // TableSelect.
            $form['psize']['addsize_wrapper'] = [
                '#type' => 'container',
                '#attributes' => ['id' => 'addsize-wrapper']
            ];
            $header = [
                'id' => t('id'),
                'size' => t('Size'),
                'price' => t('Price'),
                'operations2' => t('Delete'),
            ];
            $rows = array();
            foreach (PSizeStorage::getPSizes($this->id) as $id => $content) {
                // Row with attributes on the row and some of its cells.
                $deleteUrl = Url::fromRoute('size_delete', array('id' => $id . '~' . $this->id));
                $rows[] = array(
                    'data' => array(
                        $id,
                        $content->size, $content->price, \Drupal::l('Delete', $deleteUrl),
                    ),
                );
            }
            $table = array(
                '#type' => 'table',
                '#header' => $header,
                '#rows' => $rows,
                '#attributes' => array(
                    'id' => 'size-table',
                ),
            );

            $form['psize']['addsize_wrapper']['table'] = $table;

            $form['psize']['size'] = [
                '#type' => 'textfield',
                '#title' => t('Size'),
            ];

            $form['psize']['sprice'] = [
                '#type' => 'number',
                '#title' => t('Size Price'),
                '#description' => t('Size Price'),
                '#default_value' => ($products) ? $products->price : '',
                '#required' => TRUE,
            ];

            $form['psize']['addsize'] = [
                '#title' => t('AddSize'),
                '#value' => 'Add Size',
                '#type' => 'button',
                '#ajax' => [
                    'callback' => '::addsizeCallback',
                    'wrapper' => 'addsize-wrapper',
                ],
                '#limit_validation_errors' => array(),
            ];
        }

        // Categories Implement 

        $form['ptaxonomy'] = [
            '#type' => 'details',
            '#title' => 'Categories',
            '#group' => 'information',
        ];
        $form['ptaxonomy']['category_default'] = array(
            '#type' => 'select',
            '#title' => t('Default category'),
            '#options' => 'select',
            '#description' => t('The selected category will be shown by default on listing pages.')
        );
        // Attributs            
        $form['pattrs'] = [
            '#type' => 'details',
            '#title' => 'Attributes',
            '#group' => 'information',
        ];

        $form['actions'] = array('#type' => 'actions');
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => ($products) ? t('Edit') : t('Add'),
        );
        return $form;
    }

    public function addcolorCallback(array &$form, FormStateInterface $form_state) {

        $cid = $form_state->getValue('cid');
        $cprice = $form_state->getValue('cprice');
        if (!empty($this->id)) {
            PColorStorage::add(SafeMarkup::checkPlain($this->id), SafeMarkup::checkPlain($cid), SafeMarkup::checkPlain($cprice));
            $files = $form_state->getValue('cimages');
            //Save files
            foreach ($files as $key => $value) {
                foreach ($value as $key => $fid) {
                    if ($fid != 0) {
                        // file exists
                        $file = file_load($fid);
                        PImageStorage::add($this->id, $cid, $file->getFilename());
                        if ($file->status == 0) {
                            $file->status = FILE_STATUS_PERMANENT;
                            file_save($file);
                        }
                    }
                }
            }
        }

        $colorheader = [
            'id' => t('id'),
            'cid' => t('CID'),
            'price' => t('Price'),
            'operations2' => t('Delete'),
        ];
        $colorrows = array();
        foreach (PColorStorage::getPColors($this->id) as $id => $content) {
            // Row with attributes on the row and some of its cells.
            $deletecolorUrl = Url::fromRoute('color_delete', array('id' => $id . '~' . $this->id));
            $colorrows[] = array(
                'data' => array(
                    $id,
                    $content->cid, $content->price, \Drupal::l('Delete', $deletecolorUrl),
                ),
            );
        }

        $table = array(
            '#type' => 'table',
            '#header' => $colorheader,
            '#rows' => $colorrows,
            '#attributes' => array(
                'id' => 'color-table',
            ),
        );

        $form['pcolor']['addcolor_wrapper']['table'] = $table;

        return $form['pcolor']['addcolor_wrapper'];
    }

    public function addsizeCallback(array &$form, FormStateInterface $form_state) {
        $size = $form_state->getValue('size');
        $sprice = $form_state->getValue('sprice');
        if (!empty($this->id)) {
            PSizeStorage::add(SafeMarkup::checkPlain($this->id), SafeMarkup::checkPlain($size), SafeMarkup::checkPlain($sprice));
        }

        $header = [
            'id' => t('id'),
            'size' => t('Size'),
            'price' => t('Price'),
            'operations2' => t('Delete'),
        ];
        $rows = array();
        foreach (PSizeStorage::getPSizes($this->id) as $id => $content) {
            // Row with attributes on the row and some of its cells.
            $deleteUrl = Url::fromRoute('size_delete', array('id' => $id . '~' . $this->id));
            $rows[] = array(
                'data' => array(
                    $id,
                    $content->size, $content->price, \Drupal::l('Delete', $deleteUrl),
                ),
            );
        }
        $table = array(
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => array(
                'id' => 'size-table',
            ),
        );

        $form['psize']['addsize_wrapper']['table'] = $table;

        return $form['psize']['addsize_wrapper'];
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        
    }

    function submitForm(array &$form, FormStateInterface $form_state) {
        $title = $form_state->getValue('title');
        $sku = $form_state->getValue('sku');
        $description = $form_state->getValue('description');
        $status = $form_state->getValue('status');
        $price = $form_state->getValue('price');

        if (!empty($this->id)) {

            $files = $form_state->getValue('images');
            //Save files
            foreach ($files as $key => $value) {
                if ($value != 0) {
                    // file exists
                    $file = file_load($value);
                    PImageStorage::add($this->id, 'N/A', $file->getFilename());
                    if ($file->status == 0) {
                        $file->status = FILE_STATUS_PERMANENT;
                        file_save($file);
                    }
                }
            }
            ProductsStorage::edit($this->id, SafeMarkup::checkPlain($title), SafeMarkup::checkPlain($description), SafeMarkup::checkPlain($sku), SafeMarkup::checkPlain($price), SafeMarkup::checkPlain($status));
            drupal_set_message(t('Your message has been edited'));
        } else {
            ProductsStorage::add(SafeMarkup::checkPlain($title), SafeMarkup::checkPlain($description), SafeMarkup::checkPlain($sku), SafeMarkup::checkPlain($price), SafeMarkup::checkPlain($status));
            drupal_set_message(t('Your message has been submitted'));
        }
        $form_state->setRedirect('products_list');
        return;
    }

}
