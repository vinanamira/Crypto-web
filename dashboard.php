<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Market Data</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
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

        .table thead th {
            background-color: #615EFC;
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
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
    <div class="top-bar">
        <div class="user">Crypto</div>
        <div class="menu">
            <a href="dashboard.php" class="dashboard">Dashboard</a>
            <a href="crypto.php">Watchlist</a>
            <a href="portofolio.php">Portofolio</a>
            <a href="pantau.php">Pantau Portofolio</a>
        </div>
    </div>
    <div class="container">
        <h1 class="my-4">Dashboard</h1>
        <table id="coinTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>Symbol</th>
                    <th>Price (IDR)</th>
                    <th>Volume (IDR)</th>
                    <th>1h (%)</th>
                    <th>1d (%)</th>
                    <th>1w (%)</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        const config = require('./config');

        console.log('API KEY 1:', config.apiKey1);
        console.log('API URL 1:', config.apiUrl1);


        $(document).ready(function() {
            const url = `${config.apiUrl1}`;

            const parameters = {
                start: '1', 
                limit: '100', 
                convert: 'IDR'
            };

            const headers = {
                Accept: 'application/json', 
                'X-CMC_PRO_API_KEY': config.apiKey1 
            };

            const qs = new URLSearchParams(parameters).toString(); 
            const requestUrl = `${url}?${qs}`; 

            fetch(requestUrl, {
                    method: 'GET',
                    headers: headers
                })
                .then(response => {
                        if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const coinTable = $('#coinTable').DataTable({
                        data: data.data, 
                        columns: [{
                                data: 'cmc_rank'
                            }, 
                            {
                                data: 'name'
                            }, 
                            {
                                data: 'symbol'
                            }, 
                            {
                                data: 'quote.IDR.price', 
                                render: function(data) {
                                        return parseFloat(data).toLocaleString('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR'
                                    });
                                }
                            },
                            {
                                data: 'quote.IDR.volume_24h',
                                render: function(data) {
                                    return parseFloat(data).toLocaleString('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR'
                                    });
                                }
                            },
                            {
                                data: 'quote.IDR.percent_change_1h',
                                render: function(data) {
                                    return parseFloat(data).toFixed(2) + "%";
                                }
                            },
                            {
                                data: 'quote.IDR.percent_change_24h',
                                render: function(data) {
                                    return parseFloat(data).toFixed(2) + "%";
                                }
                            },
                            {
                                data: 'quote.IDR.percent_change_7d',
                                render: function(data) {
                                    return parseFloat(data).toFixed(2) + "%";
                                }
                            },
                        ],
                        order: [
                            [0, 'asc']
                        ],
                        paging: true,
                        pageLength: 50,
                    });
                })
                .catch(error => {
                    console.error('Terjadi kesalahan :', error);
                });
        });
    </script>
</body>

</html>