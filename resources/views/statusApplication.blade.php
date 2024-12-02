<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thông báo: Trạng thái đơn ứng tuyển đã được cập nhật</title>
    <style>
        .highlight {
            font-weight: bold;
            color: red;
        }
    </style>
</head>
<body>
    <h1>Thông báo: Trạng thái đơn ứng tuyển đã được cập nhật</h1>
    <p>Xin chào,<span class="highlight">{{ $user->username }}</span> </p>
    <p>Cảm ơn bạn đã nộp ứng tuyển tại  <span class="highlight">{{ $company->company_name }} </span>  </p>
    <p>Trạng thái đơn ứng tuyển cho công việc <span class="highlight">{{ $job->position }} </span> của bạn đang được công ty <span class="highlight">{{ $applicationStatus }}</span>.</p>
    <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.</p>
</body>
</html>
