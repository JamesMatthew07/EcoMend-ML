<!DOCTYPE html>
<html lang="en">

<?php
$servername = isset($servername) ? $servernamet : 'localhost';
$username = isset($username) ? $username : 'root';
$password = isset($password) ? $password : '';
$database = isset($database) ? $database : 'detection_history';

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detection History</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        .history-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .history-header {
            background: #446646;
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .history-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #7cb342;
        }

        .history-date {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .confidence-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin: 0.5rem 0;
        }

        .confidence-fill {
            height: 100%;
            background: #7cb342;
            transition: width 0.3s ease;
        }

        .recommendations {
            background: #fff3e0;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .back-button {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="history-container">
        <div class="history-header">
            <button onclick="window.location.href='index.html'" class="back-button">←</button>
            <h1 class="m-0">Detection History</h1>
            <div style="width: 24px"></div>
        </div>
        
        <div id="history-list">
        </div>
    </div>

    <script>
    async function loadHistory() {
        try {
            const response = await fetch('get_history.php');
            const text = await response.text();
            console.log('Response text:', text); 
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                if (text.includes('<br />')) {
                    throw new Error('Server error: ' + text);
                } else {
                    throw new Error('Invalid JSON response');
                }
            }
            
            const historyList = document.getElementById('history-list');
            
            if (!data || !data.data || data.data.length === 0) {
                historyList.innerHTML = `
                    <div class="empty-state">
                        <h3>No detections yet</h3>
                        <p>Start scanning items to build your history</p>
                    </div>
                `;
                return;
            }

            historyList.innerHTML = data.data.map(item => `
                <div class="history-card">
                    <div class="history-date">
                        ${new Date(item.processed_at).toLocaleString()}
                    </div>
                    <h3>${item.detected_item}</h3>
                    <div class="confidence-bar">
                        <div class="confidence-fill" style="width: ${item.confidence}%"></div>
                    </div>
                    <small>Confidence: ${parseFloat(item.confidence).toFixed(2)}%</small>
                    <div class="recommendations">
                        ${formatRecommendations(item.recommendations)}
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading history:', error);
            document.getElementById('history-list').innerHTML = '<div class="empty-state">Failed to load history. Please try again later.</div>';
        }
    }

    function formatRecommendations(recommendations) {
        const recs = recommendations.split('\n\n');
        return recs.map(rec => {
            const lines = rec.split('\n');
            return `
                <div class="mb-2">
                    <strong>${lines[0]}</strong>
                    <p class="mb-0">${lines.slice(1).join(' ')}</p>
                </div>
            `;
        }).join('');
    }

    loadHistory();
</script>
</body>
</html>