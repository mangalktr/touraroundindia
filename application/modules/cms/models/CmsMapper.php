<?php

class Cms_Model_CmsMapper {
    protected $_dbTable;
    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Cms_Model_DbTable_Cms');
        }

        return $this->_dbTable;
    }

    public function fetchUrlDetails($sid) {
        $resultSet = $this->getDbTable()->select()->where("identifier=?", $sid)->query()->fetchAll();
        foreach ($resultSet as $row) {
            $entry["page_id"] = $row["sid"];
            $entry["page_description"] = $row["page_description"];
            $entry["page_title"] = $row["page_title"];
            $entry["meta_title"] = $row["meta_title"];
            $entry["meta_keywords"] = $row["meta_keywords"];
            $entry["meta_description"] = $row["meta_description"];
            $entry["background_image"] = $row["background_image"];
            $entry["status"] = $row["status"];
            $entries = $entry;
        }
        return $entries;
    }
    public function getPages() {
        $resultSet = $this->getDbTable()->select()->query()->fetchAll();
        foreach ($resultSet as $row) {
            $entry["identifier"] = $row["identifier"]; 
            $entries = $entry;
        }
        return $entries;
    }
}