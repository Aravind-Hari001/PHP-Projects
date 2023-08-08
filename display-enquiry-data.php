<?php
include 'connection.php';
$db=new DB();
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
    <table class="table table-dark">
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
                <td><?php echo $data['feedback']?></td>
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
                    }    
                }?>
        </tbody>
    </table>
    <style>
    table {
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
    </script>
</body>