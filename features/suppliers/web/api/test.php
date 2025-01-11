<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat UI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
        }
        .messenger-container {
            width: 100%;
            background-color: #f1f1f1;
            overflow-y: auto;
            border-right: 1px solid #ccc;
            padding: 10px;
        }
        .messenger-item {
            padding: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }
        .messenger-item:hover {
            background-color: #e6e6e6;
        }
        .messenger-item img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        .messenger-item .details {
            flex-grow: 1;
        }
        .messenger-item .details .name {
            font-weight: bold;
        }
        .messenger-item .details .message {
            font-size: 0.9em;
            color: #666;
        }
        .messenger-item .time {
            font-size: 0.8em;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="messenger-container">
        <div class="messenger-item">
            <img src="https://via.placeholder.com/40" alt="User">
            <div class="details">
                <div class="name">Diana Bustamante</div>
                <div class="message">You: Gagawin ko palang...</div>
            </div>
            <div class="time">1m</div>
        </div>
        <div class="messenger-item">
            <img src="https://via.placeholder.com/40" alt="User">
            <div class="details">
                <div class="name">Capstone</div>
                <div class="message">Eom: Ok ok</div>
            </div>
            <div class="time">1m</div>
        </div>
        <div class="messenger-item">
            <img src="https://via.placeholder.com/40" alt="User">
            <div class="details">
                <div class="name">Eom</div>
                <div class="message">Naka alis na kayo boss</div>
            </div>
            <div class="time">6m</div>
        </div>
       
       
        <div class="messenger-item">
            <img src="https://via.placeholder.com/40" alt="User">
            <div class="details">
                <div class="name">Ivan Ablanida</div>
                <div class="message">You sent a photo.</div>
            </div>
            <div class="time">7h</div>
        </div>
       
    </div>
</body>
</html>
