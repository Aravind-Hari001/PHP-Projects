<?php
include 'connection.php';
$db=new DB();

if (isset($_GET['log-in'])) {
    $id=$_GET['id'];
    $data=$db->dql("SELECT * FROM `form` WHERE `id`=$id");
    $data=mysqli_fetch_assoc($data);

    $name=$data['name'];
    $course=$data['course'];
    $time=$data['timing'];
    $date=date("Y-m-d");
    $current_time = date("h:i A");

    $db->dml("INSERT INTO `student-login`(`id`, `name`, `course`, `timing`, `date`, `in`, `out`) VALUES ($id,'$name','$course','$time','$date','$current_time','0')");
    echo "<script>
     alert('login successfull');
     window.location.href='student-login.php';
    </script>";
}
if (isset($_GET['log-out'])) {
    $id=$_GET['id'];  
    $current_time = date("h:i A");
    $currentDate = date("Y-m-d");
    $db->dml("UPDATE `student-login` SET `out`='$current_time' WHERE `id`=$id AND `date`='$currentDate';");
    echo "<script>
    alert('logout successfull');
    window.location.href='student-login.php';
    </script>";
}
?>
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

<body style="background: #000;color:#fff;">


    <div class="wrap-content">
        <img src="assets/logo.png" alt="logo">
        <div class="d-flex head-row">
            <div class="input-group search">
                <button class="btn btn-primary px-3" onclick="clearSearch()">X</button>
                <input type="text" id="searchInput" placeholder="Enter Student Name     eg:H.Krish">
                <button class="btn btn-info px-3" onclick="searchTable()">Search</button>
            </div>
            <h3 class="mx-4">Student&nbsp;Login</h3>
        </div>

        <div id="default-data">
            <h1>Welcome to Apollo</h1>
            <h2>please search your name for loging</h2>
            <img src="assets/apollo room.png" alt="Apollo">
        </div>

        <table class="table table-dark table-out" id="table">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col" onclick="clearSearch()" style="cursor: pointer;text-align:right;">X</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php 
            $sql1="SELECT * FROM `form` WHERE `course_status`=0";
            $res1=$db->dql($sql1);
            $id=0;
            $currentDate = date("Y-m-d");
            while ($data=mysqli_fetch_assoc($res1)) {
                $refno=$data['id'];
            ?>
                <tr class="data-row" style="background: #000;color:#fff;">
                    <?php 
                        if ($data['image']!=='none') {
                            $image=$data['image'];
                            echo '<td id="student"><img src="assets/uploads/'.$image.'" alt="image"></td>';
                        }
                        else{
                            echo '<td></td>';
                        }
                    ?>
                    <td>
                        <p><b>Student Name : </b><?php echo $data['name']?></p>
                        <p><b>Course : </b><?php echo $data['course']?></p>
                        <p><b>Timing : </b><?php echo $data['timing']?></p>
                        <?php 
                            $log_out=$db->dql("SELECT `out` FROM `student-login` WHERE `id`=$refno AND `date`='$currentDate'");
                            if(mysqli_num_rows($log_out)>0){
                                $out_status=mysqli_fetch_assoc($log_out);
                                if ($out_status['out']==0) {
                                    echo '<p><a href="student-login.php?id='.$refno.'&log-out"><button class="btn btn-primary">Logout</button></a></p>';
                                }
                                else {
                                    echo '<p style="color:lightgreen">Visited</p>';
                                }
                            }
                            else{
                                echo '<p><a href="student-login.php?id='.$refno.'&log-in"><button class="btn btn-info">Login</button></a></p>';
                            }
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <style>
    .table-out {
        margin-top: 2cm;
    }

    table tr td {
        font-size: 15px;
    }

    table tr td p a button {
        width: 50%;
    }
    table tr #student img {
        width:4.5cm;
        height: 5cm;
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
        width: 100%;
        top: 5mm;
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

    .wrap-content {
        width: 80%;
        margin: 5mm 10% 0 10%;
        padding: 20px;
    }
    .wrap-content img{
        height:1.5cm;
    }
    #default-data {
        text-align: center;
        margin-top: 5%;
    }
    #default-data img{
        width:500px;
        height:400px;
        opacity: 0.8;
        margin-top:1cm;
        transition:0.5s;
    }
    #default-data img:hover{
        opacity: 1;
        scale:calc(1.1);
    }
    </style>
    <script>
    function searchTable() {
        handelTable();
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

    function handelTable() {
        let data_table = document.getElementById("table");
        let default_data = document.getElementById("default-data");

        let input = document.getElementById("searchInput");
        if (input.value == '') {
            data_table.style.display = 'none';
            default_data.style.display = '';
        } else {
            data_table.style.display = '';
            default_data.style.display = 'none';
        }
    }
    // document.getElementById("searchInput").addEventListener("keyup", searchTable);
    // document.getElementById("searchInput").addEventListener("change", searchTable);

    function clearSearch() {
        document.getElementById("searchInput").value = "";
        searchTable();

    }
    document.getElementById("table").style.display = "none";
    </script>
</body>