<?php
include 'connection.php';
$db=new DB();


if (isset($_GET['delete-data'])) {
    global $db;
    $id=$_GET['id'];
    $img=$db->dql("SELECT `image` FROM `form` WHERE `id`=$id");
    $img=mysqli_fetch_assoc($img);
    unlink($uploadDir.$img['image']);
    $db->dml("DELETE FROM `form` WHERE `id`=$id");
    $db->dml("DELETE FROM `bill` WHERE `id`=$id");
    $db->dml("ALTER TABLE `bill` DROP `sno`;");
    $db->dml("ALTER TABLE `bill` ADD `sno` SERIAL NOT NULL FIRST;");
    header('manuplate-student.php');
}
if (isset($_GET['delete-bill'])) {
    global $db;
    $sno=$_GET['sno'];
    $id=$_GET['id'];

    $db->dml("DELETE FROM `bill` WHERE `sno`=$sno");

    $get_total="SELECT SUM(`bill_amount`) AS `total` FROM `bill` WHERE `id`=$id";
    $get_total=$db->dql($get_total);
    $get_total=mysqli_fetch_assoc($get_total);

    $get_fees="SELECT `fees` FROM `form` WHERE `id`=$id";
    $get_fees=$db->dql($get_fees);
    $get_fees=mysqli_fetch_assoc($get_fees);
    $calc_output=$get_fees['fees']-$get_total['total'];

    $db->dml("UPDATE `form` SET `balance`=$calc_output WHERE `id`=$id");
    if($calc_output>0){
        $db->dml("UPDATE `form` SET `status`=0 WHERE `id`=$id");
    }
    header('manuplate-student.php');
}
?>
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
<style>
body {
    overflow-y: scroll;
}

.form-wrapper {
    top: 3%;
    margin: 0 30% 0 30%;
    position: fixed;
    width: 40%;
    box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.4);
    z-index: 10;
    padding: 20px;
    border-radius: 5px;
    background: white;
    border: 1px solid black;
    display: none;
}

form {
    max-height: 80vh;
    overflow-y: scroll;
}

.form-wrapper input,
.form-wrapper label,
.form-wrapper textarea,
.form-wrapper select {
    margin-top: 5mm;
}

.table-out {
    margin-top: 2cm;
}

table tr td {
    font-size: 15px;
}
table tr td img{
    width: 30mm;
    height: 30mm;
}
.search {
    padding: 10px;
}

.search input {
    width: 30%;
    line-height: 10mm;
    border-radius: 5px;
}

.search input::placeholder {
    padding: 0 0 0 10px;
}

.head-row {
    position: fixed;
    width: 100%;
    top: 0;
    background: black;
}

.head-row h3::after {
    content: '';
    display: block;
    height: 3px;
    width: 45%;
    margin-top: 2px;
    border-radius: 5px;
    background-color: skyblue;
}
</style>

