<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$title = $_POST['title'];
		$rate = $_POST['rate'];

		$sql = "UPDATE position SET description = '$title', rate = '$rate' WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Vị trí được cập nhật thành công';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Điền vào biểu mẫu chỉnh sửa trước';
	}

	header('location:position.php');

?>