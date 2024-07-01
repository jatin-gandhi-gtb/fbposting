<!DOCTYPE html>
<html>
<head>
    <title>Post to Facebook</title>
</head>
<body>
    @if (Auth::check())
        <form action="/post-to-facebook" method="POST">
            @csrf
            <label for="message">Message:</label>
            <input type="text" id="message" name="message">
            <button type="submit">Post to Facebook</button>
        </form>
    @else
        <a href="/auth/facebook">Login with Facebook</a>
    @endif
</body>
</html>