<?php
if (isset($_GET['course-status'])) {
    $id=$_GET['id'];
    global $db;
    $res=$db->dql("SELECT * FROM `form` WHERE `id`=$id");
    $data=mysqli_fetch_assoc($res);
    if($data['course_status']==0){
        $db->dml("UPDATE `form` SET `course_status`=1 WHERE `id`=$id");
    }
    else{
        $db->dml("UPDATE `form` SET `course_status`=0 WHERE `id`=$id");
    }
    echo "<script>
    window.location.href='manuplate-student.php';
    </script>";
}
if (isset($_POST['update-certificate-status'])) {
    $status_id=$_POST['id'];
    $date=$_POST['issued-date'];
    global $db;
    $res=$db->dql("SELECT * FROM `form` WHERE `id`=$status_id");
    $data=mysqli_fetch_assoc($res);
    if($data['certificate']==0){
        $db->dml("UPDATE `form` SET `certificate`=1,`issue_date`='$date' WHERE `id`=$status_id");
    }
    else{
        $date='none';
        $db->dml("UPDATE `form` SET `certificate`=0,`issue_date`='$date' WHERE `id`=$status_id");
    }
    echo "<script>
    window.location.href='manuplate-student.php';
    </script>";
}
if (isset($_GET['certificate-status'])) {
    $id=$_GET['id'];
    global $db;
    $res=$db->dql("SELECT * FROM `form` WHERE `id`=$id");
    $data=mysqli_fetch_assoc($res);
?>
<div class="form-wrapper" id="form-issue-status">
    <div class="form">
        <h4>Issue Date</h4>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <div class="input-group">
                <label class="input-group-text">Issued Date</label>
                <input type="date" name="issued-date" id='issue-date' class="form-control" placeholder="Ref No"
                    required>
            </div>
            <div class="input-group">
                <input type="submit" class="btn btn-primary form-control mx-1" name="update-certificate-status"
                    id='upade-issue-btn' value="Update">
                <input type="reset" class="btn btn-danger form-control" value="Cancel" onclick='close_edit()'>
            </div>
        </form>
    </div>
</div>
<?php
if($data['certificate']==1){ 
    echo "<script>
        document.getElementById('issue-date').required=false;
        document.getElementById('upade-issue-btn').click();
    </script>";
}
echo "<script>
document.getElementById('form-issue-status').style='display:unset';
</script>";
}
if(isset($_GET['delete-image'])){ 
    $id=$_GET['id'];  
    $uploadDir = "assets/uploads/";
    $img=$db->dql("SELECT `image` FROM `form` WHERE `id`=$id");
    $img=mysqli_fetch_assoc($img);
    unlink($uploadDir.$img['image']);
    $db->dml("UPDATE `form` SET `image`='none' WHERE `id`=$id");
}
if (isset($_POST['submit-upload'])) {
    $id=$_POST['id'];
    $uploadDir = "assets/uploads/";
    $fileName = uniqid() . "_" . $_FILES["image"]["name"];
    $targetPath = $uploadDir . $fileName;
    $allowedTypes = array("image/jpeg", "image/png");
    $fileType = $_FILES["image"]["type"];
    if (!in_array($fileType, $allowedTypes)) {
        echo "<script>alert('Only JPG and PNG files are allowed.')
        window.location.href='manuplate-student.php';
        </script>";
    } elseif ($_FILES["image"]["size"] > 500000) { //Max Size 500 kb
        echo "<script>alert('File size must be 500KB or less.')
        window.location.href='manuplate-student.php';
        </script>";
    } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
        $img=$db->dql("SELECT `image` FROM `form` WHERE `id`=$id");
        $img=mysqli_fetch_assoc($img);
        unlink($uploadDir.$img['image']);
        $db->dml("UPDATE `form` SET `image`='$fileName' WHERE `id`=$id");
        echo "<script>alert('File uploaded successfully as: " . $fileName."')
        window.location.href='manuplate-student.php';
        </script>";
    } else {
        echo "<script>alert('Error uploading the file.')
        window.location.href='manuplate-student.php';
        </script>";
    }
             
}

