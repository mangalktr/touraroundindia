<?php
/***************************************************************
 * Catabatic Technology Pvt. Ltd.
 * File Name    : CRUD.php
 * File Desc.   : CRUD Model
 * Created By    : Piyush Tiwari <piyush@catpl.co.in>
 * Created Date : 23-05-2018
 * Updated Date : 28-06-2018
 * 
***************************************************************/



class Admin_Model_CRUD extends Zend_Db_Table_Abstract
{
     
    function init() {
        $this->db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /**
    * rv_create() method is used to add insert into database
    * @param tablename , array
    * @return inserted id
    */
    
    public function rv_insert($tablename, array $addData)
    {
        
        $dbtable = new Zend_Db_Table($tablename);
        
        return $dbtable->insert($addData); // return inserted id
    }
    
//    public function rv_insert_footer($tablename, array $addData)
//    {
//        $dbtable = new Zend_Db_Table($tablename);
//        return $dbtable->insert($addData); // return inserted id
//    }
    /**
    * rv_update() method is used to edit 
    * @param table name, array data, where array
    * @return true 
    */
    public function rv_update($tablename, array $editData, array $where)
    {
        $dbtable = new Zend_Db_Table($tablename);
        return $dbtable->update($editData,$where); // return row effected or not
    }
    
    
    /**
    * delete() method is used to add menu
    * @param array
    * @return true 
    */
    public function rv_delete($table , $where)
    {   
        $dbtable = new Zend_Db_Table($table);
        return $dbtable->delete($where);
    }
    
    
    /**
    * rv_select_all() method is used to get all listing
    * @param table name, columns array, where array, order array
    * @return array result set
    */	
    public function rv_select_all($tablename, array $columns, array $where, array $order, $limit =false)
    {
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
        if(count($where)){
            foreach ($where as $k => $v)
            $select->where("$k =?","$v");
        }
        if(count($order)){
            foreach ($order as $k => $v)
            $select->order("$k  $v");
        }
        if($limit){
            $select->limit($limit);
        }
        $result = $dbtable->fetchAll($select);
        if($result==NULL)
            return false;
        else
            return $result->toArray();
    }
    
    public function rv_select_blog_all($tablename, array $columns, array $where, array $order, $limit =false)
    {
        $dbtable = Zend_Db_Table::getDefaultAdapter();

        $select="SELECT * FROM tbl_travelogues p LEFT JOIN ( SELECT blogId, COUNT(*) as cc FROM tbl_comments as cn GROUP BY blogId ) x ON x.blogId = p.TravId where p.isMarkForDel = '0'";

        $result = $dbtable->fetchAll($select);

        if($result==NULL)
            return false;
        else
            return $result;
    }
    
    public function rv_select_blog_all_home($tablename, array $columns, array $where, array $order, $limit =false)
    {
        $dbtable = Zend_Db_Table::getDefaultAdapter();

        $select="SELECT * FROM tbl_travelogues p LEFT JOIN ( SELECT blogId, COUNT(*) as cc FROM tbl_comments as cn GROUP BY blogId ) x ON x.blogId = p.TravId where p.isMarkForDel = '0' and p.status = '1' and p.displayOnBanner = '1'";

        $result = $dbtable->fetchAll($select);
        
        if($result==NULL)
            return false;
        else
            return $result;
    }
    
    
        public function rv_select_destination_all($tablename, array $columns)
    {
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
//        if(count($where)){
//            foreach ($where as $k => $v)
//            $select->where("$k =?","$v");
//        }
//        if(count($order)){
//            foreach ($order as $k => $v)
//            $select->order("$k  $v");
//        }
//        if($limit){
//            $select->limit($limit);
//        }
//        echo $select;
//        die('here');
        $result = $dbtable->fetchAll($select);
        if($result==NULL)
            return false;
        else
            return $result->toArray();
    }

 public function rv_select_all_package($tablename, array $columns, array $where) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $sort = isset($this->searchArr['sort']) ? $this->searchArr['sort'] : 'PkgSysId';
        $order = isset($this->searchArr['order']) ? $this->searchArr['order'] : 'DESC';
        $offset = ($page - 1) * $rows;
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);

        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
//        if (isset($this->searchArr) && !empty($this->searchArr)) {
//
//            $title = $this->searchArr['Destinations'];
//            $select->where('tbl.Destinations LIKE ?', "%$title%");
//        }
        if (isset($this->searchArr) && !empty($this->searchArr)) {
            $GTXPkgId = $this->searchArr['GTXPkgId'];
            $select->where('tbl.GTXPkgId LIKE ?', "%$GTXPkgId%");

            if (isset($this->searchArr['Destinations']) && !empty($this->searchArr['Destinations'])) {
                $title = $this->searchArr['Destinations'];
                $select->where('tbl.Destinations LIKE ?', "%$title%");
            }
            if (isset($this->searchArr['GTXPkgId']) && !empty($this->searchArr['GTXPkgId'])) {
                $GTXPkgId = $this->searchArr['GTXPkgId'];
                $select->where('tbl.GTXPkgId LIKE ?', "%$GTXPkgId%");
            }
            if (isset($this->searchArr['Title']) && !empty($this->searchArr['Title'])) {
                $Title = $this->searchArr['Title'];
                $select->where('tbl.LongJsonInfo LIKE ?', "%$Title%");
            }
            if (isset($this->searchArr['name']) && !empty($this->searchArr['name'])) {
                $Title = $this->searchArr['name'];
                $select->where('tbl.LongJsonInfo LIKE ?', "%$Title%");
            }

        }
        if (count($order)) {
            $select->order("$sort $order");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
        $result = $dbtable->fetchAll($select);
        if ($result == NULL)

            return false;
        else
            return $result->toArray();
    }

