<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Fallback style to detect if styles.css fails to load */
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
include "connection.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$alert = '';

if (isset($_GET['del'])) {
    $ID = $_GET['del'];
    $stmt = $connection->prepare("DELETE FROM SCP WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $ID);
        if ($stmt->execute()) {
            $alert = "<div class='alert alert-success'>Record successfully deleted</div>";
        } else {
            $alert = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $alert = "<div class='alert alert-danger'>Prepare failed: " . $connection->error . "</div>";
    }
}

$result = $connection->query("SELECT item_number FROM SCP ORDER BY item_number ASC");

$array = null;
if (isset($_GET['link'])) {
    $item_number = $_GET['link'];
    $query = $connection->prepare("SELECT * FROM SCP WHERE item_number = ?");
    $query->bind_param("s", $item_number);
    $query->execute();
    $result_scp = $query->get_result();
    if ($result_scp && $result_scp->num_rows > 0) {
        $array = $result_scp->fetch_assoc();
    }
    $query->close();
}
?>

<?= $alert ?>

<nav id="scp-nav" class="d-flex align-items-center">
    <div class="dropdown me-3">
        <button class="btn btn-link dropdown-toggle" type="button" id="scpDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            SCP Records
        </button>
        <ul class="dropdown-menu" aria-labelledby="scpDropdown">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php foreach ($result as $link): ?>
                    <li>
                        <a class="dropdown-item <?php echo (isset($_GET['link']) && $_GET['link'] === $link['item_number']) ? 'active' : ''; ?>"
                           href="index.php?link=<?= urlencode($link['item_number']) ?>">
                            <?= htmlspecialchars($link['item_number']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
    <a href="index.php" class="<?php echo !isset($_GET['link']) ? 'active' : ''; ?>">Index Page</a>
    <a href="create.php" class="ms-3">Add New Record</a>
</nav>

<div class="container">
    <div id="image-container">
        <?php if ($array): ?>
            <img src="<?= htmlspecialchars($array['image'] ?? '') ?>" alt="<?= htmlspecialchars($array['item_number'] ?? '') ?>">
        <?php else: ?>
            <img src="images/Neoneon.png" alt="SCP image">
        <?php endif; ?>
    </div>
    <div id="display">
        <h1>PHP CRUD Application</h1>
        <?php if ($array): ?>
            <h2><?= htmlspecialchars($array['item_number'] ?? '') ?></h2>
            <h3><?= htmlspecialchars($array['class'] ?? '') ?></h3>
            <p><?= nl2br(htmlspecialchars($array['description'] ?? '')) ?></p>
            <p><?= nl2br(htmlspecialchars($array['containment'] ?? '')) ?></p>
            <p>
                <a href="edit.php?edit=<?= htmlspecialchars($array['id'] ?? '') ?>" class="action-btn btn-edit">Edit</a>
                <a href="index.php?del=<?= htmlspecialchars($array['id'] ?? '') ?>" class="action-btn btn-delete"
                   onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
            </p>
        <?php else: ?>
            <p>> Secure, Contain, Protect — and now, CRUD.</p>
            <p>> Create new anomalies. Read classified entries. Update procedures. Delete threats.</p>
            <p>> Keep the SCP Foundation database accurate — one record at a time.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>