if(isset($_GET['upload-image'])){ 
    $id=$_GET['id'];    
?>
<div class="form-wrapper" id="form-image-upload">
    <div class="form">
        <h4>Upload Image</h4>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <div class="input-group">
                <label class="input-group-text">Select Image</label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <div class="input-group">
                <input type="submit" class="btn btn-primary form-control mx-1" name="submit-upload" id='upade-issue-btn'
                    value="Update">
                <input type="reset" class="btn btn-danger form-control" value="Cancel" onclick='close_edit()'>
            </div>
        </form>
    </div>
</div>
<?php
echo "<script>
document.getElementById('form-image-upload').style='display:unset';
</script>";
    
}
if(isset($_POST['submit-edit'])){ 
    $id=$_POST['id'];
    $ref_no=$_POST['ref-no'];
    $name=$_POST['student-name'];
    $qualification=$_POST['qualification'];
    $fname=$_POST['father-name'];
    $address=$_POST['address'];
    $city=$_POST['city'];
    $mobile=$_POST['mobile'];
    $course=$_POST['course'];
    $duration=$_POST['duration'];
    $time=$_POST['hour-from'].':'.$_POST['minute-from'].$_POST['noon-from'].'-'.$_POST['hour-to'].':'.$_POST['minute-to'].$_POST['noon-to'];
    $fees=$_POST['fees'];
    $doj=$_POST['doj'];
    $staff=$_POST['staff-name'];
    
    
    $sql="UPDATE `form` SET `id`=$ref_no, `name`='$name', `qualification`='$qualification', `father_name`='$fname', `address`='$address', `city`='$city',`mobile`=$mobile, `course`='$course', 
    `duration`='$duration', `timing`='$time', `fees`=$fees, `balance`=$fees,`doj`='$doj', `staff`='$staff' WHERE `id`=$ref_no;";

    $get_total="SELECT SUM(`bill_amount`) AS `total` FROM `bill` WHERE `id`=$id;";
    $get_total=$db->dql($get_total);
    $get_total=mysqli_fetch_assoc($get_total);

    $get_fees="SELECT `fees` FROM `form` WHERE `id`=$id";
    $get_fees=$db->dql($get_fees);
    $get_fees=mysqli_fetch_assoc($get_fees);

    $calc_output=$get_fees['fees']-$get_total['total'];
    if ($calc_output>=0) {
        $db->dml("UPDATE `form` SET `balance`=$calc_output WHERE `id`=$id");
        if($calc_output>0){
            $db->dml("UPDATE `form` SET `status`=0 WHERE `id`=$ref_no");
        }
        else if ($calc_output==0) {
            $db->dml("UPDATE `form` SET `status`=1 WHERE `id`=$ref_no");
        }
    }
    if ($db->dml($sql)) {
        echo "<script>alert('Updated Successfully');
        window.location.href='manuplate-student.php';
        </script>";
    }
    else{
        echo "<script>alert('Not Updated');
        window.location.href='manuplate-student.php';
        </script>";
    }
     
}
if(isset($_POST['submit-edit-bill'])){
    $sno=$_POST['sno'];
    $id=$_POST['id'];
    $bill_no=$_POST['bill-no'];
    $bill_date=$_POST['bill-date'];
    $bill_amount=$_POST['bill-amount'];

    $get_total="SELECT SUM(`bill_amount`) AS `total` FROM `bill` WHERE `id`=$id AND NOT `sno`=$sno";
    $get_total=$db->dql($get_total);
    $get_total=mysqli_fetch_assoc($get_total);

    $get_fees="SELECT `fees` FROM `form` WHERE `id`=$id";
    $get_fees=$db->dql($get_fees);
    $get_fees=mysqli_fetch_assoc($get_fees);

    $calc_output=$get_fees['fees']-($get_total['total']+$bill_amount);
    if ($calc_output>=0) {
        $db->dml("UPDATE `form` SET `balance`=$calc_output WHERE `id`=$id");
        if($calc_output>0){
            $db->dml("UPDATE `form` SET `status`=0 WHERE `id`=$id");
        }
        else if ($calc_output==0) {
            $db->dml("UPDATE `form` SET `status`=1 WHERE `id`=$id");
        }   
        $sql="UPDATE `bill` SET `bill_no`=$bill_no,`bill_date`='$bill_date',`bill_amount`=$bill_amount WHERE `sno`=$sno";
        if ($db->dml($sql)) {
            echo "<script>alert('Updated Successfully');
            window.location.href='manuplate-student.php';
            </script>";
        }
        else{
            echo "<script>alert('Not Updated');
            window.location.href='manuplate-student.php';
            </script>";
        }
    }
    else{
        echo "<script>alert('Total Bill Amount greater than course fees check yor entery');
            window.location.href='manuplate-student.php.php';
            </script>";
    }
}
if(isset($_GET['edit-data'])){ 
    global $db;
    $id=$_GET['id'];
    $res=$db->dql("SELECT * FROM `form` WHERE `id`=$id");
    $data=mysqli_fetch_assoc($res);
?>
<!-- edit table -->
<div class="form-wrapper" id="form-edit">
    <div class="form">
        <h4>Edit Student Data</h4>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <div class="input-group">
                <label class="input-group-text">ASVKS</label>
                <input type="number" name="ref-no" class="form-control" value="<?php echo $data['id']?>"
                    placeholder="Ref No" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Student Name</label>
                <input type="text" name="student-name" class="form-control" value="<?php echo $data['name']?>"
                    placeholder="Student Name" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Qualification</label>
                <input type="text" name="qualification" class="form-control" value="<?php echo $data['qualification']?>"
                    placeholder="Qualification" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Father's Name</label>
                <input type="text" name="father-name" class="form-control" value="<?php echo $data['father_name']?>"
                    placeholder="Father's Name" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Address</label>
                <textarea name="address" class="form-control" placeholder="Address"
                    required><?php echo $data['address']?></textarea>
            </div>
            <div class="input-group">
                <label class="input-group-text">City</label>
                <input type="text" name="city" class="form-control" value="<?php echo $data['city']?>"
                    placeholder="City" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Mobile</label>
                <input type="number" name="mobile" class="form-control" value="<?php echo $data['mobile']?>"
                    placeholder="Mobile" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Course</label>
                <input type="text" name="course" class="form-control" value="<?php echo $data['course']?>"
                    placeholder="Course" required>
            </div>
            <div class='input-group'>
                <label class="input-group-text">Duration</label>
                <input type="number" name="duration" class="form-control" value="<?php echo $data['duration']?>"
                    placeholder="Duration" required>
            </div>
            <div class="input-group">
                <label class="input-group-text" style='font-size:12px'><b>Old :</b> <?php echo $data['timing']?></label>
                <label class="input-group-text">Timing</label>
                <label class="input-group-text">From</label>
                <select id="hour1" name="hour-from"></select>
                <select id="minute1" name="minute-from"></select>
                <select name="noon-from">
                    <option value="am">am</option>
                    <option value="pm">pm</option>
                </select>
                <label class="input-group-text">To</label>
                <select id="hour2" name="hour-to"></select>
                <select id="minute2" name="minute-to"></select>
                <select name="noon-to">
                    <option value="am">am</option>
                    <option value="pm">pm</option>
                </select>
            </div>
            <div class="input-group">
                <label class="input-group-text">Fees</label>
                <input type="number" name="fees" id='fees' class="form-control" value="<?php echo $data['fees']?>"
                    placeholder="Course Fees" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Date Of Joining</label>
                <input type="date" name="doj" class="form-control" value="<?php echo $data['doj']?>"
                    placeholder="Date Of Joining" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Staff</label>
                <input type="text" name="staff-name" class="form-control" value="<?php echo $data['staff']?>"
                    placeholder="Staff Name" required>
            </div>

            <div class="input-group">
                <input type="submit" class="btn btn-primary form-control mx-1" name="submit-edit" value="Update">
                <input type="reset" class="btn btn-danger form-control" value="Cancel" onclick='close_edit()'>
            </div>
        </form>
    </div>
</div>
<?php 
echo "<script>
document.getElementById('form-edit').style='display:unset';
</script>";
} 
if(isset($_POST['submit-add-bill'])){ 
    $id=$_POST['id'];
    $bill_no=$_POST['bill-no'];
    $bill_date=$_POST['bill-date'];
    $bill_amount=$_POST['bill-amount'];
    
    $get_total="SELECT SUM(`bill_amount`) AS `total` FROM `bill` WHERE `id`=$id";
    $get_total=$db->dql($get_total);
    $get_total=mysqli_fetch_assoc($get_total);

    $get_fees="SELECT `fees` FROM `form` WHERE `id`=$id";
    $get_fees=$db->dql($get_fees);
    $get_fees=mysqli_fetch_assoc($get_fees);

    $calc_output=$get_fees['fees']-($get_total['total']+$bill_amount);
    if ($calc_output>=0) {  
        $sql="INSERT INTO `bill`(`id`,`bill_no`, `bill_amount`, `bill_date`) VALUES ($id,$bill_no,$bill_amount,'$bill_date');";
        if ($db->dml($sql)) {

            $db->dml("UPDATE `form` SET `balance`=$calc_output WHERE `id`=$id");
            if($calc_output==0){
                $db->dml("UPDATE `form` SET `status`=1 WHERE `id`=$id");
            }
            echo "<script>alert('Added Successfully');
            window.location.href='manuplate-student.php';
            </script>";
        }
        else{
            echo "<script>alert('Not Added');
            window.location.href='manuplate-student.php';
            </script>";
        }
    }
    else {
        echo "<script>alert('Total fees is more than Course fees check the pending amount');
            window.location.href='manuplate-student.php';
            </script>";
    }
}
if(isset($_GET['add-bill'])){ 
    global $db;
    $id=$_GET['id'];
?>
<div class="form-wrapper" id="form-bill-add">
    <div class="form">
        <h4>Edit Student Data</h4>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <div class="input-group">
                <label class="input-group-text">Bill No.</label>
                <input type="number" name="bill-no" class="form-control" value="<?php echo $data['bill_no']?>"
                    placeholder="Bill Number" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Bill Date</label>
                <input type="date" name="bill-date" class="form-control" value="<?php echo $data['bill_date']?>"
                    placeholder="Bill Date" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Amount</label>
                <input type="number" name="bill-amount" id='amount' class="form-control"
                    value="<?php echo $data['amount']?>" placeholder="Bill Amount" required>
            </div>
            <div class="input-group">
                <input type="submit" class="btn btn-primary form-control mx-1" name="submit-add-bill" value="Add">
                <input type="reset" class="btn btn-danger form-control" value="Cancel" onclick='close_edit()'>
            </div>
        </form>
    </div>
</div>

<?php 
echo "<script>
document.getElementById('form-bill-add').style='display:unset';
</script>";
}
if (isset($_GET['edit-bill'])) { 
    global $db;
    $id=$_GET['id'];   
    $sno=$_GET['sno']; 
?>
<div class="form-wrapper" id="form-bill-edit">
    <div class="form">
        <h4>Edit Student Data</h4>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="hidden" name="sno" value="<?php echo $sno ?>">
            <div class="input-group">
                <label class="input-group-text">Bill No.</label>
                <input type="number" name="bill-no" class="form-control" value="<?php echo $data['bill_no']?>"
                    placeholder="Bill Number" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Bill Date</label>
                <input type="date" name="bill-date" class="form-control" value="<?php echo $data['bill_date']?>"
                    placeholder="Bill Date" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Amount</label>
                <input type="number" name="bill-amount" id='amount' class="form-control"
                    value="<?php echo $data['amount']?>" placeholder="Bill Amount" required>
            </div>
            <div class="input-group">
                <input type="submit" class="btn btn-primary form-control mx-1" name="submit-edit-bill" value="Update">
                <input type="reset" class="btn btn-danger form-control" value="Cancel" onclick='close_edit()'>
            </div>
        </form>
    </div>
</div>

<?php 
echo "<script>
document.getElementById('form-bill-edit').style='display:unset';
</script>";
} ?>

