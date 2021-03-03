<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Account extends MX_Controller {

    /**
     * This controller is using for controlling account and tranjection
     *
     * Maps to the following URL
     * 		http://example.com/index.php/account
     * 	- or -  
     * 		http://example.com/index.php/account/<method_name>
     */
    function __construct() {
        parent::__construct();
        $this->load->model('accountmodel');
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login');
        }
    }

    //This function is adding now account title
    public function addAccountTitle() {
        if ($this->input->post('submit', TRUE)) {
            $accuntInfo = array(
                'account_title' => $this->db->escape_like_str($this->input->post('accountTitle', TRUE)),
                'category' => $this->db->escape_like_str($this->input->post('type', TRUE)),
                'description' => $this->db->escape_like_str($this->input->post('description', TRUE))
            );
            if ($this->db->insert('account_title', $accuntInfo)) {
                $data['allAccount'] = $this->common->getAllData('account_title');
                $data['message'] = '<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
								<strong>Success ! </strong> Account title added successfully. 
							</div>';
                $this->load->view('temp/header');
                $this->load->view('addAccountTitle', $data);
                $this->load->view('temp/footer');
            }
        } else {
            $data['allAccount'] = $this->common->getAllData('account_title');
            $this->load->view('temp/header');
            $this->load->view('addAccountTitle', $data);
            $this->load->view('temp/footer');
        }
    }

    //This function is using for show all account title view
    public function allAccount() {
        $this->load->view('temp/header');
        $this->load->view('allAccount', $data);
        $this->load->view('temp/footer');
    }

    //This function will edit Account title information here.
    public function editAccountInfo() {
        $id = $this->input->get('id', TRUE);
        if ($this->input->post('submit', TRUE)) {
            $accuntInfo = array(
                'account_title' => $this->db->escape_like_str($this->input->post('accountTitle', TRUE)),
                'category' => $this->db->escape_like_str($this->input->post('type', TRUE)),
                'description' => $this->db->escape_like_str($this->input->post('description', TRUE))
            );
            $this->db->where('id', $id);
            if ($this->db->update('account_title', $accuntInfo)) {
                $data['allAccount'] = $this->common->getAllData('account_title');
                $data['message'] = '<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
								<strong>Success ! </strong>  Account title\'s information updated successfully. 
							</div>';
                $this->load->view('temp/header');
                $this->load->view('addAccountTitle', $data);
                $this->load->view('temp/footer');
            }
        } else {
            $data['accountInfo'] = $this->common->getWhere('account_title', 'id', $id);

            $this->load->view('temp/header');
            $this->load->view('editAccount', $data);
            $this->load->view('temp/footer');
        }
    }

    //This function will delete Account Title.
    public function deleteAccount() {
        $id = $this->input->get('id', TRUE);
        $this->db->delete('account_title', array('id' => $id));

        //After deleteing the account lode all Account info.
        $data['allAccount'] = $this->common->getAllData('account_title');
        $data['message'] = '<div class="alert alert-success alert-dismissable">
                                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button"></button>
                                                        <strong>Success ! </strong>  Account title deleted successfully. 
                                                </div>';
        $this->load->view('temp/header');
        $this->load->view('addAccountTitle', $data);
        $this->load->view('temp/footer');
    }

    //This function will calculat at the month end for all student's fees
    //Note: This function is not compleat.
    public function monthFeeCal() {
        $currentDay = date('d');
        $last_day = '22';
        if ($currentDay == $last_day) {
            $classquery = $this->db->query('SELECT class_title FROM class');
            foreach ($classquery->result_array() as $row) {
                $class = $row['class_title'];
                $studentQuery = $this->db->query("SELECT student_id FROM class_students WHERE class_title = '$class'");
                foreach ($studentQuery->result_array() as $row) {
                    $studentId = $row['student_id'];
                    $feeQuery = $this->db->query("SELECT * FROM student_fee_coll WHERE student_id = '$studentId'")->row();

                    $currentMonth = 'Jan';
//                  $currentMonth = date('M');
                    if ($currentMonth == 'Jan') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        $cont = $configQuery->contributions;
                        $contributions = $cont + $feeQuery->contributions;
                        $game = $configQuery->game;
                        $gameFee = $game + $feeQuery->game;
                        $library = $configQuery->library_member_fee;
                        $libraryFee = $library + $feeQuery->library_member_fee;
                        $receipt = $configQuery->receipt;
                        $receiptFee = $receipt + $feeQuery->receipt;
                        $squar = $configQuery->square_girls_guide;
                        $square_girls_guide = $squar + $feeQuery->square_girls_guide;
                        if ($configQuery->exam_month == 'Jan') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                        }
                        $total_amount = $tuition + $contributions + $gameFee + $libraryFee + $receiptFee + $square_girls_guide + $examination_fee;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }
                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan'),
                            'month_countay' => $this->db->escape_like_str(1),
                            'contributions' => $this->db->escape_like_str($contributions),
                            'game' => $this->db->escape_like_str($gameFee),
                            'library_member_fee' => $this->db->escape_like_str($libraryFee),
                            'receipt' => $this->db->escape_like_str($receiptFee),
                            'square_girls_guide' => $this->db->escape_like_str($square_girls_guide),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Feb') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Feb') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }
                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan, Feb'),
                            'month_countay' => $this->db->escape_like_str(2),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Mar') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Mar') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }
                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - Mar'),
                            'month_countay' => $this->db->escape_like_str(3),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Apr') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Apr') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }

                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - Apr'),
                            'month_countay' => $this->db->escape_like_str(4),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'May') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'May') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }
                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - May'),
                            'month_countay' => $this->db->escape_like_str(5),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Jun') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Jun') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }
                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - Jun'),
                            'month_countay' => $this->db->escape_like_str(6),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Jul') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Jul') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }

                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - Jul'),
                            'month_countay' => $this->db->escape_like_str(7),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Aug') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Aug') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }

                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - Aug'),
                            'month_countay' => $this->db->escape_like_str(8),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Sep') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Sep') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }
                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - Sep'),
                            'month_countay' => $this->db->escape_like_str(9),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Oct') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,contributions,game,library_member_fee,receipt,square_girls_guide,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Oct') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }
                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - Oct'),
                            'month_countay' => $this->db->escape_like_str(10),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    } elseif ($currentMonth == 'Nov') {
                        $configQuery = $this->db->query("SELECT exam_month,tuition,laboratory_charges,electricity,poor_fund,development_charge,teacher_welfare_fund,religion,examination_fee FROM set_fees WHERE class_title = '$class'")->row();
                        $tuit = $configQuery->tuition * 2;
                        $tuition = $tuit + $feeQuery->tuition;
                        if ($configQuery->exam_month == 'Nov') {
                            $exami = $configQuery->examination_fee;
                            $examination_fee = $exami + $feeQuery->examination_fee;
                        } else {
                            $examination_fee = $feeQuery->examination_fee;
                            $exami = $feeQuery->examination_fee;
                        }
                        $labora = $configQuery->laboratory_charges;
                        $laboratory_charges = $labora + $feeQuery->laboratory_charges;
                        $elect = $configQuery->electricity;
                        $electricity = $elect + $feeQuery->electricity;
                        $poor = $configQuery->poor_fund;
                        $poor_fund = $poor + $feeQuery->poor_fund;
                        $devel = $configQuery->development_charge;
                        $development_charge = $devel + $feeQuery->development_charge;
                        $teac = $configQuery->teacher_welfare_fund;
                        $teacher_welfare_fund = $teac + $feeQuery->teacher_welfare_fund;
                        $relig = $configQuery->religion;
                        $religion = $relig + $feeQuery->religion;
                        $total = $feeQuery->total_amount;
                        $total_amount = $total + $tuit + $exami + $labora + $elect + $poor + $devel + $teac + $relig;
                        $submit = $feeQuery->submited_amount;
                        $due = $feeQuery->due_amount;
                        $advanced_pay = $feeQuery->advanced_pay;
                        $due_amount = $total_amount - $submit;
                        if ($advanced_pay != 0 && $advanced_pay >= $due_amount) {
                            $advanced_pay = $advanced_pay - $due_amount;
                            $finalDue = 0;
                            $submitAmount = $submit + $due_amount;
                        } elseif ($advanced_pay != 0 && $advanced_pay <= $due_amount) {
                            $finalDue = $due_amount - $advanced_pay;
                            $submitAmount = $submit + $advanced_pay;
                            $advanced_pay = 0;
                        } else {
                            $finalDue = $due_amount;
                            $submitAmount = $submit;
                        }
                        $feeUpdate = array(
                            'tuition' => $this->db->escape_like_str($tuition),
                            'month' => $this->db->escape_like_str('Jan - Dec'),
                            'month_countay' => $this->db->escape_like_str(12),
                            'laboratory_charges' => $this->db->escape_like_str($laboratory_charges),
                            'electricity' => $this->db->escape_like_str($electricity),
                            'poor_fund' => $this->db->escape_like_str($poor_fund),
                            'development_charge' => $this->db->escape_like_str($development_charge),
                            'teacher_welfare_fund' => $this->db->escape_like_str($teacher_welfare_fund),
                            'religion' => $this->db->escape_like_str($religion),
                            'examination_fee' => $this->db->escape_like_str($examination_fee),
                            'total_amount' => $this->db->escape_like_str($total_amount),
                            'due_amount' => $this->db->escape_like_str($finalDue),
                            'advanced_pay' => $this->db->escape_like_str($advanced_pay),
                            'submited_amount' => $this->db->escape_like_str($submitAmount),
                        );
                        $this->db->where('student_id', $studentId);
                        $this->db->update('student_fee_coll', $feeUpdate);
                    }
                }
            }
