<!--Start page level style-->
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<!--Start page level style-->
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                <h3 class="page-title">
                    <?php echo lang('con_set_fee'); ?> <small></small>
                </h3>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <?php echo lang('home');?>
                    </li>
                    <li>
                        <?php echo lang('con_configu'); ?>
                    </li>
                    <li>
                        <?php echo lang('con_set_st_fee'); ?>
                    </li>
                    <li id="result" class="pull-right topClock"></li>
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-offset-4 col-md-3">
                    <a class="btn blue btn-block" href="index.php/configuration/configClassFee"><h4><b><?php echo lang('con_new_class'); ?></b></h4> </a><br>
                </div>
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <?php echo lang('con_class_info'); ?> 
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo lang('slno'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_clas_tit'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_admission'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_re_add'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_tuition'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_contri'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_game'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_library'); ?> 
                                    </th>
                                    <th>
                                        <?php echo lang('con_laboratory'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_rece_book'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_sgg'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_electri'); ?>
                                    </th>
                                    <th>
                                       <?php echo lang('con_por_fund'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_religion'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_develop'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_exam_fee'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('con_tw_fu'); ?>
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                        <?php $i = 1; foreach ($classFee as $row) { ?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td>
                                            <?php echo $row['class_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['admission']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['re_admission']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['tuition']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['contributions']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['game']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['library_member_fee']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['laboratory_charges']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['receipt']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['square_girls_guide']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['electricity']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['poor_fund']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['religion']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['development_charge']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['examination_fee']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['teacher_welfare_fund']; ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs green" href="index.php/configuration/editClassFee?rid=<?php echo $row['id']; ?>"> <i class="fa fa-send-o"></i> <?php echo lang('edit'); ?> </a>
                                            <a class="btn btn-xs red" href="index.php/configuration/classFeeDelete?rid=<?php echo $row['id']; ?>" onClick="javascript:return confirm(<?php echo lang('con_cls_delet_c'); ?>)"> <i class="fa fa-trash-o"></i> <?php echo lang('delete'); ?> </a>
                                        </td>
                                    </tr>
                                    <?php $i++;} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END EXAMPLE TABLE PORTLET-->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script src="assets/admin/pages/scripts/table-advanced.js"></script>
<script>
    jQuery(document).ready(function() {
//here is auto reload after 1 second for time and date in the top
        jQuery(setInterval(function() {
            jQuery("#result").load("index.php/home/iceTime");
        }, 1000));
    });
</script>
