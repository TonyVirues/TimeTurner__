<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TimeTurner</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="/assets/css/custom.css">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    

</head>
<body>

<!--Header-->
<header>

    <div class="">

        <h1>TimeTurner</h1>

    </div>
</header>

<!-- Main content -->

<section class="main">
    
    <div class="container">
        <div class="col-12">
            <div id ="calendar"class="col-12" ></div>
        </div>
    </div>

</section>


<!--footer-->

<footer>
    <div class="environment fixed-bottom"">

        <p>Gestor de turnos, con increible tecnologia actualizada esta guaop</p>

        <p>Environment: <?= ENVIRONMENT ?></p>

    </div>

    <div class="copyrights">

        <p>&copy; <?= date('Y') ?> CodeIgniter Foundation. CodeIgniter is open source project released under the MIT
            open source licence.</p>

    </div>
</footer>




<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src='assets/main_calendar/calendar.js'></script>
<script src='assets/javascript/js.js'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
