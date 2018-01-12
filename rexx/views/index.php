<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sales</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" rel="stylesheet">
      <link href="../css/styles.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery v3.2.1 -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  </head>
  <body>
    <div class="container">

        <form action="../controller/validateInput.php" method="post" class="form-signin mt-50">
          <h2 class="form-heading">Bitte geben Sie Start-und Enddatum im Format 'tt.mm.yyyy' ein<h2>
          <h3 class="form-heading">Beispiel: 21.12.2012</h3>
          <label for="startdate" class="sr-only">Startdatum</label>
          <input type="date" name="start" class="form-control mt-10 form-fields" placeholder="Startdatum">
          <label for="endate" class="sr-only">Enddatum</label>
          <input type="date" name="end" id="inputEnd" class="form-control mt-10 form-fields" placeholder="Enddatum">
          <button class="btn btn-lg btn-primary btn-block mt-10 form-fields" type="submit">Absenden</button>
        </form>

      </div>
  </body>
</html>
