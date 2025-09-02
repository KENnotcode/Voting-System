<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$voter_id = $_POST['voter_id'];
		// Use STI_(LASTNAME) as password instead of hashing the provided password
		$password = 'STI_' . strtoupper($lastname);
		$filename = $_FILES['photo']['name'];
		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
		}

		$sql = "INSERT INTO voters (voters_id, password, firstname, lastname, photo) VALUES ('$voter_id', '$password', '$firstname', '$lastname', '$filename')";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Voter added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}

	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: voters.php');
?>
