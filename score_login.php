<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      background: #f0f2f5;
    }
    form {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      width: 90%; 
      max-width: 360px; 
      box-sizing: border-box;
    }
    h2 {
      text-align: center;
      color: #2c3e50;
      margin-top: 0;
      font-size: 24px;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
      color: #666;
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px; 
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px; 
      box-sizing: border-box;
      -webkit-appearance: none;
    }
    button {
      width: 100%;
      padding: 14px;
      background: #2c3e50;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:active {
      background: #1a252f;
      transform: scale(0.98); /* 押した感の演出 */
    }
  </style>
</head>
<body>
  <form action="score_login_act.php" method="POST">
    <h2>JSSS</h2>
    
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autocomplete="username">
    
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="current-password">
    
    <button type="submit">Login</button>
  </form>
</body>
</html>