    /**
    * rv_select_all() method is used to get all listing
    * @param table name, columns array, where array, order array
    * @return array result set
    */	
    public function rv_select_all_custom_query($tablename, array $columns, array $where, $whereCustom, array $order, $limit =false)
    {
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
        if(count($where)){
            foreach ($where as $k => $v)
            $select->where("$k =?","$v");
        }
        
        if(!empty($whereCustom)){
            $select->where("$whereCustom");
        }
    
        
        
        
        
        if(count($order)){
            foreach ($order as $k => $v)
            $select->order("$k  $v");
        }
        if($limit){
            $select->limit($limit);
        }
//        echo $select;
//        die('here');
        
        $result = $dbtable->fetchAll($select);
        
        if($result==NULL)
            return false;
        else
            return $result->toArray();
    }
    
    public function selectOne($tablename, array $columns, array $where) {
        //echo "hello";die;
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
        if(count($where)){
            foreach ($where as $k => $v){
                $select->where("$k =?" , "$v");
            }
        }
        
        $result = $dbtable->fetchRow($select);
        return $result;
    }
    /**
    * rv_select_row() method is used to get all listing
    * @param table name, columns array, where array, order array
    * @return array result set
    */	
    public function rv_select_row($tablename, array $columns, array $where, array $order)
    {
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
        if(count($where)){
            foreach ($where as $k => $v){
                $select->where("$k =?" , "$v");
            }
        }
        
        if(count($order)){
            foreach ($order as $k => $v)
            $select->order("$k  $v");
        }
        
//echo $select; die;
        
        $result = $dbtable->fetchRow($select);
        
        if($result==NULL)
            return false;
        else
            return $result->toArray();
    }
    
    /**
    * rv_select_row_where_custom() method is used to get all listing
    * @param table name, columns array, where array, order array
    * @return array result set
    */	
    public function rv_select_row_where_custom($tablename, array $columns, array $where, $whereCustom , array $order , $limit =false)
    {
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
        if(count($where)){
            foreach ($where as $k => $v)
            $select->where("$k =?","$v");
        }
        
        if(!empty($whereCustom)){
            $select->where("$whereCustom");
        }
    
        if(count($order)){
            foreach ($order as $k => $v)
            $select->order("$k  $v");
        }
        if($limit){
            $select->limit($limit);
        }
//        echo $select;
//        die('here');
        
        $result = $dbtable->fetchRow($select);
        
        if($result==NULL)
            return false;
        else
            return $result->toArray();
    }
    

    public function getCmsdata($tablename, array $columns, array $where, array $order)
    {
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
        if(count($where)){
            foreach ($where as $k => $v){
                $select->where("$k =?" , "$v");
            }
        }
        
        if(count($order)){
            foreach ($order as $k => $v)
            $select->order("$k  $v");
        }
        
//echo $select; die;
        
        $result = $dbtable->fetchRow($select);
        return $result;
    }

    
    
    /**
    * rv_rowExists() method is used to check state name exists or not in db
    * @param table name, columns array, where array, order array
    * @return array result set
    */
    
