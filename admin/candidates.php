<?php
// Handle Remove Candidates action
if(isset($_POST['remove_candidates'])){
  include 'includes/conn.php';
  $sql = "DELETE FROM candidates";
  if($conn->query($sql)){
    $_SESSION['success'] = "Success!<br>All candidates removed successfully.";
  }else{
    $_SESSION['error'] = "Error!<br>Failed to remove candidates.";
  }
  header('Location: candidates.php');
  exit();
}

// Handle Import Candidates from JSON
if(isset($_FILES['candidates_json']) && $_FILES['candidates_json']['error'] == UPLOAD_ERR_OK){
  include 'includes/conn.php';
  $jsonData = file_get_contents($_FILES['candidates_json']['tmp_name']);
  $candidates = json_decode($jsonData, true);
  if(is_array($candidates)){
    $imported = 0;
    $failed = 0;
    foreach($candidates as $cand){
      $firstname = isset($cand['firstname']) ? $conn->real_escape_string($cand['firstname']) : '';
      $lastname = isset($cand['lastname']) ? $conn->real_escape_string($cand['lastname']) : '';
      $position_id = isset($cand['position_id']) ? intval($cand['position_id']) : 0;
      $platform = isset($cand['platform']) ? $conn->real_escape_string($cand['platform']) : '';
      $photo = isset($cand['photo']) ? $conn->real_escape_string($cand['photo']) : '';
      if($firstname && $lastname && $position_id){
        $sql = "INSERT INTO candidates (firstname, lastname, position_id, platform, photo) VALUES ('$firstname', '$lastname', '$position_id', '$platform', '$photo')";
        if($conn->query($sql)){
          $imported++;
          // Store partylist information in session for display
          $partylist = isset($cand['partylist']) ? $cand['partylist'] : '';
          $_SESSION['candidate_partylist'][$conn->insert_id] = $partylist;
        }else{
          $failed++;
        }
      }else{
        $failed++;
      }
    }
    $_SESSION['success'] = "Success!<br>Imported $imported candidates. Failed: $failed.";
  }else{
    $_SESSION['error'] = "Error!<br>Invalid JSON format.";
  }
  echo '<meta http-equiv="refresh" content="0;url=candidates.php">';
  exit();
}
?>
<?php include 'includes/session.php'; ?>
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
        Candidates List
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Candidates</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <form id="importForm" method="post" enctype="multipart/form-data" style="display:inline-block; margin-left:10px;">
            <input type="file" name="candidates_json" accept="application/json" style="display:inline-block;" required>
            <button type="submit" class="btn btn-info btn-sm btn-flat"><i class="fa fa-upload"></i> Import JSON</button>
          </form>
        </div>
      </div>
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
        if(isset($_SESSION['notify'])){
          echo "
            <div class='alert alert-info alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-info'></i> Notification!</h4>
              ".$_SESSION['notify']."
            </div>
          ";
          unset($_SESSION['notify']);
        }
        if(isset($_SESSION['remove'])){
          echo "
            <div class='alert alert-warning alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-trash'></i> Removed!</h4>
              ".$_SESSION['remove']."
            </div>
          ";
          unset($_SESSION['remove']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
              <form method="post" style="display:inline-block; margin-left:10px;" onsubmit="return confirm('Are you sure you want to remove all candidates?');">
                <input type="hidden" name="remove_candidates" value="1">
                <button type="submit" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-trash"></i> Remove Candidates</button>
              </form>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Position</th>
                  <th>Photo</th>
                  <th>Firstname</th>
                  <th>Lastname</th>
                  <th>Platform</th>
                  <th>Tools</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT *, candidates.id AS canid FROM candidates LEFT JOIN positions ON positions.id=candidates.position_id ORDER BY positions.priority ASC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                      echo "
                        <tr>
                          <td class='hidden'></td>
                          <td>".$row['description']."</td>
                          <td>
                            <img src='".$image."' width='30px' height='30px'>
                            <a href='#edit_photo' data-toggle='modal' class='pull-right photo' data-id='".$row['canid']."'><span class='fa fa-edit'></span></a>
                          </td>
                          <td>".$row['firstname']."</td>
                          <td>".$row['lastname']." ".(isset($_SESSION['candidate_partylist'][$row['canid']]) && $_SESSION['candidate_partylist'][$row['canid']] ? "(".$_SESSION['candidate_partylist'][$row['canid']].")" : "")."</td>
                          <td><a href='#platform' data-toggle='modal' class='btn btn-info btn-sm btn-flat platform' data-id='".$row['canid']."'><i class='fa fa-search'></i> View</a></td>
                          <td>
                            <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['canid']."'><i class='fa fa-edit'></i> Edit</button>
                            <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['canid']."'><i class='fa fa-trash'></i> Delete</button>
                          </td>
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
  <?php include 'includes/candidates_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.photo', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.platform', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'candidates_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.id').val(response.canid);
      $('#edit_firstname').val(response.firstname);
      $('#edit_lastname').val(response.lastname);
      $('#posselect').val(response.position_id).html(response.description);      
      $('#edit_platform').val(response.platform);
      $('.fullname').html(response.firstname+' '+response.lastname);
      $('#desc').html(response.platform);
    }
  });
}
</script>
</body>
</html>