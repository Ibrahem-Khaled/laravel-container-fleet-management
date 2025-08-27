<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .selector-container {
            display: flex;
            gap: 10px;
            margin: 10px;
        }

        .dropdown select {
            padding: 10px;
            font-size: 16px;
            border-radius: 20px;
            border: 1px solid #cb0c9f;
            appearance: none;
            background-color: #cb0c9f;
            cursor: pointer;
            width: 150px;
            color: #ffffff;
        }

        .dropdown {
            position: relative;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 20px;
            background-color: #cb0c9f;
            color: #ffffff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #a00d84;
        }

        button i {
            margin-right: 0;
        }
    </style>
</head>

<body>
    <div class="selector-container">
        <div class="dropdown">
            <form action="{{ url()->current() }}" method="GET">
                <select id="year-selector" name="year">
                </select>
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const yearSelect = document.getElementById("year-selector");
            const currentYear = new Date().getFullYear();
            const selectedYear = new URLSearchParams(window.location.search).get('year');

            for (let year = currentYear; year >= 2020; year--) {
                const option = document.createElement("option");
                option.value = year;
                option.textContent = year;
                if (selectedYear && selectedYear == year) {
                    option.selected = true;
                }
                yearSelect.appendChild(option);
            }
        });
    </script>
</body>

</html>
