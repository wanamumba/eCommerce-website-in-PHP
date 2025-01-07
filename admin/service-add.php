<?php require_once('header.php'); ?>

<?php
if (isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';

    if (empty($_POST['title'])) {
        $valid = 0;
        $error_message .= 'Title can not be empty<br>';
    }

    if (empty($_POST['content'])) {
        $valid = 0;
        $error_message .= 'Content can not be empty<br>';
    }

    $path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];

    if ($path != '') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $file_name = basename($path, '.' . $ext);
        if ($ext != 'jpg' && $ext != 'png' && $ext != 'jpeg' && $ext != 'gif') {
            $valid = 0;
            $error_message .= 'You must upload a jpg, jpeg, gif, or png file<br>';
        }
    } else {
        $valid = 0;
        $error_message .= 'You must select a photo<br>';
    }

    if ($valid == 1) {
        try {
            // Insert the record and get the last inserted ID
            $statement = $pdo->prepare("INSERT INTO tbl_service (title, content, photo) VALUES (?, ?, '')");
            $statement->execute([$_POST['title'], $_POST['content']]);
            $last_id = $pdo->lastInsertId();

            // Generate unique file name and move uploaded file
            $final_name = 'service-' . $last_id . '.' . $ext;
            if (!move_uploaded_file($path_tmp, '../assets/uploads/' . $final_name)) {
                throw new Exception('Failed to move uploaded file.');
            }

            // Update the photo field in the database
            $statement = $pdo->prepare("UPDATE tbl_service SET photo = ? WHERE id = ?");
            $statement->execute([$final_name, $last_id]);

            $success_message = 'Service is added successfully!';
            unset($_POST['title']);
            unset($_POST['content']);
        } catch (Exception $e) {
            $error_message = 'An error occurred: ' . $e->getMessage();
        }
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Add Service</h1>
    </div>
    <div class="content-header-right">
        <a href="service.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php if (!empty($error_message)): ?>
                <div class="callout callout-danger">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="callout callout-success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Title <span>*</span></label>
                            <div class="col-sm-6">
                                <input type="text" autocomplete="off" class="form-control" name="title" 
                                    value="<?php echo isset($_POST['title']) ? $_POST['title'] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Content <span>*</span></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="content" style="height:200px;"><?php echo isset($_POST['content']) ? $_POST['content'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Photo <span>*</span></label>
                            <div class="col-sm-9" style="padding-top:5px">
                                <input type="file" name="photo">(Only jpg, jpeg, gif, and png are allowed)
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require_once('footer.php'); ?>