//            redirect('home/index','refresh');
            echo 'Compleate';
        } else {
            echo 'Today is not last day of the month. System is not ready for calculation.';
        }
    }

    //THis function make student tranjection and slip.
    public function studentTranjection() {
        if ($this->input->post('submit', TRUE)) {
            $studentId = $this->input->post('studentId', TRUE);
            $studentName = $this->input->post('studentName', TRUE);
            $date = $this->input->post('date', TRUE);
            $dateInte = strtotime($date);
            $classTitle = $this->input->post('class', TRUE);
            $slipNumber = $this->accountmodel->maxSlip();
            $totalAmount1 = '';
            $totalAmount2 = '';
            $totalAmount3 = '';
            $totalAmount4 = '';
            $totalAmount5 = '';
            $totalAmount6 = '';
            $totalAmount7 = '';
            $totalAmount8 = '';
            $totalAmount9 = '';
            $totalAmount10 = '';
            $totalAmount11 = '';
            $totalAmount12 = '';
            $totalAmount13 = '';
            $totalAmount14 = '';
            $totalAmount15 = '';
            $totalAmount16 = '';
            $totalAmount17 = '';
            $collTOtal = 0;
            //Here is chacking that is it admission's inforamtions ?
            if ($this->input->post('admitionFeeAmount', TRUE)) {
                $accountTitle = $this->input->post('admitionType', TRUE);
                $totalAmount1 = $this->input->post('admitionFeeAmount', TRUE);
                $incomeInfo = array(
                    'year' => date('Y'),
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str($accountTitle),
                    'amount' => $this->db->escape_like_str($totalAmount1),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal = $totalAmount1;
                $this->db->insert('stud_transaction', $incomeInfo);
            }

            //Here is chacking that is it tution Fee's inforamtions ?
            if ($this->input->post('tutionFeeAmount', TRUE)) {
                $totalAmount2 = $this->input->post('tutionFeeAmount', TRUE);
                $feeRange = $this->input->post('TFRF', TRUE) . ' - ' . $this->input->post('TFRT', TRUE);
                $incomeInfo = array(
                    'month' => $this->db->escape_like_str($feeRange),
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Tution Fee'),
                    'amount' => $this->db->escape_like_str($totalAmount2),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount2;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it admissin from fee's inforamtions ?
            if ($this->input->post('fine', TRUE)) {
                $totalAmount3 = $this->input->post('fine', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Fine'),
                    'amount' => $this->db->escape_like_str($totalAmount3),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount3;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it registration fee's inforamtions ?
            if ($this->input->post('Contributions', TRUE)) {
                $totalAmount4 = $this->input->post('Contributions', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Contributions'),
                    'amount' => $this->db->escape_like_str($totalAmount4),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount4;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it Library or Lab fee's inforamtions ?
            if ($this->input->post('game', TRUE)) {
                $totalAmount5 = $this->input->post('game', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Game Fee'),
                    'amount' => $this->db->escape_like_str($totalAmount5),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount5;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it internship fee's inforamtions ?
            if ($this->input->post('library', TRUE)) {
                $totalAmount6 = $this->input->post('library', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Library Fee'),
                    'amount' => $this->db->escape_like_str($totalAmount6),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount6;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it Certificate/Transcript Issue fee's inforamtions ?
            if ($this->input->post('laboratory', TRUE)) {
                $totalAmount7 = $this->input->post('laboratory', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Laboratory Fee'),
                    'amount' => $this->db->escape_like_str($totalAmount7),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount7;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it Exam Center fee's inforamtions ?
            if ($this->input->post('receipt', TRUE)) {
                $totalAmount8 = $this->input->post('receipt', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Receipt Book'),
                    'amount' => $this->db->escape_like_str($totalAmount8),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount8;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it ID Card fee's inforamtions ?
            if ($this->input->post('square_girls_guide', TRUE)) {
                $totalAmount9 = $this->input->post('square_girls_guide', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Square & Girls Guide'),
                    'amount' => $this->db->escape_like_str($totalAmount9),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount9;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it WorkShop Registation fee's inforamtions ?
            if ($this->input->post('electricity', TRUE)) {
                $totalAmount10 = $this->input->post('electricity', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Electricity'),
                    'amount' => $this->db->escape_like_str($totalAmount10),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount10;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it Delay fee's inforamtions ?
            if ($this->input->post('poor_fund', TRUE)) {
                $totalAmount11 = $this->input->post('poor_fund', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Poor Fund'),
                    'amount' => $this->db->escape_like_str($totalAmount11),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount11;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it  Others fee's inforamtions ?
            if ($this->input->post('development_charge', TRUE)) {
                $totalAmount12 = $this->input->post('development_charge', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Development Charge'),
                    'amount' => $this->db->escape_like_str($totalAmount12),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount12;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it  Religion inforamtions ?
            if ($this->input->post('religion', TRUE)) {
                $totalAmount13 = $this->input->post('religion', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Religion Fee'),
                    'amount' => $this->db->escape_like_str($totalAmount13),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount13;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it examination_fee ?
            if ($this->input->post('examination_fee', TRUE)) {
                $totalAmount14 = $this->input->post('examination_fee', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Examination Fee'),
                    'amount' => $this->db->escape_like_str($totalAmount14),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount14;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it teacher_welfare_fund ?
            if ($this->input->post('teacher_welfare_fund', TRUE)) {
                $totalAmount15 = $this->input->post('teacher_welfare_fund', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Teacher Welfare Fund'),
                    'amount' => $this->db->escape_like_str($totalAmount15),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount15;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it duePay ?
            if ($this->input->post('duePay', TRUE)) {
                $totalAmount16 = $this->input->post('duePay', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Due Pay'),
                    'amount' => $this->db->escape_like_str($totalAmount16),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount16;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
            //Here is chacking that is it  Others fee's inforamtions ?
            if ($this->input->post('OthersAmount', TRUE)) {
                $totalAmount17 = $this->input->post('OthersAmount', TRUE);
                $incomeInfo = array(
                    'class_title' => $this->db->escape_like_str($classTitle),
                    'student_id' => $this->db->escape_like_str($studentId),
                    'student_title' => $this->db->escape_like_str($studentName),
                    'date' => $this->db->escape_like_str($dateInte),
                    'account_title' => $this->db->escape_like_str('Others Amount'),
                    'amount' => $this->db->escape_like_str($totalAmount17),
                    'slip_number' => $this->db->escape_like_str($slipNumber)
                );
                $collTOtal += $totalAmount17;
                $this->db->insert('stud_transaction', $incomeInfo);
            }
//            $user = $this->ion_auth->user()->row();
//            $collTren = array(
//                'date' => $this->db->escape_like_str($dateInte),
//                'class_title' => $this->db->escape_like_str($classTitle),
//                'collector_user_id' => $this->db->escape_like_str($user->id),
//                'collector_title' => $this->db->escape_like_str($user->username),
//                'collect_amont' => $this->db->escape_like_str($collTOtal),
//                'status' => $this->db->escape_like_str('Pending'),
//                'slip_number' => $this->db->escape_like_str($slipNumber)
//            );
//            $this->db->insert('teacher_cullections', $collTren);
            $pre_balence = $this->accountmodel->pre_balence();
            $balence = $pre_balence + $collTOtal;
            $entry_info = $this->accountmodel->tran_check(1);
            if ($entry_info == 'no_entry') {
                $inco_data = array(
                    'date' => $this->db->escape_like_str(strtotime(date('d-m-Y'))),
                    'acco_id' => $this->db->escape_like_str(1),
                    'category' => $this->db->escape_like_str('Income'),
                    'amount' => $this->db->escape_like_str($collTOtal),
                    'balance' => $this->db->escape_like_str($balence)
                );
                $this->db->insert('transection', $inco_data);
            } else {
                $inco_data = array(
                    'date' => $this->db->escape_like_str(strtotime(date('d-m-Y'))),
                    'acco_id' => $this->db->escape_like_str(1),
                    'category' => $this->db->escape_like_str('Income'),
                    'amount' => $this->db->escape_like_str($collTOtal + $entry_info[0]['amount']),
                    'balance' => $this->db->escape_like_str($balence)
                );
                $row_id = $entry_info[0]['id'];
                $this->db->where('id', $row_id);
                $this->db->update('transection', $inco_data);
            }
            $slipInfo = array(
                'date' => $this->db->escape_like_str($dateInte),
                'class_title' => $this->db->escape_like_str($classTitle),
                'student_id' => $this->db->escape_like_str($studentId),
                'student_name' => $this->db->escape_like_str($studentName),
                'slip_number' => $this->db->escape_like_str($slipNumber)
            );
            if ($this->db->insert('slip_number', $slipInfo)) {
                redirect('account/allSlips', 'refresh');
            }
        } else {
            $data['classTile'] = $this->common->getAllData('class');
            $data['currency'] = $this->common->currencyClass();
            $this->load->view('temp/header');
            $this->load->view('studentTranjection', $data);
            $this->load->view('temp/footer');
        }
    }

    //This function will show students own due and pay
    public function due_pay() {
        if ($this->input->post('submit', TRUE)) {
            
        } else {
            $this->load->view('temp/header');
            $this->load->view('due_pay');
            $this->load->view('temp/footer');
        }
    }

    //This function will load all students trangections slips
    public function allSlips() {
        $data['slips'] = $this->accountmodel->stud_payment();
        $this->load->view('temp/header');
        $this->load->view('allSlips', $data);
        $this->load->view('temp/footer');
    }

    //Show invioce or students tranjection slips details
    public function slipDetails() {
        $slipId = $this->input->get('slipId', TRUE);
        $data['slips'] = $this->common->getAllData('slip_number');
        $data['slips'] = $this->common->getWhere('slip_number', 'slip_number', $slipId);
        $data['slipTrangaction'] = $this->common->getWhere('stud_transaction', 'slip_number', $slipId);
        $data['totalAmount'] = $this->accountmodel->totalAmount($slipId);
        $data['schoolName'] = $this->common->schoolName();
        $data['currency'] = $this->common->currencyClass();
        $this->load->view('temp/header');
        $this->load->view('slipDetails', $data);
        $this->load->view('temp/footer');
    }

    //This function make slip action.
    public function slipAction() {
        $slipId = $this->input->get('slipId', TRUE);
        $data['slips'] = $this->common->getWhere('slip_number', 'slip_number', $slipId);
        $data['slipTrangaction'] = $this->common->getWhere('stud_transaction', 'slip_number', $slipId);
        $data['totalAmount'] = $this->accountmodel->totalAmount($slipId);
        $data['schoolName'] = $this->common->schoolName();
        $this->load->view('temp/header');
        $this->load->view('actionSlip', $data);
        $this->load->view('temp/footer');
    }

    //This function can edeit slip information.
    public function editSlip() {
        $id = $this->input->get('id', TRUE);
        if ($this->input->post('submit', TRUE)) {
            if ($this->input->post('accountTitle', TRUE) == 'Tution Fee') {
                $slipNumber = $this->input->post('slipNumber', TRUE);
                $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE slip_number = '$slipNumber'")->row();
                $totalAmount = $query->collect_amont;
                $editAmount = $this->input->post('editAmount', TRUE);
                $amount = $this->input->post('amount', TRUE);
                $feeRange = $this->input->post('TFRF', TRUE) . ' - ' . $this->input->post('TFRT', TRUE);
                if ($amount < $editAmount) {
                    $value = $editAmount - $amount;
                    $updateData = array(
                        'amount' => $this->db->escape_like_str($amount + $value),
                        'month' => $this->db->escape_like_str($feeRange),
                    );

                    $this->db->where('id', $id);
                    $this->db->update('stud_transaction', $updateData);
                    $collectInfo = array(
                        'collect_amont' => $this->db->escape_like_str($totalAmount + $value)
                    );
                    $this->db->where('slip_number', $slipNumber);
                    $this->db->update('teacher_cullections', $collectInfo);
                } elseif ($amount > $editAmount) {
                    $value = $amount - $editAmount;
                    $updateData = array(
                        'amount' => $this->db->escape_like_str($amount - $value),
                        'month' => $this->db->escape_like_str($feeRange),
                    );
                    $this->db->where('id', $id);
                    $this->db->update('stud_transaction', $updateData);
                    $collectInfo = array(
                        'collect_amont' => $this->db->escape_like_str($totalAmount - $value),
                        'month' => $this->db->escape_like_str($feeRange),
                    );
                    $this->db->where('slip_number', $slipNumber);
                    $this->db->update('teacher_cullections', $collectInfo);
                } else {
                    $updateData = array(
                        'amount' => $this->db->escape_like_str($amount),
                        'month' => $this->db->escape_like_str($feeRange),
                    );
                    $this->db->where('id', $id);
                    $this->db->update('stud_transaction', $updateData);
                    $collectInfo = array(
                        'collect_amont' => $this->db->escape_like_str($totalAmount + $amount)
                    );
                    $this->db->where('slip_number', $slipNumber);
                    $this->db->update('teacher_cullections', $collectInfo);
                }
            } else {
                $slipNumber = $this->input->post('slipNumber', TRUE);
                $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE slip_number = '$slipNumber'")->row();
                $totalAmount = $query->collect_amont;
                $editAmount = $this->input->post('editAmount', TRUE);
                $amount = $this->input->post('amount', TRUE);

                if ($amount < $editAmount) {
                    $value = $editAmount - $amount;
                    $updateData = array(
                        'amount' => $this->db->escape_like_str($amount + $value),
                    );
                    $this->db->where('id', $id);
                    $this->db->update('stud_transaction', $updateData);
                    $collectInfo = array(
                        'collect_amont' => $this->db->escape_like_str($totalAmount + $value)
                    );
                    $this->db->where('slip_number', $slipNumber);
                    $this->db->update('teacher_cullections', $collectInfo);
                } elseif ($amount > $editAmount) {
                    $value = $amount - $editAmount;
                    $updateData = array(
                        'amount' => $this->db->escape_like_str($amount - $value),
                    );
                    $this->db->where('id', $id);
                    $this->db->update('stud_transaction', $updateData);
                    $collectInfo = array(
                        'collect_amont' => $this->db->escape_like_str($totalAmount - $value)
                    );
                    $this->db->where('slip_number', $slipNumber);
                    $this->db->update('teacher_cullections', $collectInfo);
                } else {
                    $updateData = array(
                        'amount' => $this->db->escape_like_str($amount),
                    );
                    $this->db->where('id', $id);
                    $this->db->update('stud_transaction', $updateData);
                    $collectInfo = array(
                        'collect_amont' => $this->db->escape_like_str($totalAmount + $amount)
                    );
                    $this->db->where('slip_number', $slipNumber);
                    $this->db->update('teacher_cullections', $collectInfo);
                }
            }
            redirect('account/allSlips', 'refresh');
        } else {
            //$data['slips'] = $this->common->getAllData('slip_number');
            $data['slipTrangaction'] = $this->common->getWhere('stud_transaction', 'id', $id);
            //$data['totalAmount'] = $this->accountmodel->totalAmount($slipId);
            $this->load->view('temp/header');
            $this->load->view('editslip', $data);
            $this->load->view('temp/footer');
        }
    }

    //This function can delete a slip full.
    public function deletSlip() {
        $id = $this->input->get('id', TRUE);
        $slipNumber = $this->input->get('slipnumber', TRUE);
        if ($this->db->delete('slip_number', array('id' => $id))) {
            $this->db->delete('stud_transaction', array('slip_number' => $slipNumber));
            $this->db->delete('teacher_cullections', array('slip_number' => $slipNumber));
            $data['slips'] = $this->common->getAllData('slip_number');
            $this->load->view('temp/header');
            $this->load->view('allSlips', $data);
            $this->load->view('temp/footer');
        }
    }

    //This function can delete item in an slip.
    public function deletSlipItem() {
        $id = $this->input->get('id', TRUE);
        $slipId = $this->input->get('slipId', TRUE);
        $amount = $this->input->get('azomu', TRUE);
        if ($this->db->delete('stud_transaction', array('id' => $id))) {
            $query = $this->db->query("SELECT collect_amont FROM teacher_cullections WHERE slip_number='$slipId'")->row();
            $collAmount = $query->collect_amont;
            $updateInfo = array(
                'collect_amont' => $this->db->escape_like_str($collAmount - $amount)
            );
            $this->db->where('slip_number', $slipId);
            if ($this->db->update('teacher_cullections', $updateInfo)) {
                $data['slips'] = $this->common->getWhere('slip_number', 'slip_number', $slipId);
                $data['slipTrangaction'] = $this->common->getWhere('stud_transaction', 'slip_number', $slipId);
                $data['totalAmount'] = $this->accountmodel->totalAmount($slipId);
                $data['schoolName'] = $this->common->schoolName();
                $this->load->view('temp/header');
                $this->load->view('actionSlip', $data);
                $this->load->view('temp/footer');
            }
        }
    }

    //This function will control cashBook page
//    public function cashBookIncom() {
//        $data['income'] = $this->accountmodel->income();
//        $this->load->view('temp/header');
//        $this->load->view('cashBookIncom', $data);
//        $this->load->view('temp/footer');
//    }
//
//    //This function will control cashBook Expanse page
//    public function cashBookExpense() {
//        $data['expance'] = $this->accountmodel->expance();
//        $this->load->view('temp/header');
//        $this->load->view('cashBookExpense', $data);
//        $this->load->view('temp/footer');
//    }
    //This function will give the student information from studentID
    public function studentInfoById() {
        $studentId = $this->input->get('q', TRUE);
        $query = $this->common->stuInfoId($studentId);
        if (empty($query)) {
            echo '<div class="form-group">
                    <label class="col-md-3 control-label"></label>
                        <div class="col-md-6">
                        <div class="alert alert-danger">
                            <strong>' . lang('tea_info') . ':</strong> ' . lang('teac_1') . ' <strong>' . $studentId . '</strong>' . lang('teac_2') . '
                    </div></div></div>';
        } else {
            echo '<div class="row"><div class="col-md-offset-2 col-md-7 stuInfoIdBox">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="col-md-4 control-label">' . lang('teac_3') . ' <span class="requiredStar">  </span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="studentName" value="' . $query->student_nam . '" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">' . lang('teac_4') . ' <span class="requiredStar">  </span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="class" value="' . $this->common->class_title($query->class_id) . '" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <img src="assets/uploads/' . $query->student_photo . '" class="img-responsive" alt=""><br>
                    </div>
                </div></div>';
        }
    }

    //This function will work to pay salary to employes
    public function paySalary() {
        if ($this->input->post('submit', TRUE)) {
            $pre_balence = $this->accountmodel->pre_balence();
            $total_amount = $this->input->post('totalSalary', TRUE);
            if ($pre_balence >= $total_amount) {
                $balence = $pre_balence - $total_amount;
                $employId = $this->input->post('employId', TRUE);
                if ($this->input->post('month', TRUE) == 1) {
                    $month = 'January';
                } elseif ($this->input->post('month', TRUE) == 2) {
                    $month = 'February';
                } elseif ($this->input->post('month', TRUE) == 3) {
                    $month = 'March';
                } elseif ($this->input->post('month', TRUE) == 4) {
                    $month = 'April';
                } elseif ($this->input->post('month', TRUE) == 5) {
                    $month = 'May';
                } elseif ($this->input->post('month', TRUE) == 6) {
                    $month = 'Jun';
                } elseif ($this->input->post('month', TRUE) == 7) {
                    $month = 'July';
                } elseif ($this->input->post('month', TRUE) == 8) {
                    $month = 'August';
                } elseif ($this->input->post('month', TRUE) == 9) {
                    $month = 'Septembore';
                } elseif ($this->input->post('month', TRUE) == 10) {
                    $month = 'October';
                } elseif ($this->input->post('month', TRUE) == 11) {
                    $month = 'November';
                } elseif ($this->input->post('month', TRUE) == 12) {
                    $month = 'December';
                }
                $salary = array(
                    'year' => $this->db->escape_like_str(date('Y')),
                    'date' => $this->db->escape_like_str(strtotime(date('d-m-Y'))),
                    'month' => $this->db->escape_like_str($month),
                    'total_amount' => $this->db->escape_like_str($total_amount),
                    'method' => $this->db->escape_like_str($this->input->post('method', TRUE)),
                    'user_id' => $this->db->escape_like_str($employId),
                    'employ_title' => $this->db->escape_like_str($this->input->post('employ_title', TRUE))
                );
                if ($this->db->insert('salary', $salary)) {
                    $entry_info = $this->accountmodel->tran_check(2);
                    if ($entry_info == 'no_entry') {
                        $inco_data = array(
                            'date' => $this->db->escape_like_str(strtotime(date('d-m-Y'))),
                            'acco_id' => $this->db->escape_like_str(2),
                            'category' => $this->db->escape_like_str('Expense'),
                            'amount' => $this->db->escape_like_str($total_amount),
                            'balance' => $this->db->escape_like_str($balence)
                        );
                        $this->db->insert('transection', $inco_data);
                    } else {
                        $inco_data = array(
                            'date' => $this->db->escape_like_str(strtotime(date('d-m-Y'))),
                            'acco_id' => $this->db->escape_like_str(2),
                            'category' => $this->db->escape_like_str('Expense'),
                            'amount' => $this->db->escape_like_str($total_amount + $entry_info[0]['amount']),
                            'balance' => $this->db->escape_like_str($balence)
                        );
                        $row_id = $entry_info[0]['id'];
                        $this->db->where('id', $row_id);
                        $this->db->update('transection', $inco_data);
                    }
                }
                $satSalaryInfo = array(
                    'month' => $this->db->escape_like_str($this->input->post('month', TRUE)),
                );
                $this->db->where('employ_user_id', $employId);
                if ($this->db->update('set_salary', $satSalaryInfo)) {
                    redirect('account/paySalary', 'refresh');
                }
            } else {
                $data['message'] = '<div class="alert alert-block alert-danger fade in">
                                    <button data-dismiss="alert" class="close" type="button"></button>
                                    <h4 class="alert-heading">' . lang('error') . '</h4> ' . lang('teac_5') . '
                            </div>';
                $data['salary_list'] = $this->accountmodel->employee_salary();
                $this->load->view('temp/header');
                $this->load->view('paySalary', $data);
                $this->load->view('temp/footer');
            }
        } else {
            $data['salary_list'] = $this->accountmodel->employee_salary();
            $this->load->view('temp/header');
            $this->load->view('paySalary', $data);
            $this->load->view('temp/footer');
        }
    }

    //This function will show the employ who will get Government salary
    public function ajaxEmployInfo() {
        $month = $this->input->get('month');
        $query = $this->accountmodel->salaryEmployList($month);
        echo '<div class="form-group">
            <label class="col-md-3 control-label">' . lang('teac_6') . ' <span class="requiredStar"> * </span></label>
            <div class="col-md-9">
                <select onchange="salaryAmount(this.value)" class="form-control" name="employId" data-validation="required" data-validation-error-msg="' . lang('teac_11') . '">
                    <option value="">' . lang('select') . '</option>';
        foreach ($query as $row) {
            echo '<option value="' . $row['employ_user_id'] . '">' . $row['employe_title'] . '</option>';
        }
        echo '</select>
            </div>
        </div>
        <div id="ajaxResult_2"></div>';
    }

//    //This function will show the employ list who will get schools salary
//    public function ajaxSchSalEmp() {
//        $month = $this->input->get('month');
//        $query = $this->accountmodel->salaryEmployList2($month);
//        echo '<div class="form-group">
//            <label class="col-md-3 control-label">Employee <span class="requiredStar"> * </span></label>
//            <div class="col-md-6">
//                <select onchange="SchEmploTItle(this.value)" class="form-control" name="employId" data-validation="required" data-validation-error-msg="Select any employ first.">
//                    <option value="">Select Employee.....</option>';
//        foreach ($query as $row) {
//            echo '<option value="' . $row['employ_user_id'] . '">' . $row['employe_title'] . '</option>';
//        }
//        echo '</select>
//            </div>
//        </div>';
//    }
//
    //This function will return one employe sallary amount
    public function ajaxSalaryAmount() {
        $uId = $this->input->get('uId');
        $query = $this->accountmodel->ajaxSalaryAmount($uId);
        echo '<div class="form-group">
            <label class="col-md-3 control-label"> ' . lang('teac_7') . ' <span class="requiredStar">  </span></label>
            <div class="col-md-9">
                <input type="text" readonly="" placeholder="Readonly" class="form-control" name="totalSalary" value="' . $query . '">
            </div>
        </div><input type="hidden" name="employ_title" value="' . $this->accountmodel->semployTitle($uId) . '">';
    }

    //this function will return employ title via user id
    public function SchEmploTItle() {
        $uId = $this->input->get('uId');
        echo '<input type="hidden" name="employ_title" value="' . $this->accountmodel->semployTitle($uId) . '">';
    }

    //This function will make transection
    public function transection() {
        $date = strtotime(date('d-m-Y'));
        if ($this->input->post('expense', TRUE)) {
            $account_id = $this->input->post('account_id', TRUE);
            $amount = $this->input->post('amount', TRUE);
            $pre_balence = $this->accountmodel->pre_balence();
            if ($pre_balence >= $amount) {
                $balence = $pre_balence - $amount;
                $entry_info = $this->accountmodel->tran_check($account_id);
                if ($entry_info == 'no_entry') {
                    $inco_data = array(
                        'date' => $this->db->escape_like_str($date),
                        'acco_id' => $this->db->escape_like_str($account_id),
                        'category' => $this->db->escape_like_str('Expense'),
                        'amount' => $this->db->escape_like_str($amount),
                        'balance' => $this->db->escape_like_str($balence)
                    );
                    if ($this->db->insert('transection', $inco_data)) {
                        $data['message'] = '<div class="alert alert-block alert-success fade in">
                                            <button data-dismiss="alert" class="close" type="button"></button>
                                            <h4 class="alert-heading">' . lang('success') . ' </h4> ' . lang('teac_8') . ' 
                                    </div>';
                        $data['income'] = $this->accountmodel->income();
                        $data['expanse'] = $this->accountmodel->expanse();
                        $data['inco_title'] = $this->accountmodel->inco_title();
                        $data['expa_title'] = $this->accountmodel->expa_title();
                        $this->load->view('temp/header');
                        $this->load->view('transection', $data);
                        $this->load->view('temp/footer');
                    }
                } else {
                    $inco_data = array(
                        'date' => $this->db->escape_like_str($date),
                        'acco_id' => $this->db->escape_like_str($account_id),
                        'category' => $this->db->escape_like_str('Expense'),
                        'amount' => $this->db->escape_like_str($amount + $entry_info[0]['amount']),
                        'balance' => $this->db->escape_like_str($balence)
                    );
                    $row_id = $entry_info[0]['id'];
                    $this->db->where('id', $row_id);
                    if ($this->db->update('transection', $inco_data)) {
                        $data['message'] = '<div class="alert alert-block alert-success fade in">
                                            <button data-dismiss="alert" class="close" type="button"></button>
                                            <h4 class="alert-heading">' . lang('success') . ' </h4> ' . lang('teac_8') . '
                                    </div>';
                        $data['income'] = $this->accountmodel->income();
                        $data['expanse'] = $this->accountmodel->expanse();
                        $data['inco_title'] = $this->accountmodel->inco_title();
                        $data['expa_title'] = $this->accountmodel->expa_title();
                        $this->load->view('temp/header');
                        $this->load->view('transection', $data);
                        $this->load->view('temp/footer');
                    }
                }
            } else {
                $data['message'] = '<div class="alert alert-block alert-danger fade in">
                                    <button data-dismiss="alert" class="close" type="button"></button>
                                    <h4 class="alert-heading">' . lang('error') . '</h4> ' . lang('teac_9') . '
                            </div>';
                $data['income'] = $this->accountmodel->income();
                $data['expanse'] = $this->accountmodel->expanse();
                $data['inco_title'] = $this->accountmodel->inco_title();
                $data['expa_title'] = $this->accountmodel->expa_title();
                $this->load->view('temp/header');
                $this->load->view('transection', $data);
                $this->load->view('temp/footer');
            }
        } elseif ($this->input->post('income', TRUE)) {
            $account_id = $this->input->post('account_id', TRUE);
            $amount = $this->input->post('amount', TRUE);
            $pre_balence = $this->accountmodel->pre_balence();
            $balence = $pre_balence + $amount;
            $entry_info = $this->accountmodel->tran_check($account_id);
            if ($entry_info == 'no_entry') {
                $inco_data = array(
                    'date' => $this->db->escape_like_str($date),
                    'acco_id' => $this->db->escape_like_str($account_id),
                    'category' => $this->db->escape_like_str('Income'),
                    'amount' => $this->db->escape_like_str($amount),
                    'balance' => $this->db->escape_like_str($balence)
                );
                if ($this->db->insert('transection', $inco_data)) {
                    $data['message_2'] = '<div class="alert alert-block alert-success fade in">
                                            <button data-dismiss="alert" class="close" type="button"></button>
                                            <h4 class="alert-heading">' . lang('success') . ' </h4> ' . lang('teac_10') . '
                                    </div>';
                    $data['income'] = $this->accountmodel->income();
                    $data['expanse'] = $this->accountmodel->expanse();
                    $data['inco_title'] = $this->accountmodel->inco_title();
                    $data['expa_title'] = $this->accountmodel->expa_title();
                    $this->load->view('temp/header');
                    $this->load->view('transection', $data);
                    $this->load->view('temp/footer');
                }
            } else {
                $inco_data = array(
                    'date' => $this->db->escape_like_str($date),
                    'acco_id' => $this->db->escape_like_str($account_id),
                    'category' => $this->db->escape_like_str('Income'),
                    'amount' => $this->db->escape_like_str($amount + $entry_info[0]['amount']),
                    'balance' => $this->db->escape_like_str($balence)
                );
                $row_id = $entry_info[0]['id'];
                $this->db->where('id', $row_id);
                if ($this->db->update('transection', $inco_data)) {
                    $data['message_2'] = '<div class="alert alert-block alert-success fade in">
                                            <button data-dismiss="alert" class="close" type="button"></button>
                                            <h4 class="alert-heading">' . lang('success') . ' </h4> ' . lang('teac_10') . '
                                    </div>';
                    $data['income'] = $this->accountmodel->income();
                    $data['expanse'] = $this->accountmodel->expanse();
                    $data['inco_title'] = $this->accountmodel->inco_title();
                    $data['expa_title'] = $this->accountmodel->expa_title();
                    $this->load->view('temp/header');
                    $this->load->view('transection', $data);
                    $this->load->view('temp/footer');
                }
            }
        } else {
            $data['income'] = $this->accountmodel->income();
            $data['expanse'] = $this->accountmodel->expanse();
            $data['inco_title'] = $this->accountmodel->inco_title();
            $data['expa_title'] = $this->accountmodel->expa_title();
            $this->load->view('temp/header');
            $this->load->view('transection', $data);
            $this->load->view('temp/footer');
        }
    }

    //This function will show expanse list by date range 
    public function exp_list_da_ra() {
        $rngstrt = strtotime($this->input->post('rngstrt', TRUE));
        $rngfin = strtotime($this->input->post('rngfin', TRUE));
        $query = $this->db->query("SELECT * FROM transection WHERE date >='$rngstrt' AND date <= '$rngfin' AND category='Expense'");
        $i = 1;
        foreach ($query->result_array() as $row) {
            echo '<tr>
                    <td>
                        ' . $i . '
                    </td>
                    <td>
                        ' . date("d-m-Y", $row['date']) . '
                    </td>
                    <td>
                        ' . $this->accountmodel->acc_tit_id($row['acco_id']) . '
                    </td>
                    <td>
                        ' . $row['amount'] . '
                    </td>
                    <td>
                        ' . $row['balance'] . '
                    </td>
                </tr>';
            $i++;
        }
    }

    //This function will show expanse list by date range 
    public function inc_list_da_ra() {
        $rngstrt = strtotime($this->input->post('rngstrt', TRUE));
        $rngfin = strtotime($this->input->post('rngfin', TRUE));
        $query = $this->db->query("SELECT * FROM transection WHERE date >='$rngstrt' AND date <= '$rngfin' AND category='Income'");
        $i = 1;
        foreach ($query->result_array() as $row) {
            echo '<tr>
                    <td>
                        ' . $i . '
                    </td>
                    <td>
                        ' . date("d-m-Y", $row['date']) . '
                    </td>
                    <td>
                        ' . $this->accountmodel->acc_tit_id($row['acco_id']) . '
                    </td>
                    <td>
                        ' . $row['amount'] . '
                    </td>
                    <td>
                        ' . $row['balance'] . '
                    </td>
                </tr>';
            $i++;
        }
    }

//    //This function will edit biltin transection information
//    public function edit_transection(){
//        if($this->input->post('submit',TRUE)){
//            $tr_id = $this->input->post('tran_id',TRUE);
//            $pre_amount = $this->input->post('pre_amount',TRUE);
//            $new_amount = $this->input->post('',TRUE);
//            if($pre_amount<$new_amount){
//                $deference = $new_amount - $pre_amount;
//                $final_amount = $pre_amount + $deference;
//            }elseif($pre_amount>$new_amount){
//                $deference = $pre_amount - $new_amount;
//                $final_amount = $pre_amount - $deference;
//            }
//        }else {
//        $tr_id = $this->input->get('tr_id');
//            $data['transection'] = $this->accountmodel->single_tran($tr_id);
//            $this->load->view('temp/header');
//            $this->load->view('edit_transection',$data);
//            $this->load->view('temp/footer');
//        }
//    }
//    //This function will return account title
//    public function accountTitle() {
//        $type = $this->input->get('typ');
//        $query = $this->db->query("SELECT account_title FROM account_title WHERE category='$type'");
//        echo '<div class="form-group">
//                <label class="col-md-3 control-label"> Account Title <span class="requiredStar"> * </span></label>
//                <div class="col-md-4">
//                    <select class="form-control" name="class" data-validation="required" data-validation-error-msg="Please select a class first.">
//                        <option value="">Select Account....</option>
//                    ';
//        foreach ($query->result_array() as $row) {
//            echo '<option value="' . $row['account_title'] . '">' . $row['account_title'] . '</option>';
//        }
//        echo '</select>
//                </div>
//            </div>';
//    }
}
