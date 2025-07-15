<?php
	include 'includes/session.php';

	if(isset($_POST['delete'])){
		$id = $_POST['id'];
		$sql = "DELETE FROM employees WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Nhân viên đã được xóa thành công';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Chọn mục cần xóa trước';
	}

	header('location: employee.php');
	
?>