    public function rv_rowExists($tablename, array $columns, array $where, array $order)
    {
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("$tablename as tbl", $columns);
        
        if(count($where)){
            foreach ($where as $k => $v)
            $select->where("$k =?","$v");
        }
        if(count($order)){
            foreach ($order as $k => $v)
            $select->order("$k  $v");
        }
        $result = $dbtable->fetchOne($select);
        return $result;

    }
    
    
    public function getDestinations( $where , $order =[] , $limit = null ) 
    {
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tb_tbb2c_destinations as tbl", ['*']);

        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.region_id = tb2.sid" , ['title as region_name']);
        
        if(count($where)){
            foreach ($where as $k => $v)
            $select->where("$k =?","$v");
        }
        if(count($order)){
            foreach ($order as $k => $v)
            $select->order("$k  $v");
        }
        if( isset($this->searchArr) && !empty($this->searchArr)) {
            $title = $this->searchArr['Title'];
            $select->where('tbl.Title LIKE ?', "%$title%");
        }
        if($limit) {
            $select->limit($limit);
        }
        
//        echo $select;
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
     public function getDestinationsInd($where, $order = [], $limit = null) {
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tb_tbb2c_destinations as tbl", ['*']);

        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.region_id = tb2.sid", ['title as region_name']);

        if (count($where)) {
            foreach ($where as $k => $v)
                $select->where("$k =?", "$v");
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if (isset($this->searchArr) && !empty($this->searchArr)) {
            $title = $this->searchArr['Title'];
            $select->where('tbl.Title LIKE ?', "%$title%");
        }
        $select->where("tbl.Countries ='India'"); 
        if ($limit) {
            $select->limit($limit);
        }
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    

    public function getDestinationsInt($where, $order = [], $limit = null,$CountryIds = null) {
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tb_tbb2c_destinations as tbl", ['*']);

        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.region_id = tb2.sid", ['title as region_name']);
        //echo $CountryIds;die;
        if (count($where)) {
            foreach ($where as $k => $v)
                $select->where("$k =?", "$v");
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if (isset($this->searchArr) && !empty($this->searchArr)) {
            $title = $this->searchArr['Title'];
            $select->where('tbl.Title LIKE ?', "%$title%");
        }
        if(!empty($CountryIds)){

           $select->where("tbl.CountryIds !=?", $CountryIds); 

          // $select->where("tbl.CountryIds !=?", $CountryIds); 
           $select->where("tbl.Countries !='India'"); 

        }
        if ($limit) {
            $select->limit($limit);
        }

        

        echo $select;exit;

        $result = $dbtable->fetchAll($select);
        return $result;
    }
    public function getDestinationsHeader($where, $order = [], $limit = null) {
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tb_tbb2c_destinations as tbl", ['*']);

        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.region_id = tb2.sid", ['title as region_name','label as region_label','image as region_image']);

        if (count($where)) {
            foreach ($where as $k => $v)
                $select->where("$k =?", "$v");
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if (isset($this->searchArr) && !empty($this->searchArr)) {
            $title = $this->searchArr['Title'];
            $select->where('tbl.Title LIKE ?', "%$title%");
        }
        if ($limit) {
            $select->limit($limit);
        }

        $result = $dbtable->fetchAll($select);
        return $result;
    }

    public function getDestinationsForFooter($where, $order = [], $limit = null) {
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tb_tbb2c_destinations as tbl", ['*']);

        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.region_id = tb2.sid", ['title as region_name']);

        if (count($where)) {
            foreach ($where as $k => $v)
                $select->where("$k =?", "$v");
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if (isset($this->searchArr) && !empty($this->searchArr)) {
            $title = $this->searchArr['Title'];
            $select->where('tbl.Title LIKE ?', "%$title%");
        }
        if ($limit) {
            $select->limit($limit);
        }

        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    public function getDestinationsIndex($where, $order = []) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tb_tbb2c_destinations as tbl", ['DesSysId','DisplayOnHeader','IsActive','Title','IsFeatured', 'Countries', 'Activities', 'Tours', 'Hotels','Image','Bannerimg','DisplayOnFooter']);


        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.region_id = tb2.sid", ['title as region_name']);

        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
        if (!empty($this->searchArr)) {
            if ($this->searchArr['Title'] != "") {
                $title = $this->searchArr['Title'];
                $select->where('tbl.Title LIKE ?', "%$title%");
            }
            if ($this->searchArr['Countries'] != "") {
                $title = $this->searchArr['Countries'];
                $select->where('tbl.Countries LIKE ?', "%$title%");
            }
            if ($this->searchArr['Region'] != "") {
                $title = $this->searchArr['Region'];
                $select->where('tb2.title LIKE ?', "%$title%");
            }
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
        $result = $dbtable->fetchAll($select);
        return $result;
    }


    public function getExpertCount($where, $order = [], $limit = null) {
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tbl_our_expert as tbl", ['ExpertId' => 'COUNT(*)']);

        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.ExpertDestination = tb2.sid", ['title as ExpertDestination']);

        if (count($where)) {
            foreach ($where as $k => $v)
                $select->where("$k =?", "$v");
        }

        $result = $dbtable->fetchAll($select);
        return $result;
    }

    public function getExpertIndex($where, $order = []) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tbl_our_expert as tbl", ['ExpertId', 'ExpertName', 'ExpertEmail', 'ExpertPhone', 'ExpertImage', 'status']);

        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.ExpertDestination = tb2.sid", ['title as ExpertDestination']);

        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
        if (!empty($this->searchArr)) {
            if ($this->searchArr['ExpertDestination'] != "") {
                $title = $this->searchArr['ExpertDestination'];
                $select->where('tb2.title LIKE ?', "%$title%");
            }
            if ($this->searchArr['ExpertName'] != "") {
                $title = $this->searchArr['ExpertName'];
                $select->where('tbl.ExpertName LIKE ?', "%$title%");
            }
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
//        echo $select;die;
        $result = $dbtable->fetchAll($select);
        return $result;
    }


    public function getTraveloguesIndex($where, $order = []) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tbl_travelogues as tbl", ['TravId', 'TravTitle','TravDestination', 'TravDate', 'TravDays', 'TravTraveller', 'TravUploadedBy', 'TravImage', 'TravBannerImage', 'status']);


        $select->joinLeft(array('tb2' => "tb_tbb2c_destinations"), "tb2.DesSysId = tbl.TravDestination" , ['title as TravDestination']);

        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
if (!empty($this->searchArr)) {
            if ($this->searchArr['Title'] != "") {
                $title = $this->searchArr['Title'];
                $select->where('tbl.TravTitle LIKE ?', "%$title%");
            }
           
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
//        echo $select;die;
        $result = $dbtable->fetchAll($select);
        return $result;
    }

     public function getpacktypeIndex($where, $order = []) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tbl_pack_type as tbl", ['packType', 'Title','Icon', 'IsActive']);


//        $select->joinLeft(array('tb2' => "tb_tbb2c_destinations"), "tb2.DesSysId = tbl.TravDestination" , ['title as TravDestination']);

        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
if (!empty($this->searchArr)) {
            if ($this->searchArr['Title'] != "") {
                $title = $this->searchArr['Title'];
                $select->where('tbl.Title LIKE ?', "%$title%");
            }
           
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
//        echo $select;die;
        $result = $dbtable->fetchAll($select);
        return $result;
    }

    
       public function getTestimonialIndex($where, $order = []) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tbl_testimonials as tbl", ['*']);


//        $select->joinLeft(array('tb2' => "tb_tbb2c_destinations"), "tb2.DesSysId = tbl.TravDestination" , ['title as TravDestination']);

        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
            if (!empty($this->searchArr)) {
            if ($this->searchArr['name'] != "") {
                $title = $this->searchArr['name'];
                $select->where('tbl.name LIKE ?', "%$title%");
            }
           
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
//        echo $select;die;
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    public function rv_select_all_activitie_custom_query($where)
    {

        $dbtable = Zend_Db_Table::getDefaultAdapter();
        
        $select = "SELECT Image,IsFeatured,PkgSysId FROM tb_tbb2c_packages_master tb1 WHERE ( 3 ) = 
                ( SELECT COUNT( tb2.PkgSysId )
                   FROM tb_tbb2c_packages_master tb2
                   WHERE tb2.PkgSysId >= tb1.PkgSysId
                   AND (tb2.GTXPkgId ='$where[GTXPkgId]') AND (tb2.ItemType ='$where[ItemType]') 
                )
                 AND (tb1.IsActive='0') AND (tb1.IsMarkForDel='1') AND (tb1.IsPublish='0')";

        $result = $dbtable->fetchAll($select);
        
        if($result==NULL){
            return false;
        }
        else{
            return $result;
        }
        
    }
    
    
     public function getCount($tablename, array $where, $Id) {
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", [$Id => 'COUNT(*)']);

        if (count($where)) {
            foreach ($where as $k => $v)
                $select->where("$k =?", "$v");
        }
        $result = $dbtable->fetchAll($select);
        if ($result == NULL)


            return false;
        else
            return $result->toArray();
    }

    public function rv_select_static($tablename, array $columns, array $where, array $order) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $dbtable = new Zend_Db_Table($tablename);
        $select = $dbtable->select()->from("$tablename as tbl", $columns);

        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
        if (isset($this->searchArr) && !empty($this->searchArr)) {
            if (isset($this->searchArr['Title']) && !empty($this->searchArr['Title'])) {
            $title = $this->searchArr['Title'];
            $select->where('tbl.page_title LIKE ?', "%$title%");
            }
            if (isset($this->searchArr['Titles']) && !empty($this->searchArr['Titles'])) {
            $title = $this->searchArr['Titles'];
            $select->where('tbl.Title LIKE ?', "%$title%");
            }
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
        $result = $dbtable->fetchAll($select);
        if ($result == NULL)

            return false;
        else
            return $result->toArray();
    }

    
      public function getFlightIndex($where, $order = []) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from("tbl_flight_booking as tbl", ['id', 'response', 'IsActive', 'CreatedDate']);

//        $select->joinLeft(array('tb2' => "tbl_regions"), "tbl.ExpertDestination = tb2.sid", ['title as ExpertDestination']);

        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
        if (!empty($this->searchArr)) {
            if ($this->searchArr['bookingId'] != "") {
                $title = $this->searchArr['bookingId'];
                $select->where('tbl.response LIKE ?', "%$title%");
            }
           
        }
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
//        echo $select;die;
        $result = $dbtable->fetchAll($select);
        return $result;
    }

    
    public function getInternationalDestination($tablename, array $columns, $limit =false) {
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $this->db->select()
                ->from(array("a" => $tablename), $columns)
                ->where("a.IsActive=?", 1)
                ->where("a.IsFeatured=?", 1)
                ->where("a.IsPublish=?", 1)
                ->where("a.IsMarkForDel=?", 0)
                ->where("a.CountryIds !=?", 101);
        if($limit){
            $select->limit($limit);
        }
        //echo $select;
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    public function getBuyHotelCityAutoSuggest($keyword) {
        $keyword = trim($keyword);
        $response = array();
        $rowset = $this->getCityListWithCountryDetail($keyword);
        $respon = array();
        if (count($rowset) > 0) {
            foreach ($rowset as $row) {
                $response[] = array('CityId' => $row['CityId'], 'CityName' => stripslashes($row['cityTitle']), 'label' => $row['cityTitle'].'('.$row['Country'].')','countryTitle' => trim($row['Country']));
            }
        }
        return $response;
    }
    	public function getCityListWithCountryDetail($where){
//		$country = array('ContId','Title as countryTitle','Code as countryCode');	
		$city=array('CityId','TBBCityId','Title as cityTitle','Alias as cityAlias','Country');		
		$select = $this->db->select();
		$select->from(array('tbl' => "tb_master_geo_city") ,$city);
		//$select->joinInner(array('tb2' => "tb_master_geo_city"), "tbl.GTXCityId = tb2.CityId");
		$select->where($where);
		$select->where("tbl.IsActive = ?", 1 );
		$select->where("tbl.IsApproved = ?", 1 );
		$select->where("tbl.IsMarkForDel = ?", 0 );
		//$select->where("tb2.IsActive = ?", 1);
                //echo $select;die;
		$result = $this->db->fetchAll($select);
        return $result;
	}
    
        
        public function rv_select_promotion_all($table,$where, $order = []) {
        $page = isset($this->searchArr['page']) ? intval($this->searchArr['page']) : 1;
        $rows = isset($this->searchArr['rows']) ? intval($this->searchArr['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = $dbtable->select()->from($table, ['*']);

      
        if (count($where)) {
            foreach ($where as $k => $v) {
                $select->where("$k =?", "$v");
            }
        }
        
        if (count($order)) {
            foreach ($order as $k => $v)
                $select->order("$k  $v");
        }
        if ($rows) {
            $select->limit($rows, $offset);
        }
        echo $select;die;
        $result = $dbtable->fetchAll($select);
        return $result;
    }
    
    public function getRegionImage($region){
        $dbtable = Zend_Db_Table::getDefaultAdapter();
        $select = "SELECT image from tbl_regions where label like '%$region%'";

        $result = $dbtable->fetchOne($select);
        return $result; 
    }
}
