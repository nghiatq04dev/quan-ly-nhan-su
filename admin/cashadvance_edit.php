<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$amount = $_POST['amount'];
		
		$sql = "UPDATE cashadvance SET amount = '$amount' WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Ứng tiền mặt được cập nhật thành công';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Điền vào biểu mẫu chỉnh sửa trước';
	}

	header('location:cashadvance.php');

?>