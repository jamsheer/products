<?php

namespace Drupal\products;

class ProductsStorage {

  static function getAll() {
    $result = db_query('SELECT * FROM {products}')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT * FROM {products} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }

  static function add($title, $description,$sku,$price,$status) {
    db_insert('products')->fields(array(
      'title' => $title,
      'description' => $description,
      'sku' => $sku,
      'price' => $price,
      'status' => $status,
    ))->execute();
  }

  static function edit($id, $title, $description,$sku,$price,$status) {
    db_update('products')->fields(array(
      'title' => $title,
      'description' => $description,
      'sku' => $sku,
      'price' => $price,
      'status' => $status,
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('products')->condition('id', $id)->execute();
  }
}