<body style="background: #000;color:#fff;">


    <div class="d-flex head-row">
        <div class="input-group search">
            <input type="text" id="searchInput" placeholder="Search...">
            <button class="btn btn-primary px-3" onclick="clearSearch()">X</button>
        </div>
        <h3 class="mx-4">Student&nbsp;Data</h3>
    </div>
    <table class="table datatable table-dark table-out">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">PIC</th>
                <th scope="col">Ref. No.</th>
                <th scope="col">Student Name</th>
                <th scope="col">Qualification</th>
                <th scope="col">Father's Name</th>
                <th scope="col">Address</th>
                <th scope="col">City</th>
                <th scope="col">Mobile</th>
                <th scope="col">Course</th>
                <th scope="col">Duration</th>
                <th scope="col">Timing</th>
                <th scope="col">Fees</th>
                <th scope="col">Date Of Joining</th>
                <th scope="col">Staff</th>
                <th scope="col">
                    <table class="table table-dark">
                        <thead>
                            <tr>
                                <th scope="col">Bill No.</th>
                                <th scope="col">Bill Date</th>
                                <th scope="col">Bill Amount</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                    </table>
                </th>
                <th scope="col">Balance</th>
                <th scope="col">Payment Status</th>
                <th scope="col">Course Status</th>
                <th scope="col">Certificate</th>
                <th scope="col">Issue Date</th>
                <th scope="col">Image</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php 
            $sql1="SELECT * FROM `form` ORDER BY `doj` DESC";
            $res1=$db->dql($sql1);
            $id=0;
            echo "<script>var data1,data2,data3;</script>";
            while ($data=mysqli_fetch_assoc($res1)) {
                $refno=$data['id'];
                $sql2="SELECT * FROM `bill` WHERE `id`=$refno;";
                $res2=$db->dql($sql2);
                $id+=1;

            ?>
            <tr class="data-row">
                <td scope="row"><?php echo $id?></td>
                <?php 
                    if ($data['image']!=='none') {
                        $image=$data['image'];
                        echo '<td><img src="assets/uploads/'.$image.'" alt="image"></td>';
                    }
                    else{
                        echo '<td class="text-info">Image Not Uploaded</td>';
                    }
                    
                ?>
                <td>ASVKS <?php echo $data['id']?></td>
                <td><?php echo $data['name']?></td>
                <td><?php echo $data['qualification']?></td>
                <td><?php echo $data['father_name']?></td>
                <td><?php echo $data['address']?></td>
                <td><?php echo $data['city']?></td>
                <td><?php echo $data['mobile']?></td>
                <td><?php echo $data['course']?></td>
                <td><?php echo $data['duration']?> month</td>
                <td><?php echo $data['timing']?></td>
                <td><?php echo $data['fees']?></td>
                <td><?php echo $data['doj']?></td>
                <td><?php echo $data['staff']?></td>
                <td>
                    <table class="table table-dark">
                        <tbody>
                            <?php  while ($bill=mysqli_fetch_assoc($res2)) {?>
                            <tr>
                                <td><?php echo $bill['bill_no']?></td>
                                <td><?php echo $bill['bill_date']?></td>
                                <td><?php echo $bill['bill_amount']?></td>
                                <td class="d-flex">
                                    <button class="btn btn-sm btn-warning mx-2"
                                        onclick="show_editBill(<?php echo $refno ?>,<?php echo $bill['sno'] ?>)">Edit</button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="callAlert(<?php echo $bill['sno']?>,'bill',<?php echo $refno ?>)">Delete</button>
                                </td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </td>
                <td><?php echo $data['balance']?></td>
                <td <?php echo "id='payment".$id."'";?>></td>
                <td><a href="manuplate-student.php?course-status&id=<?php echo $data['id'] ?>"
                        <?php echo "id='course-status".$id."'";?>></a></td>
                <td><a href="manuplate-student.php?certificate-status&id=<?php echo $data['id'] ?>"
                        <?php echo "id='certificate".$id."'";?>></a></td>
                <td><?php echo $data['issue_date'] ?></td>
                <td>
                    <?php
                        if ($data['image']=='none') {
                            echo '<a href="manuplate-student.php?id='.$data["id"].'&upload-image"><button class="btn btn-sm btn-info">upload</button></a>';
                        }
                        else{
                            echo '<a href="manuplate-student.php?id='.$data["id"].'&upload-image"><button class="btn btn-sm btn-warning">Edit</button></a>';
                            echo '<a href="manuplate-student.php?id='.$data["id"].'&delete-image"><button class="btn btn-sm btn-danger my-2">Remove</button></a>';
                        }
                    ?>
                </td>
                <td class="d-flex">
                    <button class="btn btn-sm btn-primary"
                        onclick="show_addBill(<?php echo $data['id'] ?>)">+Bill</button>
                    <button class="btn btn-sm btn-warning mx-2"
                        onclick="show_edit(<?php echo $data['id'] ?>)">Edit</button>
                    <button class="btn btn-sm btn-danger"
                        onclick="callAlert(<?php echo $data['id']?>,'data')">Delete</button>
                </td>
            </tr>

            <?php 
            if($data['status']==0) {
                echo "<script>
                    data1=document.getElementById('payment".$id."');
                    data1.style='font-weight:bold;color:red;font-size:14px;';
                    data1.innerHTML='Pending';
                </script>";
            }
            else{
                echo "<script>
                    data1=document.getElementById('payment".$id."');
                    data1.style='font-weight:bold;color:green;font-size:14px';
                    data1.innerHTML='Paid';
                </script>";
            }
            
            if($data['certificate']==0) {
               echo "<script>
                    data2=document.getElementById('certificate".$id."');
                    data2.style='font-weight:bold;color:red;font-size:14px;';
                    data2.innerHTML='Not Issued';
                </script>";
            }
            else{
                echo "<script>
                    data2=document.getElementById('certificate".$id."');
                    data2.style='font-weight:bold;color:green;';
                    data2.innerHTML='Issued';
                </script>";
                
            }

            if($data['course_status']==0) {
                echo "<script>
                     data3=document.getElementById('course-status".$id."');
                     data3.style='font-weight:bold;color:red;font-size:14px;';
                     data3.innerHTML='Not Completed';
                 </script>";
             }
             else{
                 echo "<script>
                     data3=document.getElementById('course-status".$id."');
                     data3.style='font-weight:bold;color:green;';
                     data3.innerHTML='Completed';
                 </script>";
             }
        }?>
        </tbody>
    </table>

    <script>
    function searchTable() {
        let input, filter, table, tr, td, i, txtValue, found;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("tableBody");
        tr = table.getElementsByClassName("data-row");

        for (i = 0; i < tr.length; i++) {
            found = false;
            for (var j = 0; j < tr[i].cells.length; j++) {
                td = tr[i].cells[j];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1 || filter == '') {
                        found = true;
                        break;
                    }
                }
            }
            tr[i].style.display = found ? "" : "none";
        }

    }
    document.getElementById("searchInput").addEventListener("keyup", searchTable);
    document.getElementById("searchInput").addEventListener("change", searchTable);

    function clearSearch() {
        document.getElementById("searchInput").value = "";
        searchTable();
    }

    function callAlert(id, field, refno = 0) {
        let del = confirm("Are you surly want to delete this?");
        if (del) {
            if (field == 'data')
                window.location.href = "manuplate-student.php?id=" + id + "&delete-data";
            else if (field == 'bill')
                window.location.href = "manuplate-student.php?id=" + refno + "&delete-bill&sno=" + id;
        }
    }

    const hourSelect1 = document.getElementById('hour1');
    for (let i = 1; i <= 12; i++) {
        const option = document.createElement('option');
        option.value = i.toString();
        option.textContent = i.toString();
        hourSelect1.appendChild(option);
    }

    const minuteSelect1 = document.getElementById('minute1');
    for (let i = 0; i < 60; i++) {
        const option = document.createElement('option');
        option.value = i.toString().padStart(2, '0');
        option.textContent = i.toString().padStart(2, '0');
        minuteSelect1.appendChild(option);
    }

    const hourSelect2 = document.getElementById('hour2');
    for (let i = 1; i <= 12; i++) {
        const option = document.createElement('option');
        option.value = i.toString();
        option.textContent = i.toString();
        hourSelect2.appendChild(option);
    }

    const minuteSelect2 = document.getElementById('minute2');
    for (let i = 0; i < 60; i++) {
        const option = document.createElement('option');
        option.value = i.toString().padStart(2, '0');
        option.textContent = i.toString().padStart(2, '0');
        minuteSelect2.appendChild(option);
    }

    function show_edit(id) {
        window.location.href = "manuplate-student.php?id=" + id + "&edit-data";
    }

    function show_editBill(id, sno) {
        window.location.href = "manuplate-student.php?id=" + id + "&sno=" + sno + "&edit-bill";
    }

    function show_addBill(id) {
        window.location.href = "manuplate-student.php?id=" + id + "&add-bill";
    }

    function close_edit() {
        window.location.href = "manuplate-student.php";
    }

    function calcAmount() {
        let fees = document.getElementById('fees');
        let amount = document.getElementById('amount');
        let bal = document.getElementById('balance');
        bal.value = fees.value - amount.value;
    }
    document.getElementById("amount").addEventListener("keyup", calcAmount);
    document.getElementById("amount").addEventListener("change", calcAmount);

    document.getElementById("fees").addEventListener("keyup", calcAmount);
    document.getElementById("fees").addEventListener("change", calcAmount);
    </script>
</body>