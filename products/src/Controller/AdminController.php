<?php
/**
@file
Contains \Drupal\products\Controller\AdminController.
 */

namespace Drupal\products\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\products\ProductsStorage;

class AdminController extends ControllerBase {

    function content() {
        $url = Url::fromRoute('products_add');
        //$add_link = ;
        $add_link = '<p>' . \Drupal::l(t('New Product'), $url) . '</p>';

        $text = array(
            '#type' => 'markup',
            '#markup' => $add_link,
        );

        // Table header.
        $header = array(
            'id' => t('Id'),
            'title' => t('Product title'),
            'description' => t('Description'),
            'sku' => t('sku'),
            'price' => t('price'),
            'status' => t('status'),
            'operations1' => t('Edit/Manage'),
            'operations2' => t('Delete'),
        );
        $rows = array();
        foreach (ProductsStorage::getAll() as $id => $content) {
            // Row with attributes on the row and some of its cells.
            $editUrl = Url::fromRoute('products_edit', array('id' => $id));
            $deleteUrl = Url::fromRoute('products_delete', array('id' => $id));

            $rows[] = array(
                'data' => array(
                    $id,
                    $content->title, $content->description, $content->sku, $content->price, ($content->status == 'ACTIVE') ? 'Active' : 'Inactive',
                    \Drupal::l('Edit/Manage', $editUrl),
                    \Drupal::l('Delete', $deleteUrl)
                ),
            );
        }
        $table = array(
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $rows,
            '#attributes' => array(
                'id' => 'products-table',
            ),
        );
        //return $add_link . ($table);
        return array(
            $text,
            $table,
        );
    }

}
