<?php
// Handle Clear Positions action
if(isset($_POST['clear_positions'])){
  include 'includes/conn.php';
  $sql = "DELETE FROM positions";
  if($conn->query($sql)){
    $_SESSION['success'] = "All positions have been cleared.";
  }else{
    $_SESSION['error'] = "Failed to clear positions.";
  }
  header('Location: positions.php');
  exit();
}
?>
<?php
include 'includes/session.php';
// Handle JSON import for positions
if(isset($_FILES['positions_json']) && $_FILES['positions_json']['error'] == UPLOAD_ERR_OK){
  include 'includes/conn.php';
  $jsonData = file_get_contents($_FILES['positions_json']['tmp_name']);
  $positions = json_decode($jsonData, true);
  if(is_array($positions)){
    $imported = 0;
    $failed = 0;
    // Get current max priority
    $sql = "SELECT priority FROM positions ORDER BY priority DESC LIMIT 1";
    $query = $conn->query($sql);
    $row = $query->fetch_assoc();
    $priority = $row ? $row['priority'] : 0;
    foreach($positions as $pos){
      $description = isset($pos['description']) ? $conn->real_escape_string($pos['description']) : '';
      $max_vote = isset($pos['max_vote']) ? intval($pos['max_vote']) : 1;
      $priority++;
      if($description){
        $sql = "INSERT INTO positions (description, max_vote, priority) VALUES ('$description', '$max_vote', '$priority')";
        if($conn->query($sql)){
          $imported++;
        }else{
          $failed++;
        }
      }else{
        $failed++;
      }
    }
    $_SESSION['success'] = "Imported $imported positions. Failed: $failed.";
  }else{
    $_SESSION['error'] = "Invalid JSON format.";
  }
  header('Location: positions.php');
  exit();
}
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
        Positions
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Positions</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
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
      ?>
      <div class="row">
        <div class="col-xs-12">
          <form id="importForm" method="post" enctype="multipart/form-data" style="display:inline-block; margin-left:10px;">
            <input type="file" name="positions_json" accept="application/json" style="display:inline-block;" required>
            <button type="submit" class="btn btn-info btn-sm btn-flat"><i class="fa fa-upload"></i> Import JSON</button>
          </form>
        </div>
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
              <form method="post" style="display:inline-block; margin-left:10px;" onsubmit="return confirm('Are you sure you want to clear all positions?');">
                <input type="hidden" name="clear_positions" value="1">
                <button type="submit" class="btn btn-danger btn-sm btn-flat"><i class="fa fa-trash"></i> Clear Positions</button>
              </form>
<?php
// Handle Clear Positions action
if(isset($_POST['clear_positions'])){
  include 'includes/conn.php';
  $sql = "DELETE FROM positions";
  if($conn->query($sql)){
    $_SESSION['success'] = "All positions have been cleared.";
  }else{
    $_SESSION['error'] = "Failed to clear positions.";
  }
  header('Location: positions.php');
  exit();
}
?>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Description</th>
                  <th>Maximum Vote</th>
                  <th>Tools</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM positions ORDER BY priority ASC";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      echo "
                        <tr>
                          <td class='hidden'></td>
                          <td>".$row['description']."</td>
                          <td>".$row['max_vote']."</td>
                          <td>
                            <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['id']."'><i class='fa fa-edit'></i> Edit</button>
                            <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."'><i class='fa fa-trash'></i> Delete</button>
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
  <?php include 'includes/positions_modal.php'; ?>
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

});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'positions_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.id').val(response.id);
      $('#edit_description').val(response.description);
      $('#edit_max_vote').val(response.max_vote);
      $('.description').html(response.description);
    }
  });
}
</script>
</body>
</html>