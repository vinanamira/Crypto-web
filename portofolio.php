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
            <a href="dashboard.php"">Dashboard</a>
            <a href=" crypto.php">Watchlist</a>
            <a href="portofolio.php" class="dashboard">Portofolio</a>
            <a href="pantau.php">Pantau Portofolio</a>
        </div>
    </div>

    <div class="container mt-4">
        <h1 class="text-center mb-4">My Crypto Portfolio</h1>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAssetModal">Add Asset</button>
            <div>
                <label for="filterCoin">Filter by Coin:</label>
                <select id="filterCoin" class="form-control">
                    <option value="">All Coins</option>
                    <!-- Options will be added dynamically -->
                </select>
            </div>
        </div>
        <table class="table table-striped table-bordered portfolio-table">
            <thead>
                <tr>
                    <th>Coin</th>
                    <th>Amount</th>
                    <th>Purchase Price (IDR)</th>
                    <th>Total Cost (IDR)</th>
                    <th>Current Price (IDR)</th>
                    <th>Current Value (IDR)</th>
                    <th>Profit/Loss</th>
                </tr>
            </thead>
            <tbody id="portfolioBody"></tbody>
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align:right">Total Portfolio Value:</th>
                    <th id="totalValue"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="modal fade" id="addAssetModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Asset</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addAssetForm">
                        <div class="form-group">
                            <label for="coinSelect">Select Coin:</label>
                            <select id="coinSelect" class="form-control" style="width: 100%">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="investment">Investment Amount (IDR):</label>
                            <input type="number" id="investment" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount:</label>
                            <input type="number" id="amount" class="form-control" step="0.000001" readonly>
                        </div>
                        <div class="form-group">
                            <label for="price">Purchase Price (IDR):</label>
                            <input type="number" id="price" class="form-control" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Asset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const config = require('./config');

        console.log('API KEY 1:', config.apiKey1);
        console.log('API URL 1:', config.apiUrl1);
        console.log('API KEY 4:', config.apiKey4);
        console.log('API URL 4:', config.apiUrl4);

        $(document).ready(function() {
            const listUrl = `${apiUrl1}`;
            const quoteUrl = `${apiUrl4}`;

            function loadPortfolio() {
                let portfolio = JSON.parse(localStorage.getItem('cryptoPortfolio')) || [

                ];

                let totalValue = 0;
                $('#portfolioBody').empty();
                $('#filterCoin').empty().append('<option value="">All Coins</option>');

                portfolio.forEach(coin => {
                    fetch(`${quoteUrl}?id=${coin.id}&convert=IDR`, {
                            method: 'GET',
                            headers: {
                                Accept: 'application/json',
                                'X-CMC_PRO_API_KEY': config.apiKey1
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const coinData = data.data[coin.id];
                            const currentPrice = coinData.quote.IDR.price;
                            const totalCost = coin.amount * coin.purchasePrice;
                            const currentValue = coin.amount * currentPrice;
                            const profitLoss = ((currentValue - totalCost) / totalCost) * 100;

                            totalValue += currentValue;

                            const row = `
                            <tr data-coin="${coin.symbol}">
                                <td>${coinData.name} (${coin.symbol})</td>
                                <td>${coin.amount}</td>
                                <td>${coin.purchasePrice.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</td>
                                <td>${totalCost.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</td>
                                <td>${currentPrice.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</td>
                                <td>${currentValue.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })}</td>
                                <td style="color:${profitLoss > 0 ? 'green' : 'red'}">${profitLoss.toFixed(2)}%</td>
                            </tr>
                        `;
                            $('#portfolioBody').append(row);
                            $('#filterCoin').append(`<option value="${coin.symbol}">${coin.name} (${coin.symbol})</option>`);
                            $('#totalValue').text(totalValue.toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }));
                        })
                        .catch(error => console.error('Error:', error));
                });
            }

            function loadCoinList() {
                fetch(`${listUrl}?start=1&limit=200&convert=IDR`, {
                        method: 'GET',
                        headers: {
                            Accept: 'application/jFson',
                            'X-CMC_PRO_API_KEY': config.apiKey4
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        $('#coinSelect').empty();
                        data.data.forEach(coin => {
                            $('#coinSelect').append(`<option value="${coin.id}" data-symbol="${coin.symbol}" data-name="${coin.name}">${coin.name} (${coin.symbol})</option>`);
                        });
                        $('#coinSelect').select2();
                    })
                    .catch(error => console.error('Error:', error));
            }

            $('#coinSelect, #investment').change(function() {
                const coinId = $('#coinSelect').val();
                const investment = parseFloat($('#investment').val());

                if (coinId && investment) {
                    fetch(`${quoteUrl}?id=${coinId}&convert=IDR`, {
                            method: 'GET',
                            headers: {
                                Accept: 'application/json',
                                'X-CMC_PRO_API_KEY': config.apiKey4
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const currentPrice = data.data[coinId].quote.IDR.price;
                            const amount = investment / currentPrice;

                            $('#amount').val(amount.toFixed(6));
                            $('#price').val(currentPrice.toFixed(2));
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            $('#addAssetForm').submit(function(e) {
                e.preventDefault();
                const coinId = $('#coinSelect').val();
                const coinSymbol = $('#coinSelect option:selected').data('symbol');
                const coinName = $('#coinSelect option:selected').data('name');
                const amount = parseFloat($('#amount').val());
                const price = parseFloat($('#price').val());

                let portfolio = JSON.parse(localStorage.getItem('cryptoPortfolio')) || [];
                portfolio.push({
                    id: coinId,
                    symbol: coinSymbol,
                    name: coinName,
                    amount: amount,
                    purchasePrice: price
                });
                localStorage.setItem('cryptoPortfolio', JSON.stringify(portfolio));

                $('#addAssetModal').modal('hide');
                loadPortfolio();
            });

            $('#filterCoin').change(function() {
                const selectedCoin = $(this).val();
                if (selectedCoin) {
                    $('#portfolioBody tr').hide();
                    $(`#portfolioBody tr[data-coin="${selectedCoin}"]`).show();
                } else {
                    $('#portfolioBody tr').show();
                }
            });

            $('#coinSelect').select2();
            loadPortfolio();
            loadCoinList();
        });
    </script>
</body>

</html>