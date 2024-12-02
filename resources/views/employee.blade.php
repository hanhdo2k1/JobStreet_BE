<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<body>
    <b>Kính thưa: {{ $company }}</b>
    <p>Bạn nhận được đơn ứng tuyển của ứng viên: {{ $name }}</p>
    <b>Nguyên vọng:</b><p> {{ $cover_letter}}</p> <br>
    <a href={{"http://127.0.0.1:8000/storage/".$cv }} download>Tải xuống</a>
</body>
</html>