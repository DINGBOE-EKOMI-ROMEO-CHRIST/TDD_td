<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chirper</title>
</head>
<body>
    <h1>Welcome to Chirper</h1>

    @foreach ($chirps as $chirp)
        <div>{{ $chirp->message }}</div>
    @endforeach
</body>
</html>
