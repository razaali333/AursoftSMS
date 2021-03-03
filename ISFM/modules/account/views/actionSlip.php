<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="row">
            <div class="col-md-12">
                <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                <h3 class="page-title">
                    <?php echo lang('acc_sliact'); ?><small></small>
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
                    <li>
                        <?php echo lang('acc_sliact'); ?>
                    </li>
                    <li id="result" class="pull-right topClock"></li>
                </ul>
                <!-- END PAGE TITLE & BREADCRUMB-->
            </div>
        </div>
        <!-- END PAGE HEADER-->
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12 ">
                <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet box green ">
                    <div class="portlet-title">
                        <div class="caption">
                            <?php echo lang('acc_eodssi'); ?>
                        </div>
                        <div class="tools">
                            <a href="" class="collapse">
                            </a>
                            <a href="" class="reload">
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <?php foreach ($slips as $row) { ?>
                            <div class="form-body textAlignCenter">
                                <h1><?php echo $schoolName; ?></h1>
                                <h3><?php echo lang('acc_clastitle'); ?>: <?php echo $row['class_title']; ?></h3>
                                <p>
                                    <strong> <?php echo lang(''); ?>Student Name : <?php echo $row['student_name']; ?></strong><br>
                                    <?php echo lang('acc_sid'); ?> : <?php echo $row['student_id']; ?><br>
                                    <?php echo lang('date'); ?> :  <?php echo date("d/m/Y", $row['date']) ?>
                                </p>
                                <?php echo lang('acc_traslipno'); ?>: &nbsp;<strong><?php echo $row['slip_number']; ?></strong>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                <div class="portlet box green actionSlipBorder">

                                    <div class="portlet-body">
                                        <table class="table table-striped table-bordered table-hover" id="sample_1">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <?php echo lang('srno'); ?>
                                                    </th>
                                                    <th>
                                                        <?php echo lang('acc_accotit'); ?>
                                                    </th>
                                                    <th class="textAlignCenter">
                                                        <?php echo lang('acc_amount'); ?>
                                                    </th>
                                                    <th>

                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                foreach ($slipTrangaction as $row1) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $i; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            echo $row1['account_title'];
                                                            if ($row1['account_title'] == 'Tution Fee') {
                                                                echo ' ( ' . $row1['month'] . ' )';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="textAlignCenter">
                                                            <?php echo $row1['amount']; ?>
                                                        </td>
                                                        <td>
                                                            <a href="index.php/account/editSlip?id=<?php echo $row1['id']; ?>&slipNumber=<?php echo $row['slip_number'] ?>" class="btn btn-xs default"> <i class="fa fa-pencil-square-o"></i> <?php echo lang('edit'); ?> </a>
                                                            <a href="index.php/account/deletSlipItem?id=<?php echo $row1['id']; ?>&slipId=<?php echo $_GET['slipId']; ?>&azomu=<?php echo $row1['amount']; ?>" onclick="javascript:return confirm('<?php echo lang('acc_itrmdeconf'); ?>');" class="btn btn-xs red"> <i class="fa fa-trash-o"></i> <?php echo lang('delete'); ?> </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                                ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- END EXAMPLE TABLE PORTLET-->
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-6">
                                        <button onclick="location.href = 'javascript:history.back()'" class="btn green"><?php echo lang('back'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END SAMPLE FORM PORTLET-->
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->
<script>
    jQuery(document).ready(function () {
        //here is auto reload after 1 second for time and date in the top
        jQuery(setInterval(function () {
            jQuery("#result").load("index.php/home/iceTime");
        }, 1000));
    });

</script>
