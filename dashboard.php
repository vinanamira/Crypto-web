<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Market Data</title>
    <!-- link ke bootstarp css -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- link datatables css -->
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        /* tampilan untuk halaman */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        /* tampilan untuk container */
        .container {
            margin-top: 30px;
        }
        /* tampilan untuk judul */
        h1 {
            color: #343a40;
            text-align: center;
            margin-bottom: 40px;
        }
        /* tampilan untuk header table */
        .table thead th {
            background-color: #615EFC;
            color: white;
        }
        /* tampilan untuk baris table saat di hover */
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        /* tampilan untuk topbar */
        .top-bar {
            background-color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }
        /* tampilan untuk teks user di top bar */
        .top-bar .user {
            font-size: 16px;
        }
        /* tampilan untuk menu di topbar */
        .top-bar .menu a {
            margin-right: 15px;
            text-decoration: none;
            color: #333;
        }
        /* tampilan khusus untuk link dashboard di menu */
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
            <a href="dashboard.php"class="dashboard">Dashboard</a>
            <a href="crypto.php">Watchlist</a>
            <a href="portofolio.php">Portofolio</a>
            <a href="pantau.php">Pantau Portofolio</a>
        </div>
    </div>
    <!-- kontainer utama -->
    <div class="container">
        <!-- judul  halaman-->
        <h1 class="my-4">Dashboard</h1>
        <!-- tabel untuk menampilkan data koin -->
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
        <!-- link ke jquery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <!-- link ke datatables js -->
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        const config = require('./config');
        
        console.log('API KEY 1:', config.apiKey1);
        console.log('API URL 1:', config.apiUrl1);
        

        $(document).ready(function () {
            // definisi API coinmarketcap
            const url = `${config.apiUrl1}`;

            // tetapkan parameter untuk permintaan API
            const parameters = {
                start: '1',  //memulai dari urutan pertama
                limit: '100', //mengambil data maksimal 100 mata uang crypto
                convert: 'IDR' //konversi harga ke rupiah
            };
            // menetapkan header untuk permintaan API
            const headers = {
                Accept: 'application/json', //menerima respon dalam format json
                'X-CMC_PRO_API_KEY': config.apiKey1 // API key pribadi
            };

            const qs = new URLSearchParams(parameters).toString(); //konversi parameter ke dalam query string
                                                                    // awalnya itu dari objek dijadikan string
            const requestUrl = `${url}?${qs}`; // membuat iurl lengkap dengan query string, disatuiin url dan qs

            // melakukan fetch ke API dengan metode get dan header yg telah ditetapkan
            fetch(requestUrl, {
                method: 'GET',
                headers: headers
            })
                .then(response => {
                    // mengecek jika respons dari server tidak ok, menampilkan eror
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    // mengembalikan respon ke json
                    return response.json();
                })
                .then(data => {
                    // mengisialisasi datatable dengan data yg didapat dari API
                    const coinTable = $('#coinTable').DataTable({
                        data: data.data, // mengisi table dengan data dari API
                        columns: [
                            { data: 'cmc_rank' }, // kolom untuk peringkat CMC
                            { data: 'name'}, // kolom untuk nama koin
                            { data: 'symbol' }, //kolom untuk simbol koin
                            {
                                data: 'quote.IDR.price', // kolom untuk harga dalam rupiah/IDR
                                render: function (data) {
                                    // format volume kedalam format rupiah, datanya dijadikan ke desimal dijadikan ke lokal string
                                    // currency dan idr di konversi ke float untuk desimal
                                    return parseFloat(data).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                                }
                            },
                            {
                                data: 'quote.IDR.volume_24h',
                                render: function (data) {
                                    return parseFloat(data).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                                }
                            },
                            {
                                data: 'quote.IDR.percent_change_1h',
                                render: function (data) {
                                    return parseFloat(data).toFixed(2) + "%";
                                }
                            },
                            {
                                data: 'quote.IDR.percent_change_24h',
                                render: function (data) {
                                    return parseFloat(data).toFixed(2) + "%";
                                }
                            },
                            {
                                data: 'quote.IDR.percent_change_7d',
                                render: function (data) {
                                    return parseFloat(data).toFixed(2) + "%";
                                }
                            },
                        ],
                        order: [[0, 'asc']],
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
