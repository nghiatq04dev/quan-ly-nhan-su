<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$empid = $_POST['id'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$address = $_POST['address'];
		$birthdate = $_POST['birthdate'];
		$contact = $_POST['contact'];
		$gender = $_POST['gender'];
		$position = $_POST['position'];
		$schedule = $_POST['schedule'];
		
		$sql = "UPDATE employees SET firstname = '$firstname', lastname = '$lastname', address = '$address', birthdate = '$birthdate', contact_info = '$contact', gender = '$gender', position_id = '$position', schedule_id = '$schedule' WHERE id = '$empid'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Nhân viên được cập nhật thành công';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}

	}
	else{
		$_SESSION['error'] = 'Chọn nhân viên để chỉnh sửa đầu tiên';
	}

	header('location: employee.php');
?>