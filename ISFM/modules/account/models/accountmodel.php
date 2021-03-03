<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class AccountModel extends CI_Model {

    /**
     * This model is using into the students controller
     * Load : $this->load->model('account');
     */
    function __construct() {
        parent::__construct();
        $this->load->dbforge();
    }

    //This function will return all students paments information
    public function stud_payment() {
        $data = array();
        $query = $this->db->query("SELECT * FROM slip_number ORDER BY slip_number DESC");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    //This function will return all income account title list
    public function inco_title() {
        $data = array();
        $query = $this->db->query("SELECT * FROM account_title WHERE category='Income'");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    //This function will return all income account title list
    public function expa_title() {
        $data = array();
        $query = $this->db->query("SELECT * FROM account_title WHERE category='Expense'");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    //This function will return Total amount in a transuctio slip
    public function pre_balence() {
        $data = array();
        $query = $this->db->query("SELECT * FROM transection ORDER BY id DESC LIMIT 1");
        foreach ($query->result_array() as $row) {
            $data[] = $row['balance'];
        }
        if (!empty($data)) {
            return $data[0];
        } else {
            return 0;
        }
    }

    //This function will reaturn only maximam slip_number
    function maxSlip() {
        $maxid = 0;
        $row = $this->db->query('SELECT MAX(slip_number) AS `maxid` FROM `slip_number`')->row();
        if ($row) {
            $maxid = $row->maxid;
        }return $maxid + 1;
    }

//
//    //This function will reaturn only maximam income
//    function maxIncome()
//        {
//        $maxid = 0;
//        $row = $this->db->query('SELECT MAX(total_amount) AS `maxid` FROM `stud_transaction`')->row();
//        if ($row) {
//            $maxid = $row->maxid;
//        }return $maxid;
//        }
    //This function will chack that is ther any tranjection submited today or not.
    public function tran_check($acco_id) {
        $d = date('d-m-Y');
        $date = strtotime($d);
        $data = array();
        $query = $this->db->query("SELECT id,amount FROM transection WHERE date = $date AND acco_id=$acco_id");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        } else {
            return 'no_entry';
        }
    }

    //This function will return Total amount in a transuctio slip
    public function totalAmount($slipNo) {
        $data = array();
        $query = $this->db->get_where('stud_transaction', array('slip_number' => $slipNo));
        foreach ($query->result_array() as $row) {
            $data[] = $row['amount'];
        }
        $ans = array_sum($data);
        return $ans;
    }

    //This function will return all employ who will get government salary
    public function salaryEmployList($month) {
        $data = array();
        $query = $this->db->query("SELECT employe_title,employ_user_id FROM set_salary WHERE month<'$month'");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

//    //This function will return all employ who will get school salary
//    public function salaryEmployList2($month){
//        $data = array();
//        $query = $this->db->query("SELECT employe_title,employ_user_id FROM set_salary WHERE school_salary<'$month'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row;
//        }
//        return $data;
//    }
    //This function will return one employ salary info
    public function ajaxSalaryAmount($uId) {
        $query = $this->db->query("SELECT total FROM set_salary WHERE employ_user_id='$uId'");
        foreach ($query->result_array() as $row) {
            $salary = $row['total'];
        }
        return $salary;
    }

    //This function will return all employ list which are paid from government
    public function employee_salary() {
        $data = array();
        $query = $this->db->query("SELECT * FROM salary");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

//    //This function will return all employ list which are paid from school
//    public function schoolEmployList(){
//        $data = array();
//        $query = $this->db->query("SELECT * FROM salary salary WHERE type='SCH'");
//        foreach($query->result_array() as $row){
//            $data[] = $row;
//        }
//        return $data;
//    }
    //This function will return employ's previous advanced taken amount
    public function preAdvance($uid) {
        $data = array();
        $query = $this->db->query("SELECT advanced_taken FROM set_salary WHERE employ_user_id=$uid");
        foreach ($query->result_array() as $row) {
            $data = $row['advanced_taken'];
        }
        return $data;
    }

    //This function will show employe title
    public function semployTitle($uid) {
        $data = array();
        $query = $this->db->query("SELECT employe_title FROM set_salary WHERE employ_user_id=$uid");
        foreach ($query->result_array() as $row) {
            $data = $row['employe_title'];
        }
        return $data;
    }

//    //THis function will show all teacher's list
//    public function teacherList(){
//        $data = array();
//        $query = $this->db->query("SELECT user_id,fullname FROM teachers_info");
//        foreach ($query->result_array() as $row){
//            $data[] = $row;
//        }
//        return $data;
//    }
//    
//    //This function will return total teacher collection on a day
//    public function teacCollectTotal(){
//        $data = array();
//        $date = strtotime(date('d-m-Y'));
//        $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE date='$date'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row['collect_amont'];
//        }
//        return array_sum($data);
//    }
//    //This function will return approved amount
//    public function teaColleApp(){
//        $data = array();
//        $date = strtotime(date('d-m-Y'));
//        $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE date='$date' AND status='Approved'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row['collect_amont'];
//        }
//        return array_sum($data);
//    }
//    //This function will return not approved amount
//    public function teaColleNotApp(){
//        $data = array();
//        $date = strtotime(date('d-m-Y'));
//        $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE date='$date' AND status='Pending'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row['collect_amont'];
//        }
//        return array_sum($data);
//    }
//     //This function will return total teacher collection on a day
//    public function oneTeacCollec($date,$uID,$class){
//        $data = array();
//        $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE date='$date' AND collector_user_id='$uID' AND class_title='$class' AND status='Pending'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row['collect_amont'];
//        }
//        return array_sum($data);
//    }
//    //This function teacher's total collect amount
//    public function teaTotalCollAmo($uID,$class){
//        $data = array();
//        $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE collector_user_id='$uID' AND class_title='$class'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row['collect_amont'];
//        }
//        return array_sum($data);
//    }
//    public function teaCollAmoApp($uID,$class){
//        $data = array();
//        $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE collector_user_id='$uID' AND class_title='$class' AND status='Approved'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row['collect_amont'];
//        }
//        return array_sum($data);
//    }
//    //THis function will return total teacher collection on a day
//    public function teaCollAmo($date,$uID,$class){
//        $data = array();
//        $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE date='$date' AND collector_user_id='$uID' AND class_title='$class' AND status='Pending'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row['collect_amont'];
//        }
//        return array_sum($data);
//    }
//    //THis function will return id string
//    public function teaCollAmoID($date,$uID,$class){
//        $data = array();
//        $query = $this->db->query("SELECT id FROM teacher_cullections WHERE date='$date' AND collector_user_id='$uID' AND class_title='$class' AND status='Pending'");
//        foreach ($query->result_array() as $row){
//            $data[] = $row['id'];
//        }
//        return implode("+",$data);
//    }
//    
//    //This funtion will return all expence data from expance table
//    public function expance(){
//        $data = array();
//        $query = $this->db->query("SELECT * FROM expense");
//        foreach ($query->result_array() as $row){
//            $data[] = $row;
//        }
//        return $data;
//    }
//    
//    //This function will return any fild amount by row id
//    public function fildAmount($table,$id,$title){
//        $query = $this->db->query("SELECT $title FROM $table WHERE id=$id");
//        foreach ($query->result_array() as $row){
//            $data = $row["$title"];
//        }
//        return $data;
//    }
    //This funtion will return all income's data from transection table
    public function income() {
        $data = array();
        $query = $this->db->query("SELECT * FROM transection WHERE category='Income' AND id !=1");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    //This function will return account title by id
    public function acc_tit_id($acco_id) {
        $data = array();
        $query = $this->db->query("SELECT account_title FROM account_title WHERE id =$acco_id");
        foreach ($query->result_array() as $row) {
            $data = $row['account_title'];
        }
        return $data;
    }

    //This funtion will return all income's data from transection table
    public function expanse() {
        $data = array();
        $query = $this->db->query("SELECT * FROM transection WHERE category='Expense' AND id !=2");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    //This function will return only one trangection information by trangection id
    public function single_tran($id) {
        $data = array();
        $query = $this->db->query("SELECT * FROM transection WHERE id='$id'");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

    //This function will return only transection id list 
    public function id_list($id) {
        $data = array();
        $query = $this->db->query("SELECT id FROM transection WHERE id>'$id'");
        foreach ($query->result_array() as $row) {
            $data[] = $row;
        }
        return $data;
    }

}
