<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantau Portofolio</title>
    <!-- link ke bootstrap css -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 30px;
        }

        h1 {
            color: #343a40;
            text-align: center;
            margin-bottom: 40px;
        }

        .top-bar {
            background-color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }

        .top-bar .user {
            font-size: 16px;
        }

        .top-bar .menu a {
            margin-right: 15px;
            text-decoration: none;
            color: #333;
        }

        .top-bar .menu .dashboard {
            color: #ff4500;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- top bar untuk user dan menu navigasi -->
    <div class="top-bar">
        <div class="user">Crypto</div>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="crypto.php">Watchlist</a>
            <a href="portofolio.php">Portofolio</a>
            <a href="pantau.php" class="dashboard">Pantau Portofolio</a>
        </div>
    </div>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Pantau Portofolio Crypto</h1>
        <div id="chartsContainer" class="d-flex flex-wrap"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script>
        const config = require('./config');

        console.log('API KEY 4:', config.apiKey4);
        console.log('API URL 4:', config.apiUrl4);

        $(document).ready(function() {
            const apiKey = `${config.apiKey4}`;
            const quoteUrl = `${config.apiUrl4}`;

            // Fungsi untuk memuat dan menampilkan grafik portofolio
            function loadPortfolio() {
                let portfolio = JSON.parse(localStorage.getItem('cryptoPortfolio')) || [];

                if (portfolio.length === 0) {
                    console.log("No portfolio data found in localStorage.");
                    return;
                }

                $('#chartsContainer').empty();

                portfolio.forEach(coin => {
                    fetch(`${quoteUrl}?id=${coin.id}&convert=IDR`, {
                            method: 'GET',
                            headers: {
                                Accept: 'application/json',
                                'X-CMC_PRO_API_KEY': apiKey
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const chartContainerId = `chart-${coin.symbol}`;
                            const chartContainer = `<div class="chart" id="${chartContainerId}" style="width: 100%; height: 400px; margin: 20px 0;"></div>`;
                            $('#chartsContainer').append(chartContainer);
                            new TradingView.widget({
                                "container_id": chartContainerId,
                                "width": "100%",
                                "height": 400,
                                "symbol": `BINANCE:${coin.symbol}USDT`,
                                "interval": "D",
                                "timezone": "Etc/UTC",
                                "theme": "light",
                                "style": "1",
                                "locale": "id",
                                "toolbar_bg": "#f1f3f6",
                                "enable_publishing": false,
                                "allow_symbol_change": true,
                                "hide_top_toolbar": false,
                                "withdateranges": true,
                                "details": true,
                                "hotlist": true,
                                "calendar": true,
                                "news": [
                                    "headlines"
                                ],
                                "studies": [
                                    "MACD@tv-basicstudies",
                                    "RSI@tv-basicstudies"
                                ],
                                "show_popup_button": true,
                                "popup_width": "1000",
                                "popup_height": "650"
                            });
                        })
                        .catch(error => console.error('Error:', error));
                });
            }

            loadPortfolio();
        });
    </script>
</body>

</html>