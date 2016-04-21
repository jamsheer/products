<?php

namespace Drupal\products;

class PColorStorage {

  static function getAll() {
    $result = db_query('SELECT * FROM {pcolor}')->fetchAllAssoc('id');
    return $result;
  }

  static function exists($id) {
    return (bool) $this->get($id);
  }

  static function get($id) {
    $result = db_query('SELECT * FROM {pcolor} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
    if ($result) {
      return $result[$id];
    }
    else {
      return FALSE;
    }
  }
  
  static function getPColors($pid) {
    $result = db_query('SELECT id,cid,price FROM {pcolor} WHERE pid = :pid', array(':pid' => $pid))->fetchAllAssoc('id');
    if ($result) {
      return $result;
    }
    else {
      return FALSE;
    }
  }

  static function add($pid, $cid,$price) {
      try{
    db_insert('pcolor')->fields(array(
      'pid' => $pid,
      'cid' => $cid,
      'price' => $price,
    ))->execute();
      }  catch (Exception $e){;}
  }

  static function edit($id,$pid,$cid,$price) {
    db_update('pcolor')->fields(array(
      'pid' => $pid,
      'cid' => $cid,
      'price' => $price,
    ))
    ->condition('id', $id)
    ->execute();
  }
  
  static function delete($id) {
    db_delete('pcolor')->condition('id', $id)->execute();
  }
}
