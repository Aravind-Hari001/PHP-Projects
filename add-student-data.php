<?php
if(isset($_POST['submit'])){
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
    
    include 'connection.php';
    $db=new DB();
    $sql="SELECT * FROM `form` WHERE `id`=$ref_no;";
    if(mysqli_num_rows($db->dql($sql))<1){
        $sql="INSERT INTO `form`(`id`, `name`, `qualification`, `father_name`, `address`, `city`,`mobile`, `course`, 
        `duration`, `timing`, `fees`, `balance`,`doj`, `staff`, `status`, `course_status`,`certificate`,`issue_date`,`image`) 
            VALUES ($ref_no,'$name','$qualification','$fname','$address','$city','$mobile','$course',
            $duration,'$time',$fees,$fees,'$doj','$staff',0,0,0,'none','none');";

        if ($db->dml($sql)) {
            echo "<script>alert('Added Successfully');</script>";
        }
        else{
            echo "<script>alert('Not Added');</script>";
        }
    }
    else{
        echo "<script>alert('Not Added Check ref.no it may be already exist');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Apollo Student Book</title>
</head>

<body style="background: #000;">
    <div class="form-wrapper">
        <div class="form" style="background: #fff;">
            <h3>Student Data</h3>
            <form action="" method="post">
                <div class="input-group">
                    <label class="input-group-text">ASVKS</label>
                    <input type="number" name="ref-no" class="form-control" placeholder="Ref No" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text">Student Name</label>
                    <input type="text" name="student-name" class="form-control" placeholder="Student Name" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text">Qualification</label>
                    <input type="text" name="qualification" class="form-control" placeholder="Qualification" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text">Father's Name</label>
                    <input type="text" name="father-name" class="form-control" placeholder="Father's Name" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text">Address</label>
                    <textarea name="address" class="form-control" placeholder="Address" required></textarea>
                </div>
                <div class="input-group">
                    <label class="input-group-text">City</label>
                    <input type="text" name="city" class="form-control" placeholder="City" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text">Mobile</label>
                    <input type="number" name="mobile" class="form-control" placeholder="Mobile" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text">Course</label>
                    <input type="text" name="course" class="form-control" placeholder="Course" required>
                </div>
                <div class='input-group'>
                    <label class="input-group-text">Duration</label>
                    <input type="number" name="duration" class="form-control" placeholder="Duration" required>
                </div>
                <div class="input-group">
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
                    <input type="number" name="fees" id='fees' class="form-control" placeholder="Course Fees" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text">Date Of Joining</label>
                    <input type="date" name="doj" class="form-control" placeholder="Date Of Joining" required>
                </div>
                <div class="input-group">
                    <label class="input-group-text">Staff</label>
                    <input type="text" name="staff-name" class="form-control" placeholder="Staff Name" required>
                </div>
                <input type="submit" class="btn btn-primary form-control" name="submit" value="Add">
            </form>
        </div>
    </div>


    <script>
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

</html>