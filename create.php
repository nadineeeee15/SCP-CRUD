<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New SCP Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "connection.php";

$alert = '';

if (isset($_POST['create'])) {
    if (empty($_POST['item_number']) || empty($_POST['class']) || empty($_POST['description']) || empty($_POST['containment'])) {
        $alert = "<div class='alert alert-danger'>Please fill in all required fields.</div>";
    } else {
        $check = $connection->prepare("SELECT COUNT(*) FROM SCP WHERE item_number = ?");
        $check->bind_param("s", $_POST['item_number']);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            $alert = "<div class='alert alert-danger'>Item number already exists. Please choose a different one.</div>";
        } else {
            $insert = $connection->prepare("INSERT INTO SCP (item_number, class, description, containment, image) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("sssss", $_POST['item_number'], $_POST['class'], $_POST['description'], $_POST['containment'], $_POST['image']);
            if ($insert->execute()) {
                $alert = "<div class='alert alert-success'>Record successfully created.</div>";
            } else {
                $alert = "<div class='alert alert-danger'>Error: {$insert->error}</div>";
            }
            $insert->close();
        }
    }
}
?>

<nav id="scp-nav" class="d-flex align-items-center">
    <div class="dropdown me-3">
        <button class="btn btn-link dropdown-toggle" type="button" id="scpDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            SCP Records
        </button>
        <ul class="dropdown-menu" aria-labelledby="scpDropdown">
            <?php
            $result = $connection->query("SELECT item_number FROM SCP ORDER BY item_number ASC");
            if ($result && $result->num_rows > 0) {
                foreach ($result as $link) {
                    echo '<li><a class="dropdown-item" href="index.php?link=' . urlencode($link['item_number']) . '">' . htmlspecialchars($link['item_number']) . '</a></li>';
                }
            }
            ?>
        </ul>
    </div>
    <a href="index.php">Index Page</a>
    <a href="create.php" class="ms-3 active">Add New Record</a>
</nav>

<div class="form-container">
    <?= $alert ?>
    <h1 class="form-title">Create New SCP Record</h1>
    <form method="post" action="create.php">
        <div>
            <label class="form-label" for="item_number">Item Number <span class="required">*</span></label>
            <input type="text" name="item_number" id="item_number" class="form-control custom-input"
                   placeholder="Enter SCP item #" required value="<?= htmlspecialchars($_POST['item_number'] ?? '') ?>">
        </div>
        <div>
            <label class="form-label" for="class">Class <span class="required">*</span></label>
            <input type="text" name="class" id="class" class="form-control custom-input"
                   placeholder="Enter SCP class" required value="<?= htmlspecialchars($_POST['class'] ?? '') ?>">
        </div>
        <div>
            <label class="form-label" for="description">Description <span class="required">*</span></label>
            <textarea name="description" id="description" rows="4" class="form-control custom-textarea"
                      placeholder="Enter SCP description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="form-label" for="containment">Containment <span class="required">*</span></label>
            <textarea name="containment" id="containment" rows="4" class="form-control custom-textarea"
                      placeholder="Enter SCP containment" required><?= htmlspecialchars($_POST['containment'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="form-label" for="image">Image URL</label>
            <input type="text" name="image" id="image" class="form-control custom-input"
                   placeholder="Enter image URL" value="<?= htmlspecialchars($_POST['image'] ?? '') ?>">
        </div>
        <div class="text-center">
            <button type="submit" name="create" class="action-btn btn-blue">Create Record</button>
            <a href="index.php" class="action-btn btn-red">Back to Index</a>
        </div>
    </form>
</div>
</body>
</html>