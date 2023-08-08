<?php
include 'connection.php';
$db=new DB();

if (isset($_GET['delete-data'])) {
    global $db;
    $id=$_GET['id'];
    $db->dml("DELETE FROM `enquiry_form` WHERE `sno`=$id");
    $db->dml("ALTER TABLE `enquiry_form` DROP `sno`;");
    $db->dml("ALTER TABLE `enquiry_form` ADD `sno` SERIAL NOT NULL FIRST;");
    header('manuplate-enquiry.php');
}
if(isset($_POST['submit-edit'])){ 
    global $db;
    $id=$_POST['id'];
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
    $date=$_POST['enquiry-date'];
    $current_status=$_POST['current-status'];
    $admission_status=$_POST['admission-status'];
    if($admission_status=='admit' ||$current_status='Admit'){
        $admission_status=1;
    }
    else {
        $admission_status=0;
    }
    
    $sql="UPDATE `enquiry_form` SET `name`='$name', `qualification`='$qualification', `fname`='$fname',
     `address`='$address', `city`='$city',`mobile`=$mobile, `course`='$course', `duration`='$duration', `timing`='$time', 
     `fees`=$fees, `enquiry_date`='$date',`current_status`='$current_status',`admission`='$admission_status' WHERE `sno`=$id;";

     if($admission_status=1){
        $db->dml("UPDATE `enquiry_form` SET `feedback`='none'  WHERE `sno`=$id;");
     }
    if ($db->dml($sql)) {
        echo "<script>alert('Updated Successfully');
        window.location.href='manuplate-enquiry.php';
        </script>";
    }
    else{
        echo "<script>alert('Not Updated');
        window.location.href='manuplate-enquiry.php';
        </script>";
    }
}
if(isset($_POST['submit-feedback'])){ 
    global $db;
    $id=$_POST['id'];
    $feedback=$_POST['feedback'];
    $sql="UPDATE `enquiry_form` SET `feedback`='$feedback' WHERE `sno`=$id";
    if ($db->dml($sql)) {
        echo "<script>alert('Added Successfully');
        window.location.href='manuplate-enquiry.php';
        </script>";
    }
    else{
        echo "<script>alert('Not Added');
        window.location.href='manuplate-enquiry.php';
        </script>";
    }

}
if (isset($_GET['show-feed-back'])) {
    global $db;
    $id=$_GET['id'];
?>
<div class="form-wrapper" id="feedback-form">
    <div class="form">
        <h4>Feed Back</h4>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <div class="input-group">
                <label class="input-group-text">Feed Back</label>
                <select name="feedback" class="form-control">
                    <option value="Not Intersted">Not Intersted</option>
                    <option value="Fees High">Fees High</option>
                    <option value="Join Later">Join Later</option>
                    <option value="Timing Problem">Timing Problem</option>
                </select>
            </div>
            <div class="input-group">
                <input type="submit" class="btn btn-primary form-control mx-1" name="submit-feedback" value="Update">
                <input type="reset" class="btn btn-danger form-control" value="Cancel" onclick='close_edit()'>
            </div>
        </form>
    </div>
</div>
<?php
echo "<script>
document.getElementById('feedback-form').style='display:unset';
</script>";
}
if(isset($_GET['edit-data'])){ 
    global $db;
    $id=$_GET['id'];
    $res=$db->dql("SELECT * FROM `enquiry_form` WHERE `sno`=$id");
    $data=mysqli_fetch_assoc($res);
    if ($data['admission']==0) {
        $data['admission']='Not Admit';
    }
    else {
        $data['admission']="Admit";
    }
?>
<!-- edit table -->
<div class="form-wrapper" id="form-edit">
    <div class="form">
        <h4>Edit Enquiry Data</h4>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo $id ?>">
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
                <input type="text" name="father-name" class="form-control" value="<?php echo $data['fname']?>"
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
                <label class="input-group-text">Date Of Enquiry:</label>
                <input type="date" name="enquiry-date" class="form-control" value='<?php echo $data['enquiry_date'] ?>' placeholder="Date Of Enquiry" required>
            </div>
            <div class="input-group">
                <label class="input-group-text">Current Status</label>
                <select name="current-status" class="form-control">
                    <option value="<?php echo $data['current_status']?>" class='bg-primary'>
                        <?php echo $data['current_status']?></option>
                    <option value="student">Student</option>
                    <option value="working">Working</option>
                </select>
            </div>
            <div class="input-group">
                <label class="input-group-text">Admission Status</label>
                <select name="admission-status" class="form-control">
                    <option value="<?php echo $data['admission']?>" class='bg-primary'><?php echo $data['admission']?>
                    </option>
                    <option value="admit">Admit</option>
                    <option value="not-admit">Not Admit</option>
                </select>
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
?>
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">


<body style="background: #000;color:#fff;">


    <div class="d-flex head-row">
        <div class="input-group search">
            <input type="text" id="searchInput" placeholder="Search...">
            <button class="btn btn-primary px-3" onclick="clearSearch()">X</button>
        </div>
        <h3 class="mx-4">Enquiry&nbsp;Data</h3>
    </div>
    <table class="table table-dark table-out">
        <thead>
            <tr>
                <th scope="col">#</th>
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
                <th scope="col">Enquiry Date</th>
                <th scope="col">Current Status</th>
                <th scope="col">Admission Status</th>
                <th scope="col">FeedBack</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php 
            $sql="SELECT * FROM `enquiry_form` ORDER BY `enquiry_date` DESC";
            $res=$db->dql($sql);
            $sno=0;
            echo "<script>let data;</script>";
            while ($data=mysqli_fetch_assoc($res)) {
                $sno+=1;
        ?>
            <tr class="data-row" style="background: #000;color:#fff;">
                <td scope="row"><?php echo $sno?></td>
                <td><?php echo $data['name']?></td>
                <td><?php echo $data['qualification']?></td>
                <td><?php echo $data['fname']?></td>
                <td><?php echo $data['address']?></td>
                <td><?php echo $data['city']?></td>
                <td><?php echo $data['mobile']?></td>
                <td><?php echo $data['course']?></td>
                <td><?php echo $data['duration']?> month</td>
                <td><?php echo $data['timing']?></td>
                <td><?php echo $data['fees']?></td>
                <td><?php echo $data['enquiry_date']?></td>
                <td><?php echo $data['current_status']?></td>
                <td id="admission-status<?php echo $sno?>"></td>
                <?php 
                    if($data['admission']==0) {
                        echo "<script>
                            data=document.getElementById('admission-status".$sno."');
                            data.style='font-weight:bold;color:red;font-size:14px;';
                            data.innerHTML='Not Admited';
                        </script>";
                    }
                    else{
                        echo "<script>
                            data=document.getElementById('admission-status".$sno."');
                            data.style='font-weight:bold;color:green;font-size:14px';
                            data.innerHTML='Admited';
                        </script>";
                    }    ?>
                <td>
                    <?php if($data['admission']==1){
                            echo $data['feedback'];
                        }
                        else{
                            if ($data['feedback']=='none') {
                                echo "<a href='manuplate-enquiry.php?id=".$data['sno']."&show-feed-back'><button class='btn btn-sm btn-primary mx-2'>+FeedBack</button></a>";
                            }
                            else{
                                echo $data['feedback']."<a href='manuplate-enquiry.php?id=".$data['sno']."&show-feed-back'><button class='btn btn-sm btn-primary mx-2'>Edit</button></a>";

                            }
                            
                        }
                    ?>
                </td>
                <td class="d-flex">
                    <button class="btn btn-sm btn-warning mx-2"
                        onclick="show_edit(<?php echo $data['sno'] ?>)">Edit</button>
                    <button class="btn btn-sm btn-danger"
                        onclick="callAlert(<?php echo $data['sno']?>,'data')">Delete</button>
                </td>
                <?php
                }?>
        </tbody>
    </table>
    <style>
    body {
        overflow-y: scroll;
    }

    .form-wrapper {
        top: 3%;
        margin: 0 30% 0 30%;
        position: fixed;
        width: 40%;
        padding: 20px;
        box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.4);
        z-index: 10;
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

    */
    </style>
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

    function callAlert(id) {
        let del = confirm("Are you surly want to delete this?");
        if (del) {
            window.location.href = "manuplate-enquiry.php?id=" + id + "&delete-data";
        }
    }

    function show_edit(id) {
        window.location.href = "manuplate-enquiry.php?id=" + id + "&edit-data";
    }

    function close_edit() {
        window.location.href = "manuplate-enquiry.php";
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
    </script>
</body>