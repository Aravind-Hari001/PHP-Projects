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
        <h3 class="mx-4">Student&nbsp;Data</h3>
    </div>
    <table class="table table-dark table-out">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Image</th>
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
                            </tr>
                        </thead>
                    </table>
                </th>
                <th scope="col">Balance</th>
                <th scope="col">Payment Status</th>
                <th scope="col">Course Status</th>
                <th scope="col">Certificate</th>
                <th scope="col">Issue Date</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php 
            $sql1="SELECT * FROM `form` ORDER BY `doj` DESC";
            $res1=$db->dql($sql1);
            $id=0;
            echo "<script>let data1,data2,data3;</script>";
            while ($data=mysqli_fetch_assoc($res1)) {
                $refno=$data['id'];
                $sql2="SELECT * FROM `bill` WHERE `id`=$refno;";
                $res2=$db->dql($sql2);
                $id+=1;
        ?>
            <tr class="data-row" style="background: #000;color:#fff;">
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
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </td>
                <td><?php echo $data['balance']?></td>
                <td <?php echo "id='payment".$id."'";?>></td>
                <td <?php echo "id='course-status".$id."'";?>></td>
                <td <?php echo "id='certificate".$id."'";?>></td>
                <td><?php echo $data['issue_date']?></td>
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
    <style>
    .table-out {
        margin-top: 2cm;
    }

    table tr td {
        font-size: 15px;
    }

    table tr td img {
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