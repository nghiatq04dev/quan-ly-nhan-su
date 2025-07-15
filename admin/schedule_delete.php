<?php
	include 'includes/session.php';

	if(isset($_POST['delete'])){
		$id = $_POST['id'];
		$sql = "DELETE FROM schedules WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Đã xóa lịch biểu thành công';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Chọn mục cần xóa trước';
	}

	header('location: schedule.php');
	
?>