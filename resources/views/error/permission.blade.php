<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ERROR PERMISSION</title>
    <style>
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <h1>403-Access Denied</h1>
    <p>Anda tidak memiliki izin untuk mengakses halaman ini!</p>
    <a href="{{route('login')}}">Login Kembali</a>
</body>
</html>
