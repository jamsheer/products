<?php

namespace Drupal\products;

class PSizeStorage {

  static function getAll() {
    $result = db_query('SELECT * FROM {psize}')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT * FROM {psize} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }
  
  static function getPSizes($pid) {
    $result = db_query('SELECT id,size,price FROM {psize} WHERE pid = :pid', array(':pid' => $pid))->fetchAllAssoc('id');
    if ($result) {
      return $result;
    }
    else {
      return FALSE;
    }
  }

  static function add($pid, $size,$price) {
    db_insert('psize')->fields(array(
      'pid' => $pid,
      'size' => $size,
      'price' => $price,
    ))->execute();
  }

  static function edit($id,$pid,$size,$price) {
    db_update('psize')->fields(array(
      'pid' => $pid,
      'size' => $size,
      'price' => $price,
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('psize')->condition('id', $id)->execute();
  }
}
