<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit SCP Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body::before {
            content: "CSS not loaded! Check the path to styles.css.";
            display: none;
            color: red;
            font-size: 20px;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "connection.php";

$alert = '';
$row = [];

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $connection->prepare("SELECT * FROM SCP WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}

if (isset($_POST['update'])) {
    $update = $connection->prepare("UPDATE SCP SET item_number = ?, class = ?, description = ?, containment = ?, image = ? WHERE id = ?");
    $update->bind_param("sssssi", $_POST['item_number'], $_POST['class'], $_POST['description'], $_POST['containment'], $_POST['image'], $_POST['id']);
    if ($update->execute()) {
        $alert = "<div class='alert alert-success'>Record successfully updated</div>";
    } else {
        $alert = "<div class='alert alert-danger'>Error: {$update->error}</div>";
    }
    $update->close();
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
    <a href="index.php" class="active">Index Page</a>
    <a href="create.php" class="ms-3">Add New Record</a>
</nav>

<div class="form-container">
    <?= $alert ?>
    <h1 class="form-title">Edit SCP Record</h1>
    <form method="post" action="edit.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id'] ?? '') ?>">
        <div>
            <label class="form-label" for="item_number">Item Number</label>
            <input type="text" name="item_number" id="item_number" class="form-control custom-input"
                   placeholder="Enter SCP item #" value="<?= htmlspecialchars($row['item_number'] ?? '') ?>" required>
        </div>
        <div>
            <label class="form-label" for="class">Class</label>
            <input type="text" name="class" id="class" class="form-control custom-input"
                   placeholder="Enter SCP class" value="<?= htmlspecialchars($row['class'] ?? '') ?>" required>
        </div>
        <div>
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" rows="4" class="form-control custom-textarea"
                      placeholder="Enter SCP description" required><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="form-label" for="containment">Containment</label>
            <textarea name="containment" id="containment" rows="4" class="form-control custom-textarea"
                      placeholder="Enter SCP containment" required><?= htmlspecialchars($row['containment'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="form-label" for="image">Image URL</label>
            <input type="text" name="image" id="image" class="form-control custom-input"
                   placeholder="Enter image URL" value="<?= htmlspecialchars($row['image'] ?? '') ?>">
        </div>
        <div class="text-center">
            <button type="submit" name="update" class="action-btn btn-blue">Update Record</button>
            <a href="index.php" class="action-btn btn-red">Back to Index</a>
        </div>
    </form>
</div>
</body>
</html>