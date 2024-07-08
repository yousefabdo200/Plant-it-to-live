<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <h1>Plant it to live </h1>
    <p>Dear {{ $name }},</p>
    <p>We received a request to reset your password for your account.</p>
    <p>Please click the following link to reset your password:</p>
    <a href="http://localhost:3000/forgotPasswordSecond?token={{$token}}&user=true">Reset Password (valid for one hour)</a>
    <p>If you did not request this password reset, please ignore this email.</p>
    <p>Thank you.</p>
</body>
</html>
