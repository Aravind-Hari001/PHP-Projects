<?php
include 'connection.php';
$db=new DB();
if (isset($_GET['delete-data'])) {
    $id=$_GET['id'];
    $db->dml("DELETE FROM `student-login` WHERE `sno`=$id");
    $db->dml("ALTER TABLE `student-login` DROP `sno`;");
    $db->dml("ALTER TABLE `student-login` ADD `sno` SERIAL NOT NULL FIRST;");
    // header('manuplate-student-login.php');
}
?>
<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">

<body style="background: #000;color:#fff;">


    <div class="d-flex head-row">
        <div class="input-group search">
            <input type="text" id="searchInput" placeholder="Search...">
            <button class="btn btn-primary px-3" onclick="clearSearch()">X</button>
        </div>
        <h3 class="mx-4">Student&nbsp;Login</h3>
    </div>
    <table class="table table-dark table-out">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Ref. No.</th>
                <th scope="col">Student Name</th>
                <th scope="col">Course</th>
                <th scope="col">Timing</th>
                <th scope="col">Date</th>
                <th scope="col">In-Time</th>
                <th scope="col">Out-Time</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php 
            $sql1="SELECT * FROM `student-login` ORDER BY `date` DESC";
            $res1=$db->dql($sql1);
            $sno=0;
            while ($data=mysqli_fetch_assoc($res1)) {
                $sno+=1;
                $id=$data['id'];
                if($data['out']==0){
                    $data['out']='<td style="color:lightgreen">in class</td>';
                 }
                 else{
                     $data['out']='<td>'.$data['out'].'</td>';
                 }
            ?>
            <tr class="data-row" style="background: #000;color:#fff;">
                <td scope="row"><?php echo $sno?></td>
                <td>ASVKS <?php echo $data['id']?></td>
                <td><?php echo $data['name']?></td>
                <td><?php echo $data['course']?></td>
                <td><?php echo $data['timing']?></td>
                <td><?php echo $data['date']?></td>
                <td><?php echo $data['in']?></td>
                <?php echo $data['out']?>
                <td>
                    <button class="btn btn-sm btn-danger"
                            onclick="callAlert(<?php echo $data['sno']?>)">Delete</button>
                </td>

            </tr>
            <?php } ?>
        </tbody>
    </table>
    <style>
    .table-out {
        margin-top: 2cm;
    }

    table tr td {
        font-size: 15px;
    }
    table tr td img {
        width: 40mm;
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
                window.location.href = "manuplate-student-login.php?id=" + id + "&delete-data";
        }
    }
    </script>
</body>