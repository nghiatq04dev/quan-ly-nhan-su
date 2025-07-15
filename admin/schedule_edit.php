<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$time_in = $_POST['time_in'];
		$time_in = date('H:i:s', strtotime($time_in));
		$time_out = $_POST['time_out'];
		$time_out = date('H:i:s', strtotime($time_out));

		$sql = "UPDATE schedules SET time_in = '$time_in', time_out = '$time_out' WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Lịch trình được cập nhật thành công';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Điền vào biểu mẫu chỉnh sửa trước';
	}

	header('location:schedule.php');

?>