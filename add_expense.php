<?php
include 'header.php';
include 'db.php'; 

// Initialize variables for expenses
$type = '';
$description = '';
$category = '';
$amount = ''; 
$date = date('Y-m-d'); 
$time = date('H:i'); 
$errors = [];
$successMessage = '';

// Handle form submission for expenses
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_category'])) {
    $type = $_POST['type'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $otherCategory = $_POST['other_category'] ?? ''; 

    if ($category === 'Other' && !empty($otherCategory)) {
        $user_id = $_SESSION['user_id']; 
        $insertCategory = $conn->prepare("INSERT INTO categories (category_name, type, user_id) VALUES (?, ?, ?)");
        $insertCategory->bind_param("ssi", $otherCategory, $type, $user_id);
        if (!$insertCategory->execute()) {
            $errors[] = "Error adding category: " . $insertCategory->error;
        }
        $category = $otherCategory;
    }

    if (empty($type)) $errors[] = 'Type is required.';
    if (empty($category)) $errors[] = 'Category is required.';
    if (empty($amount) || !is_numeric($amount)) $errors[] = 'Please enter a valid amount.';
    $description = !empty($description) ? $description : 'N/A';

    if (empty($errors)) {
        try {
            $datetime = $date . ' ' . $time;
            $user_id = $_SESSION['user_id'];

            $stmt = $conn->prepare(
                "INSERT INTO expenses (user_id, type, amount, description, category, date, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->bind_param("isssss", $user_id, $type, $amount, $description, $category, $datetime);

            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $successMessage = ($type == 'Income') ? 'Your Income added successfully!' : 'Your Expense added successfully!';
            $_SESSION['success_message'] = $successMessage;
            header('Location: user_dashboard.php');
            exit;
        } catch (Exception $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}

// Handle category deletion
if (isset($_POST['delete_category'])) {
    $categoryId = $_POST['category_id'];
    $deleteCategory = $conn->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
    $deleteCategory->bind_param("ii", $categoryId, $_SESSION['user_id']);
    if ($deleteCategory->execute()) {
        $successMessage = "Category deleted successfully.";
    } else {
        $errors[] = "Error deleting category: " . $deleteCategory->error;
    }
}

// Fetch categories from the database
$user_id = $_SESSION['user_id'];
$categories = [];
$sql = "SELECT * FROM categories WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Separate categories by type
$incomeCategories = array_filter($categories, fn($cat) => $cat['type'] === 'Income');
$expenseCategories = array_filter($categories, fn($cat) => $cat['type'] === 'Expense');

// Fetch static categories
$staticCategories = [];
$sql = "SELECT * FROM static_categories";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $staticCategories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense/Income - Expense Tracker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4" style="margin-top: -50px;">Add Expense/Income</h2>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                <?php endif; ?>

                <form action="add_expense.php" method="POST">
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="Income" <?= ($type == 'Income') ? 'selected' : '' ?>>Income</option>
                            <option value="Expense" <?= ($type == 'Expense') ? 'selected' : '' ?>>Expense</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($description) ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <?php foreach ($staticCategories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['category_name']) ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="other_category" name="other_category" placeholder="Enter new category" style="display: none;">
                    </div>

                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?= htmlspecialchars($amount) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="datetime">Date & Time</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <input type="time" class="form-control" id="time" name="time" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Add Entry</button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-danger btn-lg btn-block" data-toggle="modal" data-target="#manageCategoriesModal">Custom Entry</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Manage Categories Modal -->
<div class="modal fade" id="manageCategoriesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Custom Categories</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php foreach ($categories as $cat): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <?= htmlspecialchars($cat['category_name']) ?>
                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" data-category-id="<?= $cat['id'] ?>">Delete</button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="add_expense.php">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="category_id" value="">
                    <p>Are you sure you want to delete this category?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_category" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Set the current time in the time input
    const setCurrentTime = () => {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        $('#time').val(`${hours}:${minutes}`);
    };

    // Populate categories based on selected type
    const populateCategories = () => {
        const selectedType = $('#type').val();
        const categorySelect = $('#category');
        const otherCategoryInput = $('#other_category');

        categorySelect.empty();
        categorySelect.append('<option value="">Select Category</option>');

        // Add static categories
        <?php foreach ($staticCategories as $cat): ?>
            if (selectedType === '<?php echo $cat['type']; ?>') {
                categorySelect.append('<option value="<?php echo htmlspecialchars($cat['category_name']); ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>');
            }
        <?php endforeach; ?>

        // Add user custom categories based on selected type
        <?php foreach ($incomeCategories as $cat): ?>
            if (selectedType === 'Income') {
                categorySelect.append('<option value="<?php echo htmlspecialchars($cat['category_name']); ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>');
            }
        <?php endforeach; ?>

        <?php foreach ($expenseCategories as $cat): ?>
            if (selectedType === 'Expense') {
                categorySelect.append('<option value="<?php echo htmlspecialchars($cat['category_name']); ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>');
            }
        <?php endforeach; ?>

        categorySelect.append('<option value="Other">Other</option>');
        categorySelect.trigger('change');
    };

    // Show or hide the other category input
    $('#category').on('change', function() {
        if ($(this).val() === 'Other') {
            $('#other_category').show();
        } else {
            $('#other_category').hide().val(''); // Reset the "Other" category input
        }
    });

    // Update categories when type changes
    $('#type').on('change', function() {
        populateCategories();
        $('#other_category').hide(); // Hide the "Other" input if type changes
    });

    // Populate delete modal with category data
$('#deleteModal').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const categoryId = button.data('category-id');
    
    // Set the category_id in the hidden input field
    $(this).find('#category_id').val(categoryId);
});


    // Initial population of categories and setting the current time
    setCurrentTime(); // Call the function to set the time
    populateCategories(); // Initial population of categories
});

</script>
</body>
</html>
