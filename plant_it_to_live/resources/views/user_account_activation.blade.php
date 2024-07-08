<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account Activation</title>
</head>
<body>
    <h1>Plant it to live </h1>
    <p>Dear {{ $user->name }}, Wlecome to our Page</p>
    <p>Please click the following link to activate your account:</p>
    <a href="http://localhost:3000/activate?token={{$token}}">Activate Account "this link is active for only one hour"</a>
    <p>If you did not request this activation, please ignore this email.</p>
    <p>Thank you.</p>
</body>
</html>
