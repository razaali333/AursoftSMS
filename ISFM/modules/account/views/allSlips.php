<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->

<?php $user = $this->ion_auth->user()->row(); $userId = $user->id;?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                <h3 class="page-title">
                    <?php echo lang('header_stu_payme'); ?><small></small>
                </h3>
                <ul class="page-breadcrumb breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <?php echo lang('home'); ?>
                    </li>
                    <li>
                        <?php echo lang('header_account'); ?>
                    </li>
                    <li>
                        <?php echo lang('header_stu_payme'); ?>
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
                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet box green">
                    <div class="portlet-title">
                        <div class="caption">
                            <?php echo lang('acc_stsl'); ?>
                        </div>
                        <div class="tools">
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table id="sample_1" class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo lang('date'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('acc_slipno'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('acc_clastitle'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('acc_sid'); ?>
                                    </th>
                                    <th>
                                        <?php echo lang('acc_stuname'); ?>
                                    </th>
                                    <th>

                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($slips as $row) { ?>
                                    <tr>
                                        <td>
                                            <?php echo date("d/m/Y", $row['date']) ?>
                                        </td>
                                        <td>
                                            <?php echo $row['slip_number']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['class_title']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['student_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['student_name']; ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-xs green tableActionButtonMargin" href="index.php/account/slipDetails?slipId=<?php echo $row['slip_number']; ?>"> <i class="fa fa-file-text-o"></i> <?php echo lang('acc_ssdet'); ?> </a>
                                            <?php if($this->common->user_access('slip_edit_delete',$userId)){?>
                                            <a href="index.php/account/deletSlip?id=<?php echo $row['id'];?>&slipnumber=<?php echo $row['slip_number'];?>" onclick="javascript:return confirm('<?php echo lang('acc_rysywtdss'); ?>')" class="btn btn-xs red"> <i class="fa fa-trash-o"></i> <?php echo lang('delete'); ?> </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
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
<script type="text/javascript" src="assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
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