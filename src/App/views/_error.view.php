<?php
/**
 * @var Exception $error
 */
if ($error->getCode())
    http_response_code($error->getCode());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
</head>

<body>
    <div class="text-bg-dark d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="alert alert-warning text-center">
            <h3>
                <?= $error->getCode() ?>
            </h3>
            <h1>
                <?= $error->getMessage() ?>
            </h1>
        </div>
    </div>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>