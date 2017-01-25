<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Show Product Image</title>


</head>
<body>
<img src="{{ URL::to('/images/uploads/products/'.$image) }}">
</body>
</html>
