<!DOCTYPE html>
<html>
<head>
    <title>Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div id="backpack" data-page="{{json_encode($page)}}">
    @inertia
</div>

<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
