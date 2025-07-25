<?php include 'includes/session.php'; ?>
<?php
include '../timezone.php';
$range_to = date('m/d/Y');
$range_from = date('m/d/Y', strtotime('-30 day', strtotime($range_to)));
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Tiền lương
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
          <li class="active">Tiền lương</li>
        </ol>
      </section>
      <section class="content">
  <?php
  if (isset($_SESSION['error'])) {
    echo "
      <div class='alert alert-danger alert-dismissible'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
        <h4><i class='icon fa fa-warning'></i> Lỗi!</h4>
        " . $_SESSION['error'] . "
      </div>
    ";
    unset($_SESSION['error']);
  }
  if (isset($_SESSION['success'])) {
    echo "
      <div class='alert alert-success alert-dismissible'>
        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
        <h4><i class='icon fa fa-check'></i> Thành công!</h4>
        " . $_SESSION['success'] . "
      </div>
    ";
    unset($_SESSION['success']);
  }
  ?>
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header with-border">
          <div class="pull-right">
            <form method="POST" class="form-inline" id="payForm">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range" value="<?php echo (isset($_GET['range'])) ? $_GET['range'] : $range_from . ' - ' . $range_to; ?>">
              </div>
              <!-- <button type="button" class="btn btn-success btn-sm btn-flat" id="payroll"><span class="glyphicon glyphicon-print"></span> Payroll</button> -->
              <!-- <button type="button" class="btn btn-primary btn-sm btn-flat" id="payslip"><span class="glyphicon glyphicon-print"></span> Payslip</button> -->
            </form>
          </div>
        </div>
        <div class="box-body">
          <table id="example1" class="table table-bordered">
            <thead>
              <th>Tên nhân viên</th>
              <th>Mã nhân viên</th>
              <th>Tổng</th>
              <th>Khấu trừ</th>
              <th>Ứng trước tiền mặt</th>
              <th>Tổng nhận</th>
            </thead>
            <tbody>
              <?php
              // Truy vấn để tính tổng khấu trừ
              $sql = "SELECT SUM(amount) as total_amount FROM deductions";
              $query = $conn->query($sql);
              $drow = $query->fetch_assoc();
              $deduction = $drow['total_amount'] ?? 0;

              $to = date('Y-m-d');
              $from = date('Y-m-d', strtotime('-30 day', strtotime($to)));

              if (isset($_GET['range'])) {
                $range = $_GET['range'];
                $ex = explode(' - ', $range);
                $from = date('Y-m-d', strtotime($ex[0]));
                $to = date('Y-m-d', strtotime($ex[1]));
              }

              // Truy vấn để lấy thông tin chấm công và tổng số giờ làm việc
              $sql = "SELECT employees.firstname, employees.lastname, employees.employee_id, position.rate, 
                             SUM(attendance.num_hr) AS total_hr, 
                             employees.id AS empid
                      FROM attendance 
                      LEFT JOIN employees ON employees.id = attendance.employee_id 
                      LEFT JOIN position ON position.id = employees.position_id 
                      WHERE attendance.date BETWEEN '$from' AND '$to' 
                      GROUP BY employees.id 
                      ORDER BY employees.lastname ASC, employees.firstname ASC";
              
              $query = $conn->query($sql);

              while ($row = $query->fetch_assoc()) {
                $empid = $row['empid'];

                // Truy vấn để tính tổng số tiền ứng trước
                $casql = "SELECT SUM(amount) AS cashamount 
                          FROM cashadvance 
                          WHERE employee_id='$empid' 
                          AND date_advance BETWEEN '$from' AND '$to'";
                $caquery = $conn->query($casql);
                $carow = $caquery->fetch_assoc();
                $cashadvance = $carow['cashamount'] ?? 0;

                // Tính toán lương gộp, khấu trừ và lương thực nhận
                $gross = $row['rate'] * $row['total_hr'] ?? 0;
                $total_deduction = $deduction + $cashadvance ?? 0;
                $net = $gross - $total_deduction ?? 0;

                // Xuất kết quả
                echo "
                  <tr>
                    <td>" . $row['firstname'] . " " . $row['lastname'] . "</td>
                    <td>" . $row['employee_id'] . "</td>
                    <td>" . number_format($gross) . " VNĐ</td>
                    <td>" . number_format($deduction) . " VNĐ</td>
                    <td>" . number_format($cashadvance) . " VNĐ</td>
                    <td>" . number_format($net) . " VNĐ</td>
                  </tr>
                ";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

    </div>

    <?php include 'includes/footer.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>
    $(function() {
      $('.edit').click(function(e) {
        e.preventDefault();
        $('#edit').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      $('.delete').click(function(e) {
        e.preventDefault();
        $('#delete').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      $("#reservation").on('change', function() {
        var range = encodeURI($(this).val());
        window.location = 'payroll.php?range=' + range;
      });

      $('#payroll').click(function(e) {
        e.preventDefault();
        $('#payForm').attr('action', 'payroll_generate.php');
        $('#payForm').submit();
      });

      $('#payslip').click(function(e) {
        e.preventDefault();
        $('#payForm').attr('action', 'payslip_generate.php');
        $('#payForm').submit();
      });

    });

    function getRow(id) {
      $.ajax({
        type: 'POST',
        url: 'position_row.php',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          $('#posid').val(response.id);
          $('#edit_title').val(response.description);
          $('#edit_rate').val(response.rate);
          $('#del_posid').val(response.id);
          $('#del_position').html(response.description);
        }
      });
    }
  </script>
</body>

</html>