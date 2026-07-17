<?php
$conn = mysqli_connect("localhost", "root", "", "staff");
if (!$conn) {
    die("" . mysqli_connect_error());
}
// } else {
//     echo "connection success";
// }
if (isset($_POST["save"])) {
    $name = $_POST["name"];
    $gender = $_POST["gender"];
    $age = $_POST["age"];
    $position = $_POST["pos"];
    $sql = "INSERT INTO staff(name,gender,age,position) VALUES ('$name','$gender','$age','$position' )";
    $result = mysqli_query($conn, $sql);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff</title>
    <link rel="stylesheet" href="node_modules/bootstrap-icons/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</head>

<body>
    <div class="container w-75 shadow">
        <form action="staff.php" method="post">
            <div class="row">
                <div class="col-12">
                    <label for="" class="form-label">Name:</label>
                    <input type="text" class="form-control" name="name">
                </div>
                <div class="col-12">
                    <label for="" class="form-label">Gender:</label>
                    <input type="text" class="form-control" name="gender">
                </div>
                <div class="col-12">
                    <label for="" class="form-label">Age:</label>
                    <input type="text" class="form-control" name="age">
                </div>
                <div class="col-12">
                    <label for="" class="form-label">Position:</label>
                    <select class="form-select" name="pos" id="">
                        <option value="Manager">manager</option>
                        <option value="Acccount">Account</option>
                        <option value="IT">IT</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 pb-3">
                <button class="btn btn-primary" type="submit" name="save">Save</button>
                <button class="btn btn-danger" type="reset">Clear</button>
            </div>

        </form>
    </div>

</body>

</html>