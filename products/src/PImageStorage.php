<?php

namespace Drupal\products;

class PImageStorage {

    static function getAll() {
        $result = db_query('SELECT * FROM {pimages}')->fetchAllAssoc('id');
        return $result;
    }

    static function exists($id) {
        return (bool) $this->get($id);
    }

    static function get($id) {
        $result = db_query('SELECT * FROM {pimages} WHERE id = :id', array(':id' => $id))->fetchAllAssoc('id');
        if ($result) {
            return $result[$id];
        } else {
            return FALSE;
        }
    }

    static function getPImages($pid) {
        $result = db_query('SELECT id,pid,cid,url FROM {pimages} WHERE pid = :pid', array(':pid' => $pid))->fetchAllAssoc('id');
        if ($result) {
            return $result;
        } else {
            return FALSE;
        }
    }
    
    static function getPCImages($pid,$cid) {
        $result = db_query('SELECT id,pid,cid,url FROM {pimages} WHERE pid = :pid and cid = :cid', array(':pid' => $pid,':cid' => $cid))->fetchAllAssoc('id');
        if ($result) {
            return $result;
        } else {
            return FALSE;
        }
    }

    static function add($pid, $cid, $url) {
        try {
            db_insert('pimages')->fields(array(
                'pid' => $pid,
                'cid' => $cid,
                'url' => $url,
            ))->execute();
        } catch (Exception $e) {
            ;
        }
    }

    static function edit($id, $pid, $cid, $url) {
        db_update('pimages')->fields(array(
                    'pid' => $pid,
                    'cid' => $cid,
                    'url' => $url,
                ))
                ->condition('id', $id)
                ->execute();
    }

    static function delete($id) {
        db_delete('pimages')->condition('id', $id)->execute();
    }

}
